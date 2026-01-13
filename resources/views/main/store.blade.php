@extends('layouts.main')

@section('title', 'Khám phá')

@section('content')
    <!-- Main Content -->
    <section class="pt-36 pb-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Filters -->
                <aside class="lg:w-64 flex-shrink-0">
                    <div class="bg-white rounded-2xl border border-slate-200 p-4 sticky top-24">
                        <!-- Search -->
                        <div class="mb-4">
                            <div class="relative">
                                <input type="text" 
                                       id="search-input"
                                       placeholder="Tìm kiếm game..." 
                                       class="w-full px-3 py-1.5 pl-9 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-game-accent/20 focus:border-game-accent text-xs">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <!-- Gợi ý tìm kiếm -->
                                <div id="search-suggestions" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg hidden z-30 max-h-64 overflow-y-auto text-xs">
                                    <!-- Suggestions will be rendered here -->
                                </div>
                            </div>
                        </div>
                        
                        <h3 class="font-heading text-base font-bold text-slate-800 mb-3">Bộ lọc</h3>
                        
                        <!-- Categories -->
                        <div class="mb-4">
                            <h4 class="font-semibold text-slate-700 mb-2 text-sm">Danh mục</h4>
                            <div class="space-y-1 text-xs" id="category-filters">
                                <div class="animate-pulse space-y-2">
                                    <div class="h-6 bg-slate-200 rounded w-3/4"></div>
                                    <div class="h-6 bg-slate-200 rounded w-2/3"></div>
                                    <div class="h-6 bg-slate-200 rounded w-1/2"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-4">
                            <h4 class="font-semibold text-slate-700 mb-2 text-sm">Khoảng giá</h4>
                            <div class="space-y-1 text-xs">
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
                        <div class="mb-4">
                            <h4 class="font-semibold text-slate-700 mb-2 text-sm">Sắp xếp</h4>
                            <select id="sort-select" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 text-xs focus:outline-none focus:ring-2 focus:ring-game-accent/20 focus:border-game-accent">
                                <option value="id-desc">Mới nhất</option>
                                <option value="id-asc">Cũ nhất</option>
                                <option value="title-asc">Tên A-Z</option>
                                <option value="title-desc">Tên Z-A</option>
                                <option value="view_count-desc">Phổ biến nhất</option>
                            </select>
                        </div>
                        
                        <!-- Reset Button -->
                        <button id="reset-filters" class="w-full px-3 py-1.5 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-xs">
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
    const API_BASE_URL = '{{ url("/api/products") }}';

    // State
    let currentPage = 1;
    let currentCategory = '';
    let currentSearch = '';
    let currentPrice = '';
    let currentType = '';
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

    // Gợi ý tìm kiếm (search suggestions)
    let searchSuggestTimeout = null;

    function hideSearchSuggestions() {
        const box = document.getElementById('search-suggestions');
        if (box) {
            box.classList.add('hidden');
            box.innerHTML = '';
        }
    }

    async function loadSearchSuggestions(query) {
        const box = document.getElementById('search-suggestions');
        if (!box) return;

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

            box.innerHTML = data.data.map(product => `
                <button type="button"
                        class="w-full text-left px-3 py-1.5 flex items-center gap-2 hover:bg-slate-50 transition-colors"
                        data-id="${product.id}"
                        data-title="${escapeHtml(product.title)}">
                    <span class="flex-1 truncate">${escapeHtml(product.title)}</span>
                    ${product.category ? `<span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded text-[10px]">${escapeHtml(product.category)}</span>` : ''}
                </button>
            `).join('');

            box.classList.remove('hidden');
        } catch (e) {
            console.error('Error loading search suggestions:', e);
            hideSearchSuggestions();
        }
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
            if (currentPrice) {
                url += `&price=${encodeURIComponent(currentPrice)}`;
            }
            if (currentType) {
                url += `&type=${encodeURIComponent(currentType)}`;
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
                        </div>
                    </div>
                    <div class="flex items-end justify-between mt-2">
                        <div class="flex flex-col">
                            ${prices.original && prices.original !== prices.current ? `<span class="text-slate-400 line-through text-xs">${prices.original}</span>` : ''}
                            <span class="text-game-accent font-bold text-lg">${prices.current || 'Liên hệ'}</span>
                        </div>
                        <button type="button" 
                                class="w-8 h-8 bg-game-accent rounded-lg flex items-center justify-center group-hover:bg-game-accent-hover transition-colors cursor-pointer add-to-cart-btn" 
                                data-product-id="${product.id}"
                                data-product-title="${escapeHtml(product.title)}"
                                data-product-image="${product.image || ''}"
                                data-product-price="${escapeHtml(product.price || '')}"
                                onclick="return handleAddToCart(this, event);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>
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
                        <button type="button" 
                                class="px-4 py-2 bg-game-accent text-white font-semibold rounded-lg hover:bg-game-accent-hover transition-colors flex items-center gap-2 add-to-cart-btn"
                                data-product-id="${product.id}"
                                data-product-title="${escapeHtml(product.title)}"
                                data-product-image="${product.image || ''}"
                                data-product-price="${escapeHtml(product.price || '')}"
                                onclick="return handleAddToCart(this, event);">
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

        if (currentPrice) {
            let priceLabel = '';
            switch (currentPrice) {
                case '0-50000':
                    priceLabel = 'Dưới 50.000đ';
                    break;
                case '50000-100000':
                    priceLabel = '50.000đ - 100.000đ';
                    break;
                case '100000-500000':
                    priceLabel = '100.000đ - 500.000đ';
                    break;
                case '500000+':
                    priceLabel = 'Trên 500.000đ';
                    break;
            }

            if (priceLabel) {
                tags.push(`
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-game-accent/10 text-game-accent text-sm rounded-full">
                        Giá: ${priceLabel}
                        <button onclick="clearPrice()" class="hover:text-game-accent-hover">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                `);
            }
        }

        if (currentType) {
            const typeLabel = currentType === 'offline' ? 'Game Offline' : 'Game Online';
            tags.push(`
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-game-accent/10 text-game-accent text-sm rounded-full">
                    ${typeLabel}
                    <button onclick="clearType()" class="hover:text-game-accent-hover">
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

    function clearPrice() {
        currentPrice = '';
        const allPriceInput = document.querySelector('input[name="price"][value=""]');
        if (allPriceInput) {
            allPriceInput.checked = true;
        }
        currentPage = 1;
        loadProducts();
        updateActiveFilters();
    }

    function clearType() {
        currentType = '';
        // Redirect to main store page if on offline/online page
        if (window.location.pathname.includes('/store/offline') || window.location.pathname.includes('/store/online')) {
            window.location.href = BASE_URL + '/store';
        } else {
            currentPage = 1;
            loadProducts();
            updateActiveFilters();
        }
    }

    function resetFilters() {
        currentCategory = '';
        currentSearch = '';
        currentPrice = '';
        currentType = '';
        currentSort = 'id-desc';
        currentPage = 1;
        
        document.getElementById('search-input').value = '';
        document.getElementById('sort-select').value = 'id-desc';
        document.querySelector('input[name="category"][value=""]').checked = true;
        document.querySelector('input[name="price"][value=""]').checked = true;
        
        // Redirect to main store page if on offline/online page
        if (window.location.pathname.includes('/store/offline') || window.location.pathname.includes('/store/online')) {
            window.location.href = BASE_URL + '/store';
        } else {
            loadProducts();
            updateActiveFilters();
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', () => {
        // Check URL path for type (offline/online)
        const path = window.location.pathname;
        if (path.includes('/store/offline')) {
            currentType = 'offline';
        } else if (path.includes('/store/online')) {
            currentType = 'online';
        }

        // Check URL params
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('category')) {
            currentCategory = urlParams.get('category');
        }
        if (urlParams.has('search')) {
            currentSearch = urlParams.get('search');
            document.getElementById('search-input').value = currentSearch;
        }
        if (urlParams.has('type')) {
            currentType = urlParams.get('type');
        }

        loadCategories();
        loadProducts();
        updateActiveFilters();

        const searchInput = document.getElementById('search-input');
        const suggestionsBox = document.getElementById('search-suggestions');

        // Gợi ý khi gõ
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                clearTimeout(searchSuggestTimeout);
                searchSuggestTimeout = setTimeout(() => {
                    loadSearchSuggestions(value);
                }, 250);
            });

            // Search (Enter key)
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    currentSearch = e.target.value.trim();
                    currentPage = 1;
                    hideSearchSuggestions();
                    loadProducts();
                    updateActiveFilters();
                }
            });
        }

        // Click vào gợi ý
        if (suggestionsBox) {
            suggestionsBox.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-id]');
                if (!btn) return;

                const title = btn.getAttribute('data-title') || '';
                const id = btn.getAttribute('data-id');

                if (id) {
                    // Điều hướng luôn tới trang chi tiết game
                    window.location.href = `${BASE_URL}/game/${id}`;
                } else {
                    // Fallback: dùng text để search
                    if (searchInput) searchInput.value = title;
                    currentSearch = title;
                    currentPage = 1;
                    hideSearchSuggestions();
                    loadProducts();
                    updateActiveFilters();
                }
            });
        }

        // Ẩn gợi ý khi click ra ngoài
        document.addEventListener('click', (e) => {
            const wrapper = searchInput?.parentElement;
            if (wrapper && !wrapper.contains(e.target)) {
                hideSearchSuggestions();
            }
        });

        // Price range filters
        document.querySelectorAll('input[name="price"]').forEach(input => {
            input.addEventListener('change', () => {
                currentPrice = input.value;
                currentPage = 1;
                loadProducts();
                updateActiveFilters();
            });
        });
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

    // Handler function to get data from button and call addToCart
    function handleAddToCart(button, event) {
        // Ngăn chặn mọi hành vi mặc định và propagation
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const productId = parseInt(button.getAttribute('data-product-id'));
        const productTitle = button.getAttribute('data-product-title') || '';
        const productImage = button.getAttribute('data-product-image') || '';
        const productPrice = button.getAttribute('data-product-price') || '';
        
        addToCart(productId, productTitle, productImage, productPrice);
        
        return false;
    }

    // Function to add product to cart
    function addToCart(productId, productTitle, productImage, productPrice) {
        // Lấy giỏ hàng hiện tại hoặc tạo mới
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        
        // Kiểm tra xem sản phẩm đã có trong giỏ chưa
        const existingIndex = cart.findIndex(item => item.id === productId);
        
        if (existingIndex >= 0) {
            // Nếu đã có thì tăng số lượng
            cart[existingIndex].quantity += 1;
        } else {
            // Nếu chưa có thì thêm mới
            cart.push({
                id: productId,
                title: productTitle,
                image: productImage,
                price: productPrice,
                quantity: 1
            });
        }
        
        // Lưu vào localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Cập nhật số lượng trên icon giỏ hàng
        updateCartCount();
        
        // Hiển thị thông báo
        showCartNotification(productTitle);
    }

    // Function to update cart count in header
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            cartCount.textContent = total;
            if (total > 0) {
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }
        }
    }

    // Function to show notification when item added to cart
    function showCartNotification(productTitle) {
        // Tạo notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-24 right-4 bg-white border border-game-border rounded-xl shadow-lg p-4 z-50 flex items-center gap-3 animate-slide-in';
        notification.innerHTML = `
            <div class="w-10 h-10 bg-game-green/10 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-game-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800">Đã thêm vào giỏ hàng</p>
                <p class="text-sm text-slate-600">${escapeHtml(productTitle)}</p>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Update cart count on page load
    updateCartCount();
</script>
@endpush

@push('styles')
<style>
    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
        transition: all 0.3s ease-out;
    }
</style>
@endpush
