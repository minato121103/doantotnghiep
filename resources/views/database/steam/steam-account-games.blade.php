@extends('layouts.app')

@section('title', 'Quản lý Steam Account Games')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">🎲 Quản lý Steam Account Games</h1>
            <p class="text-sm text-gray-600">Quản lý liên kết giữa tài khoản Steam và game (bảng pivot)</p>
        </div>
        <div class="flex gap-2">
            <button onclick="showAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                + Thêm mới
            </button>
            <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
                ← Quay lại
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" id="search" placeholder="Username, tên game..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Steam Account</label>
                <select id="account-filter" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Highlighted</label>
                <select id="highlighted-filter" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả</option>
                    <option value="1">⭐ Highlighted</option>
                    <option value="0">Không highlight</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="id">ID</option>
                    <option value="steam_account_id">Steam Account</option>
                    <option value="product_simple_id">Product</option>
                    <option value="created_at">Ngày tạo</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <select id="sort-order" class="w-1/2 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="desc">Mới nhất</option>
                    <option value="asc">Cũ nhất</option>
                </select>
                <button onclick="loadItems()" class="w-1/2 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600" id="stat-total">-</div>
            <div class="text-sm text-blue-700">📦 Tổng liên kết</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600" id="stat-highlighted">-</div>
            <div class="text-sm text-yellow-700">⭐ Highlighted</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="stat-accounts">-</div>
            <div class="text-sm text-green-700">🎯 Tài khoản Steam</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600" id="stat-games">-</div>
            <div class="text-sm text-purple-700">🎮 Games khác nhau</div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Steam Account</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Game</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Highlighted</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="items-table" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                            Đang tải...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 flex justify-center gap-2"></div>

    <!-- Add Modal -->
    <div id="add-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">➕ Thêm liên kết Game cho tài khoản Steam</h3>
                    <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div id="add-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>
                <div id="add-success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden"></div>

                <form id="add-form" class="space-y-4">
                    <!-- Steam Account Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn tài khoản Steam *</label>
                        <input type="text" id="add-search-account" placeholder="Tìm theo username..." class="w-full p-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 text-sm mb-2" oninput="searchAccounts()">
                        <div id="add-account-list" class="border border-gray-200 rounded-md max-h-40 overflow-y-auto"></div>
                        <div id="add-selected-account" class="mt-2"></div>
                    </div>

                    <!-- Game Selection (like promotions) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn game *</label>
                        <input type="text" id="add-search-game" placeholder="Tìm game..." class="w-full p-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 text-sm mb-2" oninput="searchGamesAdd()">
                        <div id="add-selected-games" class="flex flex-wrap gap-2 mb-2"><span class="text-sm text-gray-400">Chưa chọn game nào</span></div>
                        <div id="add-game-list" class="border border-gray-200 rounded-md max-h-52 overflow-y-auto"></div>
                    </div>

                    <!-- Highlight option -->
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" id="add-is-highlighted" class="w-4 h-4 text-blue-500 rounded border-gray-300 focus:ring-blue-500">
                            ⭐ Đánh dấu Highlighted cho tất cả game được chọn
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Game được highlight sẽ hiển thị nổi bật trong tài khoản</p>
                    </div>

                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700">
                            <strong>Lưu ý:</strong> Có thể chọn nhiều game cùng lúc. Game đã tồn tại trong tài khoản sẽ tự động được bỏ qua.
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-blue-500 text-white py-2.5 rounded-lg hover:bg-blue-600 transition font-medium">Thêm liên kết</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">✏️ Chỉnh sửa liên kết Game</h3>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form id="edit-form" class="space-y-4">
                    <input type="hidden" name="edit_id" id="edit_id">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                            <input type="text" id="edit_id_display" disabled class="w-full p-2 border border-gray-200 rounded-md bg-gray-100 text-gray-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày tạo</label>
                            <input type="text" id="edit_created_at" disabled class="w-full p-2 border border-gray-200 rounded-md bg-gray-100 text-gray-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Steam Account ID *</label>
                        <div class="flex gap-2">
                            <input type="number" name="steam_account_id" id="edit_steam_account_id" required min="1" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="lookupAccountEdit()" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition text-sm">🔍</button>
                        </div>
                        <div id="edit-account-preview" class="mt-1 text-sm text-gray-500"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product ID (Game) *</label>
                        <div class="flex gap-2">
                            <input type="number" name="product_simple_id" id="edit_product_simple_id" required min="1" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="lookupProductEdit()" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-300 transition text-sm">🔍</button>
                        </div>
                        <div id="edit-product-preview" class="mt-1 text-sm text-gray-500"></div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" name="is_highlighted" id="edit_is_highlighted" class="w-4 h-4 text-blue-500 rounded border-gray-300 focus:ring-blue-500">
                            ⭐ Đánh dấu Highlighted
                        </label>
                    </div>
                    
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-700">
                            <strong>Lưu ý:</strong> Thay đổi Steam Account ID hoặc Product ID sẽ bị từ chối nếu liên kết mới đã tồn tại.
                        </p>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">💾 Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_URL = '{{ url("/api/steam-account-games") }}';
    const ACCOUNTS_API = '{{ url("/api/steam-accounts") }}';
    const PRODUCTS_API = '{{ url("/api/products") }}';
    let currentPage = 1;
    let allItems = [];

    // Data for Add modal
    let allAccountsData = [];
    let allGamesData = [];
    let selectedAccountId = null;
    let selectedGameIds = new Set();

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    // =============================================
    // STATS
    // =============================================
    async function loadStats() {
        const token = getToken();
        try {
            const response = await fetch(`${API_URL}/stats`, {
                headers: { 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' }
            });
            const result = await response.json();
            if (result.success) {
                document.getElementById('stat-total').textContent = result.data.total;
                document.getElementById('stat-highlighted').textContent = result.data.highlighted;
                document.getElementById('stat-accounts').textContent = result.data.unique_accounts;
                document.getElementById('stat-games').textContent = result.data.unique_games;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // =============================================
    // LOAD ALL ACCOUNTS & GAMES FOR ADD MODAL
    // =============================================
    async function loadAllAccounts() {
        const token = getToken();
        try {
            const response = await fetch(`${ACCOUNTS_API}?per_page=10000`, {
                headers: { 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' }
            });
            const result = await response.json();
            if (result.success) {
                allAccountsData = result.data || [];
                // Also populate the filter dropdown
                const select = document.getElementById('account-filter');
                allAccountsData.forEach(account => {
                    const option = document.createElement('option');
                    option.value = account.id;
                    option.textContent = `#${account.id} - ${account.username}`;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading accounts:', error);
        }
    }

    async function loadAllGames() {
        const token = getToken();
        try {
            const response = await fetch(`${PRODUCTS_API}?per_page=500`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) {
                allGamesData = result.data || [];
            }
        } catch (error) {
            console.error('Error loading games:', error);
        }
    }

    // =============================================
    // ADD MODAL - ACCOUNT SEARCH & SELECT
    // =============================================
    function searchAccounts() {
        const q = document.getElementById('add-search-account').value.toLowerCase();
        const filtered = allAccountsData.filter(a => 
            a.username.toLowerCase().includes(q) || 
            String(a.id).includes(q)
        );
        renderAccountList(filtered);
    }

    function renderAccountList(accounts) {
        const container = document.getElementById('add-account-list');
        if (accounts.length === 0) {
            container.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">Không tìm thấy tài khoản</div>';
            return;
        }
        container.innerHTML = accounts.slice(0, 50).map(a => {
            const isSelected = selectedAccountId === a.id;
            const isOffline = a.is_offline === true;
            const typeLabel = isOffline ? '📴 Offline' : '📱 Online';
            const statusColor = a.status === 'available' ? 'text-green-600' : 'text-red-600';
            return `<label class="flex items-center gap-3 p-2.5 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 ${isSelected ? 'bg-blue-50' : ''}">
                <input type="radio" name="add_account" value="${a.id}" ${isSelected ? 'checked' : ''} onchange="selectAccount(${a.id})" class="w-4 h-4 text-blue-500">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">
                        <span class="text-blue-600 font-semibold">#${a.id}</span> - ${a.username}
                    </div>
                    <div class="text-xs text-gray-500">${typeLabel} · <span class="${statusColor}">${a.status}</span> · Count: ${a.count ?? '-'}</div>
                </div>
            </label>`;
        }).join('');
    }

    function selectAccount(id) {
        selectedAccountId = id;
        const account = allAccountsData.find(a => a.id === id);
        const container = document.getElementById('add-selected-account');
        if (account) {
            const isOffline = account.is_offline === true;
            container.innerHTML = `<div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-sm">
                🎯 <span class="font-semibold">#${account.id}</span> - ${account.username} (${isOffline ? 'Offline' : 'Online'})
                <button type="button" onclick="deselectAccount()" class="hover:text-blue-900 font-bold">&times;</button>
            </div>`;
        }
    }

    function deselectAccount() {
        selectedAccountId = null;
        document.getElementById('add-selected-account').innerHTML = '';
        // Uncheck all radio buttons
        document.querySelectorAll('input[name="add_account"]').forEach(r => r.checked = false);
    }

    // =============================================
    // ADD MODAL - GAME SEARCH & SELECT (like promotions)
    // =============================================
    function searchGamesAdd() {
        const q = document.getElementById('add-search-game').value.toLowerCase();
        const filtered = allGamesData.filter(g => 
            (g.title || '').toLowerCase().includes(q) || 
            (g.category || '').toLowerCase().includes(q) ||
            String(g.id).includes(q)
        );
        renderGameListAdd(filtered);
    }

    function renderGameListAdd(games) {
        const container = document.getElementById('add-game-list');
        if (games.length === 0) {
            container.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">Không tìm thấy game</div>';
            return;
        }
        container.innerHTML = games.slice(0, 100).map(g => {
            const checked = selectedGameIds.has(g.id) ? 'checked' : '';
            const title = (g.title || '').replace(/'/g, "\\'");
            return `<label class="flex items-center gap-3 p-2.5 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
                <input type="checkbox" value="${g.id}" ${checked} onchange="toggleGameAdd(${g.id})" class="w-4 h-4 text-blue-500 rounded">
                <img src="${g.image || ''}" class="w-8 h-8 rounded object-cover bg-gray-200" onerror="this.style.display='none'">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">${g.title || 'N/A'}</div>
                    <div class="text-xs text-gray-500">#${g.id} · ${g.price || ''} · ${g.category || ''}</div>
                </div>
            </label>`;
        }).join('');
    }

    function toggleGameAdd(id) {
        if (selectedGameIds.has(id)) {
            selectedGameIds.delete(id);
        } else {
            selectedGameIds.add(id);
        }
        renderSelectedGames();
    }

    function renderSelectedGames() {
        const container = document.getElementById('add-selected-games');
        if (selectedGameIds.size === 0) {
            container.innerHTML = '<span class="text-sm text-gray-400">Chưa chọn game nào</span>';
            return;
        }
        container.innerHTML = Array.from(selectedGameIds).map(id => {
            const g = allGamesData.find(x => x.id === id);
            return g ? `<span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs">
                ${g.title || '#' + g.id}
                <button type="button" onclick="removeGameAdd(${id})" class="hover:text-indigo-900 font-bold">&times;</button>
            </span>` : '';
        }).join('');
    }

    function removeGameAdd(id) {
        selectedGameIds.delete(id);
        renderSelectedGames();
        // Re-render game list to uncheck
        searchGamesAdd();
    }

    // =============================================
    // MAIN TABLE - LOAD & RENDER
    // =============================================
    async function loadItems(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const accountId = document.getElementById('account-filter').value;
        const highlighted = document.getElementById('highlighted-filter').value;
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        try {
            let url = `${API_URL}?page=${page}&per_page=15&sort_by=${sortBy}&sort_order=${sortOrder}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (accountId) url += `&steam_account_id=${accountId}`;
            if (highlighted !== '') url += `&is_highlighted=${highlighted}`;
            
            const token = getToken();
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' }
            });
            
            const result = await response.json();
            
            if (result.success) {
                allItems = result.data;
                renderItems(result.data);
                renderPagination(result.pagination);
            } else {
                document.getElementById('items-table').innerHTML = `
                    <tr><td colspan="6" class="px-4 py-8 text-center text-red-500">${result.message || 'Lỗi tải dữ liệu'}</td></tr>
                `;
            }
        } catch (error) {
            document.getElementById('items-table').innerHTML = `
                <tr><td colspan="6" class="px-4 py-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>
            `;
        }
    }

    function renderItems(items) {
        const tbody = document.getElementById('items-table');
        
        if (!items || items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Không có dữ liệu</td></tr>';
            return;
        }
        
        tbody.innerHTML = items.map(item => {
            const accountInfo = item.steam_account 
                ? `<span class="font-medium text-blue-600">#${item.steam_account.id}</span> - ${item.steam_account.username}
                   <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full ${item.steam_account.status === 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${item.steam_account.status}</span>`
                : `<span class="text-gray-400">ID: ${item.steam_account_id} (không tìm thấy)</span>`;
            
            const productTitle = item.product ? (item.product.title || item.product.name || 'N/A') : 'N/A';
            const productInfo = item.product 
                ? `<div class="flex items-center gap-2">
                    ${item.product.image ? `<img src="${item.product.image}" class="w-8 h-8 rounded object-cover" alt="">` : ''}
                    <div>
                        <span class="font-medium text-purple-600">#${item.product.id}</span>
                        <span class="text-gray-700"> - ${productTitle}</span>
                    </div>
                   </div>`
                : `<span class="text-gray-400">ID: ${item.product_simple_id} (không tìm thấy)</span>`;
            
            const highlightBadge = item.is_highlighted 
                ? '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">⭐ Highlighted</span>'
                : '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Không</span>';
            
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">#${item.id}</td>
                    <td class="px-4 py-3 text-sm">${accountInfo}</td>
                    <td class="px-4 py-3 text-sm">${productInfo}</td>
                    <td class="px-4 py-3">${highlightBadge}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${formatDate(item.created_at)}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <button onclick="editItem(${item.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium" title="Chỉnh sửa">✏️ Sửa</button>
                            <button onclick="toggleHighlight(${item.id}, ${!item.is_highlighted})" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium" title="${item.is_highlighted ? 'Bỏ highlight' : 'Highlight'}">
                                ${item.is_highlighted ? '☆ Bỏ' : '⭐ HL'}
                            </button>
                            <button onclick="deleteItem(${item.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">🗑️ Xóa</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(pagination) {
        const container = document.getElementById('pagination');
        if (!pagination || pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let html = '';
        if (pagination.current_page > 1) {
            html += `<button onclick="loadItems(${pagination.current_page - 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        }
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            html += `<button onclick="loadItems(${i})" class="px-3 py-1 rounded ${i === pagination.current_page ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="loadItems(${pagination.current_page + 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        }
        container.innerHTML = html;
    }

    // =============================================
    // MODALS
    // =============================================
    function showAddModal() {
        document.getElementById('add-modal').classList.remove('hidden');
        document.getElementById('add-error').classList.add('hidden');
        document.getElementById('add-success').classList.add('hidden');
        // Reset selections
        selectedAccountId = null;
        selectedGameIds = new Set();
        document.getElementById('add-selected-account').innerHTML = '';
        document.getElementById('add-search-account').value = '';
        document.getElementById('add-search-game').value = '';
        document.getElementById('add-is-highlighted').checked = false;
        renderSelectedGames();
        renderAccountList(allAccountsData);
        renderGameListAdd(allGamesData);
    }

    function closeAddModal() {
        document.getElementById('add-modal').classList.add('hidden');
    }

    function showEditModal() {
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }

    // =============================================
    // EDIT - LOOKUP HELPERS
    // =============================================
    async function lookupAccountEdit() {
        const input = document.getElementById('edit_steam_account_id');
        await doLookupAccount(input.value, 'edit-account-preview');
    }

    async function doLookupAccount(id, previewId) {
        if (!id) return;
        const token = getToken();
        try {
            const response = await fetch(`${ACCOUNTS_API}/${id}`, {
                headers: { 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' }
            });
            const result = await response.json();
            if (result.success && result.data) {
                document.getElementById(previewId).innerHTML = `<span class="text-green-600">✅ ${result.data.username} (${result.data.status})</span>`;
            } else {
                document.getElementById(previewId).innerHTML = `<span class="text-red-500">❌ Không tìm thấy tài khoản #${id}</span>`;
            }
        } catch (error) {
            document.getElementById(previewId).innerHTML = `<span class="text-red-500">❌ Lỗi tra cứu</span>`;
        }
    }

    async function lookupProductEdit() {
        const input = document.getElementById('edit_product_simple_id');
        await doLookupProduct(input.value, 'edit-product-preview');
    }

    async function doLookupProduct(id, previewId) {
        if (!id) return;
        const token = getToken();
        try {
            const response = await fetch(`${PRODUCTS_API}/${id}`, {
                headers: { 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' }
            });
            const result = await response.json();
            if (result.success && result.data) {
                const title = result.data.title || result.data.name || 'N/A';
                document.getElementById(previewId).innerHTML = `<span class="text-green-600">✅ ${title}</span>`;
            } else {
                document.getElementById(previewId).innerHTML = `<span class="text-red-500">❌ Không tìm thấy sản phẩm #${id}</span>`;
            }
        } catch (error) {
            document.getElementById(previewId).innerHTML = `<span class="text-red-500">❌ Lỗi tra cứu</span>`;
        }
    }

    // =============================================
    // EDIT ITEM
    // =============================================
    function editItem(id) {
        const item = allItems.find(i => i.id === id);
        if (!item) {
            alert('Không tìm thấy liên kết');
            return;
        }
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_id_display').value = '#' + id;
        document.getElementById('edit_created_at').value = formatDate(item.created_at);
        document.getElementById('edit_steam_account_id').value = item.steam_account_id;
        document.getElementById('edit_product_simple_id').value = item.product_simple_id;
        document.getElementById('edit_is_highlighted').checked = item.is_highlighted;
        
        if (item.steam_account) {
            document.getElementById('edit-account-preview').innerHTML = `<span class="text-green-600">✅ ${item.steam_account.username} (${item.steam_account.status})</span>`;
        } else {
            document.getElementById('edit-account-preview').innerHTML = '';
        }
        if (item.product) {
            const title = item.product.title || item.product.name || 'N/A';
            document.getElementById('edit-product-preview').innerHTML = `<span class="text-green-600">✅ ${title}</span>`;
        } else {
            document.getElementById('edit-product-preview').innerHTML = '';
        }
        
        showEditModal();
    }

    // =============================================
    // TOGGLE HIGHLIGHT
    // =============================================
    async function toggleHighlight(id, newValue) {
        const token = getToken();
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' },
                body: JSON.stringify({ is_highlighted: newValue })
            });
            const result = await response.json();
            if (result.success) {
                loadStats();
                loadItems(currentPage);
            } else {
                alert(result.message || 'Lỗi cập nhật');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }

    // =============================================
    // FORM SUBMISSIONS
    // =============================================

    // Add form - batch store
    document.getElementById('add-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const errEl = document.getElementById('add-error');
        const sucEl = document.getElementById('add-success');
        errEl.classList.add('hidden');
        sucEl.classList.add('hidden');

        if (!selectedAccountId) {
            errEl.textContent = 'Vui lòng chọn một tài khoản Steam';
            errEl.classList.remove('hidden');
            return;
        }

        if (selectedGameIds.size === 0) {
            errEl.textContent = 'Vui lòng chọn ít nhất 1 game';
            errEl.classList.remove('hidden');
            return;
        }

        const data = {
            steam_account_id: selectedAccountId,
            product_simple_ids: Array.from(selectedGameIds),
            is_highlighted: document.getElementById('add-is-highlighted').checked,
        };
        
        const token = getToken();
        try {
            const response = await fetch(`${API_URL}/batch`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            if (result.success) {
                sucEl.textContent = result.message;
                sucEl.classList.remove('hidden');
                // Reset selections
                selectedAccountId = null;
                selectedGameIds = new Set();
                document.getElementById('add-selected-account').innerHTML = '';
                renderSelectedGames();
                searchGamesAdd();
                searchAccounts();
                // Reload data
                loadStats();
                loadItems();
                // Close modal after a brief delay to show success
                setTimeout(() => closeAddModal(), 1200);
            } else {
                const errors = result.errors ? Object.values(result.errors).flat().join(', ') : (result.message || 'Có lỗi xảy ra');
                errEl.textContent = errors;
                errEl.classList.remove('hidden');
            }
        } catch (error) {
            errEl.textContent = 'Lỗi: ' + error.message;
            errEl.classList.remove('hidden');
        }
    });

    // Edit form
    document.getElementById('edit-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const id = formData.get('edit_id');
        
        const data = {
            steam_account_id: parseInt(formData.get('steam_account_id')),
            product_simple_id: parseInt(formData.get('product_simple_id')),
            is_highlighted: document.getElementById('edit_is_highlighted').checked,
        };
        
        const token = getToken();
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            if (result.success) {
                closeEditModal();
                loadStats();
                loadItems(currentPage);
            } else {
                alert(result.message || 'Lỗi cập nhật');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    });

    // =============================================
    // DELETE
    // =============================================
    async function deleteItem(id) {
        if (!confirm('Bạn có chắc muốn xóa liên kết game này?')) return;
        
        const token = getToken();
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'Authorization': token ? `Bearer ${token}` : '' }
            });
            
            const result = await response.json();
            if (result.success) {
                loadStats();
                loadItems(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa liên kết');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }

    // =============================================
    // HELPERS
    // =============================================
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    // Close modal when clicking outside
    document.getElementById('add-modal').addEventListener('click', function(e) {
        if (e.target === this) closeAddModal();
    });
    
    document.getElementById('edit-modal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    // =============================================
    // INIT
    // =============================================
    document.addEventListener('DOMContentLoaded', () => {
        loadStats();
        loadItems();
        loadAllAccounts();
        loadAllGames();
    });
</script>
@endpush
