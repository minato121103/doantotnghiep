<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SteamAccount;
use App\Models\ProductSimple;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Order::with(['buyer', 'game', 'steamAccount']);

        // If not admin, only show user's own orders
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            if ($user) {
                $query->where('buyer_id', $user->id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }
        } else {
            // Admin can filter by buyer
            if ($request->has('buyer_id') && $request->buyer_id) {
                $query->where('buyer_id', $request->buyer_id);
            }
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by game
        if ($request->has('game_id') && $request->game_id) {
            $query->where('product_simple_id', $request->game_id);
        }

        // Search by order code
        if ($request->has('search') && $request->search) {
            $query->where('order_code', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['id', 'order_code', 'amount', 'status', 'created_at', 'completed_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, $perPage), 100);

        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created order.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_simple_id' => 'required|exists:product_simple,id',
            'payment_method' => 'required|in:balance,banking,momo,zalopay',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the game/product
        $game = ProductSimple::find($request->product_simple_id);
        if (!$game) {
            return response()->json([
                'success' => false,
                'message' => 'Game not found'
            ], 404);
        }

        // Find available steam account with this game
        $steamAccount = SteamAccount::hasGame($request->product_simple_id)->first();
        
        if (!$steamAccount) {
            return response()->json([
                'success' => false,
                'message' => 'No available steam account found for this game'
            ], 404);
        }

        // Calculate amount (using game price)
        $amount = floatval($game->price);
        $fee = 0; // Can be calculated based on payment method

        // Check balance if payment method is balance
        if ($request->payment_method === 'balance') {
            if ($user->balance < ($amount + $fee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance'
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'order_code' => Order::generateOrderCode(),
                'buyer_id' => $user->id,
                'steam_account_id' => $steamAccount->id,
                'product_simple_id' => $request->product_simple_id,
                'amount' => $amount,
                'fee' => $fee,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create order item with encrypted credentials
            $credentials = [
                'username' => Crypt::encryptString($steamAccount->username),
                'password' => Crypt::encryptString($steamAccount->password),
                'email' => Crypt::encryptString($steamAccount->email),
                'email_password' => Crypt::encryptString($steamAccount->email_password),
            ];

            OrderItem::create([
                'order_id' => $order->id,
                'steam_account_id' => $steamAccount->id,
                'product_simple_id' => $request->product_simple_id,
                'steam_credentials' => $credentials,
                'price' => $amount,
            ]);

            // Process payment if balance
            if ($request->payment_method === 'balance') {
                $balanceBefore = $user->balance;
                $balanceAfter = $balanceBefore - ($amount + $fee);
                
                $user->update([
                    'balance' => $balanceAfter,
                    'total_orders' => $user->total_orders + 1,
                    'total_spent' => $user->total_spent + ($amount + $fee),
                ]);

                // Create transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'transaction_code' => Transaction::generateTransactionCode('PUR'),
                    'type' => 'purchase',
                    'amount' => -($amount + $fee),
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'status' => 'completed',
                    'payment_method' => 'balance',
                    'description' => "Purchase order: {$order->order_code}",
                    'order_id' => $order->id,
                ]);

                // Auto complete order if paid by balance
                $order->update([
                    'status' => 'processing',
                ]);
            }

            // Mark steam account as pending
            $steamAccount->update(['status' => 'pending']);

            DB::commit();

            $order->load(['buyer', 'game', 'items']);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $order = Order::with(['buyer', 'game', 'steamAccount', 'items', 'review'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Check permission
        if (!$user || ($user->role !== 'admin' && $order->buyer_id !== $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Decrypt credentials in order items if order is completed
        if ($order->status === 'completed' && $order->items) {
            foreach ($order->items as $item) {
                if ($item->steam_credentials) {
                    try {
                        $item->steam_credentials = [
                            'username' => Crypt::decryptString($item->steam_credentials['username']),
                            'password' => Crypt::decryptString($item->steam_credentials['password']),
                            'email' => Crypt::decryptString($item->steam_credentials['email']),
                            'email_password' => Crypt::decryptString($item->steam_credentials['email_password']),
                        ];
                    } catch (\Exception $e) {
                        // Handle decryption error
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Update order status (Admin only).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,completed,cancelled,refunded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $order->status;
            $newStatus = $request->status;

            $updateData = ['status' => $newStatus];

            if ($newStatus === 'completed') {
                $updateData['completed_at'] = now();
                $order->steamAccount->update(['status' => 'sold', 'sold_at' => now()]);
            } elseif ($newStatus === 'cancelled') {
                $updateData['cancelled_at'] = now();
                $order->steamAccount->update(['status' => 'available']);
                
                // Refund if paid by balance
                if ($order->payment_method === 'balance' && $oldStatus !== 'cancelled') {
                    $buyer = $order->buyer;
                    $refundAmount = $order->amount + $order->fee;
                    $balanceBefore = $buyer->balance;
                    $balanceAfter = $balanceBefore + $refundAmount;
                    
                    $buyer->update(['balance' => $balanceAfter]);

                    Transaction::create([
                        'user_id' => $buyer->id,
                        'transaction_code' => Transaction::generateTransactionCode('REF'),
                        'type' => 'refund',
                        'amount' => $refundAmount,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'status' => 'completed',
                        'payment_method' => 'balance',
                        'description' => "Refund for cancelled order: {$order->order_code}",
                        'order_id' => $order->id,
                    ]);
                }
            }

            $order->update($updateData);

            DB::commit();

            $order->load(['buyer', 'game', 'steamAccount']);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

