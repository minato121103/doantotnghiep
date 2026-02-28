@extends('layouts.main')

@section('title', 'Ưu đãi')

@section('content')
    <section class="pt-36 pb-8">
        <div class="container mx-auto px-4">
            <!-- Hero -->
            <div class="mb-8 bg-gradient-to-r from-rose-50 to-amber-50 rounded-2xl p-6 border border-rose-200">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-rose-500 to-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="font-display text-2xl md:text-3xl font-bold text-slate-800">Ưu đãi</h1>
                        <p class="text-slate-600 mt-1">Danh sách game đang được giảm giá</p>
                    </div>
                    <a href="{{ url('/store') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-game-accent hover:border-game-accent transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Xem tất cả game
                    </a>
                </div>
            </div>

            <!-- Products grid -->
            <div id="promotions-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div class="col-span-full flex justify-center py-16">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin w-12 h-12 border-4 border-game-accent border-t-transparent rounded-full mb-4"></div>
                        <p class="text-slate-500">Đang tải danh sách ưu đãi...</p>
                    </div>
                </div>
            </div>

            <!-- Empty state (hidden by default) -->
            <div id="promotions-empty" class="hidden text-center py-16">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <h3 class="font-heading text-xl font-bold text-slate-700 mb-2">Chưa có ưu đãi</h3>
                <p class="text-slate-500 mb-6">Hiện không có game nào đang được giảm giá. Hãy quay lại sau!</p>
                <a href="{{ url('/store') }}" class="inline-flex items-center px-6 py-3 bg-game-accent text-white font-semibold rounded-xl hover:bg-game-accent-hover transition-colors">
                    Khám phá cửa hàng
                </a>
            </div>

            <!-- Pagination -->
            <div id="promotions-pagination" class="mt-8 flex justify-center gap-2"></div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("/") }}';
    const API_BASE_URL = '{{ url("/api/products") }}';
    let currentPage = 1;

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
        if (!prices || prices.length === 0) return { original: null, current: priceStr.trim() };
        if (prices.length === 1) return { original: null, current: prices[0].trim() };
        return { original: prices[0].trim(), current: prices[prices.length - 1].trim() };
    }

    function formatPriceVN(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + 'đ';
    }

    function getPromoPriceHtml(product, fontSize = 'text-lg') {
        if (product.sale_price) {
            const prices = extractPrices(product.price);
            const originalPrice = prices.current || product.price;
            return `<div class="flex flex-col">
                <span class="text-slate-400 line-through text-xs">${escapeHtml(originalPrice)}</span>
                <span class="text-game-accent font-bold ${fontSize}">${formatPriceVN(product.sale_price)}</span>
            </div>`;
        }
        const prices = extractPrices(product.price);
        if (prices.original && prices.original !== prices.current) {
            return `<div class="flex flex-col">
                <span class="text-slate-400 line-through text-xs">${escapeHtml(prices.original)}</span>
                <span class="text-game-accent font-bold ${fontSize}">${escapeHtml(prices.current || 'Liên hệ')}</span>
            </div>`;
        }
        return `<span class="text-game-accent font-bold ${fontSize}">${escapeHtml(prices.current || 'Liên hệ')}</span>`;
    }

    function getPromoDiscount(product) {
        if (product.discount_percent) return product.discount_percent;
        if (!product.price) return 0;
        const priceRegex = /[\d.,]+/g;
        const prices = product.price.match(priceRegex);
        if (!prices || prices.length < 2) return 0;
        const original = parseFloat(prices[0].replace(/\./g, '').replace(',', '.'));
        const current = parseFloat(prices[prices.length - 1].replace(/\./g, '').replace(',', '.'));
        if (original <= 0 || current >= original) return 0;
        return Math.round((1 - current / original) * 100);
    }

    function isOutOfStock(product) {
        return (product.available_accounts || 0) === 0;
    }

    function outOfStockOverlay() {
        return `<div class="absolute inset-0 flex items-center justify-center bg-white/75 z-10"><span class="font-bold text-slate-700 text-sm">Hết hàng</span></div>`;
    }

    function renderCard(product) {
        const discount = getPromoDiscount(product);
        const outOfStock = isOutOfStock(product);
        return `
            <a href="${BASE_URL}/game/${product.id}" class="group bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-game-accent hover:shadow-lg transition-all card-hover flex">
                <div class="flex-shrink-0 w-28 h-28 overflow-hidden rounded-lg m-3 relative">
                    <img src="${product.image || 'https://via.placeholder.com/150x150?text=Game'}" 
                         alt="${escapeHtml(product.title)}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                         loading="lazy">
                    ${outOfStock ? outOfStockOverlay() : ''}
                    ${!outOfStock && discount > 0 ? `<div class="absolute top-1 right-1 px-1.5 py-0.5 bg-rose-500 text-white text-[10px] font-bold rounded">-${discount}%</div>` : ''}
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
                        ${getPromoPriceHtml(product)}
                        ${outOfStock ? `<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-200 text-slate-400 cursor-not-allowed" title="Hết hàng"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></span>` : `<button type="button" 
                                class="w-8 h-8 bg-game-accent rounded-lg flex items-center justify-center group-hover:bg-game-accent-hover transition-colors cursor-pointer add-to-cart-btn" 
                                data-product-id="${product.id}"
                                data-product-title="${escapeHtml(product.title)}"
                                data-product-image="${product.image || ''}"
                                data-product-price="${escapeHtml(product.price || '')}"
                                onclick="return handleAddToCart(this, event);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>`}
                    </div>
                </div>
            </a>
        `;
    }

    function handleAddToCart(button, event) {
        event.preventDefault();
        event.stopPropagation();
        const productId = parseInt(button.getAttribute('data-product-id'));
        const productTitle = button.getAttribute('data-product-title') || '';
        const productImage = button.getAttribute('data-product-image') || '';
        const productPrice = button.getAttribute('data-product-price') || '';
        if (typeof addToCart === 'function') {
            addToCart(productId, productTitle, productImage, productPrice);
        }
        return false;
    }

    function addToCart(productId, productTitle, productImage, productPrice) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const existing = cart.find(item => item.id === productId);
        if (existing) {
            existing.quantity = (existing.quantity || 1) + 1;
        } else {
            cart.push({
                id: productId,
                title: productTitle,
                image: productImage,
                price: productPrice,
                quantity: 1
            });
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            cartCount.textContent = total;
            cartCount.classList.remove('hidden');
        }
    }

    function renderPagination(pagination) {
        const container = document.getElementById('promotions-pagination');
        if (!pagination || pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }
        let html = '';
        for (let i = 1; i <= Math.min(pagination.last_page, 10); i++) {
            const active = i === pagination.current_page ? 'bg-game-accent text-white' : 'bg-white text-slate-700 hover:bg-slate-100';
            html += `<button onclick="loadPromotions(${i})" class="px-3 py-2 rounded-lg border border-slate-200 text-sm ${active}">${i}</button>`;
        }
        container.innerHTML = html;
    }

    async function loadPromotions(page = 1) {
        currentPage = page;
        const container = document.getElementById('promotions-container');
        const emptyEl = document.getElementById('promotions-empty');
        container.innerHTML = '<div class="col-span-full flex justify-center py-16"><div class="animate-spin w-12 h-12 border-4 border-game-accent border-t-transparent rounded-full"></div></div>';
        emptyEl.classList.add('hidden');

        try {
            const res = await fetch(`${API_BASE_URL}?on_promotion=1&per_page=24&page=${page}&sort_by=id&sort_order=desc`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!data.success || !data.data) {
                container.innerHTML = '';
                emptyEl.classList.remove('hidden');
                document.getElementById('promotions-pagination').innerHTML = '';
                return;
            }

            const products = data.data;
            const pagination = data.pagination || {};

            if (products.length === 0) {
                container.innerHTML = '';
                emptyEl.classList.remove('hidden');
                document.getElementById('promotions-pagination').innerHTML = '';
                return;
            }

            container.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4';
            container.innerHTML = products.map(p => renderCard(p)).join('');
            renderPagination(pagination);
        } catch (e) {
            console.error(e);
            container.innerHTML = '';
            emptyEl.classList.remove('hidden');
            document.getElementById('promotions-pagination').innerHTML = '';
        }
    }

    document.addEventListener('DOMContentLoaded', () => loadPromotions());
</script>
@endpush
