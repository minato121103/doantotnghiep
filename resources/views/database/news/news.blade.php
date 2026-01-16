@extends('layouts.app')

@section('title', 'Quản lý Tin tức')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">📰 Quản lý Tin tức</h1>
            <p class="text-sm text-gray-600">Xem và quản lý tin tức trong hệ thống</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('database.create-news') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200 text-center">
                + Thêm mới
            </a>
            <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
                ← Quay lại
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" id="search" placeholder="Tiêu đề, tác giả..." class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="id">ID</option>
                    <option value="title">Tiêu đề</option>
                    <option value="author">Tác giả</option>
                    <option value="created_at">Ngày tạo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                <select id="sort-order" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="desc">Mới nhất</option>
                    <option value="asc">Cũ nhất</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadNews()" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600" id="stat-total">-</div>
            <div class="text-sm text-blue-700">📰 Tổng tin tức</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600" id="stat-categories">-</div>
            <div class="text-sm text-green-700">📂 Danh mục</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600" id="stat-authors">-</div>
            <div class="text-sm text-purple-700">✍️ Tác giả</div>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-orange-600" id="stat-views">-</div>
            <div class="text-sm text-orange-700">👁️ Lượt xem</div>
        </div>
    </div>

    <!-- News Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tiêu đề</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Danh mục</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tác giả</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lượt xem</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="news-table" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                            Đang tải...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 flex justify-center gap-2"></div>
@endsection

@push('scripts')
<script>
    const API_URL = '{{ url("/api/news") }}';
    const BASE_URL = '{{ url("/") }}';
    let currentPage = 1;

    // Load stats from all data (separate call)
    async function loadStats() {
        try {
            const response = await fetch(`${API_URL}?per_page=10000`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            
            if (result.success) {
                const allNews = result.data;
                
                // Total news
                document.getElementById('stat-total').textContent = result.pagination?.total || allNews.length;
                
                // Count unique categories
                const categories = new Set(allNews.map(n => n.category).filter(c => c));
                document.getElementById('stat-categories').textContent = categories.size;
                
                // Count unique authors
                const authors = new Set(allNews.map(n => n.author).filter(a => a));
                document.getElementById('stat-authors').textContent = authors.size;
                
                // Sum views
                const totalViews = allNews.reduce((sum, n) => sum + (parseInt(n.views) || 0), 0);
                document.getElementById('stat-views').textContent = totalViews.toLocaleString('vi-VN');
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async function loadNews(page = 1) {
        currentPage = page;
        const search = document.getElementById('search').value;
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        try {
            let url = `${API_URL}?page=${page}&per_page=15&sort_by=${sortBy}&sort_order=${sortOrder}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            
            const result = await response.json();
            
            if (result.success) {
                renderNews(result.data);
                renderPagination(result.pagination);
            } else {
                document.getElementById('news-table').innerHTML = `
                    <tr><td colspan="7" class="px-4 py-8 text-center text-red-500">${result.message || 'Lỗi tải dữ liệu'}</td></tr>
                `;
            }
        } catch (error) {
            document.getElementById('news-table').innerHTML = `
                <tr><td colspan="7" class="px-4 py-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>
            `;
        }
    }

    function renderNews(newsList) {
        const tbody = document.getElementById('news-table');
        
        if (!newsList || newsList.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">
                Không có tin tức. <a href="{{ route('database.create-news') }}" class="text-blue-600 hover:text-blue-800">Thêm tin tức mới</a>
            </td></tr>`;
            return;
        }
        
        tbody.innerHTML = newsList.map(news => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">#${news.id}</td>
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-blue-600 max-w-[250px] truncate">${escapeHtml(news.title || '-')}</div>
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">${escapeHtml(news.category || 'Chưa phân loại')}</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(news.author || '-')}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${(news.views || 0).toLocaleString('vi-VN')}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${formatDate(news.created_at)}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="${BASE_URL}/database/news/${news.id}/edit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Sửa</a>
                        <button onclick="deleteNews(${news.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
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
            html += `<button onclick="loadNews(${pagination.current_page - 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">←</button>`;
        }
        
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            html += `<button onclick="loadNews(${i})" class="px-3 py-1 rounded ${i === pagination.current_page ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="loadNews(${pagination.current_page + 1})" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">→</button>`;
        }
        
        container.innerHTML = html;
    }

    async function deleteNews(id) {
        if (!confirm('Bạn có chắc muốn xóa tin tức này?')) return;
        
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            });
            
            const result = await response.json();
            
            if (result.success) {
                loadNews(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa tin tức');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
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
            year: 'numeric'
        });
    }

    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadNews(1), 500);
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadStats();  // Load stats from all data
        loadNews();   // Load paginated data for table
    });
</script>
@endpush
