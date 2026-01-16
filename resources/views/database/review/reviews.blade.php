@extends('layouts.app')

@section('title', 'Quản lý Đánh giá')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">⭐ Quản lý Đánh giá</h1>
            <p class="text-sm text-gray-600">Xem và quản lý đánh giá sản phẩm từ khách hàng</p>
        </div>
        <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center">
            ← Quay lại
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đánh giá</label>
                <select id="rating-filter" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500">
                    <option value="">Tất cả</option>
                    <option value="5">⭐⭐⭐⭐⭐ 5 sao</option>
                    <option value="4">⭐⭐⭐⭐ 4 sao</option>
                    <option value="3">⭐⭐⭐ 3 sao</option>
                    <option value="2">⭐⭐ 2 sao</option>
                    <option value="1">⭐ 1 sao</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đã mua hàng</label>
                <select id="verified-filter" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500">
                    <option value="">Tất cả</option>
                    <option value="1">✓ Đã xác thực</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sắp xếp theo</label>
                <select id="sort-by" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500">
                    <option value="id">ID</option>
                    <option value="rating">Rating</option>
                    <option value="created_at">Ngày tạo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                <select id="sort-order" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500">
                    <option value="desc">Mới nhất</option>
                    <option value="asc">Cũ nhất</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="loadReviews()" class="w-full bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition">
                    🔍 Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-xl font-bold text-yellow-600" id="stat-5">-</div>
            <div class="text-xs text-yellow-700">⭐⭐⭐⭐⭐</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-xl font-bold text-yellow-600" id="stat-4">-</div>
            <div class="text-xs text-yellow-700">⭐⭐⭐⭐</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-xl font-bold text-yellow-600" id="stat-3">-</div>
            <div class="text-xs text-yellow-700">⭐⭐⭐</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-xl font-bold text-yellow-600" id="stat-2">-</div>
            <div class="text-xs text-yellow-700">⭐⭐</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-xl font-bold text-yellow-600" id="stat-1">-</div>
            <div class="text-xs text-yellow-700">⭐</div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Người đánh giá</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sản phẩm</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rating</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nội dung</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Xác thực</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ngày tạo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="reviews-table" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <div class="animate-spin w-8 h-8 border-4 border-yellow-500 border-t-transparent rounded-full mx-auto mb-2"></div>
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
    const API_URL = '{{ url("/api/reviews") }}';
    let currentPage = 1;

    // Load stats from all data
    async function loadStats() {
        try {
            const response = await fetch(`${API_URL}?per_page=10000`, {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            
            if (result.success) {
                const allReviews = result.data;
                const stats = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };
                allReviews.forEach(r => {
                    if (stats.hasOwnProperty(r.rating)) stats[r.rating]++;
                });
                for (let i = 1; i <= 5; i++) {
                    document.getElementById(`stat-${i}`).textContent = stats[i];
                }
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async function loadReviews(page = 1) {
        currentPage = page;
        const rating = document.getElementById('rating-filter').value;
        const verified = document.getElementById('verified-filter').value;
        const sortBy = document.getElementById('sort-by').value;
        const sortOrder = document.getElementById('sort-order').value;
        
        try {
            let url = `${API_URL}?page=${page}&per_page=15&sort_by=${sortBy}&sort_order=${sortOrder}`;
            if (rating) url += `&rating=${rating}`;
            if (verified) url += `&verified_only=1`;
            
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            
            const result = await response.json();
            
            if (result.success) {
                renderReviews(result.data);
                renderPagination(result.pagination);
            } else {
                document.getElementById('reviews-table').innerHTML = `
                    <tr><td colspan="8" class="px-4 py-8 text-center text-red-500">${result.message || 'Lỗi tải dữ liệu'}</td></tr>
                `;
            }
        } catch (error) {
            document.getElementById('reviews-table').innerHTML = `
                <tr><td colspan="8" class="px-4 py-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>
            `;
        }
    }

    function renderReviews(reviews) {
        const tbody = document.getElementById('reviews-table');
        
        if (!reviews || reviews.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Không có đánh giá</td></tr>';
            return;
        }
        
        tbody.innerHTML = reviews.map(review => {
            const stars = '⭐'.repeat(review.rating || 0);
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">#${review.id}</td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900">${review.buyer?.name || '-'}</div>
                        <div class="text-xs text-gray-500">${review.buyer?.email || ''}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-[150px] truncate">${review.game?.title || 'Product #' + review.product_simple_id}</td>
                    <td class="px-4 py-3 text-sm">${stars}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">
                        <div class="truncate">${review.comment || '-'}</div>
                    </td>
                    <td class="px-4 py-3">
                        ${review.is_verified_purchase ? '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">✓ Đã mua</span>' : '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Chưa xác thực</span>'}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">${formatDate(review.created_at)}</td>
                    <td class="px-4 py-3">
                        <button onclick="deleteReview(${review.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(pagination) {
        const container = document.getElementById('pagination');
        if (!pagination || pagination.last_page <= 1) {
            container.innerHTML = '';
            return;
        }
        
        let html = '';
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            html += `<button onclick="loadReviews(${i})" class="px-3 py-1 rounded ${i === pagination.current_page ? 'bg-yellow-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}">${i}</button>`;
        }
        container.innerHTML = html;
    }

    async function deleteReview(id) {
        if (!confirm('Bạn có chắc muốn xóa đánh giá này?')) return;
        
        const token = localStorage.getItem('auth_token');
        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : ''
                }
            });
            
            const result = await response.json();
            if (result.success) {
                loadReviews(currentPage);
            } else {
                alert(result.message || 'Lỗi xóa đánh giá');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadStats();    // Load stats from all data
        loadReviews();  // Load paginated data for table
    });
</script>
@endpush
