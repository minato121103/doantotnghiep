@extends('layouts.main')

@section('title', 'Trang chủ')

@section('content')
    <!-- Hero Section -->
    <section class="relative pt-36 pb-4 overflow-hidden bg-gradient-to-br from-slate-50 via-indigo-50/50 to-purple-50/50">
        <!-- Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1920')] bg-cover bg-center opacity-5"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-game-accent/5 via-transparent to-game-purple/5"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-20">
            <div class="flex flex-col lg:flex-row items-center py-6 lg:py-8">
                <!-- Left Content -->
                <div class="lg:w-1/2 text-center lg:text-left mb-12 lg:mb-0">
                    <div class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full border border-game-border shadow-sm mb-6">
                        <span class="w-2 h-2 bg-game-green rounded-full mr-2 animate-pulse"></span>
                        <span class="text-sm text-slate-600">Hơn 10,000+ tài khoản game</span>
                    </div>
                    
                    <h1 class="font-display text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-6 leading-tight">
                        <span class="text-slate-800">Khám phá</span><br>
                        <span class="gradient-text">Thế giới Game</span><br>
                        <span class="text-slate-800">của bạn</span>
                    </h1>
                    
                    <p class="text-slate-600 text-lg md:text-xl mb-8 max-w-lg mx-auto lg:mx-0">
                        Nền tảng mua bán tài khoản game uy tín. Đa dạng game, giá hợp lý, giao dịch an toàn & bảo mật.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ url('/store') }}" class="px-8 py-4 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-full hover:opacity-90 transition-all glow-effect text-center">
                            Khám phá ngay
                            <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="{{ url('/about') }}" class="px-8 py-4 bg-white border border-game-border text-slate-700 font-medium rounded-full hover:border-game-accent hover:text-game-accent transition-colors text-center shadow-sm">
                            Tìm hiểu thêm
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 mt-6 pt-4 border-t border-slate-200">
                        <div>
                            <div class="font-display text-2xl md:text-3xl font-bold text-slate-800">10K+</div>
                            <div class="text-slate-500 text-sm">Tài khoản</div>
                        </div>
                        <div>
                            <div class="font-display text-2xl md:text-3xl font-bold text-slate-800">5K+</div>
                            <div class="text-slate-500 text-sm">Khách hàng</div>
                        </div>
                        <div>
                            <div class="font-display text-2xl md:text-3xl font-bold text-slate-800">99%</div>
                            <div class="text-slate-500 text-sm">Hài lòng</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - 2 Featured Game Cards -->
                <div class="lg:w-1/2 lg:pl-8">
                    <div class="relative space-y-4 float-animation" id="hero-games-container">
                        <!-- Loading State -->
                        <div id="hero-loading" class="space-y-4">
                            <!-- Loading Card 1 -->
                            <div class="bg-white rounded-xl border border-slate-200 shadow-xl p-3 flex animate-pulse">
                                <div class="w-24 h-24 bg-slate-200 rounded-lg flex-shrink-0"></div>
                                <div class="flex-1 ml-3 space-y-2">
                                    <div class="bg-slate-200 h-4 w-3/4 rounded"></div>
                                    <div class="bg-slate-200 h-3 w-1/3 rounded"></div>
                                    <div class="flex justify-between items-end mt-2">
                                        <div class="bg-slate-200 h-5 w-1/3 rounded"></div>
                                        <div class="bg-slate-200 h-8 w-8 rounded-lg"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Loading Card 2 -->
                            <div class="bg-white rounded-xl border border-slate-200 shadow-xl p-3 flex animate-pulse">
                                <div class="w-24 h-24 bg-slate-200 rounded-lg flex-shrink-0"></div>
                                <div class="flex-1 ml-3 space-y-2">
                                    <div class="bg-slate-200 h-4 w-3/4 rounded"></div>
                                    <div class="bg-slate-200 h-3 w-1/3 rounded"></div>
                                    <div class="flex justify-between items-end mt-2">
                                        <div class="bg-slate-200 h-5 w-1/3 rounded"></div>
                                        <div class="bg-slate-200 h-8 w-8 rounded-lg"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Game Cards will be injected here -->
                        <div id="hero-content" class="hidden space-y-4"></div>
                        
                        <!-- Decorative -->
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-game-purple/20 rounded-xl blur-xl -z-10"></div>
                        <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-game-accent/20 rounded-xl blur-xl -z-10"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll indicator -->
        <div class="flex justify-center mt-2 z-20">
            <div class="w-6 h-10 border-2 border-slate-300 rounded-full flex justify-center">
                <div class="w-1.5 h-3 bg-game-accent rounded-full mt-2 animate-bounce"></div>
            </div>
        </div>
    </section>

    <!-- 1. Dành cho bạn (Featured Games) -->
    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-800 mb-2">Dành cho bạn</h2>
                    <p class="text-slate-600">Những tựa game được đề xuất cho bạn</p>
                </div>
                <a href="{{ url('/for-you') }}" class="hidden md:flex items-center text-game-accent hover:text-game-accent-hover transition-colors">
                    Xem tất cả
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="featured-games">
                <!-- Game cards will be loaded from API -->
            </div>
        </div>
    </section>

    <!-- 2. Mới ra mắt (New Releases) -->
    <section class="py-10 bg-slate-50/50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-800 mb-2">Mới ra mắt</h2>
                    <p class="text-slate-600">Những tựa game mới nhất đã có mặt</p>
                </div>
                <a href="{{ url('/store') }}?sort=newest" class="hidden md:flex items-center text-game-accent hover:text-game-accent-hover transition-colors">
                    Xem tất cả
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="new-releases">
                <!-- Game cards will be loaded from API -->
            </div>
        </div>
    </section>

    <!-- 3. Ưu đãi (Special Offers) -->
    <section id="promo-section" class="py-10 bg-gradient-to-r from-indigo-50 to-purple-50 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-game-purple/10 to-game-accent/10"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center px-4 py-2 bg-game-orange/10 text-game-orange rounded-full text-sm font-medium mb-4">
                        🔥 Ưu đãi có hạn
                    </div>
                    <h2 id="promo-heading" class="font-display text-3xl md:text-4xl font-bold text-slate-800 mb-4">
                        Giảm giá lên đến <span id="promo-discount-badge" class="gradient-text">50%</span>
                    </h2>
                    <p id="promo-description" class="text-slate-600 text-lg mb-6 max-w-lg">
                        Nhanh tay sở hữu những tựa game hot nhất với giá ưu đãi. Chương trình có hạn!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ url('/store') }}?on_sale=true" class="px-8 py-4 bg-gradient-to-r from-game-orange to-game-pink text-white font-bold rounded-full hover:opacity-90 transition-all pulse-glow">
                            Xem ưu đãi
                        </a>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <!-- 4. Tin tức (News) -->
    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-800 mb-2">Tin tức</h2>
                    <p class="text-slate-600">Cập nhật tin tức mới nhất về game</p>
                </div>
                <a href="{{ url('/news') }}" class="hidden md:flex items-center text-game-accent hover:text-game-accent-hover transition-colors">
                    Xem tất cả
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="news-section">
                <!-- News will be loaded from API -->
            </div>
        </div>
    </section>

    <!-- 5. Cộng đồng (Community) -->
    <section class="py-10 bg-slate-50/50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-800 mb-2">Cộng đồng</h2>
                    <p class="text-slate-600">Tham gia thảo luận cùng game thủ</p>
                </div>
                <a href="{{ url('/community') }}" class="hidden md:flex items-center text-game-accent hover:text-game-accent-hover transition-colors">
                    Xem tất cả
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="community-section">
                <!-- Community posts will be loaded from API -->
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // Cấu hình URL API
    const BASE_URL = '{{ url("/") }}';
    const GAME_BASE_URL = '{{ url("/game") }}';
    let API_BASE_URL = '{{ url("/api/products") }}';
    
    // Gọi API, nếu 404 thì thử lại với đường dẫn /public
    async function fetchAPI(url, options = {}) {
        const defaultHeaders = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...options.headers
        };
        
        try {
            let response = await fetch(url, {
                ...options,
                headers: defaultHeaders
            });
            
            // If 404 and URL doesn't have /public, try with /public
            if (response.status === 404 && !url.includes('/public/')) {
                const urlObj = new URL(url, window.location.origin);
                const pathParts = urlObj.pathname.split('/').filter(p => p);
                
                // Insert 'public' after the first part (project name like 'webdoan')
                if (pathParts.length > 0 && pathParts[0] !== 'public') {
                    pathParts.splice(1, 0, 'public');
                    urlObj.pathname = '/' + pathParts.join('/');
                    console.log('404 detected, retrying with /public:', urlObj.href);
                    
                    response = await fetch(urlObj.href, {
                        ...options,
                        headers: defaultHeaders
                    });
                    
                    // If successful, update API_BASE_URL for future requests
                    if (response.ok) {
                        const newBase = urlObj.origin + urlObj.pathname.split('/api/')[0] + '/api/products';
                        console.log('Success with /public, updating API_BASE_URL to:', newBase);
                        API_BASE_URL = newBase;
                    }
                }
            }
            
            return response;
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    }
    
    // Log thông tin URL phục vụ debug
    console.log('API_BASE_URL:', API_BASE_URL);
    console.log('BASE_URL:', BASE_URL);
    console.log('Current location:', window.location.href);
    console.log('Current pathname:', window.location.pathname);
    
    // Tải danh sách game nổi bật - Sử dụng AI Recommendation System
    async function loadFeaturedGames() {
        const container = document.getElementById('featured-games');
        
        // Hiển thị loading skeleton
        container.innerHTML = `
            <div class="animate-pulse bg-white rounded-xl border border-slate-200 p-3 flex">
                <div class="w-28 h-28 bg-slate-200 rounded-lg flex-shrink-0"></div>
                <div class="flex-1 ml-3 space-y-2">
                    <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                    <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                </div>
            </div>
        `.repeat(6);
        
        try {
            let games = [];
            const token = localStorage.getItem('auth_token');
            
            // Bước 1: Nếu user đã đăng nhập, thử lấy personalized recommendations
            if (token) {
                try {
                    const personalizedResponse = await fetch(`${BASE_URL}/api/recommendations/for-me?limit=6`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });
                    const personalizedData = await personalizedResponse.json();
                    
                    if (personalizedData.success && personalizedData.data && personalizedData.data.length >= 3) {
                        games = personalizedData.data;
                        console.log('🤖 Loaded personalized AI recommendations:', games.length, 'products');
                    }
                } catch (aiError) {
                    console.warn('Personalized recommendations not available:', aiError);
                }
            }
            
            // Bước 2: Nếu không có personalized, thử lấy popular recommendations
            if (games.length < 3) {
                try {
                    const popularResponse = await fetch(`${BASE_URL}/api/recommendations/popular?limit=6`);
                    const popularData = await popularResponse.json();
                    
                    if (popularData.success && popularData.data && popularData.data.length > 0) {
                        games = popularData.data;
                        console.log('🔥 Loaded popular recommendations:', games.length, 'products');
                    }
                } catch (popError) {
                    console.warn('Popular recommendations not available:', popError);
                }
            }
            
            // Bước 3: Fallback - lấy theo view_count và rating (products API)
            if (games.length < 3) {
                const apiUrl = `${API_BASE_URL}?per_page=6&sort_by=view_count&sort_order=desc`;
                console.log('📊 Fallback to view_count sort:', apiUrl);
                const response = await fetchAPI(apiUrl);
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.data.length > 0) {
                        games = result.data;
                        console.log('📈 Loaded by view_count:', games.length, 'products');
                    }
                }
            }
            
            // Render games
            if (games.length > 0) {
                renderGames(games, 'featured-games');
            } else {
                renderPlaceholderGames('featured-games');
            }
        } catch (error) {
            console.error('Error loading featured games:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                API_BASE_URL: API_BASE_URL
            });
            renderPlaceholderGames('featured-games');
        }
    }
    
    // Tải danh sách game mới
    async function loadNewReleases() {
        try {
            const apiUrl = `${API_BASE_URL}?per_page=6&sort_by=id&sort_order=desc`;
            console.log('Fetching new releases from:', apiUrl);
            const response = await fetchAPI(apiUrl);
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                renderGames(result.data, 'new-releases');
            } else {
                renderPlaceholderGames('new-releases');
            }
        } catch (error) {
            console.error('Error loading new releases:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                API_BASE_URL: API_BASE_URL
            });
            renderPlaceholderGames('new-releases');
        }
    }

    // Hiển thị danh sách game dạng thẻ ngang
    function renderGames(games, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = games.map(game => {
            const outOfStock = isOutOfStock(game);
            return `
            <div class="group bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-game-accent hover:shadow-lg transition-all card-hover flex">
                <!-- Left: Image -->
                <a href="${GAME_BASE_URL}/${game.id}" class="flex-shrink-0 w-28 h-28 overflow-hidden relative block">
                    <img src="${game.image || 'https://via.placeholder.com/150x150?text=Game'}" 
                         alt="${escapeHtml(game.title)}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    ${outOfStock ? outOfStockOverlay() : ''}
                </a>
                <!-- Right: Content -->
                <div class="flex-1 p-3 flex flex-col justify-between min-w-0">
                    <div>
                        <a href="${GAME_BASE_URL}/${game.id}" class="font-heading font-semibold text-slate-800 text-sm leading-tight line-clamp-2 group-hover:text-game-accent transition-colors">
                            ${escapeHtml(game.title)}
                        </a>
                        <div class="flex items-center gap-2 mt-1">
                            ${game.category ? `<span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs rounded">${escapeHtml(game.category)}</span>` : ''}
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        ${formatPrice(game.price)}
                        ${outOfStock ? `<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-200 text-slate-400 cursor-not-allowed" title="Hết hàng"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></span>` : `<button type="button"
                                class="w-8 h-8 bg-game-accent rounded-lg flex items-center justify-center hover:bg-game-accent-hover transition-colors cursor-pointer add-to-cart-btn"
                                data-product-id="${game.id}"
                                data-product-title="${escapeHtml(game.title)}"
                                data-product-image="${game.image || ''}"
                                data-product-price="${escapeHtml(game.price || '')}"
                                onclick="return handleAddToCart(this, event);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>`}
                    </div>
                </div>
            </div>
        `;
        }).join('');
    }
    
    // Hiển thị danh sách game giả lập khi lỗi/không có dữ liệu
    function renderPlaceholderGames(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const placeholders = [
            { title: 'Elden Ring', price: '1.200.000đ 720.000đ', category: 'RPG', image: 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1245620/header.jpg' },
            { title: 'Cyberpunk 2077', price: '800.000đ 450.000đ', category: 'Action', image: 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1091500/header.jpg' },
            { title: 'Red Dead Redemption 2', price: '900.000đ 550.000đ', category: 'Action', image: 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1174180/header.jpg' },
            { title: 'God of War', price: '1.000.000đ 650.000đ', category: 'Action', image: 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1593500/header.jpg' },
        ];
        
        container.innerHTML = placeholders.map(game => `
            <div class="group bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-game-accent hover:shadow-lg transition-all card-hover flex">
                <!-- Left: Image -->
                <a href="/store" class="flex-shrink-0 w-28 h-28 overflow-hidden">
                    <img src="${game.image}" 
                         alt="${game.title}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </a>
                <!-- Right: Content -->
                <div class="flex-1 p-3 flex flex-col justify-between min-w-0">
                    <div>
                        <a href="/store" class="font-heading font-semibold text-slate-800 text-sm leading-tight line-clamp-2 group-hover:text-game-accent transition-colors">
                            ${game.title}
                        </a>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs rounded">${game.category}</span>
                            <span class="flex items-center text-slate-400 text-xs">
                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                46
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        ${formatPrice(game.price)}
                        <a href="/store" class="w-8 h-8 bg-game-accent rounded-lg flex items-center justify-center hover:bg-game-accent-hover transition-colors">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    // Bộ đếm thời gian khuyến mãi
    function updateCountdown() {
        // Set end date (2 days from now for demo)
        const endDate = new Date();
        endDate.setDate(endDate.getDate() + 2);
        endDate.setHours(endDate.getHours() + 12);
        
        const now = new Date();
        const diff = endDate - now;
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        document.getElementById('countdown-days').textContent = String(days).padStart(2, '0');
        document.getElementById('countdown-hours').textContent = String(hours).padStart(2, '0');
        document.getElementById('countdown-minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('countdown-seconds').textContent = String(seconds).padStart(2, '0');
    }
    
    // Escape text để tránh lỗi HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Định dạng chuỗi giá (giá gốc + giá khuyến mãi nếu có)
    function formatPrice(priceStr) {
        if (!priceStr) return '<span class="text-game-accent font-bold">Liên hệ</span>';
        
        // Extract all prices (numbers followed by ₫ or đ, with optional dots/commas)
        const priceRegex = /[\d.,]+\s*[₫đ]/gi;
        const prices = priceStr.match(priceRegex);
        
        if (!prices || prices.length === 0) {
            // No valid price found, just show the original string cleaned
            const cleanStr = priceStr.replace(/Giá gốc là:|Giá hiện tại là:|Original price was:|Current price is:/gi, '').trim();
            return `<span class="text-game-accent font-bold">${escapeHtml(cleanStr)}</span>`;
        }
        
        if (prices.length === 1) {
            // Only one price - show as current price
            return `<span class="text-game-accent font-bold">${prices[0].trim()}</span>`;
        }
        
        // Multiple prices - first is original, last is current
        const originalPrice = prices[0].trim();
        const currentPrice = prices[prices.length - 1].trim();
        
        // Check if prices are different (has discount)
        if (originalPrice !== currentPrice) {
            return `<div>
                <span class="text-slate-400 line-through text-sm">${originalPrice}</span>
                <span class="text-game-accent font-bold ml-2">${currentPrice}</span>
            </div>`;
        }
        
        // Same price - just show once
        return `<span class="text-game-accent font-bold">${currentPrice}</span>`;
    }

    function isOutOfStock(product) {
        return (product.available_accounts || 0) === 0;
    }

    function outOfStockOverlay() {
        return `<div class="absolute inset-0 flex items-center justify-center bg-white/75 z-10"><span class="font-bold text-slate-700 text-sm">Hết hàng</span></div>`;
    }
    
    // Định dạng giá riêng cho thẻ Hero (cỡ chữ lớn hơn)
    function formatHeroPrice(priceStr) {
        if (!priceStr) return '<span class="text-game-accent font-bold text-xl">Liên hệ</span>';
        
        const priceRegex = /[\d.,]+\s*[₫đ]/gi;
        const prices = priceStr.match(priceRegex);
        
        if (!prices || prices.length === 0) {
            const cleanStr = priceStr.replace(/Giá gốc là:|Giá hiện tại là:|Original price was:|Current price is:/gi, '').trim();
            return `<span class="text-game-accent font-bold text-xl">${escapeHtml(cleanStr)}</span>`;
        }
        
        if (prices.length === 1) {
            return `<span class="text-game-accent font-bold text-xl">${prices[0].trim()}</span>`;
        }
        
        const originalPrice = prices[0].trim();
        const currentPrice = prices[prices.length - 1].trim();
        
        if (originalPrice !== currentPrice) {
            return `
                <span class="text-slate-400 line-through text-sm">${originalPrice}</span>
                <span class="text-game-accent font-bold text-xl ml-2">${currentPrice}</span>
            `;
        }
        
        return `<span class="text-game-accent font-bold text-xl">${currentPrice}</span>`;
    }
    
    // Tính phần trăm giảm giá từ chuỗi giá
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
    
    // Tách giá gốc và giá hiện tại từ chuỗi giá
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
        
        return {
            original: prices[0].trim(),
            current: prices[prices.length - 1].trim()
        };
    }
    
    function renderHeroGameCard(game) {
        const discount = game.discount_percent || calculateDiscount(game.price);
        const outOfStock = isOutOfStock(game);
        const prices = extractPrices(game.price);
        let priceHtml;
        if (game.sale_price) {
            priceHtml = `<span class="text-slate-400 line-through text-xs">${prices.current || game.price}</span>
                <span class="text-game-accent font-bold text-lg">${new Intl.NumberFormat('vi-VN').format(Math.round(game.sale_price))}đ</span>`;
        } else {
            priceHtml = `${prices.original && prices.original !== prices.current ? `<span class="text-slate-400 line-through text-xs">${prices.original}</span>` : ''}
                <span class="text-game-accent font-bold text-lg">${prices.current || 'Liên hệ'}</span>`;
        }
        
        return `
            <div class="group bg-white rounded-xl overflow-hidden border border-game-border shadow-xl hover:shadow-2xl hover:border-game-accent transition-all duration-300 card-hover flex">
                <a href="${GAME_BASE_URL}/${game.id}" class="flex-shrink-0 w-24 h-24 overflow-hidden rounded-lg m-3 relative">
                    <img src="${game.image || 'https://via.placeholder.com/150x150?text=Game'}" 
                         alt="${escapeHtml(game.title)}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    ${outOfStock ? outOfStockOverlay() : ''}
                    ${!outOfStock && discount > 0 ? `<div class="absolute top-1 right-1 px-1.5 py-0.5 bg-game-green text-white text-[10px] font-bold rounded">-${discount}%</div>` : ''}
                </a>
                <div class="flex-1 py-3 pr-3 flex flex-col justify-between min-w-0">
                    <div>
                        <a href="${GAME_BASE_URL}/${game.id}" class="font-heading font-semibold text-slate-800 text-sm leading-tight line-clamp-2 group-hover:text-game-accent transition-colors">
                            ${escapeHtml(game.title)}
                        </a>
                        <div class="flex items-center gap-2 mt-1.5">
                            ${game.category ? `<span class="px-2 py-0.5 bg-game-accent/10 text-game-accent text-xs font-medium rounded">${escapeHtml(game.category)}</span>` : ''}
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-2">
                            ${priceHtml}
                        </div>
                        ${outOfStock ? `<span class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-200 text-slate-400 cursor-not-allowed" title="Hết hàng"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></span>` : `<button type="button"
                                class="w-9 h-9 bg-game-accent rounded-lg flex items-center justify-center hover:bg-game-accent-hover transition-colors shadow-lg hover:shadow-xl hover:scale-105 transform duration-200 cursor-pointer add-to-cart-btn"
                                data-product-id="${game.id}"
                                data-product-title="${escapeHtml(game.title)}"
                                data-product-image="${game.image || ''}"
                                data-product-price="${escapeHtml(game.price || '')}"
                                onclick="return handleAddToCart(this, event);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </button>`}
                    </div>
                </div>
            </div>
        `;
    }
    
    // Lấy ngẫu nhiên 2 game cho Hero từ API
    async function loadHeroGame() {
        const loadingEl = document.getElementById('hero-loading');
        const contentEl = document.getElementById('hero-content');
        
        if (!loadingEl || !contentEl) return;
        
        try {
            console.log('Fetching hero games from:', API_BASE_URL);
            const response = await fetchAPI(API_BASE_URL);
            console.log('Hero games response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            
            if (data.success && data.data && data.data.length > 0) {
                // Shuffle and pick 2 random games
                const shuffled = [...data.data].sort(() => 0.5 - Math.random());
                const selectedGames = shuffled.slice(0, 2);
                
                // Build the Hero cards content
                contentEl.innerHTML = selectedGames.map(game => renderHeroGameCard(game)).join('');
                
                // Show content, hide loading
                loadingEl.classList.add('hidden');
                contentEl.classList.remove('hidden');
            } else {
                // Fallback to placeholder
                renderHeroPlaceholder();
            }
        } catch (error) {
            console.error('Error loading hero games:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack,
                API_BASE_URL: API_BASE_URL
            });
            renderHeroPlaceholder();
        }
    }
    
    // Hiển thị 2 game giả lập cho Hero khi không gọi được API
    function renderHeroPlaceholder() {
        const loadingEl = document.getElementById('hero-loading');
        const contentEl = document.getElementById('hero-content');
        
        if (!loadingEl || !contentEl) return;
        
        const placeholders = [
            { id: 1, title: 'Elden Ring', category: 'RPG', price: '1.200.000đ 720.000đ', image: 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1245620/header.jpg' },
            { id: 2, title: 'Cyberpunk 2077', category: 'Action', price: '900.000đ 450.000đ', image: 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/1091500/header.jpg' }
        ];
        
        contentEl.innerHTML = placeholders.map(game => renderHeroGameCard(game)).join('');
        
        loadingEl.classList.add('hidden');
        contentEl.classList.remove('hidden');
    }
    
    // Handler function để lấy data từ button và gọi addToCart (tương tự trang store)
    function handleAddToCart(button, event) {
        // Ngăn chặn hành vi mặc định và propagation để không chuyển trang
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

    // Thêm sản phẩm vào giỏ hàng (localStorage) – copy logic từ trang store
    function addToCart(productId, productTitle, productImage, productPrice) {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        const existingIndex = cart.findIndex(item => item.id === productId);

        if (existingIndex >= 0) {
            cart[existingIndex].quantity += 1;
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

        updateCartCount();
        showCartNotification(productTitle);
    }

    // Cập nhật số lượng giỏ hàng trên header (dùng cùng id 'cart-count')
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

    // Thông báo nhỏ khi thêm vào giỏ
    function showCartNotification(productTitle) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-20 right-4 z-50 bg-white border border-game-border rounded-xl shadow-xl px-4 py-3 flex items-center gap-3 animate-slide-in';
        notification.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-game-accent/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-game-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Đã thêm vào giỏ hàng</p>
                <p class="text-xs text-slate-500 line-clamp-1">${escapeHtml(productTitle)}</p>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }
    
    // Trích xuất ảnh đầu tiên từ nội dung HTML
    function extractFirstImage(html) {
        if (!html) return null;
        const imgMatch = html.match(/<img[^>]+src=["']([^"']+)["']/i);
        return imgMatch ? imgMatch[1] : null;
    }
    
    // Trích xuất text thuần từ HTML
    function stripHtmlTags(html) {
        if (!html) return '';
        const div = document.createElement('div');
        div.innerHTML = html;
        return div.textContent || div.innerText || '';
    }
    
    // Tải tin tức
    async function loadNews() {
        const container = document.getElementById('news-section');
        if (!container) return;
        
        // Loading skeleton
        container.innerHTML = `
            <div class="animate-pulse bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="h-40 bg-slate-200"></div>
                <div class="p-4 space-y-2">
                    <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-200 rounded w-full"></div>
                    <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                </div>
            </div>
        `.repeat(3);
        
        try {
            const response = await fetch(`${BASE_URL}/api/news?per_page=3`);
            if (!response.ok) throw new Error('Failed to load news');
            const data = await response.json();
            
            if (data.success && data.data && data.data.length > 0) {
                container.innerHTML = data.data.map(news => {
                    // Trích xuất ảnh từ description HTML
                    const imageUrl = extractFirstImage(news.description) || 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=400';
                    // Trích xuất text thuần cho excerpt
                    const excerpt = stripHtmlTags(news.description).substring(0, 120);
                    // Format ngày từ published_at hoặc created_at
                    const dateStr = news.published_at || (news.created_at ? new Date(news.created_at).toLocaleDateString('vi-VN') : '');
                    
                    return `
                        <a href="${BASE_URL}/news/${news.id}" class="group bg-white rounded-xl border border-slate-200 overflow-hidden hover:border-game-accent hover:shadow-lg transition-all">
                            <div class="h-40 overflow-hidden">
                                <img src="${imageUrl}" 
                                     alt="${escapeHtml(news.title)}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     onerror="this.src='https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=400'">
                            </div>
                            <div class="p-4">
                                <h3 class="font-heading font-semibold text-slate-800 line-clamp-2 group-hover:text-game-accent transition-colors mb-2">
                                    ${escapeHtml(news.title)}
                                </h3>
                                <p class="text-slate-500 text-sm line-clamp-2 mb-3">${escapeHtml(excerpt)}...</p>
                                <div class="flex items-center text-xs text-slate-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    ${dateStr}
                                </div>
                            </div>
                        </a>
                    `;
                }).join('');
            } else {
                renderNewsPlaceholder();
            }
        } catch (error) {
            console.error('Error loading news:', error);
            renderNewsPlaceholder();
        }
    }
    
    // Placeholder cho tin tức
    function renderNewsPlaceholder() {
        const container = document.getElementById('news-section');
        if (!container) return;
        
        const placeholders = [
            { title: 'GTA 6 chính thức công bố ngày phát hành', excerpt: 'Rockstar Games vừa xác nhận ngày phát hành chính thức của tựa game được mong đợi nhất...', image: 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=400', date: '10/02/2026' },
            { title: 'Steam mở đợt sale lớn nhất năm', excerpt: 'Đợt giảm giá mùa xuân của Steam đã bắt đầu với hàng nghìn tựa game được giảm giá...', image: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400', date: '09/02/2026' },
            { title: 'Elden Ring DLC nhận điểm review cao', excerpt: 'Shadow of the Erdtree nhận được điểm số gần như tuyệt đối từ các trang đánh giá game...', image: 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=400', date: '08/02/2026' },
        ];
        
        container.innerHTML = placeholders.map(news => `
            <a href="${BASE_URL}/news" class="group bg-white rounded-xl border border-slate-200 overflow-hidden hover:border-game-accent hover:shadow-lg transition-all">
                <div class="h-40 overflow-hidden">
                    <img src="${news.image}" alt="${news.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                <div class="p-4">
                    <h3 class="font-heading font-semibold text-slate-800 line-clamp-2 group-hover:text-game-accent transition-colors mb-2">${news.title}</h3>
                    <p class="text-slate-500 text-sm line-clamp-2 mb-3">${news.excerpt}</p>
                    <div class="flex items-center text-xs text-slate-400">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        ${news.date}
                    </div>
                </div>
            </a>
        `).join('');
    }
    
    // Format thời gian tương đối
    function formatRelativeTime(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffSecs = Math.floor(diffMs / 1000);
        const diffMins = Math.floor(diffSecs / 60);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);
        
        if (diffSecs < 60) return 'Vừa xong';
        if (diffMins < 60) return `${diffMins} phút trước`;
        if (diffHours < 24) return `${diffHours} giờ trước`;
        if (diffDays < 7) return `${diffDays} ngày trước`;
        return date.toLocaleDateString('vi-VN');
    }
    
    // Tải bài viết cộng đồng (ngẫu nhiên)
    async function loadCommunity() {
        const container = document.getElementById('community-section');
        if (!container) return;
        
        // Loading skeleton
        container.innerHTML = `
            <div class="animate-pulse bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-slate-200 rounded-full"></div>
                    <div class="space-y-1 flex-1">
                        <div class="h-3 bg-slate-200 rounded w-1/3"></div>
                        <div class="h-2 bg-slate-200 rounded w-1/4"></div>
                    </div>
                </div>
                <div class="h-4 bg-slate-200 rounded w-full mb-2"></div>
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
            </div>
        `.repeat(3);
        
        try {
            // Lấy nhiều bài viết hơn để chọn ngẫu nhiên
            const response = await fetch(`${BASE_URL}/api/community/posts?per_page=20`);
            if (!response.ok) throw new Error('Failed to load community');
            const data = await response.json();
            
            if (data.success && data.data && data.data.length > 0) {
                // Shuffle mảng và lấy 3 bài ngẫu nhiên
                const shuffled = [...data.data].sort(() => 0.5 - Math.random());
                const randomPosts = shuffled.slice(0, 3);
                
                container.innerHTML = randomPosts.map(post => `
                    <a href="${BASE_URL}/community?post=${post.id}" class="group bg-white rounded-xl border border-slate-200 p-4 hover:border-game-accent hover:shadow-lg transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <img src="${post.user?.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(post.user?.name || 'User') + '&background=random'}" 
                                 alt="${escapeHtml(post.user?.name || 'User')}"
                                 class="w-10 h-10 rounded-full object-cover border-2 border-slate-100">
                            <div>
                                <div class="font-semibold text-slate-800 text-sm">${escapeHtml(post.user?.name || 'Người dùng')}</div>
                                <div class="text-xs text-slate-400">${formatRelativeTime(post.created_at)}</div>
                            </div>
                        </div>
                        <p class="text-slate-700 line-clamp-3 mb-3 group-hover:text-slate-900 transition-colors">${escapeHtml(post.content || '')}</p>
                        ${post.media_url ? `
                            <div class="rounded-lg overflow-hidden mb-3">
                                <img src="${post.media_url}" alt="Post media" class="w-full h-32 object-cover">
                            </div>
                        ` : ''}
                        <div class="flex items-center gap-4 text-sm text-slate-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                ${post.likes_count || 0}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                ${post.comments_count || 0}
                            </span>
                        </div>
                    </a>
                `).join('');
            } else {
                // Không có bài viết - hiển thị thông báo
                container.innerHTML = `
                    <div class="col-span-full text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                        </svg>
                        <p class="text-slate-500">Chưa có bài viết nào</p>
                        <a href="${BASE_URL}/community" class="inline-block mt-4 px-6 py-2 bg-game-accent text-white rounded-lg hover:bg-game-accent-hover transition-colors">
                            Tham gia cộng đồng
                        </a>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading community:', error);
            // Hiển thị thông báo lỗi thay vì placeholder ảo
            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                    <p class="text-slate-500 mb-2">Không thể tải bài viết cộng đồng</p>
                    <a href="${BASE_URL}/community" class="inline-block px-6 py-2 bg-game-accent text-white rounded-lg hover:bg-game-accent-hover transition-colors">
                        Xem cộng đồng
                    </a>
                </div>
            `;
        }
    }

    // Khởi tạo: gọi API và bắt đầu đếm thời gian
    document.addEventListener('DOMContentLoaded', () => {
        loadHeroGame();
        loadFeaturedGames();
        loadNewReleases();
        loadNews();
        loadCommunity();
        updateCountdown();
        setInterval(updateCountdown, 1000);

        // Cập nhật lại số lượng giỏ hàng nếu đã có dữ liệu trong localStorage
        updateCartCount();
    });
</script>
@endpush

