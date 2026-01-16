<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $query = Transaction::with(['user', 'order']);

        // If not admin, only show user's own transactions
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        } else {
            // Admin can filter by user
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by transaction code
        if ($request->has('search') && $request->search) {
            $query->where('transaction_code', 'like', '%' . $request->search . '%');
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['id', 'transaction_code', 'type', 'amount', 'status', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination (allow up to 10000 for admin stats)
        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, $perPage), 10000);

        $transactions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
                'from' => $transactions->firstItem(),
                'to' => $transactions->lastItem(),
            ]
        ]);
    }

    /**
     * Display the specified transaction.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $transaction = Transaction::with(['user', 'order'])->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        // Check permission
        if ($user->role !== 'admin' && $transaction->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    /**
     * Create a deposit transaction (Admin only or for user's own balance).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id', // Admin can deposit for any user
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Determine target user
        $targetUserId = $request->user_id ?? $user->id;

        // Check permission
        if ($targetUserId !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only deposit to your own account.'
            ], 403);
        }

        $targetUser = User::find($targetUserId);

        DB::beginTransaction();
        try {
            $balanceBefore = $targetUser->balance;
            $balanceAfter = $balanceBefore + $request->amount;

            $transaction = Transaction::create([
                'user_id' => $targetUserId,
                'transaction_code' => Transaction::generateTransactionCode('DEP'),
                'type' => 'deposit',
                'amount' => $request->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'completed', // Can be changed to pending if needed
                'payment_method' => $request->payment_method,
                'description' => $request->description ?? "Deposit to account",
            ]);

            $targetUser->update(['balance' => $balanceAfter]);

            DB::commit();

            $transaction->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Deposit successful',
                'data' => $transaction
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process deposit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a withdraw transaction request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdraw(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check balance
        if ($user->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $balanceBefore = $user->balance;
            $balanceAfter = $balanceBefore - $request->amount;

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => Transaction::generateTransactionCode('WIT'),
                'type' => 'withdraw',
                'amount' => -$request->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'pending', // Requires admin approval
                'payment_method' => $request->payment_method,
                'description' => $request->description ?? "Withdraw request",
            ]);

            // Don't update balance yet - wait for admin approval
            // $user->update(['balance' => $balanceAfter]);

            DB::commit();

            $transaction->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Withdraw request created successfully. Waiting for admin approval.',
                'data' => $transaction
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create withdraw request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update transaction status (Admin only).
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

        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed,cancelled',
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
            $oldStatus = $transaction->status;
            $newStatus = $request->status;

            // If approving a withdraw request
            if ($oldStatus === 'pending' && $newStatus === 'completed' && $transaction->type === 'withdraw') {
                $targetUser = $transaction->user;
                $targetUser->update(['balance' => $transaction->balance_after]);
            }

            // If cancelling a withdraw request, restore balance
            if ($oldStatus === 'pending' && $newStatus === 'cancelled' && $transaction->type === 'withdraw') {
                // Balance was never deducted, so nothing to restore
            }

            $transaction->update(['status' => $newStatus]);

            DB::commit();

            $transaction->load(['user', 'order']);

            return response()->json([
                'success' => true,
                'message' => 'Transaction status updated successfully',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction statistics for current user or admin view
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $query = Transaction::query();

        // If not admin, only show user's own transactions
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        } else {
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_deposits' => (clone $query)->where('type', 'deposit')->where('status', 'completed')->sum('amount'),
            'total_withdraws' => abs((clone $query)->where('type', 'withdraw')->where('status', 'completed')->sum('amount')),
            'total_purchases' => abs((clone $query)->where('type', 'purchase')->where('status', 'completed')->sum('amount')),
            'total_refunds' => (clone $query)->where('type', 'refund')->where('status', 'completed')->sum('amount'),
            'total_transactions' => $query->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

