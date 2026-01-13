@extends('layouts.app')

@section('title', 'Products Management')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 md:mb-8 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">Products Management</h1>
            <p class="text-sm sm:text-base text-gray-600">Quản lý sản phẩm trong database</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
            <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
                ← Back to Dashboard
            </a>
            <a href="{{ route('database.create-product') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200 text-center text-sm sm:text-base">
                + Add New Product
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

    <!-- Search and Sorting Controls -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <!-- Search -->
        <div class="mb-4 pb-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Tìm kiếm sản phẩm</label>
                <button type="button" id="clear-search" class="text-xs text-gray-500 hover:text-gray-700 hidden">
                    Xóa tìm kiếm
                </button>
            </div>
            <div class="relative">
                <input type="text" 
                       id="search-input"
                       placeholder="Tìm kiếm theo tên sản phẩm..." 
                       class="w-full px-3 py-2 pl-10 pr-10 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <button type="button" id="clear-search-btn" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <!-- Gợi ý tìm kiếm -->
                <div id="search-suggestions" class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-30 max-h-64 overflow-y-auto">
                    <!-- Suggestions will be rendered here -->
                </div>
            </div>
        </div>

        <!-- Sorting -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="mb-2 lg:mb-0">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Sắp xếp</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">
                    Hiện tại: <span id="current-sort-field">ID</span> 
                    (<span id="current-sort-order">Tăng dần</span>)
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <label for="sort_by" class="text-xs sm:text-sm font-medium text-gray-700 sm:whitespace-nowrap">Sắp xếp theo:</label>
                    <select name="sort_by" id="sort_by" class="flex-1 sm:flex-none border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="id">ID</option>
                        <option value="title">Tên sản phẩm</option>
                        <option value="price">Giá</option>
                        <option value="category">Danh mục</option>
                        <option value="view_count">Lượt xem</option>
                    </select>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <label for="sort_order" class="text-xs sm:text-sm font-medium text-gray-700 sm:whitespace-nowrap">Thứ tự:</label>
                    <select name="sort_order" id="sort_order" class="flex-1 sm:flex-none border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="asc">Tăng dần (A-Z)</option>
                        <option value="desc">Giảm dần (Z-A)</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="button" id="apply-sort" class="flex-1 sm:flex-none bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200 text-sm">
                        Áp dụng
                    </button>
                    
                    <button type="button" id="reset-sort" class="flex-1 sm:flex-none bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-200 text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Products List (<span id="total-products">0</span> total)</h2>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="products-table-body" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Đang tải dữ liệu...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile/Tablet Card View -->
        <div id="products-card-view" class="md:hidden divide-y divide-gray-200">
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
    const API_BASE_URL = '{{ url("/api/products") }}';
    const BASE_URL = '{{ url("/") }}';
    
    // State management
    let currentPage = 1;
    let lastPage = 1;
    let sortBy = 'id';
    let sortOrder = 'asc';
    let perPage = 10;
    let currentSearch = '';
    let searchSuggestTimeout = null;

    // DOM Elements
    const loadingIndicator = document.getElementById('loading-indicator');
    const errorMessage = document.getElementById('error-message');
    const productsTableBody = document.getElementById('products-table-body');
    const productsCardView = document.getElementById('products-card-view');
    const totalProductsSpan = document.getElementById('total-products');
    const paginationContainer = document.getElementById('pagination-container');
    const paginationNav = document.getElementById('pagination-nav');
    const currentSortField = document.getElementById('current-sort-field');
    const currentSortOrder = document.getElementById('current-sort-order');
    const sortBySelect = document.getElementById('sort_by');
    const sortOrderSelect = document.getElementById('sort_order');
    const applySortBtn = document.getElementById('apply-sort');
    const resetSortBtn = document.getElementById('reset-sort');
    const pageInput = document.getElementById('page_input');
    const lastPageSpan = document.getElementById('last-page');
    const goToPageBtn = document.getElementById('go-to-page');
    const searchInput = document.getElementById('search-input');
    const searchSuggestions = document.getElementById('search-suggestions');
    const clearSearchBtn = document.getElementById('clear-search-btn');
    const clearSearchLink = document.getElementById('clear-search');

    // Sort field labels
    const sortFieldLabels = {
        'id': 'ID',
        'title': 'Tên sản phẩm',
        'price': 'Giá',
        'category': 'Danh mục',
        'view_count': 'Lượt xem'
    };

    // Load products from API
    async function loadProducts(page = 1) {
        try {
            showLoading();
            hideError();

            const params = new URLSearchParams({
                page: page,
                per_page: perPage,
                sort_by: sortBy,
                sort_order: sortOrder
            });

            if (currentSearch) {
                params.append('search', currentSearch);
            }

            const response = await fetch(`${API_BASE_URL}?${params}`);
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to load products');
            }

            const { data, pagination } = result;
            
            currentPage = pagination.current_page;
            lastPage = pagination.last_page;
            totalProductsSpan.textContent = pagination.total;
            lastPageSpan.textContent = pagination.last_page;
            pageInput.value = currentPage;
            pageInput.max = lastPage;

            renderProducts(data);
            renderPagination(pagination);
            updateSortDisplay();
            updateSearchDisplay();

            hideLoading();
        } catch (error) {
            console.error('Error loading products:', error);
            showError('Lỗi khi tải dữ liệu: ' + error.message);
            hideLoading();
        }
    }

    // Render products in table
    function renderProducts(products) {
        if (products.length === 0) {
            productsTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        No products found. <a href="{{ route('database.create-product') }}" class="text-blue-600 hover:text-blue-900">Add your first product</a>
                    </td>
                </tr>
            `;
            productsCardView.innerHTML = `
                <div class="px-4 py-8 text-center text-gray-500">
                    No products found. <a href="{{ route('database.create-product') }}" class="text-blue-600 hover:text-blue-900">Add your first product</a>
                </div>
            `;
            return;
        }

        // Desktop table view
        productsTableBody.innerHTML = products.map(product => `
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${product.id ?? 'N/A'}
                </td>
                <td class="px-2 py-3 whitespace-nowrap">
                    ${product.image ? 
                        `<img src="${product.image}" alt="${escapeHtml(product.title)}" class="h-10 w-10 object-cover rounded">` :
                        `<div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                            <span class="text-gray-500 text-xs">No</span>
                        </div>`
                    }
                </td>
                <td class="px-2 py-3">
                    <div class="text-sm font-medium text-gray-900 truncate max-w-44">${escapeHtml(product.title ?? 'N/A')}</div>
                    ${product.short_description ? 
                        `<div class="text-xs text-gray-500 truncate max-w-44">${escapeHtml(product.short_description)}</div>` : 
                        ''
                    }
                </td>
                <td class="px-2 py-3 text-sm text-gray-900">
                    <div class="truncate max-w-24" title="${escapeHtml(product.price ?? 'N/A')}">
                        ${escapeHtml(product.price ?? 'N/A')}
                    </div>
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900">
                    ${escapeHtml(product.category ?? 'N/A')}
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900">
                    ${product.view_count ?? 0}
                </td>
                <td class="px-2 py-3 whitespace-nowrap text-sm font-medium">
                    ${product.id ? `
                        <div class="flex flex-col space-y-1">
                            <a href="${BASE_URL}/database/products/${product.id}/edit?page=${currentPage}&sort_by=${sortBy}&sort_order=${sortOrder}" 
                               onclick="saveListUrl(); return true;" 
                               class="text-blue-600 hover:text-blue-900 text-xs">Edit</a>
                            <button onclick="deleteProduct(${product.id})" class="text-red-600 hover:text-red-900 text-xs text-left">Delete</button>
                        </div>
                    ` : `
                        <span class="text-gray-400 text-xs">No ID</span>
                    `}
                </td>
            </tr>
        `).join('');

        // Mobile card view
        productsCardView.innerHTML = products.map(product => `
            <div class="p-4 hover:bg-gray-50">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        ${product.image ? 
                            `<img src="${product.image}" alt="${escapeHtml(product.title)}" class="h-16 w-16 object-cover rounded">` :
                            `<div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-gray-500 text-xs">No Image</span>
                            </div>`
                        }
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-gray-500 mb-1">ID: ${product.id ?? 'N/A'}</div>
                                <h3 class="text-sm font-semibold text-gray-900 truncate">${escapeHtml(product.title ?? 'N/A')}</h3>
                                ${product.short_description ? 
                                    `<p class="text-xs text-gray-600 mt-1 line-clamp-2">${escapeHtml(product.short_description)}</p>` : 
                                    ''
                                }
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                            <div>
                                <span class="text-gray-500">Price:</span>
                                <span class="font-medium text-gray-900 ml-1">${escapeHtml(product.price ?? 'N/A')}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Category:</span>
                                <span class="font-medium text-gray-900 ml-1 truncate block">${escapeHtml(product.category ?? 'N/A')}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Views:</span>
                                <span class="font-medium text-gray-900 ml-1">${product.view_count ?? 0}</span>
                            </div>
                        </div>
                        ${product.id ? `
                            <div class="flex gap-2">
                                <a href="${BASE_URL}/database/products/${product.id}/edit?page=${currentPage}&sort_by=${sortBy}&sort_order=${sortOrder}" 
                                   onclick="saveListUrl(); return true;" 
                                   class="flex-1 bg-blue-500 text-white px-3 py-1.5 rounded text-xs text-center hover:bg-blue-600 transition">
                                    Edit
                                </a>
                                <button onclick="deleteProduct(${product.id})" class="flex-1 bg-red-500 text-white px-3 py-1.5 rounded text-xs hover:bg-red-600 transition">
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
            paginationHTML += `<button onclick="loadProducts(${pagination.current_page - 1})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">‹</button>`;
        }

        // First page
        if (showFirst) {
            paginationHTML += `<button onclick="loadProducts(1)" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">1</button>`;
            if (startPage > 2) {
                paginationHTML += `<span class="px-2 text-sm text-gray-500">...</span>`;
            }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                paginationHTML += `<span class="px-3 py-2 text-sm font-semibold text-white bg-blue-500 border border-blue-500 rounded-md">${i}</span>`;
            } else {
                paginationHTML += `<button onclick="loadProducts(${i})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">${i}</button>`;
            }
        }

        // Last page
        if (showLast) {
            if (endPage < pagination.last_page - 1) {
                paginationHTML += `<span class="px-2 text-sm text-gray-500">...</span>`;
            }
            paginationHTML += `<button onclick="loadProducts(${pagination.last_page})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">${pagination.last_page}</button>`;
        }

        // Next button
        if (pagination.current_page === pagination.last_page) {
            paginationHTML += `<span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-300 rounded-md cursor-not-allowed">›</span>`;
        } else {
            paginationHTML += `<button onclick="loadProducts(${pagination.current_page + 1})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition">›</button>`;
        }

        paginationNav.innerHTML = paginationHTML;
    }

    // Delete product
    async function deleteProduct(id) {
        if (!confirm('Are you sure you want to delete this product?')) {
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
                throw new Error(result.message || 'Failed to delete product');
            }

            // Reload products
            await loadProducts(currentPage);
            hideLoading();
        } catch (error) {
            console.error('Error deleting product:', error);
            showError('Lỗi khi xóa sản phẩm: ' + error.message);
            hideLoading();
        }
    }

    // Update sort display
    function updateSortDisplay() {
        currentSortField.textContent = sortFieldLabels[sortBy] || sortBy;
        currentSortOrder.textContent = sortOrder === 'asc' ? 'Tăng dần' : 'Giảm dần';
        sortBySelect.value = sortBy;
        sortOrderSelect.value = sortOrder;
    }

    // Update search display
    function updateSearchDisplay() {
        const hasSearch = currentSearch || (searchInput && searchInput.value.trim());
        if (hasSearch && clearSearchBtn && clearSearchLink) {
            clearSearchBtn.classList.remove('hidden');
            clearSearchLink.classList.remove('hidden');
        } else if (clearSearchBtn && clearSearchLink) {
            clearSearchBtn.classList.add('hidden');
            clearSearchLink.classList.add('hidden');
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
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Search suggestions functions
    function hideSearchSuggestions() {
        if (searchSuggestions) {
            searchSuggestions.classList.add('hidden');
            searchSuggestions.innerHTML = '';
        }
    }

    async function loadSearchSuggestions(query) {
        if (!searchSuggestions) return;

        if (!query || query.length < 2) {
            hideSearchSuggestions();
            return;
        }

        try {
            const url = `${API_BASE_URL}?per_page=5&search=${encodeURIComponent(query)}&sort_by=view_count&sort_order=desc`;
            const res = await fetch(url);
            const data = await res.json();

            if (!data.success || !data.data || data.data.length === 0) {
                hideSearchSuggestions();
                return;
            }

            searchSuggestions.innerHTML = data.data.map(product => `
                <button type="button"
                        class="w-full text-left px-3 py-2 flex items-center gap-2 hover:bg-gray-50 transition-colors text-sm"
                        data-id="${product.id}"
                        data-title="${escapeHtml(product.title)}">
                    <span class="flex-1 truncate">${escapeHtml(product.title)}</span>
                    ${product.category ? `<span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">${escapeHtml(product.category)}</span>` : ''}
                </button>
            `).join('');

            searchSuggestions.classList.remove('hidden');

            // Add click event listeners to suggestions
            searchSuggestions.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', () => {
                    const title = btn.getAttribute('data-title');
                    searchInput.value = title;
                    currentSearch = title;
                    currentPage = 1;
                    hideSearchSuggestions();
                    loadProducts(1);
                });
            });
        } catch (e) {
            console.error('Error loading search suggestions:', e);
            hideSearchSuggestions();
        }
    }

    // Event listeners
    applySortBtn.addEventListener('click', () => {
        sortBy = sortBySelect.value;
        sortOrder = sortOrderSelect.value;
        loadProducts(1);
    });

    resetSortBtn.addEventListener('click', () => {
        sortBy = 'id';
        sortOrder = 'asc';
        currentSearch = '';
        if (searchInput) {
            searchInput.value = '';
        }
        hideSearchSuggestions();
        loadProducts(1);
    });

    // Clear search
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            currentSearch = '';
            if (searchInput) {
                searchInput.value = '';
            }
            hideSearchSuggestions();
            updateSearchDisplay();
            loadProducts(1);
        });
    }

    if (clearSearchLink) {
        clearSearchLink.addEventListener('click', () => {
            currentSearch = '';
            if (searchInput) {
                searchInput.value = '';
            }
            hideSearchSuggestions();
            updateSearchDisplay();
            loadProducts(1);
        });
    }

    goToPageBtn.addEventListener('click', () => {
        const page = parseInt(pageInput.value);
        if (page >= 1 && page <= lastPage) {
            loadProducts(page);
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

    // Function to save current list URL before navigating to edit page
    // Make it global so it can be called from onclick handlers
    window.saveListUrl = function() {
        const listUrl = `${BASE_URL}/database/products?page=${currentPage}&sort_by=${sortBy}&sort_order=${sortOrder}`;
        localStorage.setItem('productsListUrl', listUrl);
        console.log('Saved list URL:', listUrl);
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        // Get initial params from URL if available
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('sort_by')) {
            sortBy = urlParams.get('sort_by');
        }
        if (urlParams.get('sort_order')) {
            sortOrder = urlParams.get('sort_order');
        }
        if (urlParams.get('page')) {
            currentPage = parseInt(urlParams.get('page'));
        }
        if (urlParams.get('search')) {
            currentSearch = urlParams.get('search');
            if (searchInput) {
                searchInput.value = currentSearch;
            }
        }

        // Search input event listeners
        if (searchInput) {
            // Show suggestions while typing
            searchInput.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                updateSearchDisplay();
                clearTimeout(searchSuggestTimeout);
                searchSuggestTimeout = setTimeout(() => {
                    loadSearchSuggestions(value);
                }, 250);
            });

            // Search on Enter key
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    currentSearch = e.target.value.trim();
                    currentPage = 1;
                    hideSearchSuggestions();
                    loadProducts(1);
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                    hideSearchSuggestions();
                }
            });
        }

        loadProducts(currentPage);
        updateSearchDisplay();
    });
</script>
@endpush
