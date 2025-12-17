@extends('layouts.main')

@section('title', 'Đăng ký')

@section('content')
<section class="min-h-screen pt-20 flex items-center justify-center relative overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-game-dark via-game-darker to-game-dark"></div>
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=1920')] bg-cover bg-center opacity-10"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-game-purple/20 rounded-full blur-3xl translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-game-accent/20 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 py-12">
        <div class="max-w-md mx-auto">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center space-x-2 group">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-game-accent to-game-purple flex items-center justify-center glow-effect">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                        </svg>
                    </div>
                    <span class="font-display text-2xl font-bold gradient-text">eStrix</span>
                </a>
                <h1 class="font-display text-2xl md:text-3xl font-bold text-white mt-6">Tạo tài khoản</h1>
                <p class="text-gray-400 mt-2">Đăng ký để bắt đầu mua game</p>
            </div>

            <!-- Register Form -->
            <div class="bg-game-card/80 backdrop-blur-md rounded-2xl p-6 md:p-8 border border-game-border">
                <!-- Error Message -->
                <div id="error-message" class="bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg mb-6 hidden">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="error-text"></span>
                    </div>
                </div>

                <!-- Success Message -->
                <div id="success-message" class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg mb-6 hidden">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="success-text"></span>
                    </div>
                </div>

                <!-- Social Register -->
                <div class="space-y-3 mb-6">
                    <button onclick="loginWithGoogle()" class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-white hover:bg-gray-100 text-gray-800 font-medium rounded-xl transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Đăng ký với Google
                    </button>
                    
                    <button onclick="loginWithFacebook()" class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-[#1877F2] hover:bg-[#166FE5] text-white font-medium rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Đăng ký với Facebook
                    </button>
                </div>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-game-border"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-game-card text-gray-500">hoặc đăng ký với email</span>
                    </div>
                </div>

                <!-- Email Register Form -->
                <form id="register-form" class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Họ và tên</label>
                        <div class="relative">
                            <input type="text" id="name" name="name" required
                                   class="w-full px-4 py-3 pl-11 bg-game-dark border border-game-border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-game-accent transition-colors"
                                   placeholder="Nguyễn Văn A">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <p id="error-name" class="text-red-400 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" required
                                   class="w-full px-4 py-3 pl-11 bg-game-dark border border-game-border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-game-accent transition-colors"
                                   placeholder="your@email.com">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p id="error-email" class="text-red-400 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Mật khẩu</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required minlength="8"
                                   class="w-full px-4 py-3 pl-11 pr-11 bg-game-dark border border-game-border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-game-accent transition-colors"
                                   placeholder="••••••••">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-400">
                                <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-gray-500 text-xs mt-1">Tối thiểu 8 ký tự</p>
                        <p id="error-password" class="text-red-400 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Xác nhận mật khẩu</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="w-full px-4 py-3 pl-11 pr-11 bg-game-dark border border-game-border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-game-accent transition-colors"
                                   placeholder="••••••••">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-400">
                                <svg id="password_confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" id="terms" required class="w-4 h-4 mt-1 rounded border-game-border bg-game-dark text-game-accent focus:ring-game-accent focus:ring-offset-game-dark">
                        <label for="terms" class="ml-2 text-sm text-gray-400">
                            Tôi đồng ý với <a href="{{ url('/terms') }}" class="text-game-accent hover:underline">Điều khoản dịch vụ</a> và <a href="{{ url('/privacy') }}" class="text-game-accent hover:underline">Chính sách bảo mật</a>
                        </label>
                    </div>

                    <button type="submit" id="submit-btn" 
                            class="w-full py-3 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-xl hover:opacity-90 transition-all glow-effect disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="submit-text">Đăng ký</span>
                        <span id="submit-loading" class="hidden">
                            <svg class="animate-spin inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang đăng ký...
                        </span>
                    </button>
                </form>

                <!-- Login Link -->
                <p class="text-center text-gray-400 mt-6">
                    Đã có tài khoản?
                    <a href="{{ url('/login') }}" class="text-game-accent hover:text-game-accent-hover font-medium transition-colors">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("/") }}';
    const API_BASE_URL = BASE_URL + '/api/auth';

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

    // Handle register form
    document.getElementById('register-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitLoading = document.getElementById('submit-loading');
        const errorMessage = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');
        const successMessage = document.getElementById('success-message');
        const successText = document.getElementById('success-text');

        // Clear previous errors
        clearErrors();
        errorMessage.classList.add('hidden');
        successMessage.classList.add('hidden');

        // Disable button
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');

        try {
            const response = await fetch(`${API_BASE_URL}/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                    password_confirmation: document.getElementById('password_confirmation').value
                })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                if (result.errors) {
                    displayValidationErrors(result.errors);
                }
                throw new Error(result.message || 'Đăng ký thất bại');
            }

            // Save token and user to localStorage
            localStorage.setItem('auth_token', result.data.token);
            localStorage.setItem('user', JSON.stringify(result.data.user));

            // Show success message
            successText.textContent = 'Đăng ký thành công! Đang chuyển hướng...';
            successMessage.classList.remove('hidden');

            // Redirect to homepage
            setTimeout(() => {
                window.location.href = BASE_URL;
            }, 1500);

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


</script>
@endpush

