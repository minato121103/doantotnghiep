@extends('layouts.main')

@section('title', 'Giỏ hàng')

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
                        <span class="gradient-text">Giỏ hàng</span>
                    </h1>
                    <p class="text-slate-600">Xem và quản lý sản phẩm của bạn</p>
                </div>
                <a href="{{ url('/store') }}" class="hidden md:flex items-center px-6 py-3 bg-white border border-game-border rounded-xl hover:border-game-accent hover:shadow-lg transition-all text-slate-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </section>

    <!-- Cart Content -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <!-- Mã ưu đãi (phía trên) -->
            <div id="cart-coupons-bar" class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-heading text-lg font-bold text-slate-800">Mã ưu đãi</h3>
                    <button type="button" id="view-all-coupons-btn" class="hidden text-sm font-semibold text-game-accent hover:underline" onclick="openAllCouponsModal()">Xem tất cả</button>
                </div>
                <div id="cart-coupons-list" class="flex flex-wrap gap-2">
                    <div class="text-slate-500 text-sm py-2">Đang tải mã ưu đãi...</div>
                </div>
                <div id="cart-coupon-detail" class="hidden mt-3 p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700">
                    <div class="flex items-start justify-between gap-2">
                        <div id="cart-coupon-detail-content"></div>
                        <button type="button" onclick="closeCouponDetail()" class="flex-shrink-0 p-1 text-slate-400 hover:text-slate-600 rounded" title="Đóng">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="lg:w-2/3">
                    <!-- Empty Cart State -->
                    <div id="empty-cart" class="hidden bg-white rounded-2xl border border-game-border p-12 text-center">
                        <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-heading text-2xl font-bold text-slate-800 mb-2">Giỏ hàng trống</h3>
                        <p class="text-slate-600 mb-8">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                        <a href="{{ url('/store') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-full hover:opacity-90 transition-all glow-effect">
                            Khám phá sản phẩm
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Cart Items List -->
                    <div id="cart-items-container" class="space-y-4">
                        <!-- Cart items will be rendered here by JavaScript -->
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-2xl border border-game-border p-6 sticky top-36">
                        <h3 class="font-heading text-xl font-bold text-slate-800 mb-6">Tóm tắt đơn hàng</h3>
                        
                        <!-- Summary Details -->
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-slate-600">
                                <span>Tạm tính</span>
                                <span id="subtotal" class="font-semibold text-slate-800">0đ</span>
                            </div>

                            <!-- Coupon Section -->
                            <div class="border-t border-game-border pt-4">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Mã ưu đãi</label>
                                <div class="flex gap-2">
                                    <input type="text" id="coupon-input" placeholder="Nhập mã..." class="flex-1 px-3 py-2 border border-game-border rounded-lg text-sm focus:ring-2 focus:ring-game-accent focus:border-transparent uppercase" style="font-family: monospace;">
                                    <button id="apply-coupon-btn" onclick="applyCoupon()" class="px-4 py-2 bg-game-accent text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-all">Áp dụng</button>
                                </div>
                                <div id="coupon-message" class="mt-2 text-sm hidden"></div>
                                <div id="coupon-applied" class="mt-2 hidden">
                                    <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span class="text-sm text-green-700 font-medium" id="coupon-applied-text"></span>
                                        </div>
                                        <button onclick="removeCoupon()" class="text-green-600 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="discount-row" class="hidden flex justify-between text-green-600">
                                <span>Giảm giá</span>
                                <span id="discount-amount" class="font-semibold">-0đ</span>
                            </div>

                            <div class="border-t border-game-border pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-heading text-lg font-bold text-slate-800">Tổng cộng</span>
                                    <span id="total" class="text-game-accent font-bold text-xl">0đ</span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <button id="checkout-btn" class="w-full px-6 py-4 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-xl hover:opacity-90 transition-all glow-effect mb-4 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Thanh toán
                        </button>

                        <!-- Continue Shopping -->
                        <a href="{{ url('/store') }}" class="block w-full text-center px-6 py-3 border border-game-border text-slate-700 rounded-xl hover:border-game-accent hover:bg-slate-50 transition-colors">
                            Tiếp tục mua sắm
                        </a>

                        <!-- Security Badge -->
                        <div class="mt-6 pt-6 border-t border-game-border">
                            <div class="flex items-center justify-center gap-2 text-slate-500 text-sm">
                                <svg class="w-5 h-5 text-game-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span>Giao dịch an toàn & bảo mật</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal: Tất cả mã ưu đãi -->
    <div id="all-coupons-modal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this) closeAllCouponsModal()">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden shadow-2xl flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h3 class="font-heading text-xl font-bold text-slate-800">Tất cả mã ưu đãi</h3>
                <button type="button" onclick="closeAllCouponsModal()" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="overflow-y-auto flex-1 p-4">
                <table class="w-full text-sm text-left">
                    <thead class="text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="py-3 font-semibold">Mã</th>
                            <th class="py-3 font-semibold">Giảm giá</th>
                            <th class="py-3 font-semibold">Đơn tối thiểu</th>
                            <th class="py-3 font-semibold">Hạn dùng</th>
                            <th class="py-3 font-semibold w-20"></th>
                        </tr>
                    </thead>
                    <tbody id="all-coupons-table-body" class="text-slate-700">
                    </tbody>
                </table>
                <div id="all-coupons-empty" class="hidden text-center py-8 text-slate-500">Chưa có mã ưu đãi nào.</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("/") }}';
    const GAME_BASE_URL = '{{ url("/game") }}';
    const API_BASE_URL = '{{ url("/api") }}';

    let productPromoData = {};
    let appliedCoupon = null;
    let activeCouponsList = [];
    const COUPONS_PREVIEW_COUNT = 4;

    function getCart() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }

    function couponValueText(c) {
        if (c.type === 'percent') return 'Giảm ' + (parseFloat(c.value) || 0) + '%';
        return 'Giảm ' + formatPrice(parseFloat(c.value) || 0);
    }

    function couponMinOrderText(c) {
        const min = parseFloat(c.min_order_amount) || 0;
        return min > 0 ? 'Đơn tối thiểu ' + formatPrice(min) : '—';
    }

    function couponValidText(c) {
        if (!c.ends_at) return '—';
        const d = new Date(c.ends_at);
        return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    function couponConditionTooltip(c) {
        const parts = ['Điều kiện: ' + couponValueText(c)];
        const min = couponMinOrderText(c);
        if (min !== '—') parts.push(min);
        parts.push('Hạn dùng: ' + couponValidText(c));
        return parts.join(' | ');
    }

    function couponConditionHtml(c) {
        return '<p class="font-semibold text-slate-800 mb-1">Mã <span class="font-mono">' + escapeHtml(c.code || '') + '</span></p>' +
            '<ul class="list-disc list-inside space-y-0.5 text-slate-600">' +
            '<li>' + couponValueText(c) + '</li>' +
            '<li>Đơn tối thiểu: ' + couponMinOrderText(c) + '</li>' +
            '<li>Hạn dùng: ' + couponValidText(c) + '</li>' +
            '</ul>';
    }

    function showCouponDetail(code) {
        const c = activeCouponsList.find(x => (x.code || '').toUpperCase() === (code || '').toUpperCase());
        if (!c) return;
        const wrap = document.getElementById('cart-coupon-detail');
        const content = document.getElementById('cart-coupon-detail-content');
        if (!wrap || !content) return;
        content.innerHTML = couponConditionHtml(c);
        wrap.classList.remove('hidden');
    }

    function closeCouponDetail() {
        const wrap = document.getElementById('cart-coupon-detail');
        if (wrap) wrap.classList.add('hidden');
    }

    async function loadActiveCoupons() {
        try {
            const res = await fetch(`${API_BASE_URL}/coupons?status=active&per_page=100`, { headers: { 'Accept': 'application/json' } });
            const result = await res.json();
            const list = (result.data && result.data.data) ? result.data.data : [];
            activeCouponsList = Array.isArray(list) ? list : [];
            renderCouponsBar();
        } catch (e) {
            console.error('Load coupons error:', e);
            document.getElementById('cart-coupons-list').innerHTML = '<span class="text-slate-500 text-sm">Không tải được mã ưu đãi.</span>';
        }
    }

    function renderCouponsBar() {
        const listEl = document.getElementById('cart-coupons-list');
        const viewAllBtn = document.getElementById('view-all-coupons-btn');
        if (!listEl) return;
        if (activeCouponsList.length === 0) {
            listEl.innerHTML = '<span class="text-slate-500 text-sm">Hiện không có mã ưu đãi.</span>';
            viewAllBtn.classList.add('hidden');
            return;
        }
        const preview = activeCouponsList.slice(0, COUPONS_PREVIEW_COUNT);
        const safeCode = (code) => (code || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        listEl.innerHTML = preview.map(c => {
            const code = c.code || '';
            const tip = escapeHtml(couponConditionTooltip(c)).replace(/"/g, '&quot;');
            return `<button type="button" onclick="useCouponCode('${safeCode(code)}')" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 font-semibold text-sm hover:bg-rose-100 transition-colors">
                <span class="font-mono">${escapeHtml(code)}</span>
                <span class="text-rose-600">${couponValueText(c)}</span>
                <span data-coupon-info role="button" tabindex="0" onclick="event.preventDefault();event.stopPropagation();showCouponDetail('${safeCode(code)}')" title="${tip}" class="ml-0.5 w-6 h-6 flex items-center justify-center rounded-full bg-rose-200/60 hover:bg-rose-200 text-rose-600 hover:text-rose-800 transition-colors flex-shrink-0" aria-label="Xem điều kiện">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                </span>
            </button>`;
        }).join('');
        if (activeCouponsList.length > COUPONS_PREVIEW_COUNT) {
            viewAllBtn.classList.remove('hidden');
        } else {
            viewAllBtn.classList.add('hidden');
        }
    }

    function useCouponCode(code) {
        const input = document.getElementById('coupon-input');
        if (input) {
            input.value = code;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }
        applyCoupon();
        closeAllCouponsModal();
    }

    function openAllCouponsModal() {
        const tbody = document.getElementById('all-coupons-table-body');
        const emptyEl = document.getElementById('all-coupons-empty');
        if (!tbody) return;
        if (activeCouponsList.length === 0) {
            tbody.innerHTML = '';
            emptyEl.classList.remove('hidden');
        } else {
            emptyEl.classList.add('hidden');
            tbody.innerHTML = activeCouponsList.map(c => `
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-3 font-mono font-semibold text-slate-800">${escapeHtml(c.code || '')}</td>
                    <td class="py-3">${couponValueText(c)}</td>
                    <td class="py-3 text-slate-600">${couponMinOrderText(c)}</td>
                    <td class="py-3 text-slate-600">${couponValidText(c)}</td>
                    <td class="py-3">
                        <button type="button" onclick="useCouponCode('${(c.code || '').replace(/'/g, "\\'")}')" class="px-3 py-1.5 bg-game-accent text-white text-xs font-semibold rounded-lg hover:opacity-90">Áp dụng</button>
                    </td>
                </tr>
            `).join('');
        }
        document.getElementById('all-coupons-modal').classList.remove('hidden');
        document.getElementById('all-coupons-modal').classList.add('flex');
    }

    function closeAllCouponsModal() {
        document.getElementById('all-coupons-modal').classList.add('hidden');
        document.getElementById('all-coupons-modal').classList.remove('flex');
    }

    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
    }

    function updateCartCount() {
        const cart = getCart();
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            cartCount.textContent = total;
            total > 0 ? cartCount.classList.remove('hidden') : cartCount.classList.add('hidden');
        }
    }

    function extractPriceNumber(priceStr) {
        if (!priceStr) return 0;
        const priceRegex = /[\d.,]+\s*[₫đ]/gi;
        const prices = priceStr.match(priceRegex);
        if (!prices || prices.length === 0) {
            const numberRegex = /[\d.,]+/g;
            const numbers = priceStr.match(numberRegex);
            if (numbers && numbers.length > 0) {
                const lastNumber = numbers[numbers.length - 1];
                return parseFloat(lastNumber.replace(/\./g, '').replace(',', '.')) || 0;
            }
            return 0;
        }
        const lastPrice = prices[prices.length - 1].trim();
        const priceNumber = lastPrice.replace(/[₫đ\s]/gi, '').replace(/\./g, '').replace(',', '.');
        return parseFloat(priceNumber) || 0;
    }

    function formatPrice(amount) {
        if (amount === 0) return '0đ';
        return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + 'đ';
    }

    function getItemEffectivePrice(item) {
        const promo = productPromoData[item.id];
        if (promo && promo.sale_price) {
            return promo.sale_price;
        }
        return extractPriceNumber(item.price);
    }

    function getPriceDisplayForItem(item) {
        const promo = productPromoData[item.id];
        if (promo && promo.sale_price) {
            const originalPrice = extractPriceNumber(item.price);
            return `<div class="flex flex-col">
                <span class="text-slate-400 line-through text-sm">${formatPrice(originalPrice)}</span>
                <div class="flex items-center gap-2">
                    <span class="text-game-accent font-bold text-lg">${formatPrice(promo.sale_price)}</span>
                    <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded">-${promo.discount_percent}%</span>
                </div>
            </div>`;
        }
        if (!item.price) return '<span class="text-game-accent font-bold text-lg">Liên hệ</span>';
        const priceRegex = /[\d.,]+\s*[₫đ]/gi;
        const prices = item.price.match(priceRegex);
        if (prices && prices.length >= 2) {
            return `<div class="flex flex-col">
                <span class="text-slate-400 line-through text-sm">${escapeHtml(prices[0].trim())}</span>
                <span class="text-game-accent font-bold text-lg">${escapeHtml(prices[prices.length-1].trim())}</span>
            </div>`;
        }
        return `<span class="text-game-accent font-bold text-lg">${formatPrice(extractPriceNumber(item.price))}</span>`;
    }

    async function fetchPromoData() {
        const cart = getCart();
        if (cart.length === 0) return;
        const ids = cart.map(item => item.id);
        try {
            const promises = ids.map(id =>
                fetch(`${API_BASE_URL}/products/${id}`, { headers: { 'Accept': 'application/json' }})
                    .then(r => r.json())
                    .catch(() => null)
            );
            const results = await Promise.all(promises);
            results.forEach(r => {
                if (r && r.success && r.data) {
                    const d = r.data;
                    if (d.sale_price) {
                        productPromoData[d.id] = {
                            sale_price: d.sale_price,
                            discount_percent: d.discount_percent,
                            promotion_name: d.promotion_name,
                        };
                    }
                }
            });
        } catch (e) { console.error(e); }
    }

    function renderCartItems() {
        const cart = getCart();
        const container = document.getElementById('cart-items-container');
        const emptyState = document.getElementById('empty-cart');
        const checkoutBtn = document.getElementById('checkout-btn');

        if (cart.length === 0) {
            container.innerHTML = '';
            emptyState.classList.remove('hidden');
            checkoutBtn.disabled = true;
            updateSummary(0, 0);
            updateCartCount();
            return;
        }

        emptyState.classList.add('hidden');
        checkoutBtn.disabled = false;

        container.innerHTML = cart.map((item, index) => {
            const price = getItemEffectivePrice(item);
            const subtotal = price * (item.quantity || 1);

            return `
                <div class="bg-white rounded-xl border border-game-border p-4 hover:shadow-lg transition-all card-hover" data-item-id="${item.id}">
                    <div class="flex gap-4">
                        <a href="${GAME_BASE_URL}/${item.id}" class="flex-shrink-0 w-24 h-24 rounded-lg overflow-hidden border border-game-border">
                            <img src="${item.image || 'https://via.placeholder.com/150x150?text=Game'}" 
                                 alt="${escapeHtml(item.title)}" 
                                 class="w-full h-full object-cover">
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 min-w-0">
                                    <a href="${GAME_BASE_URL}/${item.id}" class="font-heading font-semibold text-slate-800 hover:text-game-accent transition-colors line-clamp-2">
                                        ${escapeHtml(item.title)}
                                    </a>
                                </div>
                                <button onclick="removeItem(${index})" class="flex-shrink-0 ml-4 p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between mt-4">
                                <div class="flex flex-col">
                                    ${getPriceDisplayForItem(item)}
                                </div>
                                <div class="flex items-center gap-3">
                                    <button onclick="updateQuantity(${index}, -1)" class="w-8 h-8 flex items-center justify-center border border-game-border rounded-lg hover:border-game-accent hover:bg-game-accent/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <span class="w-12 text-center font-semibold text-slate-800" id="qty-${index}">${item.quantity || 1}</span>
                                    <button onclick="updateQuantity(${index}, 1)" class="w-8 h-8 flex items-center justify-center border border-game-border rounded-lg hover:border-game-accent hover:bg-game-accent/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-slate-500 text-sm">Thành tiền: </span>
                                <span class="font-bold text-slate-800">${formatPrice(subtotal)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        updateCartSummary();
        updateCartCount();
    }

    function updateQuantity(index, change) {
        const cart = getCart();
        if (index < 0 || index >= cart.length) return;
        cart[index].quantity = (cart[index].quantity || 1) + change;
        if (cart[index].quantity <= 0) cart.splice(index, 1);
        saveCart(cart);
        renderCartItems();
    }

    function removeItem(index) {
        if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;
        const cart = getCart();
        if (index < 0 || index >= cart.length) return;
        cart.splice(index, 1);
        saveCart(cart);
        removeCoupon();
        renderCartItems();
    }

    function calculateTotal() {
        const cart = getCart();
        let total = 0;
        cart.forEach(item => {
            total += getItemEffectivePrice(item) * (item.quantity || 1);
        });
        return total;
    }

    function updateSummary(subtotal, discount) {
        document.getElementById('subtotal').textContent = formatPrice(subtotal);
        const discountRow = document.getElementById('discount-row');
        if (discount > 0) {
            discountRow.classList.remove('hidden');
            document.getElementById('discount-amount').textContent = '-' + formatPrice(discount);
        } else {
            discountRow.classList.add('hidden');
        }
        document.getElementById('total').textContent = formatPrice(Math.max(0, subtotal - discount));
    }

    function updateCartSummary() {
        const subtotal = calculateTotal();
        const discount = appliedCoupon ? appliedCoupon.discount_amount : 0;
        updateSummary(subtotal, discount);
    }

    async function applyCoupon() {
        const code = document.getElementById('coupon-input').value.trim();
        const msgEl = document.getElementById('coupon-message');
        const appliedEl = document.getElementById('coupon-applied');
        const inputEl = document.getElementById('coupon-input');
        const btnEl = document.getElementById('apply-coupon-btn');

        msgEl.classList.add('hidden');
        appliedEl.classList.add('hidden');

        if (!code) {
            msgEl.textContent = 'Vui lòng nhập mã ưu đãi';
            msgEl.className = 'mt-2 text-sm text-red-500';
            msgEl.classList.remove('hidden');
            return;
        }

        const total = calculateTotal();
        btnEl.disabled = true;
        btnEl.textContent = '...';

        try {
            const res = await fetch(`${API_BASE_URL}/coupons/validate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ code, total }),
            });
            const json = await res.json();

            if (json.success && json.data) {
                appliedCoupon = json.data;
                document.getElementById('coupon-applied-text').textContent = `Mã ${json.data.code}: -${formatPrice(json.data.discount_amount)}`;
                appliedEl.classList.remove('hidden');
                inputEl.disabled = true;
                btnEl.classList.add('hidden');
                updateCartSummary();
            } else {
                msgEl.textContent = json.message || 'Mã không hợp lệ';
                msgEl.className = 'mt-2 text-sm text-red-500';
                msgEl.classList.remove('hidden');
                appliedCoupon = null;
                updateCartSummary();
            }
        } catch (e) {
            msgEl.textContent = 'Lỗi kết nối';
            msgEl.className = 'mt-2 text-sm text-red-500';
            msgEl.classList.remove('hidden');
        }

        btnEl.disabled = false;
        btnEl.textContent = 'Áp dụng';
    }

    function removeCoupon() {
        appliedCoupon = null;
        document.getElementById('coupon-applied').classList.add('hidden');
        document.getElementById('coupon-message').classList.add('hidden');
        document.getElementById('coupon-input').disabled = false;
        document.getElementById('coupon-input').value = '';
        document.getElementById('apply-coupon-btn').classList.remove('hidden');
        updateCartSummary();
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Checkout handler
    document.getElementById('checkout-btn').addEventListener('click', async function() {
        const cart = getCart();
        if (cart.length === 0) {
            showNotification('Giỏ hàng của bạn đang trống!', 'error');
            return;
        }

        // Check if user is logged in
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (!token) {
            showNotification('Vui lòng đăng nhập để thanh toán!', 'error');
            // Redirect to login if needed
            setTimeout(() => {
                window.location.href = BASE_URL + '/login';
            }, 2000);
            return;
        }

        const subtotal = calculateTotal();
        const discount = appliedCoupon ? appliedCoupon.discount_amount : 0;
        const finalTotal = Math.max(0, subtotal - discount);

        if (!confirm(`Bạn có chắc muốn thanh toán ${cart.length} sản phẩm với tổng tiền ${formatPrice(finalTotal)}?`)) {
            return;
        }

        const checkoutBtn = document.getElementById('checkout-btn');
        const originalText = checkoutBtn.innerHTML;
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<span class="flex items-center justify-center"><span class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Đang xử lý...</span>';

        try {
            // Prepare items for batch checkout
            const items = cart.map(item => ({
                product_simple_id: item.id,
                quantity: item.quantity || 1
            }));

            // Call batch checkout API
            const response = await fetch(`${API_BASE_URL}/orders/batch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    items: items,
                    payment_method: 'balance',
                    notes: 'Mua từ giỏ hàng',
                    coupon_code: appliedCoupon ? appliedCoupon.code : null
                })
            });

            const result = await response.json();

            if (result.success && result.data) {
                const { orders, final_total, total_amount, balance_after, discount_amount: serverDiscount, coupon_code: usedCoupon } = result.data;
                
                console.log('Checkout successful:', {
                    ordersCount: orders.length,
                    totalAmount: total_amount,
                    finalTotal: final_total,
                    discount: serverDiscount,
                    coupon: usedCoupon,
                    balanceAfter: balance_after
                });
                
                // Update user balance in header (async) - get fresh balance from server
                await updateUserBalance();
                
                // Clear cart
                localStorage.removeItem('cart');
                updateCartCount();
                
                showSuccessModal(orders.length, final_total || total_amount, serverDiscount, usedCoupon);
                
                // Redirect to orders page after 3 seconds
                setTimeout(() => {
                    window.location.href = BASE_URL + '/orders';
                }, 3000);
            } else {
                // Handle errors
                let errorMsg = result.message || 'Không thể tạo đơn hàng. Vui lòng thử lại.';
                
                if (result.errors && Array.isArray(result.errors)) {
                    errorMsg += '\n' + result.errors.join('\n');
                }
                
                if (result.message === 'Insufficient balance') {
                    errorMsg = `Số dư không đủ!\nCần: ${formatPrice(result.required || 0)}\nCó: ${formatPrice(result.available || 0)}`;
                }
                
                showNotification(errorMsg, 'error');
                checkoutBtn.disabled = false;
                checkoutBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Checkout error:', error);
            showNotification('Đã xảy ra lỗi khi thanh toán. Vui lòng thử lại.', 'error');
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = originalText;
        }
    });

    // Show notification
    function showNotification(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-game-green' : type === 'error' ? 'bg-red-500' : type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
        
        const notification = document.createElement('div');
        notification.className = `fixed top-24 right-4 ${bgColor} text-white rounded-xl shadow-lg p-4 z-50 flex items-center gap-3 animate-slide-in max-w-md`;
        notification.innerHTML = `
            <div class="flex-1">
                <p class="font-semibold">${escapeHtml(message)}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Parse balance from text (e.g., "100.000.000 đ" -> 100000000)
    function parseBalance(balanceText) {
        if (!balanceText) return 0;
        // Remove all non-digit characters except dots
        const cleaned = balanceText.replace(/[^\d.,]/g, '').replace(/\./g, '');
        return parseFloat(cleaned) || 0;
    }

    // Format balance for display
    function formatBalance(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }

    // Update user balance in header after checkout - get fresh from server
    async function updateUserBalance() {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (!token) {
            console.warn('No auth token found');
            return;
        }
        
        try {
            // Fetch updated user profile from API to get accurate balance
            const response = await fetch(`${API_BASE_URL}/user/profile`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success && result.data) {
                const user = result.data;
                // Ensure balance is a number
                const newBalance = typeof user.balance === 'string' 
                    ? parseFloat(user.balance.replace(/[^\d.,]/g, '').replace(/\./g, '').replace(',', '.')) || 0
                    : parseFloat(user.balance) || 0;
                
                console.log('User data from server:', user);
                console.log('New balance from server:', newBalance, 'Type:', typeof newBalance);
                
                // Update localStorage with new user data
                const oldUser = localStorage.getItem('user');
                localStorage.setItem('user', JSON.stringify(user));
                
                // Update balance in header immediately
                const userBalanceEl = document.getElementById('user-balance');
                const mobileUserBalanceEl = document.getElementById('mobile-user-balance');
                
                // Format balance (same format as header uses)
                const formattedBalance = formatCurrency(newBalance);
                
                if (userBalanceEl) {
                    userBalanceEl.textContent = formattedBalance;
                    console.log('Updated desktop balance:', formattedBalance);
                }
                if (mobileUserBalanceEl) {
                    mobileUserBalanceEl.textContent = formattedBalance;
                    console.log('Updated mobile balance:', formattedBalance);
                }
                
                // Trigger custom event for other tabs/windows to sync
                // Note: StorageEvent only fires in OTHER tabs, not current tab
                // So we need to manually trigger updateAuthUI in current tab
                if (typeof updateAuthUI === 'function') {
                    updateAuthUI();
                    console.log('Called updateAuthUI');
                }
                
                // Dispatch custom event for same-tab updates
                window.dispatchEvent(new CustomEvent('userBalanceUpdated', { detail: { user, balance: newBalance } }));
                
                // Also dispatch storage event for other tabs (though it won't fire in current tab)
                // This is for cross-tab synchronization
                try {
                    const storageEvent = new Event('storage');
                    Object.defineProperty(storageEvent, 'key', { value: 'user' });
                    Object.defineProperty(storageEvent, 'newValue', { value: JSON.stringify(user) });
                    Object.defineProperty(storageEvent, 'oldValue', { value: oldUser });
                    window.dispatchEvent(storageEvent);
                } catch (e) {
                    // Some browsers may not allow this
                    console.log('Could not dispatch storage event:', e);
                }
                
                console.log('Balance updated successfully:', formattedBalance);
            } else {
                console.error('Invalid response from API:', result);
                throw new Error('Invalid API response');
            }
        } catch (error) {
            console.error('Error fetching user profile:', error);
            showNotification('Đã thanh toán thành công nhưng không thể cập nhật số dư. Vui lòng refresh trang.', 'warning');
        }
    }
    
    // Fallback: manually calculate and update balance
    function updateBalanceManually(amountToSubtract) {
        const userBalanceEl = document.getElementById('user-balance');
        const mobileUserBalanceEl = document.getElementById('mobile-user-balance');
        
        if (!userBalanceEl && !mobileUserBalanceEl) return;
        
        // Get current balance
        const currentBalanceText = userBalanceEl?.textContent || mobileUserBalanceEl?.textContent || '0 đ';
        const currentBalance = parseBalance(currentBalanceText);
        
        // Calculate new balance
        const newBalance = Math.max(0, currentBalance - amountToSubtract);
        
        // Format balance (same format as header uses)
        const formattedBalance = formatCurrency(newBalance);
        
        // Update both elements
        if (userBalanceEl) {
            userBalanceEl.textContent = formattedBalance;
        }
        if (mobileUserBalanceEl) {
            mobileUserBalanceEl.textContent = formattedBalance;
        }
    }
    
    // Format currency (same as header.blade.php)
    function formatCurrency(amount) {
        if (!amount && amount !== 0) return '0 đ';
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }

    function showSuccessModal(orderCount, totalAmount = null, discountAmt = 0, couponCode = null) {
        let discountHtml = '';
        if (discountAmt > 0 && couponCode) {
            discountHtml = `<p class="text-green-600 mb-1">Mã ưu đãi <span class="font-bold">${escapeHtml(couponCode)}</span>: -${formatPrice(discountAmt)}</p>`;
        }
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center animate-scale-in">
                <div class="w-20 h-20 bg-game-green/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-game-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="font-display text-3xl font-bold text-slate-800 mb-3">Thanh toán thành công!</h2>
                <p class="text-slate-600 mb-2">
                    Đã tạo thành công <span class="font-bold text-game-accent">${orderCount}</span> đơn hàng.
                </p>
                ${discountHtml}
                ${totalAmount ? `<p class="text-slate-600 mb-6">Tổng thanh toán: <span class="font-bold text-game-accent">${formatPrice(totalAmount)}</span></p>` : ''}
                <p class="text-slate-500 text-sm mb-6">
                    Tài khoản game đã được lưu và có thể xem trong phần đơn hàng.
                </p>
                <div class="flex gap-3">
                    <button onclick="this.closest('.fixed').remove()" 
                            class="flex-1 px-6 py-3 border border-game-border text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                        Ở lại trang
                    </button>
                    <a href="${BASE_URL}/orders" 
                       class="flex-1 px-6 py-3 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-xl hover:opacity-90 transition-all">
                        Xem đơn hàng
                    </a>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Auto close after 3 seconds
        setTimeout(() => {
            modal.style.opacity = '0';
            setTimeout(() => {
                if (modal.parentElement) {
                    modal.remove();
                }
            }, 300);
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', async () => {
        loadActiveCoupons();
        await fetchPromoData();
        renderCartItems();
        updateCartCount();
    });
</script>
@endpush

