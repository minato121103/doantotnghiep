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
        $query = Order::with(['buyer', 'game', 'steamAccount', 'items']);

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

        // Decrypt credentials for all orders (all orders are completed)
        $ordersData = $orders->items();
        foreach ($ordersData as $order) {
            if ($order->items) {
                foreach ($order->items as $item) {
                    if ($item->steam_credentials) {
                        try {
                            $item->steam_credentials = [
                                'username' => Crypt::decryptString($item->steam_credentials['username']),
                                'password' => Crypt::decryptString($item->steam_credentials['password']),
                                'email' => $item->steam_credentials['email'] ? Crypt::decryptString($item->steam_credentials['email']) : null,
                                'email_password' => $item->steam_credentials['email_password'] ? Crypt::decryptString($item->steam_credentials['email_password']) : null,
                            ];
                        } catch (\Exception $e) {
                            // Handle decryption error - keep encrypted
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $ordersData,
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
     * Create multiple orders from cart (batch checkout).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchStore(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_simple_id' => 'required|exists:product_simple,id',
            'items.*.quantity' => 'required|integer|min:1|max:10',
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

        $items = $request->items;
        $paymentMethod = $request->payment_method;
        $notes = $request->notes;

        // Validate all items and calculate total before processing
        $totalAmount = 0;
        $validatedItems = [];
        $errors = [];

        foreach ($items as $index => $item) {
            $game = ProductSimple::find($item['product_simple_id']);
            if (!$game) {
                $errors[] = "Item {$index}: Game not found";
                continue;
            }

            // Parse price
            $priceStr = $game->price ?? '';
            $amount = $this->parsePrice($priceStr);
            
            if ($amount <= 0) {
                \Log::warning('Failed to parse price', [
                    'game_id' => $item['product_simple_id'],
                    'price_string' => $priceStr,
                    'parsed_amount' => $amount
                ]);
                $errors[] = "Item {$index}: Invalid price (price: " . ($priceStr ?: 'empty') . ")";
                continue;
            }

            // Check steam account availability
            $availableCount = SteamAccount::hasGame($item['product_simple_id'])->count();
            if ($availableCount < $item['quantity']) {
                $errors[] = "Item {$index}: Only {$availableCount} account(s) available, requested {$item['quantity']}";
                continue;
            }

            $validatedItems[] = [
                'game' => $game,
                'quantity' => $item['quantity'],
                'amount' => $amount,
                'product_simple_id' => $item['product_simple_id'],
            ];

            $totalAmount += $amount * $item['quantity'];
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        // Check balance if payment method is balance
        if ($paymentMethod === 'balance') {
            if ($user->balance < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance',
                    'required' => $totalAmount,
                    'available' => $user->balance
                ], 400);
            }
        }

        // Process all orders in a single transaction
        DB::beginTransaction();
        try {
            $createdOrders = [];
            $balanceBefore = $user->balance;
            $balanceAfter = $balanceBefore;

            foreach ($validatedItems as $item) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    // Find available steam account
                    $steamAccount = SteamAccount::hasGame($item['product_simple_id'])->first();
                    
                    if (!$steamAccount) {
                        throw new \Exception("No available steam account for game ID: {$item['product_simple_id']}");
                    }

                    // Create order with completed status
                    $order = Order::create([
                        'order_code' => Order::generateOrderCode(),
                        'buyer_id' => $user->id,
                        'steam_account_id' => $steamAccount->id,
                        'product_simple_id' => $item['product_simple_id'],
                        'amount' => $item['amount'],
                        'fee' => 0,
                        'payment_method' => $paymentMethod,
                        'status' => 'completed',
                        'completed_at' => now(),
                        'notes' => $notes,
                    ]);

                    // Create order item with encrypted credentials
                    $credentials = [
                        'username' => Crypt::encryptString($steamAccount->username),
                        'password' => Crypt::encryptString($steamAccount->password),
                        'email' => $steamAccount->email ? Crypt::encryptString($steamAccount->email) : null,
                        'email_password' => $steamAccount->email_password ? Crypt::encryptString($steamAccount->email_password) : null,
                    ];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'steam_account_id' => $steamAccount->id,
                        'product_simple_id' => $item['product_simple_id'],
                        'steam_credentials' => $credentials,
                        'price' => $item['amount'],
                    ]);

                    // Decrease count and mark as sold if count reaches 0
                    $newCount = max(0, $steamAccount->count - 1);
                    $updateData = ['count' => $newCount];
                    
                    if ($newCount === 0) {
                        $updateData['status'] = 'sold';
                        $updateData['sold_at'] = now();
                    }
                    
                    $steamAccount->update($updateData);

                    $createdOrders[] = $order;
                }
            }

            // Process payment if balance
            if ($paymentMethod === 'balance') {
                $balanceAfter = $balanceBefore - $totalAmount;
                
                $user->update([
                    'balance' => $balanceAfter,
                    'total_orders' => $user->total_orders + count($createdOrders),
                    'total_spent' => $user->total_spent + $totalAmount,
                ]);

                // Create transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'transaction_code' => Transaction::generateTransactionCode('PUR'),
                    'type' => 'purchase',
                    'amount' => -$totalAmount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'status' => 'completed',
                    'payment_method' => 'balance',
                    'description' => "Batch purchase: " . count($createdOrders) . " order(s)",
                ]);
            }

            DB::commit();

            // Load relationships
            foreach ($createdOrders as $order) {
                $order->load(['buyer', 'game', 'items']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orders created successfully',
                'data' => [
                    'orders' => $createdOrders,
                    'total_amount' => $totalAmount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'count' => count($createdOrders)
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Batch checkout error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'items' => $items,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create orders',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Parse price from string to float.
     * Handles formats like "1.499.000 ₫ ... 65.000 ₫" or "800.000đ"
     *
     * @param string $priceStr
     * @return float
     */
    private function parsePrice($priceStr)
    {
        if (!$priceStr || trim($priceStr) === '') {
            return 0;
        }
        
        // First, try to extract prices with currency symbol (đ or ₫)
        // Pattern: numbers with dots/commas followed by đ or ₫
        if (preg_match_all('/[\d.,]+\s*[₫đ]/ui', $priceStr, $matches)) {
            if (!empty($matches[0])) {
                // Get the last price (current price)
                $lastPrice = trim(end($matches[0]));
                // Remove currency symbol and whitespace, then remove dots and commas
                $cleaned = preg_replace('/[₫đ\s]/ui', '', $lastPrice);
                $cleaned = str_replace(['.', ','], '', $cleaned);
                $amount = (float) $cleaned;
                if ($amount > 0) {
                    return $amount;
                }
            }
        }
        
        // Fallback: extract all numbers and get the last one
        if (preg_match_all('/[\d.,]+/', $priceStr, $matches)) {
            if (!empty($matches[0])) {
                $lastNumber = end($matches[0]);
                // Remove dots (thousands separator) and commas (decimal separator)
                // Vietnamese format: dots for thousands, comma for decimal
                $cleaned = str_replace(['.', ','], '', $lastNumber);
                $amount = (float) $cleaned;
                if ($amount > 0) {
                    return $amount;
                }
            }
        }
        
        // Last resort: try to extract any number from the string
        if (preg_match('/(\d+(?:[.,]\d+)*)/', $priceStr, $match)) {
            $cleaned = str_replace(['.', ','], '', $match[1]);
            $amount = (float) $cleaned;
            if ($amount > 0) {
                return $amount;
            }
        }
        
        return 0;
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

        // Calculate amount using parsePrice method
        $amount = $this->parsePrice($game->price);
        
        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid game price'
            ], 400);
        }
        
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
            // Create order with completed status
            $order = Order::create([
                'order_code' => Order::generateOrderCode(),
                'buyer_id' => $user->id,
                'steam_account_id' => $steamAccount->id,
                'product_simple_id' => $request->product_simple_id,
                'amount' => $amount,
                'fee' => $fee,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->notes,
            ]);

            // Create order item with encrypted credentials
            $credentials = [
                'username' => Crypt::encryptString($steamAccount->username),
                'password' => Crypt::encryptString($steamAccount->password),
                'email' => $steamAccount->email ? Crypt::encryptString($steamAccount->email) : null,
                'email_password' => $steamAccount->email_password ? Crypt::encryptString($steamAccount->email_password) : null,
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
            }

            // Decrease count and mark as sold if count reaches 0
            $newCount = max(0, $steamAccount->count - 1);
            $updateData = ['count' => $newCount];
            
            if ($newCount === 0) {
                $updateData['status'] = 'sold';
                $updateData['sold_at'] = now();
            }
            
            $steamAccount->update($updateData);

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

        // Decrypt credentials in order items (all orders are completed)
        if ($order->items) {
            foreach ($order->items as $item) {
                if ($item->steam_credentials) {
                    try {
                        $item->steam_credentials = [
                            'username' => Crypt::decryptString($item->steam_credentials['username']),
                            'password' => Crypt::decryptString($item->steam_credentials['password']),
                            'email' => $item->steam_credentials['email'] ? Crypt::decryptString($item->steam_credentials['email']) : null,
                            'email_password' => $item->steam_credentials['email_password'] ? Crypt::decryptString($item->steam_credentials['email_password']) : null,
                        ];
                    } catch (\Exception $e) {
                        // Handle decryption error - log for debugging
                        \Log::error('Failed to decrypt credentials for order item: ' . $item->id, ['error' => $e->getMessage()]);
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

    /**
     * Delete an order (Admin only).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
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

        DB::beginTransaction();
        try {
            // Delete order items first
            $order->items()->delete();
            
            // Delete the order
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

