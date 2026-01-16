@extends('layouts.app')

@section('title', 'Quản lý Giao dịch')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-1">💰 Quản lý Giao dịch</h1>
            <p class="text-sm text-gray-500">Theo dõi lịch sử nạp tiền và mua hàng theo người dùng</p>
        </div>
        <a href="{{ route('database.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition duration-200 shadow-sm">
            <span>←</span> Quay lại
        </a>
    </div>

    <!-- Auth Warning -->
    <div id="auth-warning" class="hidden bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        ⚠️ Bạn cần đăng nhập với tài khoản Admin để xem tất cả giao dịch.
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm user</label>
                <input type="text" id="search" placeholder="Tên, email..." class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" onkeyup="filterUsers()">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" onchange="sortUsers()">
                    <option value="total_transactions">Số giao dịch</option>
                    <option value="total_deposit">Tổng nạp</option>
                    <option value="balance">Số dư</option>
                    <option value="name">Tên</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                <select id="sort-order" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" onchange="sortUsers()">
                    <option value="desc">Giảm dần</option>
                    <option value="asc">Tăng dần</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadData()" class="w-full bg-emerald-500 text-white px-4 py-2.5 rounded-lg hover:bg-emerald-600 transition font-medium">
                    🔄 Làm mới
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-indigo-600" id="stat-users">-</div>
            <div class="text-sm text-indigo-700 font-medium">👥 Người dùng</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-emerald-600" id="stat-total">-</div>
            <div class="text-sm text-emerald-700 font-medium">📊 Tổng GD</div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-green-600" id="stat-deposit">-</div>
            <div class="text-sm text-green-700 font-medium">💵 Nạp tiền</div>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-2xl font-bold text-amber-600" id="stat-amount">-</div>
            <div class="text-sm text-amber-700 font-medium">💰 Tổng nạp</div>
        </div>
    </div>

    <!-- Users List -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">👥 Danh sách người dùng có giao dịch</h2>
        </div>
        <div id="users-list" class="divide-y divide-gray-100">
            <div class="px-6 py-12 text-center text-gray-500">
                <div class="animate-spin w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full mx-auto mb-3"></div>
                <span class="text-sm">Đang tải dữ liệu...</span>
            </div>
        </div>
    </div>

    <!-- User Detail Modal -->
    <div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold mb-1" id="modal-user-name">Tên người dùng</h3>
                        <p class="text-emerald-100 text-sm" id="modal-user-email">email@example.com</p>
                    </div>
                    <button onclick="closeUserModal()" class="text-white hover:bg-white/20 rounded-lg p-2 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <!-- User Stats in Modal (Clickable) -->
                <div class="grid grid-cols-4 gap-4 mt-4">
                    <button onclick="filterModalTransactions('all')" id="modal-btn-all" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition cursor-pointer ring-2 ring-white/50">
                        <div class="text-2xl font-bold" id="modal-stat-total">0</div>
                        <div class="text-xs text-emerald-100">Tổng GD</div>
                    </button>
                    <button onclick="filterModalTransactions('deposit')" id="modal-btn-deposit" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition cursor-pointer">
                        <div class="text-2xl font-bold" id="modal-stat-deposit">0</div>
                        <div class="text-xs text-emerald-100">💵 Nạp</div>
                    </button>
                    <button onclick="filterModalTransactions('purchase')" id="modal-btn-purchase" class="bg-white/20 hover:bg-white/30 rounded-lg p-3 text-center transition cursor-pointer">
                        <div class="text-2xl font-bold" id="modal-stat-purchase">0</div>
                        <div class="text-xs text-emerald-100">🛒 Mua</div>
                    </button>
                    <div class="bg-white/20 rounded-lg p-3 text-center">
                        <div class="text-lg font-bold" id="modal-stat-balance">0đ</div>
                        <div class="text-xs text-emerald-100">💰 Số dư</div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">📋 <span id="modal-filter-label">Tất cả giao dịch</span></h4>
                    <span id="modal-filter-count" class="text-sm text-gray-500"></span>
                </div>
                <div id="modal-transactions" class="space-y-3">
                    <!-- Transactions will be loaded here -->
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <button onclick="closeUserModal()" class="w-full bg-gray-500 text-white py-2.5 rounded-lg hover:bg-gray-600 transition font-medium">
                    Đóng
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_URL = '{{ url("/api/transactions") }}';
    let allTransactions = [];
    let usersData = [];
    let filteredUsers = [];
    let currentModalUser = null;
    let currentModalFilter = 'all';

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    async function loadData() {
        const token = getToken();
        
        if (!token) {
            document.getElementById('auth-warning').classList.remove('hidden');
            document.getElementById('users-list').innerHTML = `
                <div class="px-6 py-12 text-center text-yellow-600">
                    Vui lòng <a href="{{ url('/login') }}" class="underline font-semibold">đăng nhập</a> để xem giao dịch
                </div>
            `;
            return;
        }
        
        document.getElementById('auth-warning').classList.add('hidden');
        document.getElementById('users-list').innerHTML = `
            <div class="px-6 py-12 text-center text-gray-500">
                <div class="animate-spin w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full mx-auto mb-3"></div>
                <span class="text-sm">Đang tải dữ liệu...</span>
            </div>
        `;
        
        try {
            const response = await fetch(`${API_URL}?per_page=10000`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const result = await response.json();
            
            if (result.success) {
                allTransactions = result.data;
                processUsersData();
                updateStats();
                sortUsers();
            } else {
                document.getElementById('users-list').innerHTML = `
                    <div class="px-6 py-12 text-center text-red-500">${result.message || 'Lỗi tải dữ liệu'}</div>
                `;
            }
        } catch (error) {
            document.getElementById('users-list').innerHTML = `
                <div class="px-6 py-12 text-center text-red-500">Lỗi: ${error.message}</div>
            `;
        }
    }

    function processUsersData() {
        const usersMap = new Map();
        
        // First pass: collect all transactions per user
        allTransactions.forEach(trans => {
            if (!trans.user) return;
            
            const userId = trans.user.id;
            
            // Always update user info from the latest transaction's user object
            // trans.user.balance is the CURRENT balance from database
            if (!usersMap.has(userId)) {
                usersMap.set(userId, {
                    id: userId,
                    name: trans.user.name,
                    email: trans.user.email,
                    balance: parseFloat(trans.user.balance) || 0, // Current balance from user table
                    transactions: [],
                    total_deposit: 0,
                    total_purchase: 0,
                    deposit_count: 0,
                    purchase_count: 0
                });
            } else {
                // Update balance to the most recent value (in case user data changed)
                const userData = usersMap.get(userId);
                // trans.user.balance is always current, so update it
                userData.balance = parseFloat(trans.user.balance) || userData.balance;
            }
            
            const userData = usersMap.get(userId);
            userData.transactions.push(trans);
            
            if (trans.type === 'deposit') {
                userData.deposit_count++;
                userData.total_deposit += parseFloat(trans.amount) || 0;
            }
            if (trans.type === 'purchase') {
                userData.purchase_count++;
                userData.total_purchase += parseFloat(trans.amount) || 0;
            }
        });
        
        // Second pass: sort transactions
        usersData = Array.from(usersMap.values());
        usersData.forEach(user => {
            user.total_transactions = user.transactions.length;
            
            // Sort transactions by date (newest first)
            user.transactions.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        });
        
        filteredUsers = [...usersData];
        
        console.log('Processed users:', usersData); // Debug log
    }

    function updateStats() {
        const totalUsers = usersData.length;
        const totalTrans = allTransactions.length;
        let depositCount = 0;
        let totalAmount = 0;
        
        allTransactions.forEach(t => {
            if (t.type === 'deposit') {
                depositCount++;
                totalAmount += parseFloat(t.amount) || 0;
            }
        });
        
        document.getElementById('stat-users').textContent = totalUsers;
        document.getElementById('stat-total').textContent = totalTrans;
        document.getElementById('stat-deposit').textContent = depositCount;
        document.getElementById('stat-amount').textContent = new Intl.NumberFormat('vi-VN').format(totalAmount) + 'đ';
    }

    function filterUsers() {
        const search = document.getElementById('search').value.toLowerCase();
        
        if (!search) {
            filteredUsers = [...usersData];
        } else {
            filteredUsers = usersData.filter(user => 
                user.name?.toLowerCase().includes(search) || 
                user.email?.toLowerCase().includes(search)
            );
        }
        
        sortUsers();
    }

    function sortUsers() {
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        filteredUsers.sort((a, b) => {
            let valA, valB;
            
            switch(sortBy) {
                case 'total_transactions':
                    valA = a.total_transactions;
                    valB = b.total_transactions;
                    break;
                case 'total_deposit':
                    valA = a.total_deposit;
                    valB = b.total_deposit;
                    break;
                case 'balance':
                    valA = a.balance;
                    valB = b.balance;
                    break;
                case 'name':
                    valA = a.name?.toLowerCase() || '';
                    valB = b.name?.toLowerCase() || '';
                    break;
                default:
                    valA = a.total_transactions;
                    valB = b.total_transactions;
            }
            
            if (sortBy === 'name') {
                return sortOrder === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
            }
            
            return sortOrder === 'asc' ? valA - valB : valB - valA;
        });
        
        renderUsers();
    }

    function renderUsers() {
        const container = document.getElementById('users-list');
        
        if (!filteredUsers || filteredUsers.length === 0) {
            container.innerHTML = '<div class="px-6 py-12 text-center text-gray-500"><div class="text-4xl mb-2">👥</div>Không có người dùng nào</div>';
            return;
        }
        
        container.innerHTML = filteredUsers.map(user => `
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <!-- User Info -->
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            ${user.name ? user.name.charAt(0).toUpperCase() : '?'}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">${user.name || 'Không có tên'}</div>
                            <div class="text-sm text-gray-500">${user.email || 'Không có email'}</div>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex items-center gap-6 text-sm">
                        <div class="text-center">
                            <div class="font-bold text-gray-800">${user.total_transactions}</div>
                            <div class="text-xs text-gray-500">Giao dịch</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-green-600">+${new Intl.NumberFormat('vi-VN').format(user.total_deposit)}đ</div>
                            <div class="text-xs text-gray-500">${user.deposit_count} lần nạp</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-blue-600">-${new Intl.NumberFormat('vi-VN').format(Math.abs(user.total_purchase))}đ</div>
                            <div class="text-xs text-gray-500">${user.purchase_count} lần mua</div>
                        </div>
                        <div class="text-center hidden sm:block">
                            <div class="font-bold text-amber-600">${new Intl.NumberFormat('vi-VN').format(user.balance)}đ</div>
                            <div class="text-xs text-gray-500">Số dư</div>
                        </div>
                    </div>
                    
                    <!-- Action -->
                    <button onclick="viewUserDetails(${user.id})" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition font-medium text-sm whitespace-nowrap">
                        📋 Xem chi tiết
                    </button>
                </div>
            </div>
        `).join('');
    }

    function viewUserDetails(userId) {
        const user = usersData.find(u => u.id === userId);
        if (!user) {
            alert('Không tìm thấy người dùng');
            return;
        }
        
        // Save current user
        currentModalUser = user;
        currentModalFilter = 'all';
        
        // Set user info
        document.getElementById('modal-user-name').textContent = user.name || 'Không có tên';
        document.getElementById('modal-user-email').textContent = user.email || 'Không có email';
        
        // Set stats
        document.getElementById('modal-stat-total').textContent = user.total_transactions;
        document.getElementById('modal-stat-deposit').textContent = user.deposit_count;
        document.getElementById('modal-stat-purchase').textContent = user.purchase_count;
        document.getElementById('modal-stat-balance').textContent = new Intl.NumberFormat('vi-VN').format(user.balance) + 'đ';
        
        // Reset filter buttons
        updateFilterButtons('all');
        
        // Render all transactions
        filterModalTransactions('all');
        
        // Show modal
        document.getElementById('user-modal').classList.remove('hidden');
    }

    function filterModalTransactions(type) {
        if (!currentModalUser) return;
        
        currentModalFilter = type;
        updateFilterButtons(type);
        
        // Filter transactions
        let filteredTrans = [...currentModalUser.transactions];
        let filterLabel = 'Tất cả giao dịch';
        
        if (type === 'deposit') {
            filteredTrans = filteredTrans.filter(t => t.type === 'deposit');
            filterLabel = '💵 Giao dịch nạp tiền';
        } else if (type === 'purchase') {
            filteredTrans = filteredTrans.filter(t => t.type === 'purchase');
            filterLabel = '🛒 Giao dịch mua hàng';
        }
        
        // Sort by date descending
        filteredTrans.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        
        // Update label and count
        document.getElementById('modal-filter-label').textContent = filterLabel;
        document.getElementById('modal-filter-count').textContent = `(${filteredTrans.length} giao dịch)`;
        
        // Render
        renderUserTransactions(filteredTrans);
    }

    function updateFilterButtons(activeType) {
        const buttons = ['all', 'deposit', 'purchase'];
        buttons.forEach(type => {
            const btn = document.getElementById(`modal-btn-${type}`);
            if (btn) {
                if (type === activeType) {
                    btn.classList.add('ring-2', 'ring-white/50');
                } else {
                    btn.classList.remove('ring-2', 'ring-white/50');
                }
            }
        });
    }

    function renderUserTransactions(transactions) {
        const container = document.getElementById('modal-transactions');
        
        if (!transactions || transactions.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 py-8">Chưa có giao dịch nào</div>';
            return;
        }
        
        container.innerHTML = transactions.map(trans => {
            const typeConfig = {
                'deposit': { color: 'bg-green-100 text-green-700 border-green-200', icon: '💵', label: 'Nạp tiền' },
                'purchase': { color: 'bg-blue-100 text-blue-700 border-blue-200', icon: '🛒', label: 'Mua hàng' }
            };
            const statusConfig = {
                'pending': { color: 'text-yellow-600', icon: '🟡', label: 'Chờ xử lý' },
                'completed': { color: 'text-green-600', icon: '🟢', label: 'Hoàn thành' },
                'failed': { color: 'text-red-600', icon: '🔴', label: 'Thất bại' }
            };
            
            const typeInfo = typeConfig[trans.type] || { color: 'bg-gray-100 text-gray-700', icon: '📋', label: trans.type };
            const statusInfo = statusConfig[trans.status] || { color: 'text-gray-600', icon: '⚪', label: trans.status };
            const isPositive = trans.type === 'deposit';
            const amountColor = isPositive ? 'text-green-600' : 'text-red-600';
            const amountSign = isPositive ? '+' : '-';
            
            return `
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-emerald-300 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${typeInfo.color} border">${typeInfo.icon} ${typeInfo.label}</span>
                            <span class="text-xs ${statusInfo.color}">${statusInfo.icon} ${statusInfo.label}</span>
                        </div>
                        <span class="text-lg font-bold ${amountColor}">${amountSign}${new Intl.NumberFormat('vi-VN').format(Math.abs(trans.amount))}đ</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span class="font-mono">${trans.transaction_code || 'TXN-' + trans.id}</span>
                        <span>${formatDate(trans.created_at)}</span>
                    </div>
                    ${trans.description ? `<div class="text-sm text-gray-600 mt-2 bg-white rounded-lg p-2 border border-gray-100">${trans.description}</div>` : ''}
                    <div class="flex justify-between text-xs text-gray-400 mt-3 pt-2 border-t border-gray-200">
                        <span>Số dư trước: <strong class="text-gray-600">${formatBalance(trans.balance_before)}</strong></span>
                        <span>Số dư sau: <strong class="text-gray-600">${formatBalance(trans.balance_after)}</strong></span>
                    </div>
                </div>
            `;
        }).join('');
    }

    function formatBalance(amount) {
        if (amount === null || amount === undefined) return '-';
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
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

    function closeUserModal() {
        document.getElementById('user-modal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('user-modal').addEventListener('click', function(e) {
        if (e.target === this) closeUserModal();
    });

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadData();
    });
</script>
@endpush
