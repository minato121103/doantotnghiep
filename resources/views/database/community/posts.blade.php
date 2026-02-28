@extends('layouts.app')

@section('title', 'Quản lý Bài viết Cộng đồng')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">🌐 Quản lý Bài viết Cộng đồng</h1>
            <p class="text-sm text-gray-600">Kiểm duyệt và quản lý các bài viết trong cộng đồng</p>
        </div>
        <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
            ← Quay lại
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600" id="stat-total">-</div>
            <div class="text-xs text-blue-700">📋 Tổng</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="stat-active">-</div>
            <div class="text-xs text-green-700">🟢 Đang hiển thị</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-600" id="stat-inactive">-</div>
            <div class="text-xs text-red-700">🔴 Đã ẩn</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" id="f-search" placeholder="Nội dung, tên người dùng..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select id="f-status" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="all">Tất cả</option>
                    <option value="active">Đang hiển thị</option>
                    <option value="inactive">Đã ẩn</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp</label>
                <select id="f-sort" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                    <option value="created_at-desc">Mới nhất</option>
                    <option value="created_at-asc">Cũ nhất</option>
                    <option value="likes_count-desc">Nhiều like nhất</option>
                    <option value="comments_count-desc">Nhiều bình luận nhất</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadPosts()" class="w-full bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Người đăng</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nội dung</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Media</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tương tác</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="posts-table" class="divide-y divide-gray-200">
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

    <!-- Post Detail Modal -->
    <div id="post-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6" id="post-modal-content">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Comments Modal -->
    <div id="comments-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-4 border-b bg-gradient-to-r from-sky-500 to-blue-600 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold" id="comments-modal-title">💬 Bình luận</h3>
                        <p class="text-sm text-sky-100" id="comments-modal-subtitle">-</p>
                    </div>
                    <button onclick="closeCommentsModal()" class="text-white hover:text-sky-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="comments-modal-content" class="flex-1 overflow-y-auto p-4">
                <!-- Comments loaded dynamically -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_URL = '{{ url("/api/community/admin/posts") }}';
    let currentPage = 1;

    function getToken() {
        return localStorage.getItem('auth_token');
    }

    async function loadPosts(page = 1) {
        currentPage = page;
        const token = getToken();
        if (!token) return;

        const status = document.getElementById('f-status').value;
        const search = document.getElementById('f-search').value;
        const sortVal = document.getElementById('f-sort').value.split('-');

        let url = `${API_URL}?page=${page}&per_page=15&sort_by=${sortVal[0]}&sort_order=${sortVal[1]}`;
        if (status !== 'all') url += `&status=${status}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;

        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            const result = await res.json();

            if (result.success) {
                renderPosts(result.data);
                renderPagination(result.pagination);
                updateStats(result.stats);
            } else {
                document.getElementById('posts-table').innerHTML = `<tr><td colspan="8" class="px-4 py-8 text-center text-red-500">Lỗi: ${result.message}</td></tr>`;
            }
        } catch (err) {
            document.getElementById('posts-table').innerHTML = `<tr><td colspan="8" class="px-4 py-8 text-center text-red-500">Lỗi kết nối: ${err.message}</td></tr>`;
        }
    }

    function updateStats(stats) {
        if (!stats) return;
        document.getElementById('stat-total').textContent = stats.total || 0;
        document.getElementById('stat-active').textContent = stats.active || 0;
        document.getElementById('stat-inactive').textContent = stats.inactive || 0;
    }

    function renderPosts(posts) {
        const tbody = document.getElementById('posts-table');
        if (!posts || posts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Không có bài viết nào</td></tr>';
            postsCache = {};
            return;
        }

        // Cache posts for quick access
        postsCache = {};
        posts.forEach(p => { postsCache[p.id] = p; });

        tbody.innerHTML = posts.map(p => {
            const images = p.images || [];
            const videos = p.videos || [];
            const mediaCount = images.length + videos.length;
            const statusCls = p.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            const statusText = p.is_active ? 'Hiển thị' : 'Đã ẩn';
            const contentPreview = (p.content || '').substring(0, 80) + (p.content?.length > 80 ? '...' : '');
            
            return `
            <tr class="hover:bg-gray-50 ${!p.is_active ? 'opacity-60' : ''}">
                <td class="px-4 py-3 text-sm text-gray-500">#${p.id}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 flex-shrink-0">
                            <img src="${p.user?.avatar || '/images/default-avatar.png'}" class="w-8 h-8 rounded-full object-cover" onerror="this.src='/images/default-avatar.png'" loading="lazy">
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">${escHtml(p.user?.name || 'Unknown')}</div>
                            <div class="text-xs text-gray-500">${escHtml(p.user?.email || '')}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-800 max-w-[200px]">${escHtml(contentPreview)}</div>
                    ${p.game_preference ? `<span class="text-xs text-purple-600">🎮 ${escHtml(p.game_preference)}</span>` : ''}
                </td>
                <td class="px-4 py-3">
                    ${mediaCount > 0 ? `
                        <span class="text-sm text-gray-600">
                            ${images.length > 0 ? `🖼️ ${images.length}` : ''}
                            ${videos.length > 0 ? `🎬 ${videos.length}` : ''}
                        </span>
                    ` : '<span class="text-xs text-gray-400">—</span>'}
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-col gap-1">
                        <span class="text-sm text-gray-600">❤️ ${p.likes_count || 0} | 💬 ${p.comments_count || 0}</span>
                        ${p.comments_count > 0 ? `<button onclick="viewComments(${p.id})" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Xem bình luận →</button>` : ''}
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full ${statusCls}">${statusText}</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">${formatDate(p.created_at)}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <button onclick="viewPost(${p.id})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Xem</button>
                        <button onclick="toggleStatus(${p.id})" class="${p.is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800'} text-sm font-medium">
                            ${p.is_active ? 'Ẩn' : 'Hiện'}
                        </button>
                        <button onclick="deletePost(${p.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function renderPagination(p) {
        const c = document.getElementById('pagination');
        if (!p || p.last_page <= 1) { c.innerHTML = ''; return; }
        let h = '';
        if (p.current_page > 1) h += `<button onclick="loadPosts(${p.current_page-1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        for (let i = Math.max(1, p.current_page-2); i <= Math.min(p.last_page, p.current_page+2); i++) {
            h += `<button onclick="loadPosts(${i})" class="px-3 py-1 rounded ${i===p.current_page?'bg-indigo-500 text-white':'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        if (p.current_page < p.last_page) h += `<button onclick="loadPosts(${p.current_page+1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        c.innerHTML = h;
    }

    // Store posts data for viewing
    let postsCache = {};
    
    function viewPost(id) {
        // Get post from cache (already loaded)
        const p = postsCache[id];
        if (p) {
            renderPostDetail(p);
            document.getElementById('post-modal').classList.remove('hidden');
        } else {
            alert('Không tìm thấy bài viết. Vui lòng tải lại trang.');
        }
    }

    function renderPostDetail(p) {
        const images = p.images || [];
        const videos = p.videos || [];
        const statusCls = p.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
        const statusText = p.is_active ? 'Đang hiển thị' : 'Đã ẩn';

        let mediaHtml = '';
        if (images.length > 0) {
            mediaHtml += `<div class="grid grid-cols-2 gap-2 mb-4">${images.map(img => `<img src="${img}" class="rounded-lg w-full h-40 object-cover cursor-pointer" onclick="window.open('${img}', '_blank')">`).join('')}</div>`;
        }
        if (videos.length > 0) {
            mediaHtml += `<div class="space-y-2 mb-4">${videos.map(vid => `<video src="${vid}" controls class="rounded-lg w-full max-h-60"></video>`).join('')}</div>`;
        }

        document.getElementById('post-modal-content').innerHTML = `
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <img src="${p.user?.avatar || '/images/default-avatar.png'}" class="w-12 h-12 rounded-full object-cover" onerror="this.src='/images/default-avatar.png'">
                    <div>
                        <h3 class="font-bold text-gray-800">${escHtml(p.user?.name || 'Unknown')}</h3>
                        <p class="text-xs text-gray-500">${formatDate(p.created_at)}</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Status -->
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 text-sm font-medium rounded-full ${statusCls}">${statusText}</span>
                ${p.game_preference ? `<span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-700">🎮 ${escHtml(p.game_preference)}</span>` : ''}
                <span class="text-sm text-gray-500">ID: #${p.id}</span>
            </div>

            <!-- Content -->
            <div class="p-4 bg-gray-50 rounded-lg mb-4">
                <p class="text-gray-800 whitespace-pre-wrap">${escHtml(p.content)}</p>
            </div>

            <!-- Media -->
            ${mediaHtml}

            <!-- Stats -->
            <div class="flex gap-6 p-3 bg-blue-50 rounded-lg mb-4">
                <div class="text-center">
                    <div class="text-xl font-bold text-blue-600">${p.likes_count || 0}</div>
                    <div class="text-xs text-gray-600">❤️ Likes</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-bold text-blue-600">${p.comments_count || 0}</div>
                    <div class="text-xs text-gray-600">💬 Comments</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                ${p.is_active 
                    ? `<button onclick="toggleStatus(${p.id}); closeModal();" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-medium">🙈 Ẩn bài viết</button>`
                    : `<button onclick="toggleStatus(${p.id}); closeModal();" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-medium">👁️ Hiện bài viết</button>`
                }
                <button onclick="deletePost(${p.id}); closeModal();" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                    🗑️ Xóa
                </button>
                <button onclick="closeModal()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Đóng
                </button>
            </div>
        `;
    }

    async function toggleStatus(id) {
        const token = getToken();
        if (!token) return;

        try {
            const res = await fetch(`{{ url("/api/community/admin/posts") }}/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const result = await res.json();
            if (result.success) {
                loadPosts(currentPage);
            } else {
                alert(result.message || 'Lỗi cập nhật');
            }
        } catch (err) {
            alert('Lỗi: ' + err.message);
        }
    }

    async function deletePost(id) {
        if (!confirm('Bạn có chắc muốn xóa bài viết này?')) return;
        const token = getToken();
        if (!token) return;

        try {
            const res = await fetch(`{{ url("/api/community/posts") }}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            const result = await res.json();
            if (result.success) {
                loadPosts(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa');
            }
        } catch (err) {
            alert('Lỗi: ' + err.message);
        }
    }

    function closeModal() {
        document.getElementById('post-modal').classList.add('hidden');
    }

    document.getElementById('post-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // ==================== COMMENTS MANAGEMENT ====================
    let currentPostIdForComments = null;

    async function viewComments(postId) {
        currentPostIdForComments = postId;
        const token = getToken();
        if (!token) return;

        const post = postsCache[postId];
        const authorName = post?.user?.name || 'Unknown';

        document.getElementById('comments-modal-title').textContent = `💬 Bình luận bài viết #${postId}`;
        document.getElementById('comments-modal-subtitle').textContent = `Bởi: ${authorName}`;
        document.getElementById('comments-modal-content').innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin w-8 h-8 border-4 border-sky-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                Đang tải bình luận...
            </div>
        `;
        document.getElementById('comments-modal').classList.remove('hidden');

        try {
            const res = await fetch(`{{ url("/api/community/admin/posts") }}/${postId}/comments`, {
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            const result = await res.json();

            if (result.success) {
                renderComments(result.data, result.stats);
            } else {
                document.getElementById('comments-modal-content').innerHTML = `<div class="text-center text-red-500 py-8">Lỗi: ${result.message}</div>`;
            }
        } catch (err) {
            document.getElementById('comments-modal-content').innerHTML = `<div class="text-center text-red-500 py-8">Lỗi: ${err.message}</div>`;
        }
    }

    function renderComments(comments, stats) {
        const container = document.getElementById('comments-modal-content');
        
        if (!comments || comments.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 py-8">Không có bình luận nào</div>';
            return;
        }

        let html = `
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-blue-50 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-blue-600">${stats?.total || 0}</div>
                    <div class="text-xs text-blue-700">Tổng</div>
                </div>
                <div class="bg-indigo-50 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-indigo-600">${stats?.root_comments || 0}</div>
                    <div class="text-xs text-indigo-700">Bình luận gốc</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-purple-600">${stats?.replies || 0}</div>
                    <div class="text-xs text-purple-700">Phản hồi</div>
                </div>
            </div>
            
            <!-- Comments Tree -->
            <div class="space-y-4">
        `;

        comments.forEach(comment => {
            html += renderCommentItem(comment, 0);
        });

        html += '</div>';
        container.innerHTML = html;
    }

    function renderCommentItem(comment, level) {
        const indent = level > 0 ? 'ml-6 border-l-2 border-sky-200 pl-4' : '';
        const avatarSize = level === 0 ? 'w-10 h-10' : 'w-8 h-8';
        const bgColor = level === 0 ? 'bg-white border border-gray-200' : 'bg-gray-50';
        
        let html = `
            <div class="${indent}">
                <div class="${bgColor} rounded-lg p-3 mb-2">
                    <div class="flex items-start gap-3">
                        <img src="${comment.user_avatar || '/images/default-avatar.png'}" class="${avatarSize} rounded-full object-cover flex-shrink-0" onerror="this.src='/images/default-avatar.png'">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="font-semibold text-gray-900 text-sm">${escHtml(comment.user_name || 'Ẩn danh')}</span>
                                <span class="text-xs text-gray-500">${formatDate(comment.created_at)}</span>
                                <span class="text-xs text-gray-400">ID: #${comment.id}</span>
                            </div>
                            <div class="text-gray-700 text-sm mb-2">${escHtml(comment.content)}</div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-pink-500">❤️ ${comment.likes_count || 0}</span>
                                <button onclick="deleteComment(${comment.id})" class="text-xs text-red-600 hover:text-red-800 font-medium">🗑️ Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
        `;

        // Render replies recursively
        if (comment.replies && comment.replies.length > 0) {
            comment.replies.forEach(reply => {
                html += renderCommentItem(reply, level + 1);
            });
        }

        html += '</div>';
        return html;
    }

    async function deleteComment(commentId) {
        if (!confirm('Bạn có chắc muốn xóa bình luận này và tất cả phản hồi của nó?')) return;
        const token = getToken();
        if (!token) return;

        try {
            const res = await fetch(`{{ url("/api/community/admin/comments") }}/${commentId}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'Authorization': `Bearer ${token}` }
            });
            const result = await res.json();
            if (result.success) {
                // Reload posts to update comment count first
                await loadPosts(currentPage);
                // Reload comments
                viewComments(currentPostIdForComments);
            } else {
                alert(result.message || 'Lỗi xóa');
            }
        } catch (err) {
            alert('Lỗi: ' + err.message);
        }
    }

    function closeCommentsModal() {
        document.getElementById('comments-modal').classList.add('hidden');
        currentPostIdForComments = null;
    }

    document.getElementById('comments-modal').addEventListener('click', function(e) {
        if (e.target === this) closeCommentsModal();
    });

    // ==================== END COMMENTS ====================

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
    window.addEventListener('authReady', () => { loadPosts(); });
    document.addEventListener('DOMContentLoaded', () => { setTimeout(() => loadPosts(), 500); });
</script>
@endpush
