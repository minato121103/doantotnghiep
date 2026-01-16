@extends('layouts.app')

@section('title', 'Quản lý Sản phẩm')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">🎮 Quản lý Sản phẩm</h1>
            <p class="text-sm text-gray-600">Xem và quản lý sản phẩm game trong hệ thống</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('database.create-product') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200 text-center">
                + Thêm mới
            </a>
            <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
                ← Quay lại
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" id="search" placeholder="Tên game..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                <select id="category-filter" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500">
                    <option value="">Tất cả</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Loại tài khoản</label>
                <select id="type-filter" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500">
                    <option value="">Tất cả</option>
                    <option value="offline">🎮 Offline</option>
                    <option value="online">🌐 Online</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500">
                    <option value="id">ID</option>
                    <option value="title">Tên game</option>
                    <option value="price">Giá</option>
                    <option value="view_count">Lượt xem</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                <select id="sort-order" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500">
                    <option value="desc">Mới nhất</option>
                    <option value="asc">Cũ nhất</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadProducts()" class="w-full bg-teal-500 text-white px-4 py-2 rounded-md hover:bg-teal-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-teal-50 border border-teal-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-teal-600" id="stat-total">-</div>
            <div class="text-sm text-teal-700">🎮 Tổng sản phẩm</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600" id="stat-categories">-</div>
            <div class="text-sm text-blue-700">📂 Danh mục</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600" id="stat-views">-</div>
            <div class="text-sm text-purple-700">👁️ Lượt xem</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="stat-accounts">-</div>
            <div class="text-sm text-green-700">🎯 Accounts</div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sản phẩm</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden md:table-cell">
                            <div class="flex items-center gap-1">
                                <span>📂</span>
                                <span>Danh mục</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Giá</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lượt xem</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Accounts</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="products-table" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="animate-spin w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full mx-auto mb-2"></div>
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
    const API_URL = '{{ url("/api/products") }}';
    const BASE_URL = '{{ url("/") }}';
    let currentPage = 1;
    let allCategories = [];

    async function loadCategories() {
        try {
            const response = await fetch(`${API_URL}/categories`);
            const result = await response.json();
            if (result.success) {
                allCategories = result.data;
                const select = document.getElementById('category-filter');
                allCategories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.category;
                    option.textContent = `${cat.category} (${cat.count})`;
                    select.appendChild(option);
                });
                
                // Update categories count from API
                document.getElementById('stat-categories').textContent = allCategories.length;
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    // Load stats from all data (separate call)
    async function loadStats() {
        try {
            const response = await fetch(`${API_URL}?per_page=10000`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            
            if (result.success) {
                const allProducts = result.data;
                
                // Total products
                document.getElementById('stat-total').textContent = result.pagination?.total || allProducts.length;
                
                // Sum views
                const totalViews = allProducts.reduce((sum, p) => sum + (parseInt(p.view_count) || 0), 0);
                document.getElementById('stat-views').textContent = totalViews.toLocaleString('vi-VN');
                
                // Sum available accounts
                const totalAccounts = allProducts.reduce((sum, p) => sum + (parseInt(p.available_accounts) || 0), 0);
                document.getElementById('stat-accounts').textContent = totalAccounts.toLocaleString('vi-VN');
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async function loadProducts(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const category = document.getElementById('category-filter').value;
        const type = document.getElementById('type-filter').value;
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        try {
            let url = `${API_URL}?page=${page}&per_page=15&sort_by=${sortBy}&sort_order=${sortOrder}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (category) url += `&category=${encodeURIComponent(category)}`;
            if (type) url += `&type=${encodeURIComponent(type)}`;
            
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            
            const result = await response.json();
            
            if (result.success) {
                renderProducts(result.data);
                renderPagination(result.pagination);
            } else {
                document.getElementById('products-table').innerHTML = `
                    <tr><td colspan="7" class="px-4 py-8 text-center text-red-500">${result.message || 'Lỗi tải dữ liệu'}</td></tr>
                `;
            }
        } catch (error) {
            document.getElementById('products-table').innerHTML = `
                <tr><td colspan="7" class="px-4 py-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>
            `;
        }
    }

    function renderProducts(products) {
        const tbody = document.getElementById('products-table');
        
        if (!products || products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">
                Không có sản phẩm. <a href="{{ route('database.create-product') }}" class="text-teal-600 hover:text-teal-800">Thêm sản phẩm mới</a>
            </td></tr>`;
            return;
        }
        
        tbody.innerHTML = products.map(product => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">#${product.id}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        ${product.image ? 
                            `<img src="${product.image}" alt="${escapeHtml(product.title)}" class="w-12 h-12 rounded object-cover">` :
                            `<div class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center text-gray-400">🎮</div>`
                        }
                        <div class="text-sm font-medium text-teal-600 max-w-[200px] truncate">${escapeHtml(product.title || '-')}</div>
                    </div>
                </td>
                <td class="px-3 py-3 hidden md:table-cell">
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-full bg-gradient-to-r from-teal-500 to-cyan-500 text-white shadow-sm hover:shadow-md hover:scale-105 transition-all duration-200 cursor-default max-w-[120px] truncate" title="${escapeHtml(product.category || 'Chưa phân loại')}">
                        <span class="flex-shrink-0">🏷️</span>
                        <span class="truncate">${escapeHtml(product.category || 'Chưa phân loại')}</span>
                    </span>
                </td>
                <td class="px-4 py-3 text-sm font-semibold text-green-600">${escapeHtml(product.price || '-')}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${(product.view_count || 0).toLocaleString('vi-VN')}</td>
                <td class="px-4 py-3 text-sm font-medium ${product.available_accounts > 0 ? 'text-green-600' : 'text-gray-400'}">
                    ${product.available_accounts || 0}
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="${BASE_URL}/database/products/${product.id}/edit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Sửa</a>
                        <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderPagination(pagination) {
        const container = document.getElementById('pagination');
        if (!pagination || pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let html = '';
        
        if (pagination.current_page > 1) {
            html += `<button onclick="loadProducts(${pagination.current_page - 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        }
        
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            html += `<button onclick="loadProducts(${i})" class="px-3 py-1 rounded ${i === pagination.current_page ? 'bg-teal-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="loadProducts(${pagination.current_page + 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        }
        
        container.innerHTML = html;
    }

    async function deleteProduct(id) {
        if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
        
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            });
            
            const result = await response.json();
            
            if (result.success) {
                loadProducts(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa sản phẩm');
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

    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadProducts(1), 500);
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadCategories();
        loadStats();     // Load stats from all data
        loadProducts();  // Load paginated data for table
    });
</script>
@endpush
