<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Database Management') - Game Store</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
        }
        .hidden-until-auth {
            display: none;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen" style="font-family: 'Be Vietnam Pro', sans-serif;">
    <!-- Loading/Auth Check -->
    <div id="auth-loading" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-gray-600">Đang kiểm tra quyền truy cập...</p>
        </div>
    </div>

    <!-- Access Denied Message -->
    <div id="access-denied" class="fixed inset-0 bg-white z-50 hidden flex items-center justify-center">
        <div class="text-center max-w-md mx-auto p-8">
            <div class="text-6xl mb-4">🚫</div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Không có quyền truy cập</h1>
            <p class="text-gray-600 mb-6" id="access-denied-message">Bạn không có quyền truy cập trang này.</p>
            <div class="space-y-3">
                <a href="{{ url('/') }}" class="block w-full bg-indigo-500 text-white py-2 rounded-lg hover:bg-indigo-600 transition">
                    ← Về trang chủ
                </a>
                <a href="{{ url('/login') }}" class="block w-full bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition">
                    Đăng nhập tài khoản khác
                </a>
            </div>
        </div>
    </div>

    <div id="main-content" class="container mx-auto px-3 sm:px-4 py-4 sm:py-6 md:py-8 hidden-until-auth">
        <div class="@yield('max-width', 'max-w-7xl') mx-auto">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        // Auth helper functions
        window.AuthHelper = {
            token: null,
            user: null,
            
            getToken() {
                return localStorage.getItem('auth_token');
            },
            
            async getUser() {
                if (this.user) return this.user;
                
                const token = this.getToken();
                if (!token) return null;
                
                try {
                    const response = await fetch('{{ url("/api/user") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        this.user = result.data || result;
                        return this.user;
                    }
                } catch (error) {
                    console.error('Auth error:', error);
                }
                
                return null;
            },
            
            isAdmin() {
                return this.user && this.user.role === 'admin';
            },
            
            isEditor() {
                return this.user && this.user.role === 'editor';
            },
            
            hasRole(...roles) {
                return this.user && roles.includes(this.user.role);
            },
            
            // Check access for current page
            async checkPageAccess(allowedRoles = ['admin']) {
                const user = await this.getUser();
                
                if (!user) {
                    return { allowed: false, reason: 'not_authenticated' };
                }
                
                if (!allowedRoles.includes(user.role)) {
                    return { allowed: false, reason: 'no_permission', userRole: user.role };
                }
                
                return { allowed: true, user: user };
            }
        };
        
        // Page access configuration
        window.pageAccess = {
            'database.index': ['admin', 'editor'],
            'database.news': ['admin', 'editor'],
            'database.create-news': ['admin', 'editor'],
            'database.edit-news': ['admin', 'editor'],
            'database.products': ['admin', 'editor'],
            'database.create-product': ['admin', 'editor'],
            'database.edit-product': ['admin', 'editor'],
            'database.discussions': ['admin', 'editor'],
            'database.users': ['admin'],
            'database.create-user': ['admin'],
            'database.edit-user': ['admin'],
            'database.orders': ['admin'],
            'database.reviews': ['admin'],
            'database.transactions': ['admin'],
            'database.steam-accounts': ['admin'],
            'database.recommendations': ['admin'],
            'database.table-structure': ['admin'],
        };
        
        // Current page route name
        window.currentRoute = '{{ Route::currentRouteName() }}';
        
        // Check access on page load
        document.addEventListener('DOMContentLoaded', async () => {
            const allowedRoles = window.pageAccess[window.currentRoute] || ['admin'];
            const result = await AuthHelper.checkPageAccess(allowedRoles);
            
            document.getElementById('auth-loading').classList.add('hidden');
            
            if (!result.allowed) {
                const deniedEl = document.getElementById('access-denied');
                const messageEl = document.getElementById('access-denied-message');
                
                if (result.reason === 'not_authenticated') {
                    messageEl.textContent = 'Vui lòng đăng nhập để truy cập trang quản trị.';
                } else {
                    messageEl.textContent = `Tài khoản của bạn (${result.userRole}) không có quyền truy cập trang này. Chỉ ${allowedRoles.join(', ')} mới có thể truy cập.`;
                }
                
                deniedEl.classList.remove('hidden');
                deniedEl.classList.add('flex');
            } else {
                document.getElementById('main-content').classList.remove('hidden-until-auth');
                
                // Dispatch event for pages to know user is authenticated
                window.dispatchEvent(new CustomEvent('authReady', { detail: result.user }));
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

