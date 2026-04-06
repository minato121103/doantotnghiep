@extends('layouts.app')

@section('title', 'Database Management')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="text-center mb-6 md:mb-8">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">Database Management</h1>
        <p class="text-sm sm:text-base text-gray-600">Quản lý và chỉnh sửa dữ liệu trong database</p>
    </div>

    <!-- User Role Badge -->
    <div id="user-role-badge" class="mb-4 hidden">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm border">
            <span class="text-sm text-gray-600">Đăng nhập với:</span>
            <span id="user-name-badge" class="font-semibold text-gray-800"></span>
            <span id="user-role-tag" class="px-2 py-0.5 text-xs font-medium rounded-full"></span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
        <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800 flex items-center gap-2">
            <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
            Thống kê tổng quan
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            <!-- Users (Admin only) -->
            <a href="{{ route('database.users') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">👥</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['users_count'] }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Users</div>
            </a>
            
            <!-- Products (Admin + Editor) -->
            <a href="{{ route('database.products') }}" data-role="admin,editor" class="role-restricted bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🎮</span>
                    <span class="text-2xl sm:text-3xl font-bold" id="products-count-text">...</span>
                </div>
                <div class="text-sm font-medium opacity-90">Products</div>
            </a>
            
            <!-- News (Admin + Editor) -->
            <a href="{{ route('database.news') }}" data-role="admin,editor" class="role-restricted bg-gradient-to-br from-orange-500 to-orange-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">📰</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['news_count'] }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">News</div>
            </a>
            
            <!-- Orders (Admin only) -->
            <a href="{{ route('database.orders') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🛒</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['orders_count'] }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Orders</div>
            </a>
            
            <!-- Reviews (Admin only) -->
            <a href="{{ route('database.reviews') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-yellow-500 to-yellow-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">⭐</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['reviews_count'] }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Reviews</div>
            </a>
            
            <!-- Transactions (Admin only) -->
            <a href="{{ route('database.transactions') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">💰</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['transactions_count'] }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Transactions</div>
            </a>
            
            <!-- Steam Accounts (Admin only) -->
            <a href="{{ route('database.steam-accounts') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-slate-600 to-slate-700 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🎯</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['steam_accounts_count'] }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Steam Accounts</div>
            </a>
            
            <!-- Steam Account Games (Admin only) -->
            <a href="{{ route('database.steam-account-games') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-indigo-500 to-violet-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🎲</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['steam_account_games_count'] ?? 0 }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Account Games</div>
            </a>
            
            <!-- Discussions (Admin + Editor) -->
            <a href="{{ route('database.discussions') }}" data-role="admin,editor" class="role-restricted bg-gradient-to-br from-pink-500 to-pink-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">💬</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['discussions_count'] }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Discussions</div>
            </a>
            
            <!-- Payment Methods (Admin only) -->
            <div data-role="admin" class="role-restricted bg-gradient-to-br from-cyan-500 to-cyan-600 text-white p-4 rounded-xl hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">💳</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['payment_methods_count'] }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Payment Methods</div>
            </div>
            
            <!-- Support Tickets (Admin only) -->
            <a href="{{ route('database.support-tickets') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-teal-500 to-cyan-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🎫</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['support_tickets_count'] ?? 0 }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Support Tickets</div>
            </a>
            
            <!-- Community Posts (Admin + Editor) -->
            <a href="{{ route('database.community-posts') }}" data-role="admin,editor" class="role-restricted bg-gradient-to-br from-sky-500 to-blue-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🌐</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['community_posts_count'] ?? 0 }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Community Posts</div>
            </a>

            <!-- Promotions (Admin only) -->
            <a href="{{ route('database.promotions') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-rose-500 to-pink-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🏷️</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['promotions_count'] ?? 0 }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Promotions</div>
            </a>

            <!-- Coupons (Admin only) -->
            <a href="{{ route('database.coupons') }}" data-role="admin" class="role-restricted bg-gradient-to-br from-amber-500 to-orange-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🎟️</span>
                    <span class="text-2xl sm:text-3xl font-bold">{{ $stats['coupons_count'] ?? 0 }}</span>
                </div>
                <div class="text-sm font-medium opacity-90">Coupons</div>
            </a>
            
            <!-- Back to Home -->
            <a href="{{ route('home') }}" class="bg-gradient-to-br from-gray-500 to-gray-600 text-white p-4 rounded-xl hover:shadow-lg transition-all duration-200 group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🏠</span>
                    <span class="text-2xl sm:text-3xl font-bold">←</span>
                </div>
                <div class="text-sm font-medium opacity-90">Back to Home</div>
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Content Management (Admin + Editor) -->
        <div data-role="admin,editor" class="role-restricted bg-white rounded-lg shadow-md p-4 sm:p-6 hidden">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800 flex items-center gap-2">
                <span class="w-1 h-6 bg-green-500 rounded-full"></span>
                Quản lý nội dung
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('database.create-product') }}" class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors group">
                    <div class="w-10 h-10 bg-green-500 text-white rounded-lg flex items-center justify-center text-lg">➕</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-green-600">Thêm Product</div>
                        <div class="text-xs text-gray-500">Tạo sản phẩm mới</div>
                    </div>
                </a>
                <a href="{{ route('database.products') }}" class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors group">
                    <div class="w-10 h-10 bg-green-500 text-white rounded-lg flex items-center justify-center text-lg">🎮</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-green-600">Quản lý Products</div>
                        <div class="text-xs text-gray-500">Sửa, xóa sản phẩm</div>
                    </div>
                </a>
                <a href="{{ route('database.create-news') }}" class="flex items-center gap-3 p-3 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition-colors group">
                    <div class="w-10 h-10 bg-orange-500 text-white rounded-lg flex items-center justify-center text-lg">➕</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-orange-600">Thêm News</div>
                        <div class="text-xs text-gray-500">Tạo tin tức mới</div>
                    </div>
                </a>
                <a href="{{ route('database.news') }}" class="flex items-center gap-3 p-3 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 transition-colors group">
                    <div class="w-10 h-10 bg-orange-500 text-white rounded-lg flex items-center justify-center text-lg">📰</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-orange-600">Quản lý News</div>
                        <div class="text-xs text-gray-500">Sửa, xóa tin tức</div>
                    </div>
                </a>
                <a href="{{ route('database.discussions') }}" class="flex items-center gap-3 p-3 bg-pink-50 border border-pink-200 rounded-lg hover:bg-pink-100 transition-colors group">
                    <div class="w-10 h-10 bg-pink-500 text-white rounded-lg flex items-center justify-center text-lg">💬</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-pink-600">Quản lý Discussions</div>
                        <div class="text-xs text-gray-500">Duyệt và quản lý thảo luận sản phẩm</div>
                    </div>
                </a>
                <a href="{{ route('database.community-posts') }}" class="flex items-center gap-3 p-3 bg-sky-50 border border-sky-200 rounded-lg hover:bg-sky-100 transition-colors group">
                    <div class="w-10 h-10 bg-sky-500 text-white rounded-lg flex items-center justify-center text-lg">🌐</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-sky-600">Quản lý Cộng đồng</div>
                        <div class="text-xs text-gray-500">Kiểm duyệt bài viết cộng đồng</div>
                    </div>
                </a>
                <a href="{{ route('database.promotions') }}" class="flex items-center gap-3 p-3 bg-rose-50 border border-rose-200 rounded-lg hover:bg-rose-100 transition-colors group">
                    <div class="w-10 h-10 bg-rose-500 text-white rounded-lg flex items-center justify-center text-lg">🏷️</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-rose-600">Quản lý Ưu đãi</div>
                        <div class="text-xs text-gray-500">Tạo và quản lý game giảm giá</div>
                    </div>
                </a>
                <a href="{{ route('database.coupons') }}" class="flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors group">
                    <div class="w-10 h-10 bg-amber-500 text-white rounded-lg flex items-center justify-center text-lg">🎟️</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-amber-600">Quản lý Mã giảm giá</div>
                        <div class="text-xs text-gray-500">Tạo và quản lý mã ưu đãi</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- User Management (Admin only) -->
        <div data-role="admin" class="role-restricted bg-white rounded-lg shadow-md p-4 sm:p-6 hidden">
            <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800 flex items-center gap-2">
                <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
                Quản lý hệ thống (Admin)
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('database.create-user') }}" class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors group">
                    <div class="w-10 h-10 bg-blue-500 text-white rounded-lg flex items-center justify-center text-lg">➕</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-blue-600">Thêm User</div>
                        <div class="text-xs text-gray-500">Tạo người dùng mới</div>
                    </div>
                </a>
                <a href="{{ route('database.users') }}" class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors group">
                    <div class="w-10 h-10 bg-blue-500 text-white rounded-lg flex items-center justify-center text-lg">👥</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-blue-600">Quản lý Users</div>
                        <div class="text-xs text-gray-500">Sửa, xóa người dùng</div>
                    </div>
                </a>
                <a href="{{ route('database.recommendations') }}" class="flex items-center gap-3 p-3 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors group">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-500 text-white rounded-lg flex items-center justify-center text-lg">🤖</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-purple-600">AI Recommendation</div>
                        <div class="text-xs text-gray-500">Hệ thống gợi ý sản phẩm</div>
                    </div>
                </a>
                <a href="{{ route('database.support-tickets') }}" class="flex items-center gap-3 p-3 bg-teal-50 border border-teal-200 rounded-lg hover:bg-teal-100 transition-colors group">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-500 text-white rounded-lg flex items-center justify-center text-lg">🎫</div>
                    <div>
                        <div class="font-semibold text-gray-800 group-hover:text-teal-600">Hỗ trợ Tickets</div>
                        <div class="text-xs text-gray-500">Quản lý yêu cầu hỗ trợ</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Database Tables (Admin only) -->
    <div data-role="admin" class="role-restricted bg-white rounded-lg shadow-md p-4 sm:p-6 hidden">
        <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800 flex items-center gap-2">
            <span class="w-1 h-6 bg-gray-500 rounded-full"></span>
            Database Tables
        </h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 sm:px-6 py-3 border-b border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Table Name
                        </th>
                        <th class="px-4 sm:px-6 py-3 border-b border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-4 sm:px-6 py-3 border-b border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $tableDescriptions = [
                            'users' => ['desc' => 'Quản lý tài khoản người dùng', 'icon' => '👥', 'color' => 'blue'],
                            'product_simple' => ['desc' => 'Danh sách sản phẩm game', 'icon' => '🎮', 'color' => 'green'],
                            'news' => ['desc' => 'Tin tức công nghệ', 'icon' => '📰', 'color' => 'orange'],
                            'orders' => ['desc' => 'Đơn hàng của khách', 'icon' => '🛒', 'color' => 'purple'],
                            'order_items' => ['desc' => 'Chi tiết đơn hàng', 'icon' => '📦', 'color' => 'purple'],
                            'reviews' => ['desc' => 'Đánh giá sản phẩm', 'icon' => '⭐', 'color' => 'yellow'],
                            'transactions' => ['desc' => 'Giao dịch thanh toán', 'icon' => '💰', 'color' => 'emerald'],
                            'steam_accounts' => ['desc' => 'Tài khoản Steam', 'icon' => '🎯', 'color' => 'slate'],
                            'steam_account_games' => ['desc' => 'Game trong tài khoản Steam', 'icon' => '🎲', 'color' => 'slate'],
                            'product_discussions' => ['desc' => 'Thảo luận sản phẩm', 'icon' => '💬', 'color' => 'pink'],
                            'payment_methods' => ['desc' => 'Phương thức thanh toán', 'icon' => '💳', 'color' => 'cyan'],
                            'user_interactions' => ['desc' => 'Tương tác người dùng (AI)', 'icon' => '🤖', 'color' => 'indigo'],
                            'product_similarities' => ['desc' => 'Độ tương đồng sản phẩm (AI)', 'icon' => '🔗', 'color' => 'indigo'],
                            'user_recommendations' => ['desc' => 'Gợi ý cho người dùng (AI)', 'icon' => '🎯', 'color' => 'indigo'],
                            'support_tickets' => ['desc' => 'Yêu cầu hỗ trợ từ người dùng', 'icon' => '🎫', 'color' => 'teal'],
                            'community_posts' => ['desc' => 'Bài viết cộng đồng', 'icon' => '🌐', 'color' => 'sky'],
                            'community_post_likes' => ['desc' => 'Like bài viết cộng đồng', 'icon' => '❤️', 'color' => 'red'],
                            'community_post_comments' => ['desc' => 'Bình luận bài viết cộng đồng', 'icon' => '💬', 'color' => 'sky'],
                            'community_comment_likes' => ['desc' => 'Like bình luận cộng đồng', 'icon' => '❤️', 'color' => 'red'],
                            'friendships' => ['desc' => 'Quan hệ bạn bè', 'icon' => '🤝', 'color' => 'blue'],
                            'messages' => ['desc' => 'Tin nhắn người dùng', 'icon' => '✉️', 'color' => 'violet'],
                            'promotions' => ['desc' => 'Ưu đãi giảm giá game', 'icon' => '🏷️', 'color' => 'rose'],
                            'promotion_product' => ['desc' => 'Game trong ưu đãi', 'icon' => '🏷️', 'color' => 'rose'],
                            'coupons' => ['desc' => 'Mã ưu đãi giảm giá', 'icon' => '🎟️', 'color' => 'amber'],
                        ];
                    @endphp
                    @foreach($stats['tables'] as $table)
                    @php
                        $info = $tableDescriptions[$table] ?? ['desc' => 'Bảng dữ liệu', 'icon' => '📋', 'color' => 'gray'];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 sm:px-6 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $info['icon'] }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $table }}</span>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3">
                            <span class="text-sm text-gray-500">{{ $info['desc'] }}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('database.table-structure', $table) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200 transition-colors">
                                    View Structure
                                </a>
                                @if($table === 'users')
                                    <a href="{{ route('database.users') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'product_simple')
                                    <a href="{{ route('database.products') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'news')
                                    <a href="{{ route('database.news') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'orders')
                                    <a href="{{ route('database.orders') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full hover:bg-purple-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'reviews')
                                    <a href="{{ route('database.reviews') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full hover:bg-yellow-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'transactions')
                                    <a href="{{ route('database.transactions') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 rounded-full hover:bg-emerald-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'steam_accounts')
                                    <a href="{{ route('database.steam-accounts') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-slate-700 bg-slate-100 rounded-full hover:bg-slate-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'steam_account_games')
                                    <a href="{{ route('database.steam-account-games') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-full hover:bg-indigo-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'product_discussions')
                                    <a href="{{ route('database.discussions') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-pink-700 bg-pink-100 rounded-full hover:bg-pink-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif(in_array($table, ['user_interactions', 'product_similarities', 'user_recommendations']))
                                    <a href="{{ route('database.recommendations') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full hover:bg-purple-200 transition-colors">
                                        AI System
                                    </a>
                                @elseif($table === 'support_tickets')
                                    <a href="{{ route('database.support-tickets') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-teal-700 bg-teal-100 rounded-full hover:bg-teal-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'community_posts')
                                    <a href="{{ route('database.community-posts') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-sky-700 bg-sky-100 rounded-full hover:bg-sky-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'promotions' || $table === 'promotion_product')
                                    <a href="{{ route('database.promotions') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-rose-700 bg-rose-100 rounded-full hover:bg-rose-200 transition-colors">
                                        Manage
                                    </a>
                                @elseif($table === 'coupons')
                                    <a href="{{ route('database.coupons') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-100 rounded-full hover:bg-amber-200 transition-colors">
                                        Manage
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // API Configuration
    const API_BASE_URL = '{{ url("/api/products") }}';
    
    // Load products count from API
    async function loadProductsCount() {
        try {
            const response = await fetch(`${API_BASE_URL}?per_page=1`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            const productsCountText = document.getElementById('products-count-text');
            
            if (result.success && result.pagination && productsCountText) {
                productsCountText.textContent = result.pagination.total;
            } else if (productsCountText) {
                productsCountText.textContent = '0';
            }
        } catch (error) {
            console.error('Error loading products count:', error);
            const productsCountText = document.getElementById('products-count-text');
            if (productsCountText) {
                productsCountText.textContent = '0';
            }
        }
    }
    
    // Show elements based on user role
    function showRoleBasedElements(userRole) {
        document.querySelectorAll('.role-restricted').forEach(el => {
            const allowedRoles = el.dataset.role?.split(',') || ['admin'];
            if (allowedRoles.includes(userRole)) {
                el.classList.remove('hidden');
            }
        });
        
        // Show user badge
        const badge = document.getElementById('user-role-badge');
        if (badge && window.AuthHelper.user) {
            document.getElementById('user-name-badge').textContent = window.AuthHelper.user.name;
            const roleTag = document.getElementById('user-role-tag');
            if (userRole === 'admin') {
                roleTag.textContent = 'Admin';
                roleTag.className = 'px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700';
            } else if (userRole === 'editor') {
                roleTag.textContent = 'Editor';
                roleTag.className = 'px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700';
            }
            badge.classList.remove('hidden');
        }
    }
    
    // Listen for auth ready event
    window.addEventListener('authReady', (e) => {
        const user = e.detail;
        if (user) {
            showRoleBasedElements(user.role);
            loadProductsCount();
        }
    });
</script>
@endpush
