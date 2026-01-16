@extends('layouts.app')

@section('title', 'Quản lý Thảo luận')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">💬 Quản lý Thảo luận</h1>
            <p class="text-sm text-gray-600">Xem và quản lý các cuộc thảo luận trong hệ thống</p>
        </div>
        <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
            ← Quay lại
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600" id="stat-comments">-</div>
            <div class="text-sm text-indigo-700">💬 Comment gốc</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600" id="stat-replies">-</div>
            <div class="text-sm text-purple-700">↳ Tổng phản hồi</div>
        </div>
        <div class="bg-pink-50 border border-pink-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-pink-600" id="stat-likes">-</div>
            <div class="text-sm text-pink-700">❤️ Tổng lượt thích</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" id="search" placeholder="Nội dung, người dùng, game..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="latest">Mới nhất</option>
                    <option value="replies">Số phản hồi</option>
                    <option value="likes">Lượt thích</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                <select id="sort-order" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="desc">Giảm dần</option>
                    <option value="asc">Tăng dần</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="renderComments()" class="w-full bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Comments List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <div class="flex items-center justify-between">
                <span class="font-medium text-gray-700">Danh sách comment gốc</span>
                <span class="text-sm text-gray-500" id="showing-info">-</span>
            </div>
        </div>
        <div id="comments-container" class="divide-y divide-gray-200">
            <div class="p-8 text-center text-gray-500">
                <div class="animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                Đang tải...
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 flex justify-center gap-2"></div>

    <!-- Replies Modal -->
    <div id="replies-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-4 border-b bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                <div class="flex justify-between items-start">
                    <div class="flex-1 min-w-0 pr-4">
                        <h3 class="text-lg font-bold" id="modal-title">💬 Chi tiết thảo luận</h3>
                        <p class="text-sm text-indigo-100 truncate" id="modal-game">-</p>
                    </div>
                    <button onclick="closeModal()" class="text-white hover:text-indigo-200 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="modal-content" class="flex-1 overflow-y-auto p-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_URL = '{{ url("/api/discussions") }}';
    let allDiscussions = [];
    let parentComments = [];
    let repliesMap = {};
    let currentPage = 1;
    const perPage = 10;

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    // Load all discussions
    async function loadData() {
        try {
            const response = await fetch(`${API_URL}?per_page=10000`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            
            if (result.success) {
                allDiscussions = result.data;
                processData();
                updateStats();
                renderComments();
            }
        } catch (error) {
            console.error('Error loading data:', error);
            document.getElementById('comments-container').innerHTML = `
                <div class="p-8 text-center text-red-500">Lỗi: ${error.message}</div>
            `;
        }
    }

    function processData() {
        parentComments = [];
        repliesMap = {};
        
        // Separate parent comments and replies
        allDiscussions.forEach(disc => {
            if (!disc.parent_id) {
                parentComments.push({
                    ...disc,
                    repliesCount: 0,
                    totalLikes: disc.like_count || 0
                });
            } else {
                if (!repliesMap[disc.parent_id]) {
                    repliesMap[disc.parent_id] = [];
                }
                repliesMap[disc.parent_id].push(disc);
            }
        });
        
        // Count replies for each parent
        parentComments.forEach(comment => {
            const replies = repliesMap[comment.id] || [];
            comment.repliesCount = replies.length;
            comment.totalLikes += replies.reduce((sum, r) => sum + (r.like_count || 0), 0);
        });
    }

    function updateStats() {
        const commentsCount = parentComments.length;
        const repliesCount = allDiscussions.filter(d => d.parent_id).length;
        const totalLikes = allDiscussions.reduce((sum, d) => sum + (d.like_count || 0), 0);
        
        document.getElementById('stat-comments').textContent = commentsCount;
        document.getElementById('stat-replies').textContent = repliesCount;
        document.getElementById('stat-likes').textContent = totalLikes;
    }

    function renderComments() {
        const container = document.getElementById('comments-container');
        const search = document.getElementById('search').value.toLowerCase();
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        // Filter comments
        let filtered = parentComments.filter(c => {
            if (search) {
                const matchContent = c.content && c.content.toLowerCase().includes(search);
                const matchUser = c.display_name && c.display_name.toLowerCase().includes(search);
                const matchGame = c.product?.title && c.product.title.toLowerCase().includes(search);
                if (!matchContent && !matchUser && !matchGame) return false;
            }
            return true;
        });
        
        // Sort comments
        filtered.sort((a, b) => {
            let valA, valB;
            switch (sortBy) {
                case 'replies':
                    valA = a.repliesCount;
                    valB = b.repliesCount;
                    break;
                case 'likes':
                    valA = a.totalLikes;
                    valB = b.totalLikes;
                    break;
                case 'latest':
                default:
                    valA = new Date(a.created_at || 0);
                    valB = new Date(b.created_at || 0);
            }
            return sortOrder === 'asc' ? valA - valB : valB - valA;
        });
        
        // Pagination
        const totalPages = Math.ceil(filtered.length / perPage);
        const start = (currentPage - 1) * perPage;
        const paginated = filtered.slice(start, start + perPage);
        
        document.getElementById('showing-info').textContent = `Hiển thị ${start + 1}-${Math.min(start + perPage, filtered.length)} / ${filtered.length} comment`;
        
        if (paginated.length === 0) {
            container.innerHTML = '<div class="p-8 text-center text-gray-500">Không có comment nào</div>';
            document.getElementById('pagination').innerHTML = '';
            return;
        }
        
        const statusConfig = {
            'approved': { color: 'bg-green-100 text-green-700 border-green-200', icon: '✅' },
            'pending': { color: 'bg-yellow-100 text-yellow-700 border-yellow-200', icon: '⏳' }
        };
        
        container.innerHTML = paginated.map(comment => {
            const statusInfo = statusConfig[comment.status] || { color: 'bg-gray-100 text-gray-700', icon: '❓' };
            const hasPendingReplies = (repliesMap[comment.id] || []).some(r => r.status === 'pending');
            
            return `
                <div class="p-4 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                            ${(comment.display_name || 'U').charAt(0).toUpperCase()}
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <!-- User & Game Info -->
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="font-semibold text-gray-900">${escapeHtml(comment.display_name || 'Ẩn danh')}</span>
                                <span class="text-gray-400">•</span>
                                <span class="text-xs px-2 py-0.5 rounded-full border ${statusInfo.color}">${statusInfo.icon}</span>
                                <span class="text-xs text-gray-500">${formatDate(comment.created_at)}</span>
                            </div>
                            
                            <!-- Game Badge -->
                            <div class="mb-2">
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700">
                                    🎮 ${escapeHtml(comment.product?.title || 'Product #' + comment.product_simple_id)}
                                </span>
                            </div>
                            
                            <!-- Comment Content -->
                            <div class="text-gray-700 mb-2 line-clamp-2">${escapeHtml(comment.content)}</div>
                            
                            <!-- Stats -->
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <span class="text-pink-500">❤️</span> ${comment.like_count || 0} thích
                                </span>
                                <span class="flex items-center gap-1 ${comment.repliesCount > 0 ? 'text-blue-600 font-medium' : ''}">
                                    <span>💬</span> ${comment.repliesCount} phản hồi
                                </span>
                                ${hasPendingReplies ? '<span class="text-yellow-600 text-xs">⚠️ Có phản hồi chờ duyệt</span>' : ''}
                                <span class="text-gray-400 text-xs">ID: #${comment.id}</span>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-col gap-2 flex-shrink-0">
                            <button onclick="viewReplies(${comment.id})" class="px-4 py-2 bg-indigo-500 text-white text-sm rounded-lg hover:bg-indigo-600 transition flex items-center gap-1">
                                <span>Xem chi tiết</span>
                                ${comment.repliesCount > 0 ? `<span class="bg-white/20 px-1.5 py-0.5 rounded text-xs">${comment.repliesCount}</span>` : ''}
                            </button>
                            <div class="flex gap-1">
                                ${comment.status === 'pending' ? `<button onclick="approveComment(${comment.id})" class="flex-1 px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">Duyệt</button>` : ''}
                                <button onclick="deleteComment(${comment.id})" class="flex-1 px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        // Render pagination
        renderPagination(totalPages, filtered.length);
    }

    function renderPagination(totalPages, totalItems) {
        const container = document.getElementById('pagination');
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let html = '';
        
        if (currentPage > 1) {
            html += `<button onclick="goToPage(${currentPage - 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        }
        
        for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
            html += `<button onclick="goToPage(${i})" class="px-3 py-1 rounded ${i === currentPage ? 'bg-indigo-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        
        if (currentPage < totalPages) {
            html += `<button onclick="goToPage(${currentPage + 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        }
        
        container.innerHTML = html;
    }

    function goToPage(page) {
        currentPage = page;
        renderComments();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function viewReplies(commentId) {
        const comment = parentComments.find(c => c.id === commentId);
        if (!comment) return;
        
        const replies = repliesMap[commentId] || [];
        
        // Sort replies by date (oldest first for conversation flow)
        replies.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        
        document.getElementById('modal-title').textContent = `💬 Cuộc trò chuyện (${replies.length} phản hồi)`;
        document.getElementById('modal-game').textContent = `🎮 ${comment.product?.title || 'Product #' + comment.product_simple_id}`;
        
        const statusConfig = {
            'approved': { color: 'bg-green-100 text-green-700 border-green-200', icon: '✅' },
            'pending': { color: 'bg-yellow-100 text-yellow-700 border-yellow-200', icon: '⏳' }
        };
        
        const commentStatusInfo = statusConfig[comment.status] || { color: 'bg-gray-100 text-gray-700', icon: '❓' };
        
        let html = `
            <!-- Original Comment -->
            <div class="mb-6">
                <div class="text-xs text-gray-500 uppercase tracking-wide mb-2 font-medium">Comment gốc</div>
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center text-white font-semibold flex-shrink-0">
                            ${(comment.display_name || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="font-semibold text-gray-900">${escapeHtml(comment.display_name || 'Ẩn danh')}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full border ${commentStatusInfo.color}">${commentStatusInfo.icon}</span>
                                <span class="text-xs text-gray-500">${formatDate(comment.created_at)}</span>
                                <span class="text-xs text-gray-400">• ID: #${comment.id}</span>
                            </div>
                            <div class="text-gray-700 mb-2">${escapeHtml(comment.content)}</div>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="text-pink-500">❤️ ${comment.like_count || 0}</span>
                                <div class="ml-auto flex gap-2">
                                    ${comment.status === 'pending' ? `<button onclick="approveAndRefresh(${comment.id})" class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200">Duyệt</button>` : ''}
                                    <button onclick="deleteAndRefresh(${comment.id})" class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200">Xóa comment</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Replies Section -->
            <div>
                <div class="text-xs text-gray-500 uppercase tracking-wide mb-2 font-medium flex items-center gap-2">
                    <span>↳ Phản hồi</span>
                    <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">${replies.length}</span>
                </div>
        `;
        
        if (replies.length === 0) {
            html += '<div class="text-center text-gray-500 py-8 bg-gray-50 rounded-lg">Chưa có phản hồi nào</div>';
        } else {
            html += '<div class="space-y-3">';
            replies.forEach((reply, index) => {
                const replyStatusInfo = statusConfig[reply.status] || { color: 'bg-gray-100 text-gray-700', icon: '❓' };
                html += `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 ml-4 border-l-4 border-l-blue-400">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-400 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                ${(reply.display_name || 'U').charAt(0).toUpperCase()}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="font-medium text-gray-900 text-sm">${escapeHtml(reply.display_name || 'Ẩn danh')}</span>
                                    <span class="text-xs px-1.5 py-0.5 rounded-full border ${replyStatusInfo.color}">${replyStatusInfo.icon}</span>
                                    <span class="text-xs text-gray-500">${formatDate(reply.created_at)}</span>
                                    <span class="text-xs text-gray-400">• ID: #${reply.id}</span>
                                </div>
                                <div class="text-gray-600 text-sm mb-1">${escapeHtml(reply.content)}</div>
                                <div class="flex items-center gap-3 text-xs">
                                    <span class="text-pink-500">❤️ ${reply.like_count || 0}</span>
                                    <div class="ml-auto flex gap-2">
                                        ${reply.status === 'pending' ? `<button onclick="approveReply(${reply.id}, ${commentId})" class="px-2 py-0.5 bg-green-100 text-green-700 rounded hover:bg-green-200">Duyệt</button>` : ''}
                                        <button onclick="deleteReply(${reply.id}, ${commentId})" class="px-2 py-0.5 bg-red-100 text-red-700 rounded hover:bg-red-200">Xóa</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        html += '</div>';
        
        document.getElementById('modal-content').innerHTML = html;
        document.getElementById('replies-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('replies-modal').classList.add('hidden');
    }

    async function approveComment(id) {
        await doApprove(id);
        await loadData();
    }

    async function deleteComment(id) {
        if (!confirm('Bạn có chắc muốn xóa comment này và tất cả phản hồi của nó?')) return;
        await doDelete(id);
        await loadData();
    }

    async function approveAndRefresh(id) {
        await doApprove(id);
        await loadData();
        closeModal();
    }

    async function deleteAndRefresh(id) {
        if (!confirm('Bạn có chắc muốn xóa comment này và tất cả phản hồi của nó?')) return;
        await doDelete(id);
        await loadData();
        closeModal();
    }

    async function approveReply(replyId, commentId) {
        await doApprove(replyId);
        await loadData();
        viewReplies(commentId);
    }

    async function deleteReply(replyId, commentId) {
        if (!confirm('Bạn có chắc muốn xóa phản hồi này?')) return;
        await doDelete(replyId);
        await loadData();
        viewReplies(commentId);
    }

    async function doApprove(id) {
        const token = getToken();
        if (!token) {
            alert('Vui lòng đăng nhập để thực hiện thao tác này');
            return false;
        }
        
        try {
            const response = await fetch(`${API_URL}/${id}/approve`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            const result = await response.json();
            if (!result.success) {
                alert(result.message || 'Lỗi duyệt thảo luận');
                return false;
            }
            return true;
        } catch (error) {
            alert('Lỗi: ' + error.message);
            return false;
        }
    }

    async function doDelete(id) {
        const token = getToken();
        if (!token) {
            alert('Vui lòng đăng nhập để thực hiện thao tác này');
            return false;
        }
        
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            const result = await response.json();
            if (!result.success) {
                alert(result.message || 'Lỗi xóa thảo luận');
                return false;
            }
            return true;
        } catch (error) {
            alert('Lỗi: ' + error.message);
            return false;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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

    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            renderComments();
        }, 500);
    });
    
    // Close modal when clicking outside
    document.getElementById('replies-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('DOMContentLoaded', () => loadData());
</script>
@endpush
