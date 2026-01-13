@extends('layouts.main')

@section('title', 'Đơn hàng')

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
                        <span class="gradient-text">Đơn hàng</span>
                    </h1>
                    <p class="text-slate-600">Xem lịch sử và quản lý đơn hàng của bạn</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Orders Content -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <!-- Tabs -->
            <div class="mb-6">
                <div class="flex gap-2 border-b border-game-border">
                    <button id="tab-offline" 
                            class="px-6 py-3 font-heading font-semibold text-game-accent border-b-2 border-game-accent transition-colors tab-btn active"
                            data-tab="offline">
                        Tài khoản Offline
                    </button>
                    <button id="tab-online" 
                            class="px-6 py-3 font-heading font-semibold text-slate-600 border-b-2 border-transparent hover:text-game-accent transition-colors tab-btn"
                            data-tab="online">
                        Tài khoản Online
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading" class="text-center py-16">
                <div class="inline-flex flex-col items-center">
                    <div class="animate-spin w-12 h-12 border-4 border-game-accent border-t-transparent rounded-full mb-4"></div>
                    <p class="text-slate-500">Đang tải đơn hàng...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden bg-white rounded-2xl border border-game-border p-12 text-center">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-heading text-2xl font-bold text-slate-800 mb-2">Chưa có đơn hàng</h3>
                <p class="text-slate-600 mb-8">Bạn chưa có đơn hàng nào trong danh mục này</p>
                <a href="{{ url('/store') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-full hover:opacity-90 transition-all glow-effect">
                    Khám phá sản phẩm
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

            <!-- Orders List -->
            <div id="orders-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                <!-- Orders will be rendered here by JavaScript -->
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("/") }}';
    const API_BASE_URL = '{{ url("/api") }}';
    const GAME_BASE_URL = '{{ url("/game") }}';
    
    let currentTab = 'offline';
    let allOrders = [];

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active tab
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'border-game-accent', 'text-game-accent');
                b.classList.add('border-transparent', 'text-slate-600');
            });
            btn.classList.add('active', 'border-game-accent', 'text-game-accent');
            btn.classList.remove('border-transparent', 'text-slate-600');
            
            currentTab = btn.dataset.tab;
            filterAndRenderOrders();
        });
    });

    // Load orders from API
    async function loadOrders() {
        const loadingEl = document.getElementById('loading');
        const containerEl = document.getElementById('orders-container');
        const emptyEl = document.getElementById('empty-state');

        try {
            loadingEl.classList.remove('hidden');
            containerEl.classList.add('hidden');
            emptyEl.classList.add('hidden');

            // Get auth token if available
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const response = await fetch(`${API_BASE_URL}/orders`, {
                headers: headers
            });

            if (response.status === 401) {
                // Not authenticated - show empty state
                loadingEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                emptyEl.querySelector('h3').textContent = 'Vui lòng đăng nhập';
                emptyEl.querySelector('p').textContent = 'Bạn cần đăng nhập để xem đơn hàng của mình';
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success && result.data) {
                allOrders = result.data;
                filterAndRenderOrders();
            } else {
                allOrders = [];
                filterAndRenderOrders();
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            emptyEl.querySelector('h3').textContent = 'Đã xảy ra lỗi';
            emptyEl.querySelector('p').textContent = 'Không thể tải danh sách đơn hàng. Vui lòng thử lại sau.';
        }
    }

    // Filter orders by type (offline/online)
    async function filterAndRenderOrders() {
        const loadingEl = document.getElementById('loading');
        const containerEl = document.getElementById('orders-container');
        const emptyEl = document.getElementById('empty-state');

        loadingEl.classList.add('hidden');

        // Filter orders by game type
        const filteredOrders = await filterOrdersByType(allOrders, currentTab);

        if (filteredOrders.length === 0) {
            containerEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            return;
        }

        emptyEl.classList.add('hidden');
        containerEl.classList.remove('hidden');
        renderOrders(filteredOrders);
    }

    // Filter orders by game type
    async function filterOrdersByType(orders, type) {
        if (!orders || orders.length === 0) return [];

        // First, filter orders that have game data with type
        const ordersWithType = orders.filter(order => order.game && order.game.type);
        const filteredByType = ordersWithType.filter(order => order.game.type === type);

        // For orders without game type, fetch them
        const ordersWithoutType = orders.filter(order => !order.game || !order.game.type);
        
        if (ordersWithoutType.length === 0) {
            return filteredByType;
        }

        // Get unique game IDs that need type lookup
        const gameIds = [...new Set(ordersWithoutType.map(o => o.product_simple_id).filter(id => id))];
        
        if (gameIds.length === 0) {
            return filteredByType;
        }

        // Fetch game types for orders without type
        const gameTypes = {};
        try {
            const gamePromises = gameIds.map(async (gameId) => {
                try {
                    const response = await fetch(`${BASE_URL}/api/products/${gameId}`);
                    if (response.ok) {
                        const result = await response.json();
                        if (result.success && result.data) {
                            return { id: gameId, type: result.data.type || 'offline' };
                        }
                    }
                } catch (error) {
                    console.error(`Error fetching game ${gameId}:`, error);
                }
                return { id: gameId, type: 'offline' };
            });

            const gameResults = await Promise.all(gamePromises);
            gameResults.forEach(game => {
                gameTypes[game.id] = game.type;
            });
        } catch (error) {
            console.error('Error fetching game types:', error);
        }

        // Filter orders without type
        const filteredWithoutType = ordersWithoutType.filter(order => {
            const gameType = gameTypes[order.product_simple_id] || 'offline';
            return gameType === type;
        });

        return [...filteredByType, ...filteredWithoutType];
    }

    // Render orders
    function renderOrders(orders) {
        const container = document.getElementById('orders-container');
        
        container.innerHTML = orders.map(order => {
            const statusConfig = getStatusConfig(order.status);
            const formattedDate = formatDate(order.created_at);
            
            // Get current price from game price string (preferred) or fallback to order amount
            let amount = 0;
            if (order.game && order.game.price) {
                // Extract current price from game price string
                amount = extractPriceNumber(order.game.price);
            }
            
            // Fallback to order.amount if game price not available
            if (amount <= 0) {
                amount = typeof order.amount === 'string' ? parseFloat(order.amount) : (order.amount || 0);
            }
            
            const formattedAmount = formatPrice(amount);

            return `
                <div class="bg-white rounded-xl border border-game-border p-6 hover:shadow-lg transition-all card-hover">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Left: Game Image -->
                        <a href="${GAME_BASE_URL}/${order.product_simple_id}" class="flex-shrink-0 w-full md:w-32 h-32 rounded-lg overflow-hidden border border-game-border">
                            <img src="${order.game?.image || 'https://via.placeholder.com/200x200?text=Game'}" 
                                 alt="${escapeHtml(order.game?.title || 'Game')}" 
                                 class="w-full h-full object-cover">
                        </a>

                        <!-- Right: Order Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
                                <div class="flex-1">
                                    <div class="mb-2">
                                        <h3 class="font-heading font-semibold text-slate-800 text-lg">
                                            <a href="${GAME_BASE_URL}/${order.product_simple_id}" class="hover:text-game-accent transition-colors">
                                                ${escapeHtml(order.game?.title || 'Không xác định')}
                                            </a>
                                        </h3>
                                    </div>
                                    <p class="text-slate-500 text-sm mb-2">
                                        Mã đơn: <span class="font-semibold text-slate-700">${escapeHtml(order.order_code || 'N/A')}</span>
                                    </p>
                                    <p class="text-slate-500 text-sm">
                                        Ngày đặt: <span class="font-semibold text-slate-700">${formattedDate}</span>
                                    </p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-slate-500 text-sm mb-1">Thành tiền</p>
                                    <p class="text-game-accent font-bold text-xl">${formattedAmount}</p>
                                </div>
                            </div>

                            <!-- Order Actions -->
                            <div class="flex flex-wrap gap-3 pt-4 border-t border-game-border">
                                ${(order.status === 'processing' || order.status === 'completed') && order.items && order.items.length > 0 ? `
                                    <button onclick="viewCredentials(${order.id})" 
                                            class="px-4 py-2 bg-game-green text-white font-semibold rounded-lg hover:bg-game-green/90 transition-colors text-sm">
                                        Xem thông tin đăng nhập
                                    </button>
                                ` : ''}
                                ${order.status === 'pending' ? `
                                    <button onclick="cancelOrder(${order.id})" 
                                            class="px-4 py-2 border border-red-300 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-colors text-sm">
                                        Hủy đơn
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Get status configuration
    function getStatusConfig(status) {
        const configs = {
            'pending': { label: 'Chờ xử lý', bg: 'bg-yellow-100', text: 'text-yellow-700' },
            'processing': { label: 'Đang xử lý', bg: 'bg-blue-100', text: 'text-blue-700' },
            'completed': { label: 'Hoàn thành', bg: 'bg-game-green/10', text: 'text-game-green' },
            'cancelled': { label: 'Đã hủy', bg: 'bg-red-100', text: 'text-red-700' },
            'refunded': { label: 'Đã hoàn tiền', bg: 'bg-slate-100', text: 'text-slate-700' },
        };
        return configs[status] || { label: status, bg: 'bg-slate-100', text: 'text-slate-700' };
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

    // Extract price number from price string (get current price - last number)
    function extractPriceNumber(priceStr) {
        if (!priceStr) return 0;
        
        // Extract prices with currency symbol (đ or ₫) - similar to extractPrices in other files
        const priceRegex = /[\d.,]+\s*[₫đ]/gi;
        const prices = priceStr.match(priceRegex);
        
        if (!prices || prices.length === 0) {
            // Fallback: try to extract any number if no currency symbol found
            const numberRegex = /[\d.,]+/g;
            const numbers = priceStr.match(numberRegex);
            if (numbers && numbers.length > 0) {
                const lastNumber = numbers[numbers.length - 1];
                return parseFloat(lastNumber.replace(/\./g, '').replace(',', '.')) || 0;
            }
            return 0;
        }
        
        // Get the last price (current price) - remove currency symbol and parse
        const lastPrice = prices[prices.length - 1].trim();
        // Remove currency symbol and parse number
        const priceNumber = lastPrice.replace(/[₫đ\s]/gi, '').replace(/\./g, '').replace(',', '.');
        return parseFloat(priceNumber) || 0;
    }

    // Format price
    function formatPrice(amount) {
        if (!amount && amount !== 0) return '0đ';
        
        // Convert to number if it's a string
        let numAmount = typeof amount === 'string' ? parseFloat(amount.replace(/[^\d.,]/g, '').replace(/\./g, '').replace(',', '.')) : parseFloat(amount);
        
        if (isNaN(numAmount)) {
            console.warn('Invalid amount:', amount);
            return '0đ';
        }
        
        // Format with Vietnamese locale
        return new Intl.NumberFormat('vi-VN').format(numAmount) + 'đ';
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // View credentials (for processing/completed orders)
    async function viewCredentials(orderId) {
        try {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            const response = await fetch(`${API_BASE_URL}/orders/${orderId}`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                }
            });

            if (!response.ok) {
                throw new Error('Không thể tải thông tin đơn hàng');
            }

            const result = await response.json();
            if (!result.success || !result.data) {
                throw new Error('Không tìm thấy đơn hàng');
            }

            const order = result.data;
            
            // Check if order has items with credentials
            if (!order.items || order.items.length === 0) {
                alert('Đơn hàng chưa có thông tin tài khoản.');
                return;
            }

            // Show credentials modal
            showCredentialsModal(order);
        } catch (error) {
            console.error('Error loading credentials:', error);
            alert('Không thể tải thông tin đăng nhập. Vui lòng thử lại.');
        }
    }

    // Show credentials modal
    function showCredentialsModal(order) {
        const item = order.items[0];
        const credentials = item.steam_credentials || {};

        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 animate-scale-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-heading text-2xl font-bold text-slate-800">Thông tin đăng nhập</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">Tên đăng nhập Steam</label>
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   value="${escapeHtml(credentials.username || '')}" 
                                   readonly 
                                   class="flex-1 px-4 py-2 bg-slate-50 border border-game-border rounded-lg text-slate-800 font-mono"
                                   id="cred-username">
                            <button onclick="copyToClipboard('cred-username')" 
                                    class="px-4 py-2 bg-game-accent text-white rounded-lg hover:bg-game-accent-hover transition-colors text-sm">
                                Copy
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">Mật khẩu Steam</label>
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   value="${escapeHtml(credentials.password || '')}" 
                                   readonly 
                                   class="flex-1 px-4 py-2 bg-slate-50 border border-game-border rounded-lg text-slate-800 font-mono"
                                   id="cred-password">
                            <button onclick="copyToClipboard('cred-password')" 
                                    class="px-4 py-2 bg-game-accent text-white rounded-lg hover:bg-game-accent-hover transition-colors text-sm">
                                Copy
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">Mã Steam</label>
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   value="" 
                                   readonly 
                                   class="flex-1 px-4 py-2 bg-slate-50 border border-game-border rounded-lg text-slate-800 font-mono"
                                   id="cred-steam-code">
                            <button onclick="generateSteamCode()" 
                                    class="px-4 py-2 bg-game-purple text-white rounded-lg hover:bg-game-purple/90 transition-colors text-sm font-semibold">
                                Lấy mã
                            </button>
                            <button onclick="copyToClipboard('cred-steam-code')" 
                                    class="px-4 py-2 bg-game-accent text-white rounded-lg hover:bg-game-accent-hover transition-colors text-sm hidden"
                                    id="btn-copy-steam-code">
                                Copy
                            </button>
                        </div>
                    </div>
                    ${credentials.email ? `
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">Email</label>
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   value="${escapeHtml(credentials.email || '')}" 
                                   readonly 
                                   class="flex-1 px-4 py-2 bg-slate-50 border border-game-border rounded-lg text-slate-800 font-mono"
                                   id="cred-email">
                            <button onclick="copyToClipboard('cred-email')" 
                                    class="px-4 py-2 bg-game-accent text-white rounded-lg hover:bg-game-accent-hover transition-colors text-sm">
                                Copy
                            </button>
                        </div>
                    </div>
                    ` : ''}
                    ${credentials.email_password ? `
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">Mật khẩu Email</label>
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   value="${escapeHtml(credentials.email_password || '')}" 
                                   readonly 
                                   class="flex-1 px-4 py-2 bg-slate-50 border border-game-border rounded-lg text-slate-800 font-mono"
                                   id="cred-email-password">
                            <button onclick="copyToClipboard('cred-email-password')" 
                                    class="px-4 py-2 bg-game-accent text-white rounded-lg hover:bg-game-accent-hover transition-colors text-sm">
                                Copy
                            </button>
                        </div>
                    </div>
                    ` : ''}
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-yellow-800">
                        <strong>Lưu ý:</strong> Vui lòng lưu thông tin này vào nơi an toàn. Thông tin chỉ hiển thị một lần.
                    </p>
                </div>
                
                <button onclick="this.closest('.fixed').remove()" 
                        class="w-full px-6 py-3 bg-game-accent text-white font-bold rounded-xl hover:bg-game-accent-hover transition-colors">
                    Đã lưu thông tin
                </button>
            </div>
        `;
        
        document.body.appendChild(modal);
    }

    // Generate Steam code (5 random uppercase alphanumeric characters)
    function generateSteamCode() {
        const input = document.getElementById('cred-steam-code');
        const copyBtn = document.getElementById('btn-copy-steam-code');
        if (!input) return;
        
        // Characters: A-Z and 0-9
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        
        // Generate 5 random characters
        for (let i = 0; i < 5; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        // Display the code
        input.value = code;
        
        // Show copy button
        if (copyBtn) {
            copyBtn.classList.remove('hidden');
        }
    }

    // Copy to clipboard
    function copyToClipboard(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        // Don't copy if input is empty
        if (!input.value.trim()) {
            if (inputId === 'cred-steam-code') {
                alert('Vui lòng lấy mã trước khi copy.');
            }
            return;
        }
        
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            document.execCommand('copy');
            
            // Find the copy button - could be nextElementSibling or a separate button
            let btn;
            if (inputId === 'cred-steam-code') {
                btn = document.getElementById('btn-copy-steam-code');
            } else {
                // For other inputs, find the copy button in the same container
                const container = input.closest('.flex');
                btn = container.querySelector('button[onclick*="copyToClipboard"]');
            }
            
            if (btn) {
                const originalText = btn.textContent;
                btn.textContent = 'Đã copy!';
                btn.classList.add('bg-game-green');
                btn.classList.remove('bg-game-accent', 'bg-game-purple');
                
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.classList.remove('bg-game-green');
                    if (inputId === 'cred-steam-code') {
                        btn.classList.add('bg-game-accent');
                    } else {
                        btn.classList.add('bg-game-accent');
                    }
                }, 2000);
            }
        } catch (err) {
            alert('Không thể copy. Vui lòng copy thủ công.');
        }
    }

    // Cancel order
    function cancelOrder(orderId) {
        if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) return;
        
        // TODO: Implement cancel order API call
        alert('Chức năng hủy đơn hàng sẽ được tích hợp sau.');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadOrders();
    });
</script>
@endpush

