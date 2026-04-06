<?php

namespace App\Http\Controllers;

use App\Models\SteamAccountGame;
use App\Models\SteamAccount;
use App\Models\ProductSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SteamAccountGameController extends Controller
{
    /**
     * Display a listing of steam account games.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = SteamAccountGame::with(['steamAccount', 'product']);

        // Filter by steam_account_id
        if ($request->has('steam_account_id') && $request->steam_account_id) {
            $query->where('steam_account_id', $request->steam_account_id);
        }

        // Filter by product_simple_id
        if ($request->has('product_simple_id') && $request->product_simple_id) {
            $query->where('product_simple_id', $request->product_simple_id);
        }

        // Filter by is_highlighted
        if ($request->has('is_highlighted') && $request->is_highlighted !== '') {
            $query->where('is_highlighted', $request->is_highlighted);
        }

        // Search by steam account username or product name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('steamAccount', function($sq) use ($search) {
                    $sq->where('username', 'like', '%' . $search . '%');
                })->orWhereHas('product', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['id', 'steam_account_id', 'product_simple_id', 'is_highlighted', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, $perPage), 10000);

        $items = $query->paginate($perPage);

        // Transform data
        $itemsData = $items->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'steam_account_id' => $item->steam_account_id,
                'product_simple_id' => $item->product_simple_id,
                'is_highlighted' => $item->is_highlighted,
                'steam_account' => $item->steamAccount ? [
                    'id' => $item->steamAccount->id,
                    'username' => $item->steamAccount->username,
                    'status' => $item->steamAccount->status,
                ] : null,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'title' => $item->product->title,
                    'image' => $item->product->image ?? null,
                    'price' => $item->product->price ?? null,
                    'category' => $item->product->category ?? null,
                ] : null,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $itemsData,
            'pagination' => [
                'current_page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created steam account game.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'steam_account_id' => 'required|exists:steam_accounts,id',
            'product_simple_id' => 'required|exists:product_simple,id',
            'is_highlighted' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check unique constraint
        $exists = SteamAccountGame::where('steam_account_id', $request->steam_account_id)
            ->where('product_simple_id', $request->product_simple_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Liên kết này đã tồn tại! Tài khoản Steam này đã có game này rồi.'
            ], 422);
        }

        try {
            $item = SteamAccountGame::create([
                'steam_account_id' => $request->steam_account_id,
                'product_simple_id' => $request->product_simple_id,
                'is_highlighted' => $request->is_highlighted ?? false,
            ]);

            $item->load(['steamAccount', 'product']);

            return response()->json([
                'success' => true,
                'message' => 'Thêm liên kết game thành công',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm liên kết game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch store: add multiple games to a steam account at once.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'steam_account_id' => 'required|exists:steam_accounts,id',
            'product_simple_ids' => 'required|array|min:1',
            'product_simple_ids.*' => 'required|exists:product_simple,id',
            'is_highlighted' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $steamAccountId = $request->steam_account_id;
        $isHighlighted = $request->is_highlighted ?? false;
        $added = [];
        $skipped = [];

        DB::beginTransaction();
        try {
            foreach ($request->product_simple_ids as $productId) {
                $exists = SteamAccountGame::where('steam_account_id', $steamAccountId)
                    ->where('product_simple_id', $productId)
                    ->exists();

                if ($exists) {
                    $skipped[] = $productId;
                    continue;
                }

                $item = SteamAccountGame::create([
                    'steam_account_id' => $steamAccountId,
                    'product_simple_id' => $productId,
                    'is_highlighted' => $isHighlighted,
                ]);
                $added[] = $productId;
            }

            DB::commit();

            $message = 'Đã thêm ' . count($added) . ' game thành công.';
            if (count($skipped) > 0) {
                $message .= ' Bỏ qua ' . count($skipped) . ' game đã tồn tại.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'added' => $added,
                    'skipped' => $skipped,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm liên kết game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified steam account game.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = SteamAccountGame::with(['steamAccount', 'product'])->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy liên kết game'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified steam account game.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $item = SteamAccountGame::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy liên kết game'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'steam_account_id' => 'sometimes|required|exists:steam_accounts,id',
            'product_simple_id' => 'sometimes|required|exists:product_simple,id',
            'is_highlighted' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check unique constraint if changing account or product
        $steamAccountId = $request->steam_account_id ?? $item->steam_account_id;
        $productSimpleId = $request->product_simple_id ?? $item->product_simple_id;

        $exists = SteamAccountGame::where('steam_account_id', $steamAccountId)
            ->where('product_simple_id', $productSimpleId)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Liên kết này đã tồn tại! Tài khoản Steam này đã có game này rồi.'
            ], 422);
        }

        try {
            $updateData = [];
            if ($request->has('steam_account_id')) $updateData['steam_account_id'] = $request->steam_account_id;
            if ($request->has('product_simple_id')) $updateData['product_simple_id'] = $request->product_simple_id;
            if ($request->has('is_highlighted')) $updateData['is_highlighted'] = $request->is_highlighted;

            if (!empty($updateData)) {
                $item->update($updateData);
            }

            $item->load(['steamAccount', 'product']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật liên kết game thành công',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật liên kết game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified steam account game.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = SteamAccountGame::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy liên kết game'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa liên kết game thành công'
        ]);
    }

    /**
     * Get statistics for steam account games.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $total = SteamAccountGame::count();
        $highlighted = SteamAccountGame::where('is_highlighted', true)->count();
        $uniqueAccounts = SteamAccountGame::distinct('steam_account_id')->count('steam_account_id');
        $uniqueGames = SteamAccountGame::distinct('product_simple_id')->count('product_simple_id');

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'highlighted' => $highlighted,
                'unique_accounts' => $uniqueAccounts,
                'unique_games' => $uniqueGames,
            ]
        ]);
    }
}
