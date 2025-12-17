@extends('layouts.main')

@section('title', 'Đang xử lý...')

@section('content')
<section class="min-h-screen pt-20 flex items-center justify-center">
    <div class="text-center">
        <div class="w-16 h-16 border-4 border-game-accent border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-white text-lg">Đang xử lý đăng nhập...</p>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Handle callback from social login
    const baseUrl = '{{ url("/") }}';
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    const userStr = urlParams.get('user');

    if (token && userStr) {
        try {
            localStorage.setItem('auth_token', token);
            localStorage.setItem('user', userStr);
            
            // Parse user to check role
            const user = JSON.parse(decodeURIComponent(userStr));
            
            // Redirect based on role
            if (user.role === 'admin') {
                window.location.href = baseUrl + '/database';
            } else {
                window.location.href = baseUrl;
            }
        } catch (e) {
            console.error('Error saving auth data:', e);
            window.location.href = baseUrl + '/login?error=' + encodeURIComponent('Có lỗi xảy ra. Vui lòng thử lại.');
        }
    } else {
        window.location.href = baseUrl + '/login?error=' + encodeURIComponent('Đăng nhập thất bại. Vui lòng thử lại.');
    }
</script>
@endpush

