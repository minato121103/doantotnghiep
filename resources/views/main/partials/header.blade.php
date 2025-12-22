<header class="fixed top-0 left-0 right-0 z-50">
    <!-- Top Bar -->
    <div class="bg-slate-900">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16 md:h-20 gap-4">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center space-x-2 group flex-shrink-0">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-game-accent to-game-purple flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                        </svg>
                    </div>
                    <span class="font-display text-xl md:text-2xl font-bold text-game-accent hidden sm:block">GameTech</span>
                </a>
                
                <!-- Search Bar - Center -->
                <div class="flex-1 max-w-2xl hidden md:block">
                    <div class="relative">
                        <input type="text" 
                               id="header-search"
                               placeholder="Nhập nội dung cần tìm..." 
                               class="w-full px-5 py-3 pr-12 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-game-accent focus:ring-1 focus:ring-game-accent transition-colors">
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-game-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                        <!-- Gợi ý tìm kiếm header -->
                        <div id="header-search-suggestions" class="absolute left-0 right-0 mt-1 bg-slate-900 border border-slate-700 rounded-xl shadow-xl hidden z-40 max-h-72 overflow-y-auto text-sm">
                            <!-- Suggestions will be rendered here -->
                        </div>
                    </div>
                </div>
                
                <!-- Right Actions -->
                <div class="flex items-center space-x-3">
                    <!-- User Menu (Guest) -->
                    <div class="items-center space-x-2" id="guest-menu" style="display: none;">
                        <a href="{{ url('/login') }}" class="hidden sm:flex items-center space-x-2 px-4 py-2 border border-slate-600 text-slate-300 rounded-xl hover:border-game-accent hover:text-game-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium">Đăng nhập</span>
                        </a>
                    </div>

                    <!-- User Menu (Logged In) -->
                    <div class="items-center space-x-3" id="user-menu" style="display: none;">
                        <!-- Balance -->
                        <div class="hidden sm:flex items-center space-x-2 px-3 py-2 bg-slate-800 rounded-xl border border-slate-700">
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-green-400" id="user-balance">0đ</span>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative" id="user-dropdown">
                            <button class="flex items-center space-x-2 px-3 py-2 bg-slate-800 rounded-xl border border-slate-700 hover:border-game-accent transition-colors" id="user-dropdown-btn">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm font-medium text-white max-w-24 truncate hidden sm:block" id="user-name"></span>
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-56 bg-slate-800 rounded-xl border border-slate-700 shadow-xl hidden" id="dropdown-menu">
                                <div class="p-4 border-b border-slate-700">
                                    <p class="text-xs text-slate-500">Đăng nhập với</p>
                                    <p class="text-white font-medium truncate" id="dropdown-email"></p>
                                </div>
                                <div class="py-2">
                                    <a href="{{ url('/profile') }}" class="flex items-center space-x-3 px-4 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Tài khoản</span>
                                    </a>
                                    <a href="{{ url('/orders') }}" class="flex items-center space-x-3 px-4 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>Đơn hàng</span>
                                    </a>
                                    <a href="{{ url('/wallet') }}" class="flex items-center space-x-3 px-4 py-2.5 text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        <span>Ví tiền</span>
                                    </a>
                                    <a href="{{ url('/database') }}" id="admin-dashboard-link" class="hidden items-center space-x-3 px-4 py-2.5 text-game-accent hover:bg-slate-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>Quản trị</span>
                                    </a>
                                </div>
                                <div class="border-t border-slate-700 py-2">
                                    <button onclick="handleLogout()" class="w-full flex items-center space-x-3 px-4 py-2.5 text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Đăng xuất</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cart -->
                    <a href="{{ url('/cart') }}" class="flex items-center space-x-2 px-4 py-2 bg-game-accent rounded-xl hover:bg-game-accent-hover transition-colors">
                        <span class="text-sm font-medium text-white hidden sm:block">Giỏ hàng</span>
                        <div class="relative">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 rounded-full text-xs font-bold flex items-center justify-center text-white" id="cart-count">0</span>
                        </div>
                    </a>
                    
                    <!-- Mobile Menu Button -->
                    <button class="lg:hidden p-2 text-slate-400 hover:text-white transition-colors" id="mobile-menu-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation Bar -->
    <div class="bg-slate-800 border-t border-slate-700 hidden lg:block">
        <div class="container mx-auto px-4">
            <div class="flex items-center h-12 gap-1">
                <!-- Categories Dropdown -->
                <div class="relative group" id="categories-dropdown">
                    <button class="flex items-center space-x-2 px-4 py-2 bg-game-accent rounded-lg text-white font-medium hover:bg-game-accent-hover transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span>Danh mục</span>
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute top-full left-0 pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="bg-slate-800 rounded-xl border border-slate-700 shadow-xl p-4 min-w-[280px]" id="categories-dropdown-menu">
                            <div class="flex items-center justify-center py-4" id="categories-loading">
                                <div class="animate-spin w-6 h-6 border-2 border-game-accent border-t-transparent rounded-full"></div>
                            </div>
                            <div class="grid grid-cols-1 gap-1 hidden" id="categories-list"></div>
                            <div class="pt-3 mt-3 border-t border-slate-700 hidden" id="categories-footer">
                                <a href="{{ url('/store') }}" class="flex items-center justify-center gap-2 text-sm font-medium text-game-accent hover:text-game-accent-hover transition-colors">
                                    Xem tất cả danh mục
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <nav class="flex items-center ml-6 space-x-1">
                    <a href="{{ url('/') }}" class="px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors text-sm font-medium">
                        Trang chủ
                    </a>
                    <a href="{{ url('/store') }}" class="px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors text-sm font-medium">
                        Khám phá
                    </a>
                    <a href="{{ url('/news') }}" class="px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors text-sm font-medium">
                        Tin tức
                    </a>
                    <a href="{{ url('/support') }}" class="px-4 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors text-sm font-medium">
                        Hỗ trợ
                    </a>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div class="lg:hidden hidden bg-slate-900 border-t border-slate-700" id="mobile-menu">
        <div class="container mx-auto px-4 py-4">
            <!-- Mobile Search -->
            <div class="relative mb-4">
                <input type="text" 
                       placeholder="Tìm kiếm game..." 
                       class="w-full px-4 py-3 pl-10 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-game-accent transition-colors"
                       id="mobile-search">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            <!-- Mobile Nav Links -->
            <div class="flex flex-col space-y-2">
                <a href="{{ url('/') }}" class="px-4 py-3 text-white hover:bg-slate-800 rounded-lg transition-colors">Trang chủ</a>
                <a href="{{ url('/store') }}" class="px-4 py-3 text-slate-300 hover:bg-slate-800 rounded-lg transition-colors">Khám phá</a>
                <a href="{{ url('/categories') }}" class="px-4 py-3 text-slate-300 hover:bg-slate-800 rounded-lg transition-colors">Danh mục</a>
            </div>

            <!-- Mobile User Menu (Guest) -->
            <div class="pt-4 mt-4 border-t border-slate-700 flex gap-3" id="mobile-guest-menu" style="display: none;">
                <a href="{{ url('/login') }}" class="flex-1 py-3 text-center text-slate-300 border border-slate-600 rounded-lg hover:border-game-accent transition-colors">Đăng nhập</a>
                <a href="{{ url('/register') }}" class="flex-1 py-3 text-center bg-game-accent text-white rounded-lg font-medium hover:bg-game-accent-hover transition-colors">Đăng ký</a>
            </div>

            <!-- Mobile User Menu (Logged In) -->
            <div class="pt-4 mt-4 border-t border-slate-700" id="mobile-user-menu" style="display: none;">
                <div class="flex items-center space-x-3 mb-4 px-4 py-3 bg-slate-800 rounded-lg">
                    <img src="" alt="" class="w-10 h-10 rounded-full object-cover border-2 border-game-accent" id="mobile-user-avatar">
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-medium truncate" id="mobile-user-name"></p>
                        <p class="text-sm text-slate-400 truncate" id="mobile-user-email"></p>
                    </div>
                </div>
                <div class="flex items-center justify-between px-4 py-3 bg-slate-800 rounded-lg mb-4">
                    <span class="text-slate-400">Số dư:</span>
                    <span class="text-green-400 font-bold" id="mobile-user-balance">0đ</span>
                </div>
                <div class="space-y-1">
                    <a href="{{ url('/profile') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Tài khoản</span>
                    </a>
                    <a href="{{ url('/orders') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>Đơn hàng</span>
                    </a>
                    <a href="{{ url('/database') }}" id="mobile-admin-link" class="hidden items-center space-x-3 px-4 py-3 rounded-lg text-game-accent hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Quản trị</span>
                    </a>
                    <button onclick="handleLogout()" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Đăng xuất</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    const HEADER_BASE_URL = '{{ url("/") }}';
    const HEADER_API_BASE_URL = '{{ url("/api/products") }}';

    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    mobileMenuBtn?.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // User dropdown toggle
    const userDropdownBtn = document.getElementById('user-dropdown-btn');
    const dropdownMenu = document.getElementById('dropdown-menu');
    
    userDropdownBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownMenu.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!document.getElementById('user-dropdown')?.contains(e.target)) {
            dropdownMenu?.classList.add('hidden');
        }
    });

    // Check auth status and update UI
    function updateAuthUI() {
        const token = localStorage.getItem('auth_token');
        const userStr = localStorage.getItem('user');
        
        const guestMenu = document.getElementById('guest-menu');
        const userMenu = document.getElementById('user-menu');
        const mobileGuestMenu = document.getElementById('mobile-guest-menu');
        const mobileUserMenu = document.getElementById('mobile-user-menu');

        if (token && userStr) {
            try {
                const user = JSON.parse(userStr);
                
                if (guestMenu) guestMenu.style.display = 'none';
                if (userMenu) userMenu.style.display = 'flex';
                if (mobileGuestMenu) mobileGuestMenu.style.display = 'none';
                if (mobileUserMenu) mobileUserMenu.style.display = 'block';
                
                const defaultAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=6366f1&color=fff&size=64';
                
                const userName = document.getElementById('user-name');
                const userBalance = document.getElementById('user-balance');
                const dropdownEmail = document.getElementById('dropdown-email');
                
                if (userName) userName.textContent = user.name;
                if (userBalance) userBalance.textContent = formatCurrency(user.balance || 0);
                if (dropdownEmail) dropdownEmail.textContent = user.email;
                
                const mobileUserAvatar = document.getElementById('mobile-user-avatar');
                const mobileUserName = document.getElementById('mobile-user-name');
                const mobileUserEmail = document.getElementById('mobile-user-email');
                const mobileUserBalance = document.getElementById('mobile-user-balance');
                
                if (mobileUserAvatar) mobileUserAvatar.src = user.avatar || defaultAvatar;
                if (mobileUserName) mobileUserName.textContent = user.name;
                if (mobileUserEmail) mobileUserEmail.textContent = user.email;
                if (mobileUserBalance) mobileUserBalance.textContent = formatCurrency(user.balance || 0);
                
                const adminLink = document.getElementById('admin-dashboard-link');
                const mobileAdminLink = document.getElementById('mobile-admin-link');
                
                if (user.role === 'admin') {
                    if (adminLink) { adminLink.classList.remove('hidden'); adminLink.classList.add('flex'); }
                    if (mobileAdminLink) { mobileAdminLink.classList.remove('hidden'); mobileAdminLink.classList.add('flex'); }
                }
                
            } catch (e) {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                showGuestMenu();
            }
        } else {
            showGuestMenu();
        }
    }
    
    function showGuestMenu() {
        const guestMenu = document.getElementById('guest-menu');
        const userMenu = document.getElementById('user-menu');
        const mobileGuestMenu = document.getElementById('mobile-guest-menu');
        const mobileUserMenu = document.getElementById('mobile-user-menu');
        
        if (guestMenu) guestMenu.style.display = 'flex';
        if (userMenu) userMenu.style.display = 'none';
        if (mobileGuestMenu) mobileGuestMenu.style.display = 'flex';
        if (mobileUserMenu) mobileUserMenu.style.display = 'none';
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }

    async function handleLogout() {
        const token = localStorage.getItem('auth_token');
        const baseUrl = '{{ url("/") }}';
        
        if (token) {
            try {
                await fetch(baseUrl + '/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
            } catch (e) {}
        }
        
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = baseUrl;
    }

    // Search suggestions (header)
    let headerSearchTimeout = null;

    function hideHeaderSearchSuggestions() {
        const box = document.getElementById('header-search-suggestions');
        if (box) {
            box.classList.add('hidden');
            box.innerHTML = '';
        }
    }

    async function loadHeaderSearchSuggestions(query) {
        const box = document.getElementById('header-search-suggestions');
        if (!box) return;

        if (!query || query.length < 2) {
            hideHeaderSearchSuggestions();
            return;
        }

        try {
            const url = `${HEADER_API_BASE_URL}?per_page=5&search=${encodeURIComponent(query)}&sort_by=view_count&sort_order=desc`;
            const res = await fetch(url);
            const data = await res.json();

            if (!data.success || !data.data || data.data.length === 0) {
                hideHeaderSearchSuggestions();
                return;
            }

            box.innerHTML = data.data.map(product => `
                <button type="button"
                        class="w-full text-left px-4 py-2 flex items-center gap-2 hover:bg-slate-800 transition-colors"
                        data-id="${product.id}"
                        data-title="${(product.title || '').replace(/"/g, '&quot;')}">
                    <span class="flex-1 truncate text-slate-100">${product.title || ''}</span>
                    ${product.category ? `<span class="px-2 py-0.5 bg-slate-800 text-slate-400 rounded text-[11px] flex-shrink-0">${product.category}</span>` : ''}
                </button>
            `).join('');

            box.classList.remove('hidden');
        } catch (e) {
            console.error('Error loading header search suggestions:', e);
            hideHeaderSearchSuggestions();
        }
    }

    // Search functionality (header + mobile)
    const headerSearchInput = document.getElementById('header-search');
    const mobileSearchInput = document.getElementById('mobile-search');
    const headerSuggestionsBox = document.getElementById('header-search-suggestions');

    if (headerSearchInput) {
        // Gợi ý khi gõ
        headerSearchInput.addEventListener('input', (e) => {
            const value = e.target.value.trim();
            clearTimeout(headerSearchTimeout);
            headerSearchTimeout = setTimeout(() => {
                loadHeaderSearchSuggestions(value);
            }, 250);
        });

        // Enter để chuyển sang trang store với search
        headerSearchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = headerSearchInput.value.trim();
                if (query) {
                    hideHeaderSearchSuggestions();
                    window.location.href = `${HEADER_BASE_URL}/store?search=` + encodeURIComponent(query);
                }
            }
        });
    }

    if (headerSuggestionsBox) {
        headerSuggestionsBox.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-id]');
            if (!btn) return;

            const id = btn.getAttribute('data-id');
            if (id) {
                window.location.href = `${HEADER_BASE_URL}/game/${id}`;
            }
        });
    }

    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = `${HEADER_BASE_URL}/store?search=` + encodeURIComponent(query);
                }
            }
        });
    }

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            cartCount.textContent = total;
        }
    }

    // Load categories for dropdown
    let categoriesLoaded = false;
    async function loadDropdownCategories() {
        if (categoriesLoaded) return;
        
        const loading = document.getElementById('categories-loading');
        const list = document.getElementById('categories-list');
        const footer = document.getElementById('categories-footer');
        
        if (!loading || !list || !footer) return;
        
        try {
            const baseUrl = '{{ url("/") }}';
            const response = await fetch(`${baseUrl}/api/products/categories`);
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                const categoryIcons = {
                    'Hành Động': 'bolt', 'RPG': 'auto_stories', 'Phiêu Lưu': 'explore',
                    'Chiến Thuật': 'psychology', 'Giả Lập': 'computer', 'Thể Thao': 'sports_soccer',
                    'indie': 'sports_esports', 'default': 'sports_esports'
                };
                
                const categories = result.data.slice(0, 8);
                
                list.innerHTML = categories.map(cat => {
                    const icon = categoryIcons[cat.category] || categoryIcons['default'];
                    return `
                        <a href="${baseUrl}/store?category=${encodeURIComponent(cat.category)}" 
                           class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-700 transition-colors group/item">
                            <div class="w-7 h-7 bg-game-accent/20 rounded-lg flex items-center justify-center group-hover/item:bg-game-accent/30 transition-colors">
                                <span class="material-icons-outlined text-base text-game-accent">${icon}</span>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium text-white text-xs">${cat.category}</span>
                                <span class="text-[11px] text-slate-400 ml-2">(${cat.count})</span>
                            </div>
                        </a>
                    `;
                }).join('');
                
                loading.classList.add('hidden');
                list.classList.remove('hidden');
                footer.classList.remove('hidden');
                categoriesLoaded = true;
            }
        } catch (error) {
            loading.innerHTML = '<p class="text-sm text-slate-400 text-center">Không thể tải danh mục</p>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateAuthUI();
        updateCartCount();
        
        const dropdown = document.getElementById('categories-dropdown');
        if (dropdown) {
            dropdown.addEventListener('mouseenter', loadDropdownCategories);
        }
    });
</script>
