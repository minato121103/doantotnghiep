<?php

namespace App\Http\Controllers;

use App\Models\SteamAccount;
use App\Models\ProductSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SteamAccountController extends Controller
{
    /**
     * Display a listing of steam accounts (Admin only).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = SteamAccount::with('games');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by game
        if ($request->has('game_id') && $request->game_id) {
            $query->whereHas('games', function($q) use ($request) {
                $q->where('product_simple.id', $request->game_id);
            });
        }

        // Search by username or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['id', 'username', 'email', 'status', 'sold_at', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination (allow up to 10000 for stats)
        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, $perPage), 10000);

        $accounts = $query->paginate($perPage);

        // Transform data and add is_offline flag
        $accountsData = $accounts->getCollection()->map(function ($account) {
            $isOffline = empty($account->getAttributes()['email']) || empty($account->getAttributes()['email_password']);
            
            return [
                'id' => $account->id,
                'username' => $account->username,
                'email' => $account->getAttributes()['email'] ? '***' : null, // Hide actual email but indicate if exists
                'has_email' => !empty($account->getAttributes()['email']),
                'has_email_password' => !empty($account->getAttributes()['email_password']),
                'is_offline' => $isOffline,
                'count' => $account->count,
                'status' => $account->status,
                'sold_at' => $account->sold_at,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at,
                'games' => $account->games,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $accountsData,
            'pagination' => [
                'current_page' => $accounts->currentPage(),
                'per_page' => $accounts->perPage(),
                'total' => $accounts->total(),
                'last_page' => $accounts->lastPage(),
                'from' => $accounts->firstItem(),
                'to' => $accounts->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created steam account (Admin only).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100',
            'password' => 'required|string',
            'email' => 'nullable|email|max:255',
            'email_password' => 'nullable|string',
            'status' => 'nullable|in:available,sold,pending,suspended',
            'games' => 'required|array|min:1',
            'games.*' => 'required|exists:product_simple,id',
            'is_highlighted' => 'nullable|array',
            'is_highlighted.*' => 'boolean',
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
            // Calculate count: 10 for offline (no email), 1 for online (has email)
            $count = SteamAccount::getInitialCount($request->email, $request->email_password);
            
            $account = SteamAccount::create([
                'username' => $request->username,
                'password' => $request->password, // Will be encrypted by model accessor
                'email' => $request->email,
                'email_password' => $request->email_password, // Will be encrypted by model accessor
                'count' => $count,
                'status' => $request->status ?? 'available',
            ]);

            // Attach games
            $games = $request->games;
            $isHighlighted = $request->is_highlighted ?? [];
            
            foreach ($games as $gameId) {
                $account->games()->attach($gameId, [
                    'is_highlighted' => isset($isHighlighted[$gameId]) && $isHighlighted[$gameId]
                ]);
            }

            DB::commit();

            $account->load('games');
            $account->makeHidden(['password', 'email_password']);

            return response()->json([
                'success' => true,
                'message' => 'Steam account created successfully',
                'data' => $account
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create steam account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified steam account (Admin only).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $account = SteamAccount::with('games')->find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Steam account not found'
            ], 404);
        }

        // Hide sensitive data
        $account->makeHidden(['password', 'email_password']);

        return response()->json([
            'success' => true,
            'data' => $account
        ]);
    }

    /**
     * Update the specified steam account (Admin only).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $account = SteamAccount::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Steam account not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|string|max:100',
            'password' => 'sometimes|required|string',
            'email' => 'nullable|email|max:255',
            'email_password' => 'nullable|string',
            'count' => 'nullable|integer|min:0',
            'status' => 'nullable|in:available,sold,pending,suspended',
            'games' => 'sometimes|array|min:1',
            'games.*' => 'required|exists:product_simple,id',
            'is_highlighted' => 'nullable|array',
            'is_highlighted.*' => 'boolean',
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
            $updateData = [];
            
            if ($request->has('username')) $updateData['username'] = $request->username;
            if ($request->has('password')) $updateData['password'] = $request->password;
            if ($request->has('email')) $updateData['email'] = $request->email;
            if ($request->has('email_password')) $updateData['email_password'] = $request->email_password;
            if ($request->has('count')) $updateData['count'] = $request->count;
            if ($request->has('status')) $updateData['status'] = $request->status;

            if (!empty($updateData)) {
                $account->update($updateData);
            }

            // Update games if provided
            if ($request->has('games')) {
                $account->games()->detach();
                
                $games = $request->games;
                $isHighlighted = $request->is_highlighted ?? [];
                
                foreach ($games as $gameId) {
                    $account->games()->attach($gameId, [
                        'is_highlighted' => isset($isHighlighted[$gameId]) && $isHighlighted[$gameId]
                    ]);
                }
            }

            DB::commit();

            $account->load('games');
            $account->makeHidden(['password', 'email_password']);

            return response()->json([
                'success' => true,
                'message' => 'Steam account updated successfully',
                'data' => $account
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update steam account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified steam account (Admin only).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $account = SteamAccount::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Steam account not found'
            ], 404);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Steam account deleted successfully'
        ]);
    }

    /**
     * Find available steam account by game ID
     * This is used when creating an order
     *
     * @param int $gameId
     * @return \Illuminate\Http\JsonResponse
     */
    public function findAvailableByGame($gameId)
    {
        $account = SteamAccount::hasGame($gameId)->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'No available steam account found for this game'
            ], 404);
        }

        // Only return basic info, not credentials
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $account->id,
                'status' => $account->status,
                'has_game' => true
            ]
        ]);
    }
}

