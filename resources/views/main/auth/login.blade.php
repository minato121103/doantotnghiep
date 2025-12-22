@extends('layouts.main')

@section('title', 'Đăng nhập')

@section('content')
<section class="min-h-screen pt-24 pb-12 flex items-center justify-center relative overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-indigo-50/50 to-purple-50/50"></div>
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=1920')] bg-cover bg-center opacity-5"></div>
        <div class="absolute top-0 left-0 w-96 h-96 bg-game-accent/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-game-purple/10 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-md mx-auto">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center space-x-2 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-game-accent to-game-purple flex items-center justify-center shadow-lg shadow-game-accent/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                        </svg>
                    </div>
                    <span class="font-display text-2xl font-bold gradient-text">eStrix</span>
                </a>
                <h1 class="font-display text-2xl md:text-3xl font-bold text-slate-800 mt-6">Chào mừng trở lại!</h1>
                <p class="text-slate-500 mt-2">Đăng nhập để tiếp tục mua sắm</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl p-6 md:p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
                <!-- Error Message -->
                <div id="error-message" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 hidden">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="error-text" class="text-sm"></span>
                    </div>
                </div>

                <!-- Social Login -->
                <div class="space-y-3 mb-6">
                    <button onclick="loginWithGoogle()" class="w-full flex items-center justify-center gap-3 px-4 py-3.5 bg-white border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 font-medium rounded-xl transition-all">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Tiếp tục với Google
                    </button>
                    
                    <button onclick="loginWithFacebook()" class="w-full flex items-center justify-center gap-3 px-4 py-3.5 bg-[#1877F2] hover:bg-[#166FE5] text-white font-medium rounded-xl transition-all shadow-lg shadow-blue-500/25">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Tiếp tục với Facebook
                    </button>
                </div>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-slate-400">hoặc</span>
                    </div>
                </div>

                <!-- Email Login Form -->
                <form id="login-form" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" required
                                   class="w-full px-4 py-3.5 pl-11 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-game-accent focus:ring-2 focus:ring-game-accent/20 focus:bg-white transition-all"
                                   placeholder="your@email.com">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p id="error-email" class="text-red-500 text-sm mt-1.5 hidden"></p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Mật khẩu</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-3.5 pl-11 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-game-accent focus:ring-2 focus:ring-game-accent/20 focus:bg-white transition-all"
                                   placeholder="••••••••">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p id="error-password" class="text-red-500 text-sm mt-1.5 hidden"></p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="remember" class="w-4 h-4 rounded border-slate-300 bg-slate-50 text-game-accent focus:ring-game-accent focus:ring-offset-0">
                            <span class="ml-2 text-sm text-slate-600">Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="{{ url('/forgot-password') }}" class="text-sm text-game-accent hover:text-game-accent-hover font-medium transition-colors">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" id="submit-btn" 
                            class="w-full py-3.5 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-xl hover:opacity-90 transition-all shadow-lg shadow-game-accent/25 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                        <span id="submit-text">Đăng nhập</span>
                        <span id="submit-loading" class="hidden">
                            <svg class="animate-spin inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang đăng nhập...
                        </span>
                    </button>
                </form>

                <!-- Register Link -->
                <p class="text-center text-slate-500 mt-6">
                    Chưa có tài khoản?
                    <a href="{{ url('/register') }}" class="text-game-accent hover:text-game-accent-hover font-semibold transition-colors">Đăng ký ngay</a>
                </p>
            </div>

            <!-- Footer Text -->
            <p class="text-center text-slate-400 text-sm mt-6">
                Bằng việc đăng nhập, bạn đồng ý với 
                <a href="#" class="text-slate-600 hover:text-game-accent transition-colors">Điều khoản dịch vụ</a>
                và 
                <a href="#" class="text-slate-600 hover:text-game-accent transition-colors">Chính sách bảo mật</a>
            </p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("/") }}';
    const API_BASE_URL = '{{ url("/api/auth") }}';

    // Toggle password visibility
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(inputId + '-eye');
        
        if (input.type === 'password') {
            input.type = 'text';
            eye.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
        } else {
            input.type = 'password';
            eye.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    }

    // Social login
    function loginWithGoogle() {
        window.location.href = BASE_URL + '/auth/google';
    }

    function loginWithFacebook() {
        window.location.href = BASE_URL + '/auth/facebook';
    }

    // Handle login form
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitLoading = document.getElementById('submit-loading');
        const errorMessage = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');

        // Clear previous errors
        clearErrors();
        errorMessage.classList.add('hidden');

        // Disable button
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');

        try {
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value
                })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                if (result.errors) {
                    displayValidationErrors(result.errors);
                }
                throw new Error(result.message || 'Đăng nhập thất bại');
            }

            // Save token and user to localStorage
            localStorage.setItem('auth_token', result.data.token);
            localStorage.setItem('user', JSON.stringify(result.data.user));

            // Redirect based on role
            const user = result.data.user;
            if (user.role === 'admin') {
                // Admin users go to database management page
                window.location.href = BASE_URL + '/database';
            } else {
                // Regular users go to homepage or previous page
                const redirectParam = new URLSearchParams(window.location.search).get('redirect');
                const redirect = redirectParam ? BASE_URL + redirectParam : BASE_URL;
                window.location.href = redirect;
            }

        } catch (error) {
            errorText.textContent = error.message;
            errorMessage.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoading.classList.add('hidden');
        }
    });

    function displayValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(`error-${field}`);
            if (errorElement) {
                errorElement.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                errorElement.classList.remove('hidden');
            }
        });
    }

    function clearErrors() {
        document.querySelectorAll('[id^="error-"]').forEach(el => {
            if (el.id !== 'error-message' && el.id !== 'error-text') {
                el.classList.add('hidden');
                el.textContent = '';
            }
        });
    }

    // Check URL for errors (from social login)
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    if (error) {
        document.getElementById('error-text').textContent = decodeURIComponent(error);
        document.getElementById('error-message').classList.remove('hidden');
    }

    // Check if already logged in
    if (localStorage.getItem('auth_token')) {
        const userStr = localStorage.getItem('user');
        if (userStr) {
            try {
                const user = JSON.parse(userStr);
                if (user.role === 'admin') {
                    window.location.href = BASE_URL + '/database';
                } else {
                    window.location.href = BASE_URL;
                }
            } catch (e) {
                window.location.href = BASE_URL;
            }
        } else {
            window.location.href = BASE_URL;
        }
    }
</script>
@endpush

