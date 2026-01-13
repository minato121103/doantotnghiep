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

        #lọc theo danh mục
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        #tìm kiếm theo tên
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        #lọc theo type (online/offline)
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        #lọc theo khoảng giá (dùng giá hiện tại - số cuối trong chuỗi price)
        if ($request->has('price') && $request->price) {
            // Lấy số cuối cùng trong chuỗi price, loại bỏ . và đơn vị đ/₫ rồi ép kiểu số
            $currentPriceExpr = "CAST(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(price, ' ', -1), '.', ''), 'đ', ''), '₫', '') AS UNSIGNED)";

            switch ($request->price) {
                case '0-50000':
                    $query->whereRaw("$currentPriceExpr <= ?", [50000]);
                    break;
                case '50000-100000':
                    $query->whereRaw("$currentPriceExpr BETWEEN ? AND ?", [50000, 100000]);
                    break;
                case '100000-500000':
                    $query->whereRaw("$currentPriceExpr BETWEEN ? AND ?", [100000, 500000]);
                    break;
                case '500000+':
                    $query->whereRaw("$currentPriceExpr >= ?", [500000]);
                    break;
            }
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSortFields = ['id', 'title', 'price', 'category', 'view_count'];
        
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
            'type' => 'nullable|string|in:online,offline',
            'tags' => 'nullable|array',
            'view_count' => 'nullable|integer|min:0',
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
            'type' => 'nullable|string|in:online,offline',
            'tags' => 'nullable|array',
            'view_count' => 'nullable|integer|min:0',
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

