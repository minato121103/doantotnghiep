@extends('layouts.app')

@section('title', 'Sửa Mã ưu đãi')

@section('max-width', 'max-w-3xl')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Sửa Mã ưu đãi</h1>
            <p class="text-sm text-gray-600">Chỉnh sửa thông tin mã giảm giá</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ url('/database/coupons') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-sm">← Danh sách</a>
            <button onclick="deleteCoupon()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm">Xóa mã</button>
        </div>
    </div>

    <div id="loading" class="text-center py-12 text-gray-500">Đang tải...</div>
    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>
    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden"></div>

    <div id="form-container" class="bg-white rounded-lg shadow-md p-6 hidden">
        <form id="coupon-form" onsubmit="submitForm(event)">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã ưu đãi *</label>
                    <input type="text" id="code" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm uppercase font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại giảm giá *</label>
                    <select id="type" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm" onchange="updateValueLabel()">
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Số tiền cố định (đ)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" id="value-label">Giá trị giảm *</label>
                    <input type="number" id="value" min="0.01" step="0.01" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đơn hàng tối thiểu (đ)</label>
                    <input type="number" id="min_order_amount" min="0" step="1000" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số lần sử dụng tối đa</label>
                    <input type="number" id="max_uses" min="1" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đã sử dụng</label>
                    <input type="text" id="used_count" readonly class="w-full p-3 border border-gray-200 rounded-md bg-gray-50 text-sm text-gray-600">
                </div>

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
                <button type="submit" class="px-6 py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition text-sm font-medium">Lưu thay đổi</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
const COUPON_ID = {{ $id }};
const BASE_URL = '{{ url("/") }}';
const API = '{{ url("/api/coupons") }}';

function getToken() { return localStorage.getItem('auth_token'); }

function toLocalDatetime(isoStr) {
    if (!isoStr) return '';
    const d = new Date(isoStr);
    const offset = d.getTimezoneOffset();
    const local = new Date(d.getTime() - offset * 60000);
    return local.toISOString().slice(0, 16);
}

function updateValueLabel() {
    const type = document.getElementById('type').value;
    document.getElementById('value-label').textContent = type === 'percent' ? 'Giá trị giảm (%) *' : 'Giá trị giảm (đ) *';
}

async function loadData() {
    try {
        const res = await fetch(`${API}/${COUPON_ID}`, { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() }});
        const json = await res.json();
        const c = json.data;

        document.getElementById('code').value = c.code;
        document.getElementById('type').value = c.type;
        document.getElementById('value').value = c.value;
        document.getElementById('min_order_amount').value = c.min_order_amount || '';
        document.getElementById('max_uses').value = c.max_uses || '';
        document.getElementById('used_count').value = c.used_count;
        document.getElementById('starts_at').value = toLocalDatetime(c.starts_at);
        document.getElementById('ends_at').value = toLocalDatetime(c.ends_at);
        updateValueLabel();

        document.getElementById('loading').classList.add('hidden');
        document.getElementById('form-container').classList.remove('hidden');
    } catch (e) {
        document.getElementById('loading').innerHTML = '<span class="text-red-500">Lỗi tải dữ liệu</span>';
        console.error(e);
    }
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
    body.min_order_amount = minOrder ? parseFloat(minOrder) : null;

    const maxUses = document.getElementById('max_uses').value;
    body.max_uses = maxUses ? parseInt(maxUses) : null;

    try {
        const res = await fetch(`${API}/${COUPON_ID}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() },
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (res.ok && json.success) {
            sucEl.textContent = json.message || 'Cập nhật thành công!';
            sucEl.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
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

async function deleteCoupon() {
    if (!confirm('Bạn có chắc muốn xóa mã ưu đãi này?')) return;
    try {
        await fetch(`${API}/${COUPON_ID}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() },
        });
        window.location.href = BASE_URL + '/database/coupons';
    } catch (e) { console.error(e); }
}

window.addEventListener('authReady', () => loadData());
</script>
@endpush
