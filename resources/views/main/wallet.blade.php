@extends('layouts.main')

@section('title', 'Ví tiền')

@section('content')
    <!-- Hero Section -->
    <section class="relative pt-36 pb-8 overflow-hidden bg-gradient-to-br from-slate-50 via-indigo-50/50 to-purple-50/50">
        <!-- Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1920')] bg-cover bg-center opacity-5"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-game-accent/5 via-transparent to-game-purple/5"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-20">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="font-display text-4xl md:text-5xl font-bold mb-2">
                        <span class="gradient-text">Ví tiền</span>
                    </h1>
                    <p class="text-slate-600">Quản lý số dư và lịch sử giao dịch của bạn</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Wallet Content -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 bg-game-green/10 border border-game-green/20 text-game-green px-6 py-4 rounded-xl flex items-center gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl flex items-center gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Balance & Deposit -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Balance Card -->
                    <div class="bg-gradient-to-br from-game-accent to-game-purple rounded-2xl p-6 text-white shadow-xl">
                        <div class="mb-4">
                            <p class="text-slate-200 text-sm mb-1">Số dư hiện tại</p>
                            <h2 class="font-display text-3xl md:text-4xl font-bold" id="current-balance">
                                <span class="inline-flex items-center gap-2">
                                    <span class="animate-pulse">...</span>
                                </span>
                            </h2>
                        </div>
                        <div class="pt-4 border-t border-white/20">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-200">Tổng nạp</span>
                                <span class="font-semibold" id="total-deposit">...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Deposit Card -->
                    <div class="bg-white rounded-2xl border border-game-border p-6 shadow-lg">
                        <h3 class="font-heading text-xl font-bold text-slate-800 mb-4">Nạp tiền</h3>
                        
                        <form id="deposit-form" class="space-y-4">
                            <!-- Amount Input -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">
                                    Số tiền nạp (VNĐ)
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           id="deposit-amount" 
                                           name="amount"
                                           min="10000" 
                                           max="100000000" 
                                           step="10000"
                                           placeholder="Nhập số tiền (tối thiểu 10,000đ)"
                                           class="w-full px-4 py-3 border border-game-border rounded-xl focus:outline-none focus:border-game-accent focus:ring-2 focus:ring-game-accent/20 transition-colors text-slate-800"
                                           required>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">đ</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">Số tiền tối thiểu: 10,000đ - Tối đa: 100,000,000đ</p>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">
                                    Chọn nhanh
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" class="quick-amount-btn px-4 py-2 border border-game-border rounded-lg hover:bg-game-accent hover:text-white hover:border-game-accent transition-colors text-sm font-medium" data-amount="50000">
                                        50k
                                    </button>
                                    <button type="button" class="quick-amount-btn px-4 py-2 border border-game-border rounded-lg hover:bg-game-accent hover:text-white hover:border-game-accent transition-colors text-sm font-medium" data-amount="100000">
                                        100k
                                    </button>
                                    <button type="button" class="quick-amount-btn px-4 py-2 border border-game-border rounded-lg hover:bg-game-accent hover:text-white hover:border-game-accent transition-colors text-sm font-medium" data-amount="200000">
                                        200k
                                    </button>
                                    <button type="button" class="quick-amount-btn px-4 py-2 border border-game-border rounded-lg hover:bg-game-accent hover:text-white hover:border-game-accent transition-colors text-sm font-medium" data-amount="500000">
                                        500k
                                    </button>
                                    <button type="button" class="quick-amount-btn px-4 py-2 border border-game-border rounded-lg hover:bg-game-accent hover:text-white hover:border-game-accent transition-colors text-sm font-medium" data-amount="1000000">
                                        1M
                                    </button>
                                    <button type="button" class="quick-amount-btn px-4 py-2 border border-game-border rounded-lg hover:bg-game-accent hover:text-white hover:border-game-accent transition-colors text-sm font-medium" data-amount="2000000">
                                        2M
                                    </button>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">
                                    Phương thức thanh toán
                                </label>
                                <div class="bg-slate-50 border border-game-border rounded-xl p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">VNPay</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-slate-800">VNPay</p>
                                            <p class="text-xs text-slate-500">Thanh toán qua cổng VNPay</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" 
                                    id="deposit-submit"
                                    class="w-full px-6 py-4 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-xl hover:opacity-90 transition-all glow-effect flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Nạp tiền ngay</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Transaction History -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-game-border p-6 shadow-lg">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-heading text-xl font-bold text-slate-800">Lịch sử giao dịch</h3>
                        </div>

                        <!-- Loading State -->
                        <div id="transactions-loading" class="text-center py-16">
                            <div class="inline-flex flex-col items-center">
                                <div class="animate-spin w-12 h-12 border-4 border-game-accent border-t-transparent rounded-full mb-4"></div>
                                <p class="text-slate-500">Đang tải giao dịch...</p>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div id="transactions-empty" class="hidden text-center py-16">
                            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="font-heading text-2xl font-bold text-slate-800 mb-2">Chưa có giao dịch</h3>
                            <p class="text-slate-600">Bạn chưa có giao dịch nào</p>
                        </div>

                        <!-- Transactions List -->
                        <div id="transactions-container" class="space-y-3 hidden">
                            <!-- Transactions will be rendered here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("/") }}';
    const API_BASE_URL = '{{ url("/api") }}';
    
    let currentFilter = 'all'; // kept for compatibility, but filters UI removed
    let userBalance = 0;

    // Load user balance and statistics
    async function loadUserData() {
        try {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            if (!token) {
                // Not logged in - redirect to login
                window.location.href = '{{ url("/login") }}';
                return;
            }

            // Load user info
            const userResponse = await fetch(`${API_BASE_URL}/auth/me`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (userResponse.ok) {
                const userResult = await userResponse.json();
                if (userResult.success && userResult.data) {
                    userBalance = parseFloat(userResult.data.balance || 0);
                    document.getElementById('current-balance').innerHTML = formatPrice(userBalance);
                }
            }

            // Load statistics
            const statsResponse = await fetch(`${API_BASE_URL}/transactions/statistics`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (statsResponse.ok) {
                const statsResult = await statsResponse.json();
                if (statsResult.success && statsResult.data) {
                    const totalDeposit = statsResult.data.total_deposits || 0;
                    document.getElementById('total-deposit').textContent = formatPrice(totalDeposit);
                }
            }
        } catch (error) {
            console.error('Error loading user data:', error);
        }
    }

    // Load transactions
    async function loadTransactions() {
        const loadingEl = document.getElementById('transactions-loading');
        const containerEl = document.getElementById('transactions-container');
        const emptyEl = document.getElementById('transactions-empty');

        try {
            loadingEl.classList.remove('hidden');
            containerEl.classList.add('hidden');
            emptyEl.classList.add('hidden');

            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            if (!token) {
                loadingEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                return;
            }

            let url = `${API_BASE_URL}/transactions?per_page=20`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.status === 401) {
                window.location.href = '{{ url("/login") }}';
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success && result.data && result.data.length > 0) {
                loadingEl.classList.add('hidden');
                emptyEl.classList.add('hidden');
                containerEl.classList.remove('hidden');
                renderTransactions(result.data);
            } else {
                loadingEl.classList.add('hidden');
                containerEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading transactions:', error);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
        }
    }

    // Render transactions
    function renderTransactions(transactions) {
        const container = document.getElementById('transactions-container');
        
        container.innerHTML = transactions.map(transaction => {
            const statusConfig = getStatusConfig(transaction.status);
            const typeConfig = getTypeConfig(transaction.type);
            const formattedDate = formatDate(transaction.created_at);
            const formattedAmount = formatPrice(Math.abs(transaction.amount));
            const isPositive = transaction.type === 'deposit' || transaction.type === 'refund';

            return `
                <div class="flex items-center justify-between p-4 border border-game-border rounded-xl hover:shadow-md transition-all">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="w-12 h-12 rounded-lg ${typeConfig.bg} flex items-center justify-center flex-shrink-0">
                            ${typeConfig.icon}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-semibold text-slate-800">${escapeHtml(transaction.description || typeConfig.label)}</h4>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full ${statusConfig.bg} ${statusConfig.text}">
                                    ${statusConfig.label}
                                </span>
                            </div>
                            <p class="text-sm text-slate-500">
                                ${formattedDate} • Mã: ${escapeHtml(transaction.transaction_code || 'N/A')}
                            </p>
                        </div>
                    </div>
                    <div class="text-right ml-4">
                        <p class="font-bold text-lg ${isPositive ? 'text-game-green' : 'text-red-600'}">
                            ${isPositive ? '+' : '-'}${formattedAmount}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">
                            Số dư: ${formatPrice(transaction.balance_after || 0)}
                        </p>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Get status configuration
    function getStatusConfig(status) {
        const configs = {
            'pending': { label: 'Chờ xử lý', bg: 'bg-yellow-100', text: 'text-yellow-700' },
            'completed': { label: 'Thành công', bg: 'bg-game-green/10', text: 'text-game-green' },
            'failed': { label: 'Thất bại', bg: 'bg-red-100', text: 'text-red-700' },
            'cancelled': { label: 'Đã hủy', bg: 'bg-slate-100', text: 'text-slate-700' },
        };
        return configs[status] || { label: status, bg: 'bg-slate-100', text: 'text-slate-700' };
    }

    // Get type configuration
    function getTypeConfig(type) {
        const configs = {
            'deposit': { 
                label: 'Nạp tiền', 
                bg: 'bg-blue-100', 
                icon: '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>'
            },
            'withdraw': { 
                label: 'Rút tiền', 
                bg: 'bg-orange-100', 
                icon: '<svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>'
            },
            'purchase': { 
                label: 'Mua hàng', 
                bg: 'bg-purple-100', 
                icon: '<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>'
            },
            'refund': { 
                label: 'Hoàn tiền', 
                bg: 'bg-green-100', 
                icon: '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>'
            },
        };
        return configs[type] || { 
            label: type, 
            bg: 'bg-slate-100', 
            icon: '<svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
        };
    }

    // Format date
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Format price
    function formatPrice(amount) {
        if (!amount && amount !== 0) return '0đ';
        const numAmount = parseFloat(amount);
        if (isNaN(numAmount)) return '0đ';
        return new Intl.NumberFormat('vi-VN').format(numAmount) + 'đ';
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Quick amount buttons
    document.querySelectorAll('.quick-amount-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const amount = btn.dataset.amount;
            document.getElementById('deposit-amount').value = amount;
            
            // Update active state
            document.querySelectorAll('.quick-amount-btn').forEach(b => {
                b.classList.remove('bg-game-accent', 'text-white', 'border-game-accent');
            });
            btn.classList.add('bg-game-accent', 'text-white', 'border-game-accent');
        });
    });

    // Filter buttons đã bị ẩn khỏi UI; luôn hiển thị tất cả giao dịch

    // Deposit form submission
    document.getElementById('deposit-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const amount = parseFloat(document.getElementById('deposit-amount').value);
        const submitBtn = document.getElementById('deposit-submit');
        
        if (!amount || amount < 10000 || amount > 100000000) {
            alert('Số tiền không hợp lệ. Vui lòng nhập số tiền từ 10,000đ đến 100,000,000đ.');
            return;
        }

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></div>';

            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '{{ url("/login") }}';
                return;
            }

            const response = await fetch(`${API_BASE_URL}/wallet/create-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ amount: amount })
            });

            const result = await response.json();

            if (result.success && result.data && result.data.payment_url) {
                // Redirect to VNPay
                window.location.href = result.data.payment_url;
            } else {
                alert(result.message || 'Có lỗi xảy ra khi tạo giao dịch. Vui lòng thử lại.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nạp tiền ngay</span>
                `;
            }
        } catch (error) {
            console.error('Error creating payment:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại sau.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nạp tiền ngay</span>
            `;
        }
    });

    // Show payment callback messages as toast notification
    function showPaymentMessage() {
        const urlParams = new URLSearchParams(window.location.search);
        const paymentSuccess = urlParams.get('payment_success');
        const paymentError = urlParams.get('payment_error');
        
        if (paymentSuccess || paymentError) {
            const message = decodeURIComponent(paymentSuccess || paymentError);
            const isSuccess = !!paymentSuccess;
            
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `fixed top-24 right-4 z-50 bg-white border-2 rounded-xl shadow-2xl px-5 py-4 flex items-center gap-4 min-w-[320px] max-w-md animate-toast-slide-in ${
                isSuccess 
                    ? 'border-game-green/30 bg-gradient-to-r from-game-green/5 to-game-green/10' 
                    : 'border-red-300 bg-gradient-to-r from-red-50 to-red-100'
            }`;
            
            // Icon
            const iconSvg = isSuccess
                ? '<div class="w-10 h-10 rounded-full bg-game-green/20 flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-game-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>'
                : '<div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>';
            
            // Content
            const title = isSuccess ? 'Nạp tiền thành công' : 'Nạp tiền thất bại';
            toast.innerHTML = `
                ${iconSvg}
                <div class="flex-1">
                    <p class="font-semibold text-slate-800 text-sm mb-1">${title}</p>
                    <p class="text-sm ${isSuccess ? 'text-slate-600' : 'text-red-700'}">${escapeHtml(message)}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, 5000);
            
            // Remove params from URL without reload
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        showPaymentMessage();
        loadUserData();
        loadTransactions();
    });
</script>
@endpush

@push('styles')
<style>
    @keyframes toast-slide-in {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    .animate-toast-slide-in {
        animation: toast-slide-in 0.4s ease-out;
        transition: opacity 0.3s ease-out, transform 0.3s ease-out;
    }
</style>
@endpush
