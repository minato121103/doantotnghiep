<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->has('status')) {
            $now = now();
            if ($request->status === 'active') {
                $query->where('starts_at', '<=', $now)
                      ->where('ends_at', '>=', $now)
                      ->where(function ($q) {
                          $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
                      });
            } elseif ($request->status === 'expired') {
                $query->where('ends_at', '<', $now);
            }
        }

        $coupons = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json(['success' => true, 'data' => $coupons]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $coupon = Coupon::create($validated);

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Tạo mã ưu đãi thành công',
        ], 201);
    }

    public function show($id)
    {
        $coupon = Coupon::findOrFail($id);
        return response()->json(['success' => true, 'data' => $coupon]);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|max:50|unique:coupons,code,' . $id,
            'type' => 'sometimes|in:percent,fixed',
            'value' => 'sometimes|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'sometimes|date|after:starts_at',
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper($validated['code']);
        }

        $coupon->update($validated);

        return response()->json([
            'success' => true,
            'data' => $coupon,
            'message' => 'Cập nhật mã ưu đãi thành công',
        ]);
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa mã ưu đãi thành công',
        ]);
    }

    public function validate_coupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã ưu đãi không tồn tại',
            ], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã ưu đãi đã hết hạn hoặc đã hết lượt sử dụng',
            ], 422);
        }

        $total = (float) $request->total;
        $discount = $coupon->applyToTotal($total);

        if ($discount <= 0) {
            $minFormatted = number_format((float) $coupon->min_order_amount, 0, ',', '.');
            return response()->json([
                'success' => false,
                'message' => "Đơn hàng tối thiểu {$minFormatted}đ để áp dụng mã này",
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'coupon_id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount_amount' => $discount,
                'final_total' => max(0, $total - $discount),
            ],
            'message' => 'Áp dụng mã ưu đãi thành công',
        ]);
    }
}
