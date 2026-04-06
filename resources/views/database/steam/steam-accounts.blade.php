@extends('layouts.app')

@section('title', 'Quản lý Tài khoản Steam')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">🎯 Quản lý Tài khoản Steam</h1>
            <p class="text-sm text-gray-600">Quản lý kho tài khoản Steam để bán</p>
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
                <input type="text" id="search" placeholder="Username, email..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select id="status-filter" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả</option>
                    <option value="available">🟢 Available</option>
                    <option value="sold">🔴 Sold / Hết hàng</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="id">ID</option>
                    <option value="username">Username</option>
                    <option value="created_at">Ngày tạo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                <select id="sort-order" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="desc">Mới nhất</option>
                    <option value="asc">Cũ nhất</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadAccounts()" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600" id="stat-total">-</div>
            <div class="text-sm text-blue-700">📦 Tổng tài khoản</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="stat-available">-</div>
            <div class="text-sm text-green-700">🟢 Còn hàng</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600" id="stat-offline">-</div>
            <div class="text-sm text-purple-700">📴 Offline (share)</div>
        </div>
        <div class="bg-teal-50 border border-teal-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-teal-600" id="stat-online">-</div>
            <div class="text-sm text-teal-700">📱 Online (full)</div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Loại</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Count</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Games</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="accounts-table" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
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
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Thêm tài khoản Steam</h3>
                    <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form id="add-form" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                        <input type="text" name="username" required class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="text" name="password" required class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700 mb-2">
                            <strong>Lưu ý:</strong> Để trống Email và Email Password để tạo tài khoản <strong>Offline (share)</strong> với count = 10.
                            Điền đầy đủ để tạo tài khoản <strong>Online (full)</strong> với count = 1.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400">(tùy chọn)</span></label>
                        <input type="email" name="email" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Password <span class="text-gray-400">(tùy chọn)</span></label>
                        <input type="text" name="email_password" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Game IDs <span class="text-gray-400">(tùy chọn, cách nhau bởi dấu phẩy)</span></label>
                        <input type="text" name="game_ids" placeholder="1, 2, 3" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Có thể để trống và gán game sau tại trang <strong>Quản lý Steam Account Games</strong></p>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">Thêm tài khoản</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">✏️ Chỉnh sửa tài khoản Steam</h3>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại</label>
                            <input type="text" id="edit_type_display" disabled class="w-full p-2 border border-gray-200 rounded-md bg-gray-100 text-gray-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" id="edit_username" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400">(để trống nếu không đổi)</span></label>
                        <input type="text" name="password" id="edit_password" placeholder="••••••••" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="edit_email" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Password <span class="text-gray-400">(để trống nếu không đổi)</span></label>
                        <input type="text" name="email_password" id="edit_email_password" placeholder="••••••••" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng (Count)</label>
                            <input type="number" name="count" id="edit_count" min="0" max="100" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select name="status" id="edit_status" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                <option value="available">🟢 Available</option>
                                <option value="sold">🔴 Sold</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-700">
                            <strong>Lưu ý:</strong> Nếu count = 0, tài khoản sẽ tự động chuyển sang trạng thái "Hết hàng".
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
    const API_URL = '{{ url("/api/steam-accounts") }}';
    let currentPage = 1;
    let allAccounts = [];

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    // Load stats from all data
    async function loadStats() {
        const token = getToken();
        try {
            const response = await fetch(`${API_URL}?per_page=10000`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                }
            });
            const result = await response.json();
            
            if (result.success) {
                allAccounts = result.data;
                
                // Use pagination.total for accurate count
                const total = result.pagination?.total || allAccounts.length;
                const available = allAccounts.filter(a => a.status === 'available' && a.count > 0).length;
                const offline = allAccounts.filter(a => a.is_offline === true).length;
                const online = allAccounts.filter(a => a.is_offline === false).length;
                
                document.getElementById('stat-total').textContent = total;
                document.getElementById('stat-available').textContent = available;
                document.getElementById('stat-offline').textContent = offline;
                document.getElementById('stat-online').textContent = online;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async function loadAccounts(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const status = document.getElementById('status-filter').value;
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        try {
            let url = `${API_URL}?page=${page}&per_page=15&sort_by=${sortBy}&sort_order=${sortOrder}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (status) url += `&status=${status}`;
            
            const token = getToken();
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                renderAccounts(result.data);
                renderPagination(result.pagination);
            } else {
                document.getElementById('accounts-table').innerHTML = `
                    <tr><td colspan="8" class="px-4 py-8 text-center text-red-500">${result.message || 'Lỗi tải dữ liệu'}</td></tr>
                `;
            }
        } catch (error) {
            document.getElementById('accounts-table').innerHTML = `
                <tr><td colspan="8" class="px-4 py-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>
            `;
        }
    }

    function renderAccounts(accounts) {
        const tbody = document.getElementById('accounts-table');
        
        if (!accounts || accounts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Không có tài khoản</td></tr>';
            return;
        }
        
        tbody.innerHTML = accounts.map(account => {
            const isOffline = account.is_offline === true;
            const isOutOfStock = account.count <= 0;
            
            const typeInfo = isOffline 
                ? { label: 'Offline', color: 'bg-purple-100 text-purple-800', icon: '📴' }
                : { label: 'Online', color: 'bg-teal-100 text-teal-800', icon: '📱' };
            
            let statusInfo;
            if (isOutOfStock) {
                statusInfo = { color: 'bg-red-100 text-red-800', icon: '🔴', label: 'Hết hàng' };
            } else if (account.status === 'available') {
                statusInfo = { color: 'bg-green-100 text-green-800', icon: '🟢', label: 'Còn hàng' };
            } else {
                statusInfo = { color: 'bg-gray-100 text-gray-800', icon: '⚪', label: account.status };
            }
            
            const gamesCount = account.games ? account.games.length : 0;
            const countColor = account.count > 5 ? 'text-green-600' : (account.count > 0 ? 'text-yellow-600' : 'text-red-600');
            
            return `
                <tr class="hover:bg-gray-50 ${isOutOfStock ? 'opacity-60' : ''}">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">#${account.id}</td>
                    <td class="px-4 py-3 text-sm font-medium text-blue-600">${account.username || '-'}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${typeInfo.color}">${typeInfo.icon} ${typeInfo.label}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-lg font-bold ${countColor}">${account.count ?? 1}</span>
                        <span class="text-xs text-gray-400">/ ${isOffline ? '10' : '1'}</span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="px-2 py-1 bg-slate-100 rounded text-xs">${gamesCount} games</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${statusInfo.color}">${statusInfo.icon} ${statusInfo.label}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">${formatDate(account.created_at)}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <button onclick="editAccount(${account.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium" title="Chỉnh sửa">✏️ Sửa</button>
                            <button onclick="deleteAccount(${account.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
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
            html += `<button onclick="loadAccounts(${pagination.current_page - 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        }
        
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            html += `<button onclick="loadAccounts(${i})" class="px-3 py-1 rounded ${i === pagination.current_page ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="loadAccounts(${pagination.current_page + 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        }
        
        container.innerHTML = html;
    }

    function showAddModal() {
        document.getElementById('add-modal').classList.remove('hidden');
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

    function editAccount(id) {
        const account = allAccounts.find(a => a.id === id);
        if (!account) {
            alert('Không tìm thấy tài khoản');
            return;
        }
        
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_id_display').value = '#' + id;
        document.getElementById('edit_type_display').value = account.is_offline ? '📴 Offline (share)' : '📱 Online (full)';
        document.getElementById('edit_username').value = account.username || '';
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_email').value = account.has_email ? '' : '';
        document.getElementById('edit_email').placeholder = account.has_email ? '(đã có email)' : '';
        document.getElementById('edit_email_password').value = '';
        document.getElementById('edit_email_password').placeholder = account.has_email_password ? '(đã có)' : '';
        document.getElementById('edit_count').value = account.count ?? 1;
        document.getElementById('edit_status').value = account.status || 'available';
        
        showEditModal();
    }

    document.getElementById('edit-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const id = formData.get('edit_id');
        
        const data = {};
        
        if (formData.get('username')) data.username = formData.get('username');
        if (formData.get('password')) data.password = formData.get('password');
        if (formData.get('email')) data.email = formData.get('email');
        if (formData.get('email_password')) data.email_password = formData.get('email_password');
        
        const count = parseInt(formData.get('count'));
        if (!isNaN(count)) data.count = count;
        
        data.status = formData.get('status');
        
        // Auto set status to sold if count is 0
        if (data.count === 0) {
            data.status = 'sold';
        }
        
        const token = getToken();
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            if (result.success) {
                closeEditModal();
                loadStats();
                loadAccounts(currentPage);
            } else {
                alert(result.message || 'Lỗi cập nhật tài khoản');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    });

    document.getElementById('add-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const gameIds = formData.get('game_ids') ? formData.get('game_ids').split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id)) : [];
        
        const data = {
            username: formData.get('username'),
            password: formData.get('password'),
            email: formData.get('email') || null,
            email_password: formData.get('email_password') || null,
        };

        // Only include games if provided
        if (gameIds.length > 0) {
            data.games = gameIds;
        }
        
        const token = getToken();
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            if (result.success) {
                closeAddModal();
                e.target.reset();
                loadStats();
                loadAccounts();
            } else {
                alert(result.message || 'Lỗi thêm tài khoản');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    });

    async function deleteAccount(id) {
        if (!confirm('Bạn có chắc muốn xóa tài khoản này?')) return;
        
        const token = getToken();
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                }
            });
            
            const result = await response.json();
            if (result.success) {
                loadStats();
                loadAccounts(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa tài khoản');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }

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

    document.addEventListener('DOMContentLoaded', () => {
        loadStats();     // Load stats from all data
        loadAccounts();  // Load paginated data for table
    });
</script>
@endpush
