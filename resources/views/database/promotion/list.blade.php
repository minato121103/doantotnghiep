@extends('layouts.app')

@section('title', 'Quản lý Ưu đãi')

@section('max-width', 'max-w-7xl')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Quản lý Ưu đãi</h1>
            <p class="text-sm text-gray-600">Tạo và quản lý các đợt giảm giá game</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ url('/database') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-sm">← Dashboard</a>
            <a href="{{ url('/database/promotions/create') }}" class="bg-rose-500 text-white px-4 py-2 rounded-lg hover:bg-rose-600 transition text-sm">+ Tạo ưu đãi</a>
        </div>
    </div>

    <div class="mb-4 flex gap-2">
        <button onclick="filterStatus('')" class="filter-btn active px-3 py-1.5 text-sm rounded-full bg-gray-800 text-white">Tất cả</button>
        <button onclick="filterStatus('active')" class="filter-btn px-3 py-1.5 text-sm rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300">Đang hoạt động</button>
        <button onclick="filterStatus('upcoming')" class="filter-btn px-3 py-1.5 text-sm rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300">Sắp tới</button>
        <button onclick="filterStatus('expired')" class="filter-btn px-3 py-1.5 text-sm rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300">Đã hết hạn</button>
    </div>

    <div id="loading" class="text-center py-12 text-gray-500">Đang tải...</div>
    <div id="promotions-container" class="hidden">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tên</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giảm giá</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Số game</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Thời gian</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="promotions-table" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
        <div id="pagination" class="mt-4 flex justify-center gap-2"></div>
    </div>
    <div id="empty-state" class="hidden text-center py-12">
        <div class="text-4xl mb-3">🏷️</div>
        <p class="text-gray-500">Chưa có ưu đãi nào</p>
    </div>
@endsection

@push('scripts')
<script>
const BASE_URL = '{{ url("/") }}';
const API = '{{ url("/api/promotions") }}';
let currentStatus = '';
let currentPage = 1;

function getToken() { return localStorage.getItem('auth_token'); }

async function loadPromotions(page = 1) {
    currentPage = page;
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('promotions-container').classList.add('hidden');
    document.getElementById('empty-state').classList.add('hidden');

    let url = `${API}?page=${page}`;
    if (currentStatus) url += `&status=${currentStatus}`;

    try {
        const res = await fetch(url, { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() }});
        const json = await res.json();
        const promos = json.data?.data || [];
        const pagination = json.data;

        document.getElementById('loading').classList.add('hidden');

        if (promos.length === 0) {
            document.getElementById('empty-state').classList.remove('hidden');
            return;
        }

        document.getElementById('promotions-container').classList.remove('hidden');
        const tbody = document.getElementById('promotions-table');
        tbody.innerHTML = promos.map(p => {
            const now = new Date();
            const start = new Date(p.starts_at);
            const end = new Date(p.ends_at);
            let status, statusClass;
            if (now < start) { status = 'Sắp tới'; statusClass = 'bg-blue-100 text-blue-700'; }
            else if (now > end) { status = 'Hết hạn'; statusClass = 'bg-gray-100 text-gray-700'; }
            else { status = 'Đang hoạt động'; statusClass = 'bg-green-100 text-green-700'; }

            return `<tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-600">${p.id}</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${p.name || '(Không tên)'}</td>
                <td class="px-4 py-3"><span class="px-2 py-1 text-sm font-bold text-rose-600 bg-rose-50 rounded">-${p.discount_percent}%</span></td>
                <td class="px-4 py-3 text-sm text-gray-600">${p.products_count ?? 0} game</td>
                <td class="px-4 py-3 text-sm text-gray-600">${formatDate(p.starts_at)} - ${formatDate(p.ends_at)}</td>
                <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${status}</span></td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="${BASE_URL}/database/promotions/${p.id}/edit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Sửa</a>
                        <button onclick="deletePromotion(${p.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
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
        const active = i === data.current_page ? 'bg-rose-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100';
        html += `<button onclick="loadPromotions(${i})" class="px-3 py-1 rounded border text-sm ${active}">${i}</button>`;
    }
    container.innerHTML = html;
}

function formatDate(d) {
    if (!d) return '';
    const date = new Date(d);
    return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function filterStatus(status) {
    currentStatus = status;
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('active', 'bg-gray-800', 'text-white');
        b.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('active', 'bg-gray-800', 'text-white');
    loadPromotions(1);
}

async function deletePromotion(id) {
    if (!confirm('Bạn có chắc muốn xóa ưu đãi này?')) return;
    try {
        await fetch(`${API}/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() }
        });
        loadPromotions(currentPage);
    } catch (e) { console.error(e); }
}

window.addEventListener('authReady', () => loadPromotions());
</script>
@endpush
