@extends('layouts.app')

@section('title', 'Sửa Ưu đãi')

@section('max-width', 'max-w-4xl')

@section('content')
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Sửa Ưu đãi</h1>
            <p class="text-sm text-gray-600">Chỉnh sửa thông tin đợt giảm giá</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ url('/database/promotions') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition text-sm">← Danh sách</a>
            <button onclick="deletePromotion()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm">Xóa ưu đãi</button>
        </div>
    </div>

    <div id="loading" class="text-center py-12 text-gray-500">Đang tải...</div>
    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>
    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden"></div>

    <div id="form-container" class="bg-white rounded-lg shadow-md p-6 hidden">
        <form id="promotion-form" onsubmit="submitForm(event)">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tên ưu đãi</label>
                        <input type="text" id="name" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mức giảm giá (%) *</label>
                        <input type="number" id="discount_percent" min="1" max="100" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày bắt đầu *</label>
                        <input type="datetime-local" id="starts_at" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ngày kết thúc *</label>
                        <input type="datetime-local" id="ends_at" required class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-transparent text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn game *</label>
                    <input type="text" id="search-game" placeholder="Tìm game..." class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-rose-500 focus:border-transparent text-sm mb-3" oninput="searchGames()">
                    <div id="selected-games" class="flex flex-wrap gap-2 mb-3"></div>
                    <div id="game-list" class="border border-gray-200 rounded-md max-h-64 overflow-y-auto"></div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ url('/database/promotions') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">Hủy</a>
                <button type="submit" class="px-6 py-2.5 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition text-sm font-medium">Lưu thay đổi</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
const PROMO_ID = {{ $id }};
const BASE_URL = '{{ url("/") }}';
const API_PRODUCTS = '{{ url("/api/products") }}';
const API_PROMOS = '{{ url("/api/promotions") }}';
let allGames = [];
let selectedIds = new Set();

function getToken() { return localStorage.getItem('auth_token'); }

function toLocalDatetime(isoStr) {
    if (!isoStr) return '';
    const d = new Date(isoStr);
    const offset = d.getTimezoneOffset();
    const local = new Date(d.getTime() - offset * 60000);
    return local.toISOString().slice(0, 16);
}

async function loadData() {
    try {
        const [promoRes, productsRes] = await Promise.all([
            fetch(`${API_PROMOS}/${PROMO_ID}`, { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() }}),
            fetch(`${API_PRODUCTS}?per_page=500`, { headers: { 'Accept': 'application/json' }}),
        ]);
        const promoJson = await promoRes.json();
        const productsJson = await productsRes.json();

        const promo = promoJson.data;
        allGames = productsJson.data || [];

        document.getElementById('name').value = promo.name || '';
        document.getElementById('discount_percent').value = promo.discount_percent;
        document.getElementById('starts_at').value = toLocalDatetime(promo.starts_at);
        document.getElementById('ends_at').value = toLocalDatetime(promo.ends_at);

        (promo.products || []).forEach(p => selectedIds.add(p.id));

        renderSelectedGames();
        renderGameList(allGames);

        document.getElementById('loading').classList.add('hidden');
        document.getElementById('form-container').classList.remove('hidden');
    } catch (e) {
        document.getElementById('loading').innerHTML = '<span class="text-red-500">Lỗi tải dữ liệu</span>';
        console.error(e);
    }
}

function searchGames() {
    const q = document.getElementById('search-game').value.toLowerCase();
    const filtered = allGames.filter(g => g.title.toLowerCase().includes(q) || (g.category || '').toLowerCase().includes(q));
    renderGameList(filtered);
}

function renderGameList(games) {
    const container = document.getElementById('game-list');
    if (games.length === 0) {
        container.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">Không tìm thấy game</div>';
        return;
    }
    container.innerHTML = games.map(g => {
        const checked = selectedIds.has(g.id) ? 'checked' : '';
        return `<label class="flex items-center gap-3 p-2.5 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
            <input type="checkbox" value="${g.id}" ${checked} onchange="toggleGame(${g.id})" class="w-4 h-4 text-rose-500 rounded">
            <img src="${g.image || ''}" class="w-8 h-8 rounded object-cover bg-gray-200" onerror="this.style.display='none'">
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-gray-900 truncate">${g.title}</div>
                <div class="text-xs text-gray-500">${g.price || ''} - ${g.category || ''}</div>
            </div>
        </label>`;
    }).join('');
}

function toggleGame(id) {
    if (selectedIds.has(id)) selectedIds.delete(id);
    else selectedIds.add(id);
    renderSelectedGames();
}

function renderSelectedGames() {
    const container = document.getElementById('selected-games');
    if (selectedIds.size === 0) {
        container.innerHTML = '<span class="text-sm text-gray-400">Chưa chọn game nào</span>';
        return;
    }
    container.innerHTML = Array.from(selectedIds).map(id => {
        const g = allGames.find(x => x.id === id);
        const title = g ? g.title : `Game #${id}`;
        return `<span class="inline-flex items-center gap-1 px-2 py-1 bg-rose-100 text-rose-700 rounded-full text-xs">
            ${title}
            <button type="button" onclick="removeGame(${id})" class="hover:text-rose-900">&times;</button>
        </span>`;
    }).join('');
}

function removeGame(id) {
    selectedIds.delete(id);
    renderSelectedGames();
    searchGames();
}

async function submitForm(e) {
    e.preventDefault();
    const errEl = document.getElementById('error-message');
    const sucEl = document.getElementById('success-message');
    errEl.classList.add('hidden');
    sucEl.classList.add('hidden');

    if (selectedIds.size === 0) {
        errEl.textContent = 'Vui lòng chọn ít nhất 1 game';
        errEl.classList.remove('hidden');
        return;
    }

    const body = {
        name: document.getElementById('name').value || null,
        discount_percent: parseInt(document.getElementById('discount_percent').value),
        starts_at: document.getElementById('starts_at').value,
        ends_at: document.getElementById('ends_at').value,
        product_simple_ids: Array.from(selectedIds),
    };

    try {
        const res = await fetch(`${API_PROMOS}/${PROMO_ID}`, {
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

async function deletePromotion() {
    if (!confirm('Bạn có chắc muốn xóa ưu đãi này?')) return;
    try {
        await fetch(`${API_PROMOS}/${PROMO_ID}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + getToken() },
        });
        window.location.href = BASE_URL + '/database/promotions';
    } catch (e) { console.error(e); }
}

window.addEventListener('authReady', () => loadData());
</script>
@endpush
