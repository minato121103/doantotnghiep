@extends('layouts.main')

@section('title', 'Chi tiết sản phẩm')

@section('content')
    <!-- Breadcrumb -->
    <section class="pt-36 pb-4 bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30">
        <div class="container mx-auto px-4">
            <nav class="flex items-center text-sm text-slate-500" id="breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-game-accent transition-colors">Trang chủ</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ url('/store') }}" class="hover:text-game-accent transition-colors">Khám phá</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-800 font-medium" id="breadcrumb-title">Đang tải...</span>
            </nav>
        </div>
    </section>

    <!-- Main Product Section -->
    <section class="py-8 bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30">
        <div class="container mx-auto px-4">
            <!-- Loading State -->
            <div id="product-loading" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="animate-pulse">
                    <div class="bg-slate-200 rounded-2xl h-96"></div>
                </div>
                <div class="animate-pulse space-y-4">
                    <div class="bg-slate-200 h-8 w-3/4 rounded"></div>
                    <div class="bg-slate-200 h-6 w-1/2 rounded"></div>
                    <div class="bg-slate-200 h-4 w-full rounded"></div>
                    <div class="bg-slate-200 h-4 w-2/3 rounded"></div>
                    <div class="bg-slate-200 h-12 w-1/3 rounded"></div>
                </div>
            </div>

            <!-- Product Content -->
            <div id="product-content" class="hidden grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                <!-- Left: Product Image -->
                <div class="space-y-4">
                    <div class="relative bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-xl">
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 z-10 flex flex-col gap-2" id="product-badges"></div>
                        
                        <!-- Main Image -->
                        <img id="product-image" src="" alt="" 
                             class="w-full h-auto max-h-[500px] object-contain bg-gradient-to-br from-slate-100 to-slate-50">
                    </div>
                    
                    <!-- Image Gallery Thumbnails (if multiple images) -->
                    <div id="image-gallery" class="hidden grid grid-cols-4 gap-2">
                        <!-- Thumbnails will be loaded here -->
                    </div>
                </div>

                <!-- Right: Product Info -->
                <div class="space-y-6">
                    <!-- Category & Title -->
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span id="product-category" class="px-3 py-1 bg-game-accent/10 text-game-accent text-sm font-medium rounded-full"></span>
                            <span id="product-tags" class="flex gap-2"></span>
                        </div>
                        <h1 id="product-title" class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-slate-800 leading-tight"></h1>
                    </div>

                    <!-- Rating & Stats -->
                    <div class="flex flex-wrap items-center gap-4 pb-4 border-b border-slate-200">
                        <div class="flex items-center gap-1">
                            <div id="product-stars" class="flex text-yellow-400"></div>
                            <span id="product-rating" class="text-slate-600 font-medium ml-1"></span>
                        </div>
                        <div class="flex items-center gap-1 text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span id="product-views">0</span> lượt xem
                        </div>
                        <div class="flex items-center gap-1 text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span id="product-sold">0</span> đã bán
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div id="product-short-desc" class="text-slate-600 leading-relaxed"></div>

                    <!-- Price -->
                    <div class="bg-gradient-to-r from-slate-50 to-indigo-50/50 rounded-xl p-6 border border-slate-200">
                        <div class="flex items-end gap-3">
                            <span id="product-original-price" class="text-slate-400 line-through text-lg hidden"></span>
                            <span id="product-current-price" class="text-game-accent font-bold text-3xl md:text-4xl"></span>
                            <span id="product-discount" class="px-2 py-1 bg-game-green text-white text-sm font-bold rounded hidden"></span>
                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white rounded-xl p-4 border border-slate-200 text-center">
                            <div class="text-game-accent mb-2">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-xs text-slate-500">Giao hàng</div>
                            <div class="text-sm font-semibold text-slate-800">5-15 phút</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-slate-200 text-center">
                            <div class="text-game-green mb-2">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="text-xs text-slate-500">Bảo hành</div>
                            <div class="text-sm font-semibold text-slate-800">Trọn đời</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-slate-200 text-center">
                            <div class="text-game-purple mb-2">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="text-xs text-slate-500">Gửi qua</div>
                            <div class="text-sm font-semibold text-slate-800">Email</div>
                        </div>
                    </div>

                    <!-- Quantity & Add to Cart -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-white">
                            <button id="qty-minus" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number" id="qty-input" value="1" min="1" 
                                   class="w-16 h-12 text-center border-x border-slate-200 font-semibold text-slate-800 focus:outline-none">
                            <button id="qty-plus" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                        <button id="add-to-cart-btn" 
                                class="flex-1 px-8 py-4 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-xl hover:opacity-90 transition-all glow-effect flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Thêm vào giỏ hàng
                        </button>
                        <button id="buy-now-btn" 
                                class="px-8 py-4 bg-game-orange text-white font-bold rounded-xl hover:bg-orange-600 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Mua ngay
                        </button>
                    </div>

                    <!-- Wishlist & Share -->
                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <button class="flex items-center gap-2 text-slate-600 hover:text-game-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Yêu thích
                        </button>
                        <button class="flex items-center gap-2 text-slate-600 hover:text-game-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Chia sẻ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Description Tabs -->
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4">
            <!-- Tabs -->
            <div class="flex border-b border-slate-200 mb-8">
                <button class="tab-btn active px-6 py-4 font-heading font-semibold text-lg border-b-2 border-game-accent text-game-accent transition-colors" data-tab="description">
                    Mô tả sản phẩm
                </button>
                <button class="tab-btn px-6 py-4 font-heading font-semibold text-lg border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors" data-tab="reviews">
                    Đánh giá (<span id="review-count">0</span>)
                </button>
                <button class="tab-btn px-6 py-4 font-heading font-semibold text-lg border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors" data-tab="faq">
                    Hỏi đáp
                </button>
            </div>

            <!-- Tab Content -->
            <div id="tab-description" class="tab-content">
                <div id="product-detail-desc" class="prose prose-slate max-w-none">
                    <!-- Detail description will be loaded here -->
                </div>
            </div>

            <div id="tab-reviews" class="tab-content hidden">
                <!-- Reviews Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Rating Summary -->
                    <div class="bg-slate-50 rounded-2xl p-6">
                        <div class="text-center">
                            <div id="avg-rating-display" class="text-5xl font-bold text-slate-800">0</div>
                            <div id="avg-stars-display" class="flex justify-center my-2 text-yellow-400"></div>
                            <div class="text-slate-500"><span id="total-reviews">0</span> đánh giá</div>
                        </div>
                        <div class="mt-6 space-y-2" id="rating-bars">
                            <!-- Rating bars will be generated here -->
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="lg:col-span-2 space-y-6" id="reviews-list">
                        <p class="text-slate-500 text-center py-8">Chưa có đánh giá nào.</p>
                    </div>
                </div>
            </div>

            <div id="tab-faq" class="tab-content hidden">
                <div class="max-w-3xl mx-auto space-y-4">
                    <div class="bg-slate-50 rounded-xl p-6">
                        <h4 class="font-semibold text-slate-800 mb-2">Làm sao để nhận tài khoản sau khi mua?</h4>
                        <p class="text-slate-600">Tài khoản sẽ được gửi về email của bạn trong vòng 5-15 phút sau khi thanh toán thành công.</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-6">
                        <h4 class="font-semibold text-slate-800 mb-2">Tài khoản có được bảo hành không?</h4>
                        <p class="text-slate-600">Có, tất cả tài khoản đều được bảo hành trọn đời. Nếu gặp vấn đề, hãy liên hệ với chúng tôi để được hỗ trợ.</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-6">
                        <h4 class="font-semibold text-slate-800 mb-2">Có thể chơi offline được không?</h4>
                        <p class="text-slate-600">Tùy thuộc vào loại tài khoản. Với Steam Offline, bạn chỉ cần kích hoạt 1 lần và có thể chơi offline sau đó.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="py-10 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-800">Sản phẩm tương tự</h2>
                <a href="{{ url('/store') }}" class="flex items-center text-game-accent hover:text-game-accent-hover transition-colors">
                    Xem tất cả
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4" id="related-products">
                <!-- Related products will be loaded here -->
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .prose h2 { @apply text-xl font-bold text-slate-800 mt-6 mb-3; }
    .prose h3 { @apply text-lg font-semibold text-slate-800 mt-4 mb-2; }
    .prose p { @apply text-slate-600 mb-4 leading-relaxed; }
    .prose ul { @apply list-disc list-inside text-slate-600 mb-4 space-y-1; }
    .prose li { @apply text-slate-600; }
    .prose strong { @apply text-slate-800 font-semibold; }
</style>
@endpush

@push('scripts')
<script>
    // API Configuration
    const BASE_URL = '{{ url("/") }}';
    const API_BASE_URL = BASE_URL + '/api/products';
    
    const gameId = {{ $gameId }};
    let productData = null;

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

    function renderDetailDescription(product) {
        const raw = product.detail_description;

        // Fallback mặc định khi không có mô tả chi tiết
        if (!raw) {
            return `
                <h2>Thông tin sản phẩm</h2>
                <p>${escapeHtml(product.title)} - Tài khoản game chính hãng, bảo hành trọn đời.</p>
                <h3>Đặc điểm nổi bật</h3>
                <ul>
                    <li>Tài khoản chính chủ, full quyền truy cập</li>
                    <li>Bảo hành trọn đời, hỗ trợ 24/7</li>
                    <li>Giao hàng nhanh chóng qua email</li>
                    <li>Hướng dẫn chi tiết cách sử dụng</li>
                </ul>
                <h3>Hướng dẫn sử dụng</h3>
                <p>Sau khi mua, bạn sẽ nhận được thông tin tài khoản qua email. Đăng nhập và tải game để bắt đầu trải nghiệm!</p>
            `;
        }

        const lines = raw.split(/\r?\n/);
        const imgRegex = /^https?:\/\/[^\s]+?\.(jpe?g|png|webp|gif)$/i;

        const parts = lines.map((line, index) => {
            const trimmed = line.trim();
            if (!trimmed) return '';

            // Nếu là link ảnh thuần, render <img>
            if (imgRegex.test(trimmed)) {
                return `
                    <img
                        src="${trimmed}"
                        alt="${escapeHtml(product.title)} screenshot"
                        class="w-full rounded-xl shadow-md my-6"
                    >
                `;
            }

            // Dòng đầu tiên coi như heading chính
            if (index === 0) {
                return `<h2>${escapeHtml(trimmed)}</h2>`;
            }

            // Các dòng text thường
            return `<p>${escapeHtml(trimmed)}</p>`;
        });

        return parts.join('\n');
    }

    function generateStars(rating) {
        let stars = '';
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 >= 0.5;
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                stars += '<svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            } else if (i === fullStars && hasHalf) {
                stars += '<svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><defs><linearGradient id="half"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#e5e7eb"/></linearGradient></defs><path fill="url(#half)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            } else {
                stars += '<svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            }
        }
        return stars;
    }

    // Load product data
    async function loadProduct() {
        try {
            const response = await fetch(`${API_BASE_URL}/${gameId}`);
            const data = await response.json();

            if (data.success && data.data) {
                productData = data.data;
                renderProduct(productData);
                loadRelatedProducts(productData.category);
            } else {
                showError('Không tìm thấy sản phẩm');
            }
        } catch (error) {
            console.error('Error loading product:', error);
            showError('Đã xảy ra lỗi khi tải sản phẩm');
        }
    }

    function renderProduct(product) {
        // Update page title
        document.title = `${product.title} - Game Store`;
        
        // Breadcrumb
        document.getElementById('breadcrumb-title').textContent = product.title;

        // Badges
        const badges = document.getElementById('product-badges');
        const discount = calculateDiscount(product.price);
        badges.innerHTML = `
            <span class="px-3 py-1 bg-game-orange text-white text-xs font-bold rounded-full shadow-lg">HOT</span>
            ${discount > 0 ? `<span class="px-3 py-1 bg-game-green text-white text-xs font-bold rounded-full shadow-lg">-${discount}%</span>` : ''}
        `;

        // Image
        document.getElementById('product-image').src = product.image || 'https://via.placeholder.com/600x400?text=Game';
        document.getElementById('product-image').alt = product.title;

        // Category & Title
        document.getElementById('product-category').textContent = product.category || 'Game';
        document.getElementById('product-title').textContent = product.title;

        // Tags
        if (product.tags && Array.isArray(product.tags) && product.tags.length > 0) {
            document.getElementById('product-tags').innerHTML = product.tags.slice(0, 3).map(tag => 
                `<span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded">${escapeHtml(tag)}</span>`
            ).join('');
        }

        // Rating & Stats
        const rating = parseFloat(product.average_rating) || 0;
        document.getElementById('product-stars').innerHTML = generateStars(rating);
        document.getElementById('product-rating').textContent = rating.toFixed(1);
        document.getElementById('product-views').textContent = (product.view_count || 0).toLocaleString();
        document.getElementById('product-sold').textContent = (product.rating_count || 0).toLocaleString();

        // Short description
        document.getElementById('product-short-desc').innerHTML = product.short_description || 
            `<p>Tài khoản ${product.title} - Kích hoạt nhanh chóng, bảo hành trọn đời.</p>`;

        // Price
        const prices = extractPrices(product.price);
        if (prices.original && prices.original !== prices.current) {
            document.getElementById('product-original-price').textContent = prices.original;
            document.getElementById('product-original-price').classList.remove('hidden');
        }
        document.getElementById('product-current-price').textContent = prices.current || 'Liên hệ';
        
        if (discount > 0) {
            document.getElementById('product-discount').textContent = `-${discount}%`;
            document.getElementById('product-discount').classList.remove('hidden');
        }

        // Detail description - render từ text + link ảnh trong database
        document.getElementById('product-detail-desc').innerHTML = renderDetailDescription(product);

        // Reviews
        document.getElementById('review-count').textContent = product.rating_count || 0;
        document.getElementById('avg-rating-display').textContent = rating.toFixed(1);
        document.getElementById('avg-stars-display').innerHTML = generateStars(rating);
        document.getElementById('total-reviews').textContent = product.rating_count || 0;

        // Rating bars
        const ratingBars = document.getElementById('rating-bars');
        ratingBars.innerHTML = [5,4,3,2,1].map(star => `
            <div class="flex items-center gap-2">
                <span class="text-sm w-3">${star}</span>
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-400 rounded-full" style="width: ${star === Math.round(rating) ? '60' : Math.random() * 30}%"></div>
                </div>
            </div>
        `).join('');

        // Show content, hide loading
        document.getElementById('product-loading').classList.add('hidden');
        document.getElementById('product-content').classList.remove('hidden');
        document.getElementById('product-content').classList.add('grid');
    }

    function showError(message) {
        document.getElementById('product-loading').innerHTML = `
            <div class="col-span-2 text-center py-16">
                <div class="text-6xl mb-4">😔</div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">${message}</h2>
                <p class="text-slate-600 mb-6">Sản phẩm này có thể đã bị xóa hoặc không tồn tại.</p>
                <a href="/store" class="inline-flex items-center px-6 py-3 bg-game-accent text-white font-semibold rounded-xl hover:bg-game-accent-hover transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Quay lại cửa hàng
                </a>
            </div>
        `;
    }

    // Load related products
    async function loadRelatedProducts(category) {
        const container = document.getElementById('related-products');
        
        try {
            const response = await fetch(`${API_BASE_URL}?category=${encodeURIComponent(category || '')}&per_page=4`);
            const data = await response.json();

            if (data.success && data.data && data.data.length > 0) {
                // Filter out current product
                const related = data.data.filter(p => p.id !== gameId).slice(0, 4);
                
                if (related.length === 0) {
                    container.innerHTML = '<p class="col-span-4 text-center text-slate-500 py-8">Không có sản phẩm tương tự.</p>';
                    return;
                }

                container.innerHTML = related.map(product => {
                    const prices = extractPrices(product.price);
                    const discount = calculateDiscount(product.price);

                    return `
                        <a href="/game/${product.id}" class="group bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-game-accent hover:shadow-lg transition-all card-hover flex">
                            <div class="flex-shrink-0 w-24 h-24 overflow-hidden rounded-lg m-3 relative">
                                <img src="${product.image || 'https://via.placeholder.com/150x150?text=Game'}" 
                                     alt="${escapeHtml(product.title)}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                ${discount > 0 ? `<div class="absolute top-1 right-1 px-1.5 py-0.5 bg-game-green text-white text-[10px] font-bold rounded">-${discount}%</div>` : ''}
                            </div>
                            <div class="flex-1 py-3 pr-3 flex flex-col justify-between min-w-0">
                                <div>
                                    <h3 class="font-heading font-semibold text-slate-800 text-sm leading-tight line-clamp-2 group-hover:text-game-accent transition-colors">
                                        ${escapeHtml(product.title)}
                                    </h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        ${product.category ? `<span class="px-2 py-0.5 bg-game-accent/10 text-game-accent text-xs font-medium rounded">${escapeHtml(product.category)}</span>` : ''}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    ${prices.original && prices.original !== prices.current ? `<span class="text-slate-400 line-through text-xs">${prices.original}</span>` : ''}
                                    <span class="text-game-accent font-bold">${prices.current || 'Liên hệ'}</span>
                                </div>
                            </div>
                        </a>
                    `;
                }).join('');
            } else {
                container.innerHTML = '<p class="col-span-4 text-center text-slate-500 py-8">Không có sản phẩm tương tự.</p>';
            }
        } catch (error) {
            console.error('Error loading related products:', error);
            container.innerHTML = '<p class="col-span-4 text-center text-slate-500 py-8">Không thể tải sản phẩm tương tự.</p>';
        }
    }

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active from all tabs
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'border-game-accent', 'text-game-accent');
                b.classList.add('border-transparent', 'text-slate-500');
            });
            
            // Add active to clicked tab
            btn.classList.add('active', 'border-game-accent', 'text-game-accent');
            btn.classList.remove('border-transparent', 'text-slate-500');
            
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            
            // Show selected tab content
            document.getElementById(`tab-${btn.dataset.tab}`).classList.remove('hidden');
        });
    });

    // Quantity controls
    document.getElementById('qty-minus').addEventListener('click', () => {
        const input = document.getElementById('qty-input');
        const value = parseInt(input.value) || 1;
        if (value > 1) input.value = value - 1;
    });

    document.getElementById('qty-plus').addEventListener('click', () => {
        const input = document.getElementById('qty-input');
        const value = parseInt(input.value) || 1;
        input.value = value + 1;
    });

    // Add to cart
    document.getElementById('add-to-cart-btn').addEventListener('click', () => {
        if (!productData) return;
        
        const qty = parseInt(document.getElementById('qty-input').value) || 1;
        
        // Get existing cart or create new
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        
        // Check if product already in cart
        const existingIndex = cart.findIndex(item => item.id === productData.id);
        if (existingIndex >= 0) {
            cart[existingIndex].quantity += qty;
        } else {
            cart.push({
                id: productData.id,
                title: productData.title,
                image: productData.image,
                price: productData.price,
                quantity: qty
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Update cart count in header
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCountEl.textContent = totalItems;
            cartCountEl.classList.remove('hidden');
        }
        
        // Show success message
        alert(`Đã thêm ${qty} "${productData.title}" vào giỏ hàng!`);
    });

    // Buy now
    document.getElementById('buy-now-btn').addEventListener('click', () => {
        if (!productData) return;
        
        const qty = parseInt(document.getElementById('qty-input').value) || 1;
        
        // Clear cart and add only this product
        const cart = [{
            id: productData.id,
            title: productData.title,
            image: productData.image,
            price: productData.price,
            quantity: qty
        }];
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Redirect to checkout
        window.location.href = '/checkout';
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', loadProduct);
</script>
@endpush
