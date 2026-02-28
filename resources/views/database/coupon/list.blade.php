@extends('layouts.app')

@section('title', 'Quản lý Mã ưu đãi')

@section('max-width', 'max-w-7xl')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Quản lý Mã ưu đãi</h1>
            <p class="text-sm text-gray-600">Tạo và quản lý các mã giảm giá cho đơn hàng</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ url('/database') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-sm">← Dashboard</a>
            <a href="{{ url('/database/coupons/create') }}" class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition text-sm">+ Tạo mã</a>
        </div>
    </div>

    <div class="mb-4 flex gap-2">
        <button onclick="filterStatus('')" class="filter-btn active px-3 py-1.5 text-sm rounded-full bg-gray-800 text-white">Tất cả</button>
        <button onclick="filterStatus('active')" class="filter-btn px-3 py-1.5 text-sm rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300">Đang hoạt động</button>
        <button onclick="filterStatus('expired')" class="filter-btn px-3 py-1.5 text-sm rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300">Đã hết hạn</button>
    </div>

    <div id="loading" class="text-center py-12 text-gray-500">Đang tải...</div>
    <div id="coupons-container" class="hidden">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mã</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Loại</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giá trị</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Đơn tối thiểu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sử dụng</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Thời gian</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="coupons-table" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
        <div id="pagination" class="mt-4 flex justify-center gap-2"></div>
    </div>
    <div id="empty-state" class="hidden text-center py-12">
        <div class="text-4xl mb-3">🎟️</div>
        <p class="text-gray-500">Chưa có mã ưu đãi nào</p>
    </div>
@endsection

@push('scripts')
<script>
const BASE_URL = '{{ url("/") }}';
const API = '{{ url("/api/coupons") }}';
let currentStatus = '';
let currentPage = 1;

function getToken() { return localStorage.getItem('auth_token'); }

function formatMoney(v) {
    return Number(v).toLocaleString('vi-VN') + 'đ';
}

function formatDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

async function loadCoupons(page = 1) {
    currentPage = page;
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('coupons-container').classList.add('hidden');
    document.getElementById('empty-state').classList.add('hidden');

    let url = `${API}?page=${page}`;
    if (currentStatus) url += `&status=${currentStatus}`;

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() }});
        const json = await res.json();
        const coupons = json.data?.data || [];
        const pagination = json.data;

        document.getElementById('loading').classList.add('hidden');

        if (coupons.length === 0) {
            document.getElementById('empty-state').classList.remove('hidden');
            return;
        }

        document.getElementById('coupons-container').classList.remove('hidden');
        const tbody = document.getElementById('coupons-table');
        tbody.innerHTML = coupons.map(c => {
            const now = new Date();
            const start = new Date(c.starts_at);
            const end = new Date(c.ends_at);
            const maxReached = c.max_uses && c.used_count >= c.max_uses;
            let status, statusClass;
            if (now > end || maxReached) { status = 'Hết hạn'; statusClass = 'bg-gray-100 text-gray-700'; }
            else if (now < start) { status = 'Sắp tới'; statusClass = 'bg-blue-100 text-blue-700'; }
            else { status = 'Hoạt động'; statusClass = 'bg-green-100 text-green-700'; }

            const typeLabel = c.type === 'percent' ? 'Phần trăm' : 'Cố định';
            const valueLabel = c.type === 'percent' ? c.value + '%' : formatMoney(c.value);
            const usesLabel = c.max_uses ? `${c.used_count}/${c.max_uses}` : `${c.used_count}/∞`;
            const minOrder = c.min_order_amount ? formatMoney(c.min_order_amount) : '—';

            return `<tr class="hover:bg-gray-50">
                <td class="px-4 py-3"><span class="px-2 py-1 bg-amber-100 text-amber-800 rounded font-mono text-sm font-bold">${c.code}</span></td>
                <td class="px-4 py-3 text-sm text-gray-600">${typeLabel}</td>
                <td class="px-4 py-3 text-sm font-semibold text-amber-600">${valueLabel}</td>
                <td class="px-4 py-3 text-sm text-gray-600">${minOrder}</td>
                <td class="px-4 py-3 text-sm text-gray-600">${usesLabel}</td>
                <td class="px-4 py-3 text-sm text-gray-600">${formatDate(c.starts_at)} - ${formatDate(c.ends_at)}</td>
                <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${status}</span></td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="${BASE_URL}/database/coupons/${c.id}/edit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Sửa</a>
                        <button onclick="deleteCoupon(${c.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
                    </div>
                </td>
            </tr>`;
        }).join('');

        renderPagination(pagination);
    } catch (e) {
        document.getElementById('loading').classList.add('hidden');
        console.error(e);
    }
}

function renderPagination(data) {
    const container = document.getElementById('pagination');
    if (data.last_page <= 1) { container.innerHTML = ''; return; }
    let html = '';
    for (let i = 1; i <= data.last_page; i++) {
        const active = i === data.current_page ? 'bg-amber-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100';
        html += `<button onclick="loadCoupons(${i})" class="px-3 py-1 rounded border text-sm ${active}">${i}</button>`;
    }
    container.innerHTML = html;
}

function filterStatus(status) {
    currentStatus = status;
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('active', 'bg-gray-800', 'text-white');
        b.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('active', 'bg-gray-800', 'text-white');
    loadCoupons(1);
}

async function deleteCoupon(id) {
    if (!confirm('Bạn có chắc muốn xóa mã ưu đãi này?')) return;
    try {
        await fetch(`${API}/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() }
        });
        loadCoupons(currentPage);
    } catch (e) { console.error(e); }
}

window.addEventListener('authReady', () => loadCoupons());
</script>
@endpush
