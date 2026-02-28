<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\ProductSimple;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::withCount('products');

        if ($request->has('status')) {
            $now = now();
            if ($request->status === 'active') {
                $query->where('starts_at', '<=', $now)->where('ends_at', '>=', $now);
            } elseif ($request->status === 'upcoming') {
                $query->where('starts_at', '>', $now);
            } elseif ($request->status === 'expired') {
                $query->where('ends_at', '<', $now);
            }
        }

        $promotions = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json(['success' => true, 'data' => $promotions]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'discount_percent' => 'required|integer|min:1|max:100',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'product_simple_ids' => 'required|array|min:1',
            'product_simple_ids.*' => 'exists:product_simple,id',
        ]);

        $promotion = Promotion::create([
            'name' => $validated['name'],
            'discount_percent' => $validated['discount_percent'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
        ]);

        $promotion->products()->attach($validated['product_simple_ids']);

        return response()->json([
            'success' => true,
            'data' => $promotion->load('products'),
            'message' => 'Tạo ưu đãi thành công',
        ], 201);
    }

    public function show($id)
    {
        $promotion = Promotion::with('products')->withCount('products')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $promotion]);
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'discount_percent' => 'sometimes|integer|min:1|max:100',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'sometimes|date|after:starts_at',
            'product_simple_ids' => 'sometimes|array',
            'product_simple_ids.*' => 'exists:product_simple,id',
        ]);

        $promotion->update(collect($validated)->except('product_simple_ids')->toArray());

        if (isset($validated['product_simple_ids'])) {
            $promotion->products()->sync($validated['product_simple_ids']);
        }

        return response()->json([
            'success' => true,
            'data' => $promotion->load('products'),
            'message' => 'Cập nhật ưu đãi thành công',
        ]);
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->products()->detach();
        $promotion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa ưu đãi thành công',
        ]);
    }

    public function removeProduct($id, $productId)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->products()->detach($productId);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa game khỏi ưu đãi',
            'data' => $promotion->load('products'),
        ]);
    }
}
