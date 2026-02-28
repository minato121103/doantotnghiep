@extends('layouts.main')

@section('title', 'Dành cho bạn')

@section('content')
    <section class="pt-36 pb-8">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-8 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-6 border border-purple-200">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-game-purple to-game-accent rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="font-display text-2xl md:text-3xl font-bold text-slate-800">Dành cho bạn</h1>
                        <p class="text-slate-600 mt-1">Game được đề xuất dựa trên sở thích của bạn</p>
                    </div>
                    <a href="{{ url('/store') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-game-accent hover:border-game-accent transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Xem tất cả game
                    </a>
                </div>
            </div>

            <!-- All Recommendations Section -->
            <div id="recommendations-section" class="mb-8">
                <div id="recommendations-games" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <!-- Loading placeholder -->
                    <div class="col-span-full flex justify-center py-16">
                        <div class="flex flex-col items-center">
                            <div class="animate-spin w-12 h-12 border-4 border-game-accent border-t-transparent rounded-full mb-4"></div>
                            <p class="text-slate-500">Đang tải đề xuất cho bạn...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Required Modal -->
    <div id="login-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
            <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h3 class="text-xl font-bold text-slate-700 mb-2">Đăng nhập để xem đề xuất</h3>
            <p class="text-slate-500 mb-6">Vui lòng đăng nhập để xem các game được đề xuất dành riêng cho bạn</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ url('/store') }}" class="px-6 py-3 border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                    Xem tất cả game
                </a>
                <a href="{{ url('/login') }}" class="px-6 py-3 bg-game-accent text-white font-semibold rounded-xl hover:bg-game-accent-hover transition-colors">
                    Đăng nhập
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("/") }}';
    const RECOMMENDATIONS_API = '{{ url("/api/recommendations") }}';

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

    function isOutOfStock(product) {
        return (product.available_accounts || 0) === 0;
    }

    function outOfStockOverlay() {
        return `<div class="absolute inset-0 flex items-center justify-center bg-white/75 z-10"><span class="font-bold text-slate-700 text-sm">Hết hàng</span></div>`;
    }

    // Render a game card
    function renderGameCard(product) {
        const prices = extractPrices(product.price);
        const discount = calculateDiscount(product.price);
        const outOfStock = isOutOfStock(product);

        return `
            <a href="${BASE_URL}/game/${product.id}" class="group bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-game-accent hover:shadow-lg transition-all flex">
                <div class="flex-shrink-0 w-28 h-28 overflow-hidden rounded-lg m-3 relative">
                    <img src="${product.image || 'https://via.placeholder.com/150x150?text=Game'}" 
                         alt="${escapeHtml(product.title)}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                         loading="lazy">
                    ${outOfStock ? outOfStockOverlay() : ''}
                    ${!outOfStock && discount > 0 ? `<div class="absolute top-1 right-1 px-1.5 py-0.5 bg-game-green text-white text-[10px] font-bold rounded">-${discount}%</div>` : ''}
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
                        ${outOfStock ? `<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-200 text-slate-400 cursor-not-allowed" title="Hết hàng"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></span>` : `<button type="button" 
                                class="w-8 h-8 bg-game-accent rounded-lg flex items-center justify-center group-hover:bg-game-accent-hover transition-colors cursor-pointer" 
                                data-product-id="${product.id}"
                                data-product-title="${escapeHtml(product.title)}"
                                data-product-image="${product.image || ''}"
                                data-product-price="${escapeHtml(product.price || '')}"
                                onclick="handleAddToCart(this, event)">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>`}
                    </div>
                </div>
            </a>
        `;
    }

    // Handle add to cart
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


    // Load recommendations
    async function loadRecommendations() {
        const token = localStorage.getItem('auth_token');
        
        if (!token) {
            document.getElementById('login-modal').classList.remove('hidden');
            document.getElementById('recommendations-games').innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`${RECOMMENDATIONS_API}/for-me?limit=50`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    document.getElementById('login-modal').classList.remove('hidden');
                    document.getElementById('recommendations-games').innerHTML = '';
                    return;
                }
                throw new Error('Failed to load recommendations');
            }

            const data = await response.json();

            if (data.success && data.data && data.data.length > 0) {
                renderRecommendations(data.data);
            } else {
                // Fallback to popular
                await loadPopularFallback();
            }
        } catch (error) {
            console.error('Error loading recommendations:', error);
            showError();
        }
    }

    // Render all recommendations in one section
    // Priority: Realtime (recent_hot_interest, category_preference, similar_to_purchased, similar_to_viewed) first, then Hybrid
    function renderRecommendations(products) {
        const container = document.getElementById('recommendations-games');
        
        if (!products || products.length === 0) {
            showNoRecommendations();
            return;
        }

        // Define realtime algorithm types (should appear first)
        const realtimeAlgorithms = [
            'recent_hot_interest',
            'category_preference', 
            'similar_to_purchased',
            'similar_to_viewed'
        ];

        // Separate realtime vs hybrid recommendations
        const realtimeRecs = products.filter(p => realtimeAlgorithms.includes(p.algorithm));
        const hybridRecs = products.filter(p => !realtimeAlgorithms.includes(p.algorithm));

        // Combine: Realtime first, then Hybrid
        const sortedProducts = [...realtimeRecs, ...hybridRecs];

        // Render all products
        container.innerHTML = sortedProducts.map(p => renderGameCard(p)).join('');
    }

    // Load popular as fallback
    async function loadPopularFallback() {
        try {
            const response = await fetch(`${RECOMMENDATIONS_API}/popular?limit=20`);
            const data = await response.json();

            if (data.success && data.data && data.data.length > 0) {
                const container = document.getElementById('recommendations-games');
                container.innerHTML = data.data.map(p => renderGameCard(p)).join('');
            } else {
                showNoRecommendations();
            }
        } catch (error) {
            console.error('Error loading popular:', error);
            showNoRecommendations();
        }
    }

    // Show no recommendations
    function showNoRecommendations() {
        document.getElementById('recommendations-games').innerHTML = `
            <div class="col-span-full text-center py-16">
                <svg class="w-24 h-24 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <h3 class="text-xl font-bold text-slate-700 mb-2">Chưa có đề xuất</h3>
                <p class="text-slate-500 mb-6">Hãy khám phá thêm các game để hệ thống hiểu sở thích của bạn</p>
                <a href="${BASE_URL}/store" class="px-6 py-3 bg-game-accent text-white font-semibold rounded-xl hover:bg-game-accent-hover transition-colors inline-block">
                    Khám phá game
                </a>
            </div>
        `;
    }

    // Show error
    function showError() {
        document.getElementById('recommendations-games').innerHTML = `
            <div class="col-span-full text-center py-16">
                <svg class="w-24 h-24 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-slate-700 mb-2">Không thể tải đề xuất</h3>
                <p class="text-slate-500 mb-6">Đã có lỗi xảy ra. Vui lòng thử lại.</p>
                <button onclick="loadRecommendations()" class="px-6 py-3 bg-game-accent text-white font-semibold rounded-xl hover:bg-game-accent-hover transition-colors">
                    Thử lại
                </button>
            </div>
        `;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadRecommendations();
    });
</script>
@endpush
