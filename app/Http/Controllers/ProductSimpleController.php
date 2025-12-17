<?php

namespace App\Http\Controllers;

use App\Models\ProductSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductSimpleController extends Controller
{

    /**
     * hiển thị sản phẩm
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = ProductSimple::query();

        #sắp xếp
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        #tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSortFields = ['id', 'title', 'price', 'category', 'view_count', 'average_rating'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        #phân trang
        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, $perPage), 100);

        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    /**
     * tạo sản phẩm
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'price' => 'required|string|max:100',
            'image' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'detail_description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'view_count' => 'nullable|integer|min:0',
            'rating_count' => 'nullable|integer|min:0',
            'average_rating' => 'nullable|numeric|min:0|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = ProductSimple::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * đếm lượt xem sản phẩm
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = ProductSimple::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $product->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * cập nhật sản phẩm
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $product = ProductSimple::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|string|max:100',
            'image' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'detail_description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'view_count' => 'nullable|integer|min:0',
            'rating_count' => 'nullable|integer|min:0',
            'average_rating' => 'nullable|numeric|min:0|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'cập nhật sản phẩm thành công!',
            'data' => $product
        ]);
    }

    /**
     * xóa sản phẩm
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = ProductSimple::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'xóa sản phẩm thành công!'
        ]);
    }

    /**
     * lấy danh mục sản phẩm
     */
    public function categories()
    {
        $categories = ProductSimple::select('category')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}

