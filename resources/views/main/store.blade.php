@extends('layouts.main')

@section('title', 'Khám phá')

@section('content')
    <!-- Main Content -->
    <section class="pt-36 pb-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="lg:w-72 flex-shrink-0">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 sticky top-24">
                        <!-- Search -->
                        <div class="mb-6">
                            <div class="relative">
                                <input type="text" 
                                       id="search-input"
                                       placeholder="Tìm kiếm game..." 
                                       class="w-full px-4 py-2 pl-10 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-game-accent/20 focus:border-game-accent text-sm">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <h3 class="font-heading text-lg font-bold text-slate-800 mb-4">Bộ lọc</h3>
                        
                        <!-- Categories -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-slate-700 mb-3">Danh mục</h4>
                            <div class="space-y-2" id="category-filters">
                                <div class="animate-pulse space-y-2">
                                    <div class="h-6 bg-slate-200 rounded w-3/4"></div>
                                    <div class="h-6 bg-slate-200 rounded w-2/3"></div>
                                    <div class="h-6 bg-slate-200 rounded w-1/2"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-slate-700 mb-3">Khoảng giá</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="price" value="" class="w-4 h-4 text-game-accent border-slate-300 focus:ring-game-accent" checked>
                                    <span class="ml-2 text-slate-600 group-hover:text-game-accent transition-colors">Tất cả</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="price" value="0-50000" class="w-4 h-4 text-game-accent border-slate-300 focus:ring-game-accent">
                                    <span class="ml-2 text-slate-600 group-hover:text-game-accent transition-colors">Dưới 50.000đ</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="price" value="50000-100000" class="w-4 h-4 text-game-accent border-slate-300 focus:ring-game-accent">
                                    <span class="ml-2 text-slate-600 group-hover:text-game-accent transition-colors">50.000đ - 100.000đ</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="price" value="100000-500000" class="w-4 h-4 text-game-accent border-slate-300 focus:ring-game-accent">
                                    <span class="ml-2 text-slate-600 group-hover:text-game-accent transition-colors">100.000đ - 500.000đ</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="price" value="500000+" class="w-4 h-4 text-game-accent border-slate-300 focus:ring-game-accent">
                                    <span class="ml-2 text-slate-600 group-hover:text-game-accent transition-colors">Trên 500.000đ</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Sort -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-slate-700 mb-3">Sắp xếp</h4>
                            <select id="sort-select" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-game-accent/20 focus:border-game-accent">
                                <option value="id-desc">Mới nhất</option>
                                <option value="id-asc">Cũ nhất</option>
                                <option value="title-asc">Tên A-Z</option>
                                <option value="title-desc">Tên Z-A</option>
                                <option value="view_count-desc">Phổ biến nhất</option>
                                <option value="average_rating-desc">Đánh giá cao</option>
                            </select>
                        </div>
                        
                        <!-- Reset Button -->
                        <button id="reset-filters" class="w-full px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors">
                            Xóa bộ lọc
                        </button>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">
                    <!-- View Toggle -->
                    <div class="flex justify-end mb-6">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 text-sm">Hiển thị:</span>
                            <button class="view-btn active p-2 rounded-lg border border-slate-200 hover:border-game-accent transition-colors" data-view="grid">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </button>
                            <button class="view-btn p-2 rounded-lg border border-slate-200 hover:border-game-accent transition-colors" data-view="list">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Active Filters -->
                    <div id="active-filters" class="hidden flex flex-wrap gap-2 mb-6">
                        <!-- Active filter tags will be added here -->
                    </div>

                    <!-- Products Container -->
                    <div id="products-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <!-- Loading State -->
                        <div class="col-span-full flex justify-center py-16">
                            <div class="flex flex-col items-center">
                                <div class="animate-spin w-12 h-12 border-4 border-game-accent border-t-transparent rounded-full mb-4"></div>
                                <p class="text-slate-500">Đang tải sản phẩm...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination" class="flex justify-center items-center gap-2 mt-8">
                        <!-- Pagination will be generated here -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .view-btn.active {
        background-color: rgb(99 102 241 / 0.1);
        border-color: rgb(99 102 241);
        color: rgb(99 102 241);
    }
</style>
@endpush

@push('scripts')
<script>
    // API Configuration
    const BASE_URL = '{{ url("/") }}';
    const API_BASE_URL = BASE_URL + '/api/products';

    // State
    let currentPage = 1;
    let currentCategory = '';
    let currentSearch = '';
    let currentSort = 'id-desc';
    let currentView = 'grid';
    let perPage = 12;

    // Utility functions
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function extractPrices(priceStr) {
        if (!priceStr) return { original: null, current: null };
        const priceRegex = /[\d.,]+\s*[₫đ]/gi;
        const prices = priceStr.match(priceRegex);
        if (!prices || prices.length === 0) {
            return { original: null, current: priceStr.replace(/Giá gốc là:|Giá hiện tại là:/gi, '').trim() };
        }
        if (prices.length === 1) {
            return { original: null, current: prices[0].trim() };
        }
        return { original: prices[0].trim(), current: prices[prices.length - 1].trim() };
    }

    function calculateDiscount(priceStr) {
        if (!priceStr) return 0;
        const priceRegex = /[\d.,]+/g;
        const prices = priceStr.match(priceRegex);
        if (!prices || prices.length < 2) return 0;
        const original = parseFloat(prices[0].replace(/\./g, '').replace(',', '.'));
        const current = parseFloat(prices[prices.length - 1].replace(/\./g, '').replace(',', '.'));
        if (original <= 0 || current >= original) return 0;
        return Math.round((1 - current / original) * 100);
    }

    // Load categories for filter
    async function loadCategories() {
        try {
            const response = await fetch(`${API_BASE_URL}/categories`);
            const data = await response.json();

            if (data.success && data.data) {
                const container = document.getElementById('category-filters');
                container.innerHTML = `
                    <label class="flex items-center cursor-pointer group">
                        <input type="radio" name="category" value="" class="w-4 h-4 text-game-accent border-slate-300 focus:ring-game-accent" ${!currentCategory ? 'checked' : ''}>
                        <span class="ml-2 text-slate-600 group-hover:text-game-accent transition-colors">Tất cả</span>
                    </label>
                    ${data.data.map(cat => `
                        <label class="flex items-center cursor-pointer group">
                            <input type="radio" name="category" value="${escapeHtml(cat.category)}" class="w-4 h-4 text-game-accent border-slate-300 focus:ring-game-accent" ${currentCategory === cat.category ? 'checked' : ''}>
                            <span class="ml-2 text-slate-600 group-hover:text-game-accent transition-colors">${escapeHtml(cat.category)} (${cat.count})</span>
                        </label>
                    `).join('')}
                `;

                // Add event listeners
                container.querySelectorAll('input[name="category"]').forEach(input => {
                    input.addEventListener('change', () => {
                        currentCategory = input.value;
                        currentPage = 1;
                        loadProducts();
                        updateActiveFilters();
                    });
                });
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    // Load products
    async function loadProducts() {
        const container = document.getElementById('products-container');
        container.innerHTML = `
            <div class="col-span-full flex justify-center py-16">
                <div class="flex flex-col items-center">
                    <div class="animate-spin w-12 h-12 border-4 border-game-accent border-t-transparent rounded-full mb-4"></div>
                    <p class="text-slate-500">Đang tải sản phẩm...</p>
                </div>
            </div>
        `;

        try {
            const [sortBy, sortOrder] = currentSort.split('-');
            let url = `${API_BASE_URL}?page=${currentPage}&per_page=${perPage}&sort_by=${sortBy}&sort_order=${sortOrder}`;
            
            if (currentCategory) {
                url += `&category=${encodeURIComponent(currentCategory)}`;
            }
            if (currentSearch) {
                url += `&search=${encodeURIComponent(currentSearch)}`;
            }

            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                renderProducts(data.data, data.pagination);
            } else {
                showNoResults();
            }
        } catch (error) {
            console.error('Error loading products:', error);
            showError();
        }
    }

    // Render products
    function renderProducts(products, pagination) {
        const container = document.getElementById('products-container');
        
        if (!products || products.length === 0) {
            showNoResults();
            return;
        }


        if (currentView === 'grid') {
            container.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4';
            container.innerHTML = products.map(product => renderGridCard(product)).join('');
        } else {
            container.className = 'space-y-4';
            container.innerHTML = products.map(product => renderListCard(product)).join('');
        }

        renderPagination(pagination);
    }

    // Grid card template
    function renderGridCard(product) {
        const prices = extractPrices(product.price);
        const discount = calculateDiscount(product.price);

        return `
            <a href="${BASE_URL}/game/${product.id}" class="group bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-game-accent hover:shadow-lg transition-all card-hover flex">
                <div class="flex-shrink-0 w-28 h-28 overflow-hidden rounded-lg m-3 relative">
                    <img src="${product.image || 'https://via.placeholder.com/150x150?text=Game'}" 
                         alt="${escapeHtml(product.title)}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                         loading="lazy">
                    ${discount > 0 ? `<div class="absolute top-1 right-1 px-1.5 py-0.5 bg-game-green text-white text-[10px] font-bold rounded">-${discount}%</div>` : ''}
                </div>
                <div class="flex-1 py-3 pr-3 flex flex-col justify-between min-w-0">
                    <div>
                        <h3 class="font-heading font-semibold text-slate-800 text-sm leading-tight line-clamp-2 group-hover:text-game-accent transition-colors">
                            ${escapeHtml(product.title)}
                        </h3>
                        <div class="flex items-center gap-2 mt-1.5">
                            ${product.category ? `<span class="px-2 py-0.5 bg-game-accent/10 text-game-accent text-xs font-medium rounded">${escapeHtml(product.category)}</span>` : ''}
                            <span class="flex items-center text-slate-400 text-xs">
                                <svg class="w-3 h-3 mr-0.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                ${product.average_rating ? parseFloat(product.average_rating).toFixed(1) : 'N/A'}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-end justify-between mt-2">
                        <div class="flex flex-col">
                            ${prices.original && prices.original !== prices.current ? `<span class="text-slate-400 line-through text-xs">${prices.original}</span>` : ''}
                            <span class="text-game-accent font-bold text-lg">${prices.current || 'Liên hệ'}</span>
                        </div>
                        <div class="w-8 h-8 bg-game-accent rounded-lg flex items-center justify-center group-hover:bg-game-accent-hover transition-colors">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
        `;
    }

    // List card template
    function renderListCard(product) {
        const prices = extractPrices(product.price);
        const discount = calculateDiscount(product.price);

        return `
            <a href="${BASE_URL}/game/${product.id}" class="group bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-game-accent hover:shadow-lg transition-all flex">
                <div class="flex-shrink-0 w-32 h-32 md:w-40 md:h-40 overflow-hidden">
                    <img src="${product.image || 'https://via.placeholder.com/200x200?text=Game'}" 
                         alt="${escapeHtml(product.title)}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         loading="lazy">
                </div>
                <div class="flex-1 p-4 flex flex-col justify-between min-w-0">
                    <div>
                        <div class="flex items-start justify-between gap-4">
                            <h3 class="font-heading font-semibold text-slate-800 text-lg leading-tight group-hover:text-game-accent transition-colors">
                                ${escapeHtml(product.title)}
                            </h3>
                            ${discount > 0 ? `<span class="flex-shrink-0 px-2 py-1 bg-game-green text-white text-xs font-bold rounded">-${discount}%</span>` : ''}
                        </div>
                        <div class="flex items-center gap-3 mt-2">
                            ${product.category ? `<span class="px-2 py-0.5 bg-game-accent/10 text-game-accent text-xs font-medium rounded">${escapeHtml(product.category)}</span>` : ''}
                            <span class="flex items-center text-slate-400 text-sm">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                ${product.average_rating ? parseFloat(product.average_rating).toFixed(1) : 'N/A'}
                            </span>
                            <span class="text-slate-400 text-sm">
                                ${(product.view_count || 0).toLocaleString()} lượt xem
                            </span>
                        </div>
                        <p class="text-slate-500 text-sm mt-2 line-clamp-2 hidden md:block">
                            ${product.short_description ? escapeHtml(product.short_description.substring(0, 150)) + '...' : ''}
                        </p>
                    </div>
                    <div class="flex items-end justify-between mt-3">
                        <div class="flex flex-col">
                            ${prices.original && prices.original !== prices.current ? `<span class="text-slate-400 line-through text-sm">${prices.original}</span>` : ''}
                            <span class="text-game-accent font-bold text-xl">${prices.current || 'Liên hệ'}</span>
                        </div>
                        <button class="px-4 py-2 bg-game-accent text-white font-semibold rounded-lg hover:bg-game-accent-hover transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Mua ngay
                        </button>
                    </div>
                </div>
            </a>
        `;
    }

    // Render pagination
    function renderPagination(pagination) {
        const container = document.getElementById('pagination');
        
        if (pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';

        // Previous button
        html += `
            <button class="px-3 py-2 rounded-lg border ${currentPage === 1 ? 'border-slate-200 text-slate-400 cursor-not-allowed' : 'border-slate-200 text-slate-600 hover:border-game-accent hover:text-game-accent'}" 
                    ${currentPage === 1 ? 'disabled' : ''} 
                    onclick="goToPage(${currentPage - 1})">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        `;

        // Page numbers
        const range = 2;
        let start = Math.max(1, currentPage - range);
        let end = Math.min(pagination.last_page, currentPage + range);

        if (start > 1) {
            html += `<button class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:border-game-accent hover:text-game-accent" onclick="goToPage(1)">1</button>`;
            if (start > 2) {
                html += `<span class="px-2 text-slate-400">...</span>`;
            }
        }

        for (let i = start; i <= end; i++) {
            html += `
                <button class="px-3 py-2 rounded-lg border ${i === currentPage ? 'border-game-accent bg-game-accent text-white' : 'border-slate-200 text-slate-600 hover:border-game-accent hover:text-game-accent'}" 
                        onclick="goToPage(${i})">${i}</button>
            `;
        }

        if (end < pagination.last_page) {
            if (end < pagination.last_page - 1) {
                html += `<span class="px-2 text-slate-400">...</span>`;
            }
            html += `<button class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:border-game-accent hover:text-game-accent" onclick="goToPage(${pagination.last_page})">${pagination.last_page}</button>`;
        }

        // Next button
        html += `
            <button class="px-3 py-2 rounded-lg border ${currentPage === pagination.last_page ? 'border-slate-200 text-slate-400 cursor-not-allowed' : 'border-slate-200 text-slate-600 hover:border-game-accent hover:text-game-accent'}" 
                    ${currentPage === pagination.last_page ? 'disabled' : ''} 
                    onclick="goToPage(${currentPage + 1})">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        `;

        container.innerHTML = html;
    }

    function goToPage(page) {
        currentPage = page;
        loadProducts();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Show no results
    function showNoResults() {
        const container = document.getElementById('products-container');
        container.innerHTML = `
            <div class="col-span-full text-center py-16">
                <div class="text-6xl mb-4">🎮</div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Không tìm thấy sản phẩm</h2>
                <p class="text-slate-600 mb-6">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                <button onclick="resetFilters()" class="px-6 py-3 bg-game-accent text-white font-semibold rounded-xl hover:bg-game-accent-hover transition-colors">
                    Xóa bộ lọc
                </button>
            </div>
        `;
        document.getElementById('pagination').innerHTML = '';
    }

    // Show error
    function showError() {
        const container = document.getElementById('products-container');
        container.innerHTML = `
            <div class="col-span-full text-center py-16">
                <div class="text-6xl mb-4">😔</div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Đã xảy ra lỗi</h2>
                <p class="text-slate-600 mb-6">Không thể tải danh sách sản phẩm</p>
                <button onclick="loadProducts()" class="px-6 py-3 bg-game-accent text-white font-semibold rounded-xl hover:bg-game-accent-hover transition-colors">
                    Thử lại
                </button>
            </div>
        `;
    }

    // Update active filters display
    function updateActiveFilters() {
        const container = document.getElementById('active-filters');
        let tags = [];

        if (currentCategory) {
            tags.push(`
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-game-accent/10 text-game-accent text-sm rounded-full">
                    ${escapeHtml(currentCategory)}
                    <button onclick="clearCategory()" class="hover:text-game-accent-hover">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            `);
        }

        if (currentSearch) {
            tags.push(`
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-game-accent/10 text-game-accent text-sm rounded-full">
                    Tìm: "${escapeHtml(currentSearch)}"
                    <button onclick="clearSearch()" class="hover:text-game-accent-hover">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            `);
        }

        if (tags.length > 0) {
            container.innerHTML = tags.join('');
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function clearCategory() {
        currentCategory = '';
        document.querySelector('input[name="category"][value=""]').checked = true;
        currentPage = 1;
        loadProducts();
        updateActiveFilters();
    }

    function clearSearch() {
        currentSearch = '';
        document.getElementById('search-input').value = '';
        currentPage = 1;
        loadProducts();
        updateActiveFilters();
    }

    function resetFilters() {
        currentCategory = '';
        currentSearch = '';
        currentSort = 'id-desc';
        currentPage = 1;
        
        document.getElementById('search-input').value = '';
        document.getElementById('sort-select').value = 'id-desc';
        document.querySelector('input[name="category"][value=""]').checked = true;
        document.querySelector('input[name="price"][value=""]').checked = true;
        
        loadProducts();
        updateActiveFilters();
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', () => {
        // Check URL params
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('category')) {
            currentCategory = urlParams.get('category');
        }
        if (urlParams.has('search')) {
            currentSearch = urlParams.get('search');
            document.getElementById('search-input').value = currentSearch;
        }

        loadCategories();
        loadProducts();
        updateActiveFilters();
    });

    // Search (Enter key)
    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            currentSearch = e.target.value.trim();
            currentPage = 1;
            loadProducts();
            updateActiveFilters();
        }
    });

    // Sort
    document.getElementById('sort-select').addEventListener('change', (e) => {
        currentSort = e.target.value;
        currentPage = 1;
        loadProducts();
    });

    // View toggle
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentView = btn.dataset.view;
            loadProducts();
        });
    });

    // Reset filters button
    document.getElementById('reset-filters').addEventListener('click', resetFilters);
</script>
@endpush
