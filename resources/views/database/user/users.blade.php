@extends('layouts.app')

@section('title', 'Quản lý Người dùng')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">👥 Quản lý Người dùng</h1>
            <p class="text-sm text-gray-600">Xem và quản lý người dùng trong hệ thống</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('database.create-user') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200 text-center">
                + Thêm mới
            </a>
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
                <input type="text" id="search" placeholder="Tên, email..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                <select id="role-filter" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tất cả</option>
                    <option value="admin">🔴 Admin</option>
                    <option value="buyer">🔵 Buyer</option>
                    <option value="editor">🟢 Editor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="id">ID</option>
                    <option value="name">Tên</option>
                    <option value="balance">Số dư</option>
                    <option value="total_orders">Đơn hàng</option>
                    <option value="created_at">Ngày tạo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                <select id="sort-order" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="desc">Mới nhất</option>
                    <option value="asc">Cũ nhất</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadUsers()" class="w-full bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600" id="stat-total">-</div>
            <div class="text-sm text-indigo-700">👥 Tổng người dùng</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-600" id="stat-admin">-</div>
            <div class="text-sm text-red-700">🔴 Admin</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600" id="stat-buyer">-</div>
            <div class="text-sm text-blue-700">🔵 Buyer</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="stat-balance">-</div>
            <div class="text-sm text-green-700">💰 Tổng số dư</div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Người dùng</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vai trò</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Số dư</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Đơn hàng</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                            Đang tải...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 flex justify-center gap-2"></div>
@endsection

@push('scripts')
<script>
    const API_URL = '{{ url("/api/users") }}';
    const BASE_URL = '{{ url("/") }}';
    let currentPage = 1;

    // Load stats from all data (separate call)
    async function loadStats() {
        try {
            const response = await fetch(`${API_URL}?per_page=10000`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            
            if (result.success) {
                const allUsers = result.data;
                
                // Total users
                document.getElementById('stat-total').textContent = result.pagination?.total || allUsers.length;
                
                // Count by role
                const adminCount = allUsers.filter(u => u.role === 'admin').length;
                const buyerCount = allUsers.filter(u => u.role === 'buyer').length;
                document.getElementById('stat-admin').textContent = adminCount;
                document.getElementById('stat-buyer').textContent = buyerCount;
                
                // Sum balance
                const totalBalance = allUsers.reduce((sum, u) => sum + parseFloat(u.balance || 0), 0);
                document.getElementById('stat-balance').textContent = formatCurrency(totalBalance);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async function loadUsers(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const role = document.getElementById('role-filter').value;
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        try {
            let url = `${API_URL}?page=${page}&per_page=15&sort_by=${sortBy}&sort_order=${sortOrder}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (role) url += `&role=${role}`;
            
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            
            const result = await response.json();
            
            if (result.success) {
                renderUsers(result.data);
                renderPagination(result.pagination);
            } else {
                document.getElementById('users-table').innerHTML = `
                    <tr><td colspan="8" class="px-4 py-8 text-center text-red-500">${result.message || 'Lỗi tải dữ liệu'}</td></tr>
                `;
            }
        } catch (error) {
            document.getElementById('users-table').innerHTML = `
                <tr><td colspan="8" class="px-4 py-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>
            `;
        }
    }

    function renderUsers(users) {
        const tbody = document.getElementById('users-table');
        
        if (!users || users.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">
                Không có người dùng. <a href="{{ route('database.create-user') }}" class="text-indigo-600 hover:text-indigo-800">Thêm người dùng mới</a>
            </td></tr>`;
            return;
        }
        
        const roleConfig = {
            'admin': { color: 'bg-red-100 text-red-800', icon: '🔴' },
            'buyer': { color: 'bg-blue-100 text-blue-800', icon: '🔵' },
            'editor': { color: 'bg-green-100 text-green-800', icon: '🟢' }
        };
        
        tbody.innerHTML = users.map(user => {
            const roleInfo = roleConfig[user.role] || { color: 'bg-gray-100 text-gray-800', icon: '⚪' };
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">#${user.id}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            ${user.avatar ? 
                                `<img src="${user.avatar}" alt="${escapeHtml(user.name)}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'U')}&background=6366f1&color=fff'">` :
                                `<div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">${(user.name || 'U').charAt(0).toUpperCase()}</div>`
                            }
                            <div class="text-sm font-medium text-gray-900">${escapeHtml(user.name || '-')}</div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(user.email || '-')}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${roleInfo.color}">${roleInfo.icon} ${user.role}</span>
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-green-600">${formatCurrency(user.balance)}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${user.total_orders || 0}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${formatDate(user.created_at)}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="${BASE_URL}/database/users/${user.id}/edit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Sửa</a>
                            <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
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
            html += `<button onclick="loadUsers(${pagination.current_page - 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        }
        
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            html += `<button onclick="loadUsers(${i})" class="px-3 py-1 rounded ${i === pagination.current_page ? 'bg-indigo-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="loadUsers(${pagination.current_page + 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        }
        
        container.innerHTML = html;
    }

    async function deleteUser(id) {
        if (!confirm('Bạn có chắc muốn xóa người dùng này?')) return;
        
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            });
            
            const result = await response.json();
            
            if (result.success) {
                loadUsers(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa người dùng');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount || 0) + ' đ';
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadUsers(1), 500);
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadStats();  // Load stats from all data
        loadUsers();  // Load paginated data for table
    });
</script>
@endpush
