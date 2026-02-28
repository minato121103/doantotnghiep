@extends('layouts.app')

@section('title', 'Tạo Mã ưu đãi')

@section('max-width', 'max-w-3xl')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Tạo Mã ưu đãi</h1>
            <p class="text-sm text-gray-600">Tạo mã giảm giá cho đơn hàng</p>
        </div>
        <a href="{{ url('/database/coupons') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-sm text-center">← Danh sách mã</a>
    </div>

    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>
    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden"></div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form id="coupon-form" onsubmit="submitForm(event)">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã ưu đãi *</label>
                    <div class="flex gap-2">
                        <input type="text" id="code" required class="flex-1 p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm uppercase font-mono" placeholder="VD: SALE20">
                        <button type="button" onclick="generateCode()" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition text-sm">Tạo tự động</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại giảm giá *</label>
                    <select id="type" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm" onchange="updateValueLabel()">
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Số tiền cố định (đ)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" id="value-label">Giá trị giảm (%) *</label>
                    <input type="number" id="value" min="0.01" step="0.01" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm" placeholder="VD: 20">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đơn hàng tối thiểu (đ)</label>
                    <input type="number" id="min_order_amount" min="0" step="1000" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm" placeholder="VD: 500000 (để trống = không giới hạn)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số lần sử dụng tối đa</label>
                    <input type="number" id="max_uses" min="1" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm" placeholder="Để trống = không giới hạn">
                </div>

                <div></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu *</label>
                    <input type="datetime-local" id="starts_at" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày kết thúc *</label>
                    <input type="datetime-local" id="ends_at" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ url('/database/coupons') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">Hủy</a>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition text-sm font-medium">Tạo mã ưu đãi</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
const BASE_URL = '{{ url("/") }}';
const API = '{{ url("/api/coupons") }}';

function getToken() { return localStorage.getItem('auth_token'); }

function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 8; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('code').value = code;
}

function updateValueLabel() {
    const type = document.getElementById('type').value;
    const label = document.getElementById('value-label');
    label.textContent = type === 'percent' ? 'Giá trị giảm (%) *' : 'Giá trị giảm (đ) *';
}

async function submitForm(e) {
    e.preventDefault();
    const errEl = document.getElementById('error-message');
    const sucEl = document.getElementById('success-message');
    errEl.classList.add('hidden');
    sucEl.classList.add('hidden');

    const body = {
        code: document.getElementById('code').value.trim(),
        type: document.getElementById('type').value,
        value: parseFloat(document.getElementById('value').value),
        starts_at: document.getElementById('starts_at').value,
        ends_at: document.getElementById('ends_at').value,
    };

    const minOrder = document.getElementById('min_order_amount').value;
    if (minOrder) body.min_order_amount = parseFloat(minOrder);

    const maxUses = document.getElementById('max_uses').value;
    if (maxUses) body.max_uses = parseInt(maxUses);

    try {
        const res = await fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() },
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (res.ok && json.success) {
            sucEl.textContent = json.message || 'Tạo mã ưu đãi thành công!';
            sucEl.classList.remove('hidden');
            setTimeout(() => window.location.href = BASE_URL + '/database/coupons', 1000);
        } else {
            const errors = json.errors ? Object.values(json.errors).flat().join(', ') : (json.message || 'Có lỗi xảy ra');
            errEl.textContent = errors;
            errEl.classList.remove('hidden');
        }
    } catch (e) {
        errEl.textContent = 'Lỗi kết nối';
        errEl.classList.remove('hidden');
    }
}
</script>
@endpush
