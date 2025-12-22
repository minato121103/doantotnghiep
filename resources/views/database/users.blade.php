@extends('layouts.app')

@section('title', 'Users Management')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 md:mb-8 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">Users Management</h1>
            <p class="text-sm sm:text-base text-gray-600">Quản lý người dùng trong database</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
            <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
                ← Back to Dashboard
            </a>
            <a href="{{ route('database.create-user') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200 text-center text-sm sm:text-base">
                + Add New User
            </a>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loading-indicator" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 hidden">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Đang tải dữ liệu...</span>
        </div>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Filter Controls -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="mb-2 lg:mb-0">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Bộ lọc & Sắp xếp</h3>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <label for="filter_role" class="text-xs sm:text-sm font-medium text-gray-700 sm:whitespace-nowrap">Vai trò:</label>
                    <select name="filter_role" id="filter_role" class="flex-1 sm:flex-none border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        <option value="admin">Admin</option>
                        <option value="buyer">Buyer</option>
                        <option value="editor">Editor</option>
                    </select>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <label for="filter_status" class="text-xs sm:text-sm font-medium text-gray-700 sm:whitespace-nowrap">Trạng thái:</label>
                    <select name="filter_status" id="filter_status" class="flex-1 sm:flex-none border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tất cả</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="banned">Banned</option>
                    </select>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <label for="search" class="text-xs sm:text-sm font-medium text-gray-700 sm:whitespace-nowrap">Tìm kiếm:</label>
                    <input type="text" id="search" name="search" placeholder="Tên hoặc email..."
                           class="flex-1 sm:flex-none border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="sort_by" class="text-xs sm:text-sm font-medium text-gray-700 sm:whitespace-nowrap">Sắp xếp theo:</label>
                <select name="sort_by" id="sort_by" class="flex-1 sm:flex-none border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="id">ID</option>
                    <option value="name">Tên</option>
                    <option value="email">Email</option>
                    <option value="role">Vai trò</option>
                    <option value="status">Trạng thái</option>
                    <option value="balance">Số dư</option>
                    <option value="total_orders">Tổng đơn hàng</option>
                    <option value="total_spent">Tổng chi tiêu</option>
                    <option value="created_at">Ngày tạo</option>
                </select>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="sort_order" class="text-xs sm:text-sm font-medium text-gray-700 sm:whitespace-nowrap">Thứ tự:</label>
                <select name="sort_order" id="sort_order" class="flex-1 sm:flex-none border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="asc">Tăng dần</option>
                    <option value="desc" selected>Giảm dần</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="button" id="apply-filters" class="flex-1 sm:flex-none bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200 text-sm">
                    Áp dụng
                </button>
                
                <button type="button" id="reset-filters" class="flex-1 sm:flex-none bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-200 text-sm">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Users List (<span id="total-users">0</span> total)</h2>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avatar</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Đang tải dữ liệu...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile/Tablet Card View -->
        <div id="users-card-view" class="md:hidden divide-y divide-gray-200">
            <div class="px-4 py-8 text-center text-gray-500">
                Đang tải dữ liệu...
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination-container" class="px-4 sm:px-6 py-4 border-t border-gray-200 hidden">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="flex items-center justify-center sm:justify-start overflow-x-auto">
                    <nav id="pagination-nav" class="flex items-center gap-1"></nav>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-2">
                    <div class="flex items-center gap-2">
                        <label for="page_input" class="text-xs sm:text-sm font-medium text-gray-700 whitespace-nowrap">Đi đến trang:</label>
                        <input type="number" 
                               id="page_input" 
                               name="page" 
                               value="1" 
                               min="1" 
                               max="1"
                               class="w-16 sm:w-20 px-2 sm:px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="text-xs sm:text-sm text-gray-600 whitespace-nowrap">/ <span id="last-page">1</span></span>
                    </div>
                    <button type="button" id="go-to-page" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200 text-sm whitespace-nowrap">
                        Đi
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // API Configuration
    const API_BASE_URL = '{{ url("/api/users") }}';
    const BASE_URL = '{{ url("/") }}';
    
    // State management
    let currentPage = 1;
    let lastPage = 1;
    let sortBy = 'id';
    let sortOrder = 'desc';
    let perPage = 15;
    let filters = {
        role: '',
        status: '',
        search: ''
    };

    // DOM Elements
    const loadingIndicator = document.getElementById('loading-indicator');
    const errorMessage = document.getElementById('error-message');
    const usersTableBody = document.getElementById('users-table-body');
    const usersCardView = document.getElementById('users-card-view');
    const totalUsersSpan = document.getElementById('total-users');
    const paginationContainer = document.getElementById('pagination-container');
    const paginationNav = document.getElementById('pagination-nav');
    const filterRoleSelect = document.getElementById('filter_role');
    const filterStatusSelect = document.getElementById('filter_status');
    const searchInput = document.getElementById('search');
    const sortBySelect = document.getElementById('sort_by');
    const sortOrderSelect = document.getElementById('sort_order');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const pageInput = document.getElementById('page_input');
    const lastPageSpan = document.getElementById('last-page');
    const goToPageBtn = document.getElementById('go-to-page');

    // Role and Status badges
    function getRoleBadge(role) {
        const badges = {
            'admin': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Admin</span>',
            'buyer': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Buyer</span>',
            'editor': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Editor</span>'
        };
        return badges[role] || `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">${escapeHtml(role)}</span>`;
    }

    function getStatusBadge(status) {
        const badges = {
            'active': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>',
            'inactive': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Inactive</span>',
            'banned': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Banned</span>'
        };
        return badges[status] || `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">${escapeHtml(status)}</span>`;
    }

    // Load users from API
    async function loadUsers(page = 1) {
        try {
            showLoading();
            hideError();

            const params = new URLSearchParams({
                page: page,
                per_page: perPage,
                sort_by: sortBy,
                sort_order: sortOrder
            });

            if (filters.role) params.append('role', filters.role);
            if (filters.status) params.append('status', filters.status);
            if (filters.search) params.append('search', filters.search);

            const response = await fetch(`${API_BASE_URL}?${params}`);
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to load users');
            }

            const { data, pagination } = result;
            
            currentPage = pagination.current_page;
            lastPage = pagination.last_page;
            totalUsersSpan.textContent = pagination.total;
            lastPageSpan.textContent = pagination.last_page;
            pageInput.value = currentPage;
            pageInput.max = lastPage;

            renderUsers(data);
            renderPagination(pagination);

            hideLoading();
        } catch (error) {
            console.error('Error loading users:', error);
            showError('Lỗi khi tải dữ liệu: ' + error.message);
            hideLoading();
        }
    }

    // Render users in table
    function renderUsers(users) {
        if (users.length === 0) {
            usersTableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        No users found. <a href="{{ route('database.create-user') }}" class="text-blue-600 hover:text-blue-900">Add your first user</a>
                    </td>
                </tr>
            `;
            usersCardView.innerHTML = `
                <div class="px-4 py-8 text-center text-gray-500">
                    No users found. <a href="{{ route('database.create-user') }}" class="text-blue-600 hover:text-blue-900">Add your first user</a>
                </div>
            `;
            return;
        }

        // Desktop table view
        usersTableBody.innerHTML = users.map(user => `
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${user.id ?? 'N/A'}
                </td>
                <td class="px-2 py-3 whitespace-nowrap">
                    ${user.avatar ? 
                        `<img src="${user.avatar}" 
                              alt="${escapeHtml(user.name)}" 
                              referrerpolicy="no-referrer"
                              onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('${(user.name || 'U').charAt(0).toUpperCase()}') + '&background=6366f1&color=fff&size=96';"
                              class="h-10 w-10 object-cover rounded-full">` :
                        `<div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                            <span class="text-gray-500 text-xs">${(user.name || 'U').charAt(0).toUpperCase()}</span>
                        </div>`
                    }
                </td>
                <td class="px-2 py-3">
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(user.name ?? 'N/A')}</div>
                    ${user.phone ? 
                        `<div class="text-xs text-gray-500">${escapeHtml(user.phone)}</div>` : 
                        ''
                    }
                </td>
                <td class="px-2 py-3 text-sm text-gray-900">
                    <div class="truncate max-w-48" title="${escapeHtml(user.email ?? 'N/A')}">
                        ${escapeHtml(user.email ?? 'N/A')}
                    </div>
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-sm">
                    ${getRoleBadge(user.role)}
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-sm">
                    ${getStatusBadge(user.status)}
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900">
                    ${user.balance ? parseFloat(user.balance).toLocaleString('vi-VN') : '0'} đ
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900">
                    ${user.total_orders ?? 0}
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-sm font-medium">
                    ${user.id ? `
                        <div class="flex flex-col space-y-1">
                            <a href="${BASE_URL}/database/users/${user.id}/edit?page=${currentPage}" class="text-blue-600 hover:text-blue-900 text-xs">Edit</a>
                            <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900 text-xs text-left">Delete</button>
                        </div>
                    ` : `
                        <span class="text-gray-400 text-xs">No ID</span>
                    `}
                </td>
            </tr>
        `).join('');

        // Mobile card view
        usersCardView.innerHTML = users.map(user => `
            <div class="p-4 hover:bg-gray-50">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        ${user.avatar ? 
                            `<img src="${user.avatar}" 
                                  alt="${escapeHtml(user.name)}" 
                                  referrerpolicy="no-referrer"
                                  onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent('${(user.name || 'U').charAt(0).toUpperCase()}') + '&background=6366f1&color=fff&size=96';"
                                  class="h-16 w-16 object-cover rounded-full">` :
                            `<div class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-500 text-lg font-semibold">${(user.name || 'U').charAt(0).toUpperCase()}</span>
                            </div>`
                        }
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-gray-500 mb-1">ID: ${user.id ?? 'N/A'}</div>
                                <h3 class="text-sm font-semibold text-gray-900 truncate">${escapeHtml(user.name ?? 'N/A')}</h3>
                                <p class="text-xs text-gray-600 mt-1 truncate">${escapeHtml(user.email ?? 'N/A')}</p>
                                ${user.phone ? 
                                    `<p class="text-xs text-gray-500 mt-1">${escapeHtml(user.phone)}</p>` : 
                                    ''
                                }
                            </div>
                        </div>
                        <div class="flex gap-2 mb-2">
                            ${getRoleBadge(user.role)}
                            ${getStatusBadge(user.status)}
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                            <div>
                                <span class="text-gray-500">Balance:</span>
                                <span class="font-medium text-gray-900 ml-1">${user.balance ? parseFloat(user.balance).toLocaleString('vi-VN') : '0'} đ</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Orders:</span>
                                <span class="font-medium text-gray-900 ml-1">${user.total_orders ?? 0}</span>
                            </div>
                        </div>
                        ${user.id ? `
                            <div class="flex gap-2">
                                <a href="${BASE_URL}/database/users/${user.id}/edit?page=${currentPage}" class="flex-1 bg-blue-500 text-white px-3 py-1.5 rounded text-xs text-center hover:bg-blue-600 transition">
                                    Edit
                                </a>
                                <button onclick="deleteUser(${user.id})" class="flex-1 bg-red-500 text-white px-3 py-1.5 rounded text-xs hover:bg-red-600 transition">
                                    Delete
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Render pagination
    function renderPagination(pagination) {
        if (pagination.last_page <= 1) {
            paginationContainer.classList.add('hidden');
            return;
        }

        paginationContainer.classList.remove('hidden');
        
        const showPages = 5;
        const half = Math.floor(showPages / 2);
        let startPage, endPage;

        if (pagination.last_page <= showPages) {
            startPage = 1;
            endPage = pagination.last_page;
        } else {
            if (pagination.current_page <= half + 1) {
                startPage = 1;
                endPage = showPages;
            } else if (pagination.current_page >= pagination.last_page - half) {
                startPage = pagination.last_page - showPages + 1;
                endPage = pagination.last_page;
            } else {
                startPage = pagination.current_page - half;
                endPage = pagination.current_page + half;
            }
        }

        const showFirst = (startPage > 1 && pagination.current_page > 3);
        const showLast = (endPage < pagination.last_page && pagination.current_page < pagination.last_page - 2);

        let paginationHTML = '';

        // Previous button
        if (pagination.current_page === 1) {
            paginationHTML += `<span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-300 rounded-md cursor-not-allowed">‹</span>`;
        } else {
            paginationHTML += `<button onclick="loadUsers(${pagination.current_page - 1})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">‹</button>`;
        }

        // First page
        if (showFirst) {
            paginationHTML += `<button onclick="loadUsers(1)" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">1</button>`;
            if (startPage > 2) {
                paginationHTML += `<span class="px-2 text-sm text-gray-500">...</span>`;
            }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                paginationHTML += `<span class="px-3 py-2 text-sm font-semibold text-white bg-blue-500 border border-blue-500 rounded-md">${i}</span>`;
            } else {
                paginationHTML += `<button onclick="loadUsers(${i})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">${i}</button>`;
            }
        }

        // Last page
        if (showLast) {
            if (endPage < pagination.last_page - 1) {
                paginationHTML += `<span class="px-2 text-sm text-gray-500">...</span>`;
            }
            paginationHTML += `<button onclick="loadUsers(${pagination.last_page})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">${pagination.last_page}</button>`;
        }

        // Next button
        if (pagination.current_page === pagination.last_page) {
            paginationHTML += `<span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-300 rounded-md cursor-not-allowed">›</span>`;
        } else {
            paginationHTML += `<button onclick="loadUsers(${pagination.current_page + 1})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">›</button>`;
        }

        paginationNav.innerHTML = paginationHTML;
    }

    // Delete user
    async function deleteUser(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
            return;
        }

        try {
            showLoading();
            hideError();

            const response = await fetch(`${API_BASE_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to delete user');
            }

            // Reload users
            await loadUsers(currentPage);
            hideLoading();
        } catch (error) {
            console.error('Error deleting user:', error);
            showError('Lỗi khi xóa người dùng: ' + error.message);
            hideLoading();
        }
    }

    // Utility functions
    function showLoading() {
        loadingIndicator.classList.remove('hidden');
    }

    function hideLoading() {
        loadingIndicator.classList.add('hidden');
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
    }

    function hideError() {
        errorMessage.classList.add('hidden');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Event listeners
    applyFiltersBtn.addEventListener('click', () => {
        filters.role = filterRoleSelect.value;
        filters.status = filterStatusSelect.value;
        filters.search = searchInput.value.trim();
        sortBy = sortBySelect.value;
        sortOrder = sortOrderSelect.value;
        loadUsers(1);
    });

    resetFiltersBtn.addEventListener('click', () => {
        filterRoleSelect.value = '';
        filterStatusSelect.value = '';
        searchInput.value = '';
        sortBySelect.value = 'id';
        sortOrderSelect.value = 'desc';
        filters = { role: '', status: '', search: '' };
        sortBy = 'id';
        sortOrder = 'desc';
        loadUsers(1);
    });

    // Search on Enter key
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            applyFiltersBtn.click();
        }
    });

    goToPageBtn.addEventListener('click', () => {
        const page = parseInt(pageInput.value);
        if (page >= 1 && page <= lastPage) {
            loadUsers(page);
        } else {
            pageInput.value = currentPage;
        }
    });

    pageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            goToPageBtn.click();
        }
    });

    pageInput.addEventListener('blur', () => {
        const page = parseInt(pageInput.value);
        if (isNaN(page) || page < 1) {
            pageInput.value = 1;
        } else if (page > lastPage) {
            pageInput.value = lastPage;
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('sort_by')) {
            sortBy = urlParams.get('sort_by');
            sortBySelect.value = sortBy;
        }
        if (urlParams.get('sort_order')) {
            sortOrder = urlParams.get('sort_order');
            sortOrderSelect.value = sortOrder;
        }
        if (urlParams.get('role')) {
            filters.role = urlParams.get('role');
            filterRoleSelect.value = filters.role;
        }
        if (urlParams.get('status')) {
            filters.status = urlParams.get('status');
            filterStatusSelect.value = filters.status;
        }
        if (urlParams.get('search')) {
            filters.search = urlParams.get('search');
            searchInput.value = filters.search;
        }
        if (urlParams.get('page')) {
            currentPage = parseInt(urlParams.get('page'));
        }

        loadUsers(currentPage);
    });
</script>
@endpush

