@extends('layouts.app')

@section('title', 'Quản lý Đơn hàng')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">🛒 Quản lý Đơn hàng</h1>
            <p class="text-sm text-gray-600">Xem và quản lý đơn hàng đã hoàn thành</p>
        </div>
        <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
            ← Quay lại
        </a>
    </div>

    <!-- Auth Warning -->
    <div id="auth-warning" class="hidden bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        ⚠️ Bạn cần đăng nhập với tài khoản Admin để xem tất cả đơn hàng.
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm mã đơn hàng</label>
                <input type="text" id="search" placeholder="ORD..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="id">ID</option>
                    <option value="amount">Số tiền</option>
                    <option value="created_at">Ngày tạo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                <select id="sort-order" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="desc">Mới nhất</option>
                    <option value="asc">Cũ nhất</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadOrders()" class="w-full bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="stat-total">-</div>
            <div class="text-sm text-green-700">🟢 Tổng đơn hàng</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600" id="stat-revenue">-</div>
            <div class="text-sm text-purple-700">💰 Tổng doanh thu</div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mã đơn</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Người mua</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sản phẩm</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Số tiền</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Thanh toán</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ngày mua</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="orders-table" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="animate-spin w-8 h-8 border-4 border-purple-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                            Đang tải...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 flex justify-center gap-2"></div>

    <!-- Order Detail Modal -->
    <div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Chi tiết đơn hàng</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="order-detail-content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_URL = '{{ url("/api/orders") }}';
    let currentPage = 1;
    let allOrders = [];

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    // Load stats from all data
    async function loadStats() {
        const token = getToken();
        if (!token) return;
        
        try {
            const response = await fetch(`${API_URL}?per_page=10000`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const result = await response.json();
            
            if (result.success) {
                const allData = result.data;
                document.getElementById('stat-total').textContent = result.pagination?.total || allData.length;
                const totalRevenue = allData.reduce((sum, o) => sum + parseFloat(o.amount || 0), 0);
                document.getElementById('stat-revenue').textContent = formatCurrency(totalRevenue);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async function loadOrders(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        const token = getToken();
        
        if (!token) {
            document.getElementById('auth-warning').classList.remove('hidden');
            document.getElementById('orders-table').innerHTML = `
                <tr><td colspan="7" class="px-4 py-8 text-center text-yellow-600">
                    Vui lòng <a href="{{ url('/login') }}" class="underline font-semibold">đăng nhập</a> để xem đơn hàng
                </td></tr>
            `;
            return;
        }
        
        document.getElementById('auth-warning').classList.add('hidden');
        
        try {
            let url = `${API_URL}?page=${page}&per_page=15&sort_by=${sortBy}&sort_order=${sortOrder}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                allOrders = result.data;
                renderOrders(result.data);
                renderPagination(result.pagination);
            } else {
                document.getElementById('orders-table').innerHTML = `
                    <tr><td colspan="7" class="px-4 py-8 text-center text-red-500">${result.message || 'Lỗi tải dữ liệu'}</td></tr>
                `;
            }
        } catch (error) {
            document.getElementById('orders-table').innerHTML = `
                <tr><td colspan="7" class="px-4 py-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>
            `;
        }
    }

    function renderOrders(orders) {
        const tbody = document.getElementById('orders-table');
        
        if (!orders || orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Không có đơn hàng</td></tr>';
            return;
        }
        
        const paymentMethods = {
            'balance': '💰 Balance',
            'banking': '🏦 Banking',
            'momo': '📱 Momo',
            'zalopay': '💳 ZaloPay'
        };
        
        tbody.innerHTML = orders.map(order => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-purple-600">${order.order_code || '#' + order.id}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900">${order.buyer?.name || '-'}</div>
                    <div class="text-xs text-gray-500">${order.buyer?.email || ''}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-600 max-w-[200px] truncate">${order.game?.title || 'Product #' + order.product_simple_id}</div>
                </td>
                <td class="px-4 py-3 text-sm font-semibold text-green-600">${formatCurrency(order.amount)}</td>
                <td class="px-4 py-3 text-sm text-gray-600">${paymentMethods[order.payment_method] || order.payment_method}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${formatDate(order.completed_at || order.created_at)}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <button onclick="viewOrder(${order.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Chi tiết</button>
                        <button onclick="deleteOrder(${order.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderPagination(pagination) {
        const container = document.getElementById('pagination');
        if (!pagination || pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let html = '';
        
        if (pagination.current_page > 1) {
            html += `<button onclick="loadOrders(${pagination.current_page - 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        }
        
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            html += `<button onclick="loadOrders(${i})" class="px-3 py-1 rounded ${i === pagination.current_page ? 'bg-purple-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="loadOrders(${pagination.current_page + 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        }
        
        container.innerHTML = html;
    }

    async function viewOrder(id) {
        const token = getToken();
        if (!token) return;
        
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                renderOrderDetail(result.data);
                document.getElementById('order-modal').classList.remove('hidden');
            } else {
                alert(result.message || 'Lỗi tải chi tiết đơn hàng');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }

    function renderOrderDetail(order) {
        let credentialsHtml = '';
        if (order.items && order.items.length > 0) {
            credentialsHtml = `
                <div class="mt-4 p-4 bg-slate-800 rounded-lg text-white">
                    <h4 class="font-semibold mb-2">🔐 Thông tin tài khoản Steam</h4>
                    ${order.items.map(item => `
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-gray-400">Username:</span> <span class="font-mono">${item.steam_credentials?.username || '-'}</span></div>
                            <div><span class="text-gray-400">Password:</span> <span class="font-mono">${item.steam_credentials?.password || '-'}</span></div>
                            ${item.steam_credentials?.email ? `<div><span class="text-gray-400">Email:</span> <span class="font-mono">${item.steam_credentials.email}</span></div>` : ''}
                            ${item.steam_credentials?.email_password ? `<div><span class="text-gray-400">Email Pass:</span> <span class="font-mono">${item.steam_credentials.email_password}</span></div>` : ''}
                        </div>
                    `).join('<hr class="my-2 border-gray-600">')}
                </div>
            `;
        }
        
        document.getElementById('order-detail-content').innerHTML = `
            <div class="space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-lg font-bold text-purple-600">${order.order_code || '#' + order.id}</p>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">🟢 Hoàn thành</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600">${formatCurrency(order.amount)}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="text-xs text-gray-500">Người mua</p>
                        <p class="font-medium">${order.buyer?.name || '-'}</p>
                        <p class="text-sm text-gray-500">${order.buyer?.email || ''}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Sản phẩm</p>
                        <p class="font-medium">${order.game?.title || '-'}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Thanh toán</p>
                        <p class="font-medium">${order.payment_method}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Ngày mua</p>
                        <p class="font-medium text-green-600">${formatDate(order.completed_at || order.created_at)}</p>
                    </div>
                </div>
                
                ${order.notes ? `<div class="p-3 bg-yellow-50 rounded-lg"><p class="text-sm text-yellow-700"><strong>Ghi chú:</strong> ${order.notes}</p></div>` : ''}
                
                ${credentialsHtml}
                
                <div class="flex gap-2 pt-4 border-t">
                    <button onclick="deleteOrder(${order.id}); closeModal();" class="flex-1 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">🗑️ Xóa đơn hàng</button>
                </div>
            </div>
        `;
    }

    function closeModal() {
        document.getElementById('order-modal').classList.add('hidden');
    }

    async function deleteOrder(id) {
        if (!confirm('Bạn có chắc muốn xóa đơn hàng này? Hành động này không thể hoàn tác.')) return;
        
        const token = getToken();
        if (!token) return;
        
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                loadOrders(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa đơn hàng');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount || 0) + ' đ';
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('vi-VN', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    // Close modal when clicking outside
    document.getElementById('order-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadStats();   // Load stats from all data
        loadOrders();  // Load paginated data for table
    });
</script>
@endpush
