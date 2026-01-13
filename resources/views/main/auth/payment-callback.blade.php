@extends('layouts.main')

@section('title', 'Đang xử lý...')

@section('content')
<section class="min-h-screen pt-20 flex items-center justify-center">
    <div class="text-center">
        <div class="w-16 h-16 border-4 border-game-accent border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-white text-lg">Đang xử lý thanh toán...</p>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Handle callback from payment
    const baseUrl = '{{ url("/") }}';
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    const userStr = urlParams.get('user');
    const message = urlParams.get('message');
    const messageType = urlParams.get('message_type') || 'success'; // success or error

    if (token && userStr) {
        try {
            // Save token and user to localStorage
            localStorage.setItem('auth_token', token);
            localStorage.setItem('user', userStr);
            
            // Parse user to get updated balance
            const user = JSON.parse(decodeURIComponent(userStr));
            
            // Redirect to wallet page with message
            let redirectUrl = baseUrl + '/wallet';
            if (message) {
                // Add message to URL as query parameter
                redirectUrl += '?payment_' + messageType + '=' + encodeURIComponent(message);
            }
            
            window.location.href = redirectUrl;
        } catch (e) {
            console.error('Error saving auth data:', e);
            window.location.href = baseUrl + '/wallet?payment_error=' + encodeURIComponent('Có lỗi xảy ra. Vui lòng thử lại.');
        }
    } else {
        // No token provided, redirect to wallet with error
        let redirectUrl = baseUrl + '/wallet';
        if (message) {
            redirectUrl += '?payment_error=' + encodeURIComponent(message);
        } else {
            redirectUrl += '?payment_error=' + encodeURIComponent('Thanh toán thất bại. Vui lòng thử lại.');
        }
        window.location.href = redirectUrl;
    }
</script>
@endpush
