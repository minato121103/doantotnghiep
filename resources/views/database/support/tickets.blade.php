@extends('layouts.app')

@section('title', 'Quản lý Yêu cầu Hỗ trợ')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">🎫 Quản lý Yêu cầu Hỗ trợ</h1>
            <p class="text-sm text-gray-600">Xem, phản hồi và quản lý các ticket hỗ trợ từ người dùng</p>
        </div>
        <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
            ← Quay lại
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600" id="stat-total">-</div>
            <div class="text-xs text-blue-700">📋 Tổng</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-600" id="stat-open">-</div>
            <div class="text-xs text-red-700">🔴 Mở</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600" id="stat-progress">-</div>
            <div class="text-xs text-yellow-700">🟡 Đang xử lý</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="stat-resolved">-</div>
            <div class="text-xs text-green-700">🟢 Đã giải quyết</div>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-gray-600" id="stat-closed">-</div>
            <div class="text-xs text-gray-700">⚫ Đã đóng</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" id="f-search" placeholder="Mã ticket, tên, email..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select id="f-status" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="all">Tất cả</option>
                    <option value="open">Mở</option>
                    <option value="in_progress">Đang xử lý</option>
                    <option value="resolved">Đã giải quyết</option>
                    <option value="closed">Đã đóng</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                <select id="f-category" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="all">Tất cả</option>
                    <option value="account">Tài khoản</option>
                    <option value="payment">Thanh toán</option>
                    <option value="order">Đơn hàng</option>
                    <option value="refund">Hoàn tiền</option>
                    <option value="technical">Kĩ thuật</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ưu tiên</label>
                <select id="f-priority" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="all">Tất cả</option>
                    <option value="urgent">Khẩn cấp</option>
                    <option value="high">Cao</option>
                    <option value="medium">Trung bình</option>
                    <option value="low">Thấp</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadTickets()" class="w-full bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mã ticket</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Người gửi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Danh mục</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tiêu đề</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ưu tiên</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="tickets-table" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                            Đang tải...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 flex justify-center gap-2"></div>

    <!-- Ticket Detail / Reply Modal -->
    <div id="ticket-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6" id="ticket-modal-content">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_URL = '{{ url("/api/support-tickets") }}';
    let currentPage = 1;

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    const categoryLabels = {
        account: '👤 Tài khoản',
        payment: '💳 Thanh toán',
        order: '📦 Đơn hàng',
        refund: '💰 Hoàn tiền',
        technical: '🔧 Kĩ thuật',
        other: '📋 Khác'
    };

    const statusLabels = {
        open: { text: 'Mở', cls: 'bg-red-100 text-red-700' },
        in_progress: { text: 'Đang xử lý', cls: 'bg-yellow-100 text-yellow-700' },
        resolved: { text: 'Đã giải quyết', cls: 'bg-green-100 text-green-700' },
        closed: { text: 'Đã đóng', cls: 'bg-gray-100 text-gray-700' }
    };

    const priorityLabels = {
        low: { text: 'Thấp', cls: 'bg-blue-100 text-blue-700' },
        medium: { text: 'TB', cls: 'bg-indigo-100 text-indigo-700' },
        high: { text: 'Cao', cls: 'bg-orange-100 text-orange-700' },
        urgent: { text: 'Khẩn cấp', cls: 'bg-red-100 text-red-700' }
    };

    async function loadTickets(page = 1) {
        currentPage = page;
        const token = getToken();
        if (!token) return;

        const status = document.getElementById('f-status').value;
        const category = document.getElementById('f-category').value;
        const priority = document.getElementById('f-priority').value;
        const search = document.getElementById('f-search').value;

        let url = `${API_URL}?page=${page}&per_page=15&sort_by=created_at&sort_order=desc`;
        if (status !== 'all') url += `&status=${status}`;
        if (category !== 'all') url += `&category=${category}`;
        if (priority !== 'all') url += `&priority=${priority}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;

        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            const result = await res.json();

            if (result.success) {
                renderTickets(result.data);
                renderPagination(result.pagination);
                updateStats(result.stats);
            } else {
                document.getElementById('tickets-table').innerHTML = `<tr><td colspan="8" class="px-4 py-8 text-center text-red-500">Lỗi: ${result.message}</td></tr>`;
            }
        } catch (err) {
            document.getElementById('tickets-table').innerHTML = `<tr><td colspan="8" class="px-4 py-8 text-center text-red-500">Lỗi kết nối: ${err.message}</td></tr>`;
        }
    }

    function updateStats(stats) {
        if (!stats) return;
        document.getElementById('stat-total').textContent = stats.total || 0;
        document.getElementById('stat-open').textContent = stats.open || 0;
        document.getElementById('stat-progress').textContent = stats.in_progress || 0;
        document.getElementById('stat-resolved').textContent = stats.resolved || 0;
        document.getElementById('stat-closed').textContent = stats.closed || 0;
    }

    function renderTickets(tickets) {
        const tbody = document.getElementById('tickets-table');
        if (!tickets || tickets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Không có yêu cầu hỗ trợ nào</td></tr>';
            return;
        }

        tbody.innerHTML = tickets.map(t => {
            const st = statusLabels[t.status] || statusLabels.open;
            const pr = priorityLabels[t.priority] || priorityLabels.medium;
            const cat = categoryLabels[t.category] || t.category;
            return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                    <div class="text-sm font-semibold text-indigo-600">${t.ticket_code}</div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900">${escHtml(t.name)}</div>
                    <div class="text-xs text-gray-500">${escHtml(t.email)}</div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">${cat}</td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-800 max-w-[200px] truncate">${escHtml(t.subject)}</div>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full ${pr.cls}">${pr.text}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full ${st.cls}">${st.text}</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">${formatDate(t.created_at)}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <button onclick="viewTicket(${t.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Xem</button>
                        <button onclick="deleteTicket(${t.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function renderPagination(p) {
        const c = document.getElementById('pagination');
        if (!p || p.last_page <= 1) { c.innerHTML = ''; return; }
        let h = '';
        if (p.current_page > 1) h += `<button onclick="loadTickets(${p.current_page-1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        for (let i = Math.max(1, p.current_page-2); i <= Math.min(p.last_page, p.current_page+2); i++) {
            h += `<button onclick="loadTickets(${i})" class="px-3 py-1 rounded ${i===p.current_page?'bg-indigo-500 text-white':'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        if (p.current_page < p.last_page) h += `<button onclick="loadTickets(${p.current_page+1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        c.innerHTML = h;
    }

    async function viewTicket(id) {
        const token = getToken();
        if (!token) return;
        try {
            const res = await fetch(`${API_URL}/${id}`, {
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            const result = await res.json();
            if (result.success) {
                renderTicketDetail(result.data);
                document.getElementById('ticket-modal').classList.remove('hidden');
            }
        } catch (err) {
            alert('Lỗi: ' + err.message);
        }
    }

    function renderTicketDetail(t) {
        const st = statusLabels[t.status] || statusLabels.open;
        const pr = priorityLabels[t.priority] || priorityLabels.medium;
        const cat = categoryLabels[t.category] || t.category;

        document.getElementById('ticket-modal-content').innerHTML = `
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">${escHtml(t.subject)}</h3>
                    <p class="text-sm text-indigo-600 font-semibold mt-1">${t.ticket_code}</p>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-gray-50 rounded-lg mb-5">
                <div>
                    <p class="text-xs text-gray-500">Người gửi</p>
                    <p class="font-medium text-sm">${escHtml(t.name)}</p>
                    <p class="text-xs text-gray-500">${escHtml(t.email)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Danh mục</p>
                    <p class="font-medium text-sm">${cat}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Ngày tạo</p>
                    <p class="font-medium text-sm">${formatDate(t.created_at)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Mã đơn hàng</p>
                    <p class="font-medium text-sm">${t.order_code || '—'}</p>
                </div>
            </div>

            <!-- Message -->
            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg mb-5">
                <p class="text-xs text-blue-600 font-semibold mb-2">💬 Nội dung yêu cầu</p>
                <p class="text-sm text-gray-800 whitespace-pre-wrap">${escHtml(t.message)}</p>
            </div>

            ${t.admin_reply ? `
            <div class="p-4 bg-green-50 border border-green-100 rounded-lg mb-5">
                <p class="text-xs text-green-600 font-semibold mb-2">✅ Phản hồi từ Admin${t.admin ? ` (${escHtml(t.admin.name)})` : ''} — ${t.replied_at ? formatDate(t.replied_at) : ''}</p>
                <p class="text-sm text-gray-800 whitespace-pre-wrap">${escHtml(t.admin_reply)}</p>
            </div>` : ''}

            <!-- Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select id="modal-status" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="open" ${t.status==='open'?'selected':''}>🔴 Mở</option>
                        <option value="in_progress" ${t.status==='in_progress'?'selected':''}>🟡 Đang xử lý</option>
                        <option value="resolved" ${t.status==='resolved'?'selected':''}>🟢 Đã giải quyết</option>
                        <option value="closed" ${t.status==='closed'?'selected':''}>⚫ Đã đóng</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ưu tiên</label>
                    <select id="modal-priority" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="low" ${t.priority==='low'?'selected':''}>Thấp</option>
                        <option value="medium" ${t.priority==='medium'?'selected':''}>Trung bình</option>
                        <option value="high" ${t.priority==='high'?'selected':''}>Cao</option>
                        <option value="urgent" ${t.priority==='urgent'?'selected':''}>Khẩn cấp</option>
                    </select>
                </div>
            </div>

            <!-- Reply -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phản hồi cho người dùng</label>
                <textarea id="modal-reply" rows="4" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500" placeholder="Nhập nội dung phản hồi...">${t.admin_reply || ''}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3">
                <button onclick="updateTicket(${t.id})" class="flex-1 bg-indigo-500 text-white py-2 rounded-lg hover:bg-indigo-600 transition font-medium">
                    💾 Lưu & Phản hồi
                </button>
                <button onclick="deleteTicket(${t.id}); closeModal();" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                    🗑️ Xóa
                </button>
                <button onclick="closeModal()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Đóng
                </button>
            </div>
        `;
    }

    async function updateTicket(id) {
        const token = getToken();
        if (!token) return;

        const status = document.getElementById('modal-status').value;
        const priority = document.getElementById('modal-priority').value;
        const reply = document.getElementById('modal-reply').value.trim();

        const body = { status, priority };
        if (reply) body.admin_reply = reply;

        try {
            const res = await fetch(`${API_URL}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(body)
            });
            const result = await res.json();
            if (result.success) {
                closeModal();
                loadTickets(currentPage);
            } else {
                alert(result.message || 'Lỗi cập nhật');
            }
        } catch (err) {
            alert('Lỗi: ' + err.message);
        }
    }

    async function deleteTicket(id) {
        if (!confirm('Bạn có chắc muốn xóa ticket này?')) return;
        const token = getToken();
        if (!token) return;

        try {
            const res = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            const result = await res.json();
            if (result.success) {
                loadTickets(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa');
            }
        } catch (err) {
            alert('Lỗi: ' + err.message);
        }
    }

    function closeModal() {
        document.getElementById('ticket-modal').classList.add('hidden');
    }

    document.getElementById('ticket-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    function formatDate(d) {
        if (!d) return '—';
        return new Date(d).toLocaleDateString('vi-VN', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
    }

    function escHtml(s) {
        if (!s) return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    // Init
    window.addEventListener('authReady', () => { loadTickets(); });
    document.addEventListener('DOMContentLoaded', () => { setTimeout(() => loadTickets(), 500); });
</script>
@endpush
