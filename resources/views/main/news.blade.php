@extends('layouts.main')

@section('title', 'Tin tức công nghệ')

@push('styles')
<style>
    .news-content {
        font-size: 16px;
        line-height: 1.8;
        color: #475569;
    }
    .news-content h1, .news-content h2, .news-content h3, 
    .news-content h4, .news-content h5, .news-content h6 {
        font-weight: 700;
        color: #1e293b;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }
    .news-content h1 { font-size: 2em; }
    .news-content h2 { font-size: 1.5em; }
    .news-content h3 { font-size: 1.25em; }
    .news-content p {
        margin-bottom: 1em;
    }
    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1em 0;
    }
    .news-content ul, .news-content ol {
        margin-left: 1.5em;
        margin-bottom: 1em;
    }
    .news-content li {
        margin-bottom: 0.5em;
    }
    .news-content a {
        color: #6366f1;
        text-decoration: underline;
    }
    .news-content a:hover {
        color: #4f46e5;
    }
    .news-content blockquote {
        border-left: 4px solid #6366f1;
        padding-left: 1em;
        margin: 1em 0;
        color: #64748b;
        font-style: italic;
    }
    .news-content strong, .news-content b {
        font-weight: 700;
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="relative pt-28 md:pt-32 pb-4 overflow-hidden bg-gradient-to-br from-slate-50 via-indigo-50/50 to-purple-50/50">
        <!-- Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1920')] bg-cover bg-center opacity-5"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-game-accent/5 via-transparent to-game-purple/5"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-20">
            <div class="mb-4">
                <h1 class="font-display text-3xl md:text-4xl font-bold mb-1.5">
                    <span class="gradient-text">Tin tức công nghệ</span>
                </h1>
                <p class="text-slate-600 text-sm md:text-base">Cập nhật những tin tức công nghệ mới nhất</p>
            </div>
            
            <!-- Categories -->
            <div>
                <div id="categories-container" class="flex flex-wrap gap-2 md:gap-3">
                    <!-- Categories will be loaded here -->
                </div>
            </div>
        </div>
    </section>

    <!-- News Content -->
    <section class="py-8 bg-slate-50/50">
        <div class="container mx-auto px-4">
            <!-- Loading State -->
            <div id="loading-state" class="text-center py-16">
                <div class="inline-flex flex-col items-center">
                    <div class="animate-spin w-12 h-12 border-4 border-game-accent border-t-transparent rounded-full mb-4"></div>
                    <p class="text-slate-500">Đang tải tin tức...</p>
                </div>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden bg-white rounded-2xl border border-game-border p-12 text-center">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                <h3 class="font-heading text-2xl font-bold text-slate-800 mb-2">Chưa có tin tức</h3>
                <p class="text-slate-600 mb-8">Hiện tại chưa có tin tức nào được đăng tải</p>
            </div>

            <!-- Featured News + Sidebar -->
            <div id="featured-section" class="hidden mb-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Featured Article (Left - 2 cols) -->
                    <div id="featured-news" class="lg:col-span-2">
                        <!-- Will be populated by JS -->
                    </div>

                    <!-- Sidebar (Right - 1 col) -->
                    <div id="sidebar-news" class="space-y-4">
                        <!-- Will be populated by JS -->
                    </div>
                </div>
            </div>

            <!-- News Grid -->
            <div id="news-grid-section" class="hidden">
                <h2 class="font-heading text-2xl font-bold text-slate-800 mb-6 flex items-center">
                    <span class="w-1 h-6 bg-gradient-to-b from-game-accent to-game-purple rounded-full mr-3"></span>
                    Tin tức mới nhất
                </h2>
                <div id="news-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Will be populated by JS -->
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination-container" class="hidden mt-8 flex justify-center">
                <nav id="pagination-nav" class="flex items-center gap-2">
                    <!-- Will be populated by JS -->
                </nav>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const API_BASE_URL = '{{ url("/api/news") }}';
    
    let currentPage = 1;
    let lastPage = 1;
    let allNews = [];
    let currentCategory = '';
    let allCategories = [];

    // Extract first image URL from HTML content
    function extractFirstImage(html) {
        if (!html) return null;
        const imgMatch = html.match(/<img[^>]+src=["']([^"']+)["']/i);
        return imgMatch ? imgMatch[1] : null;
    }

    // Load categories from all news
    async function loadCategories() {
        try {
            const response = await fetch(`${API_BASE_URL}?per_page=1000`);
            const result = await response.json();

            if (result.success && result.data) {
                // Extract unique categories
                const categoryMap = new Map();
                result.data.forEach(news => {
                    if (news.category && news.category.trim()) {
                        const category = news.category.trim();
                        categoryMap.set(category, (categoryMap.get(category) || 0) + 1);
                    }
                });

                allCategories = Array.from(categoryMap.entries())
                    .map(([category, count]) => ({ category, count }))
                    .sort((a, b) => b.count - a.count);

                renderCategories();
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    // Render categories
    function renderCategories() {
        const container = document.getElementById('categories-container');
        if (!container) return;

        let html = `
            <button onclick="filterByCategory('')" 
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all ${
                        currentCategory === '' 
                            ? 'bg-game-accent text-white shadow-md' 
                            : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'
                    }">
                Tất cả
            </button>
        `;

        allCategories.forEach(cat => {
            html += `
                <button onclick="filterByCategory('${escapeHtml(cat.category)}')" 
                        class="px-4 py-2 rounded-full text-sm font-medium transition-all ${
                            currentCategory === cat.category 
                                ? 'bg-game-accent text-white shadow-md' 
                                : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'
                        }">
                    ${escapeHtml(cat.category)} <span class="text-xs opacity-75">(${cat.count})</span>
                </button>
            `;
        });

        container.innerHTML = html;
    }

    // Filter by category
    function filterByCategory(category) {
        currentCategory = category;
        currentPage = 1;
        loadNews(1);
        renderCategories();
    }

    // Load news from API
    async function loadNews(page = 1) {
        const loadingEl = document.getElementById('loading-state');
        const emptyEl = document.getElementById('empty-state');
        const featuredSection = document.getElementById('featured-section');
        const gridSection = document.getElementById('news-grid-section');
        const paginationContainer = document.getElementById('pagination-container');

        try {
            loadingEl.classList.remove('hidden');
            emptyEl.classList.add('hidden');
            featuredSection.classList.add('hidden');
            gridSection.classList.add('hidden');
            paginationContainer.classList.add('hidden');

            let url = `${API_BASE_URL}?page=${page}&per_page=13&sort_by=time&sort_order=desc`;
            if (currentCategory) {
                url += `&category=${encodeURIComponent(currentCategory)}`;
            }

            const response = await fetch(url);
            const result = await response.json();

            if (!result.success || !result.data || result.data.length === 0) {
                loadingEl.classList.add('hidden');
                emptyEl.classList.remove('hidden');
                return;
            }

            allNews = result.data;
            currentPage = result.pagination.current_page;
            lastPage = result.pagination.last_page;

            renderNews();
            
            loadingEl.classList.add('hidden');
            featuredSection.classList.remove('hidden');
            gridSection.classList.remove('hidden');
            
            if (lastPage > 1) {
                renderPagination();
                paginationContainer.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading news:', error);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            emptyEl.querySelector('h3').textContent = 'Đã xảy ra lỗi';
            emptyEl.querySelector('p').textContent = 'Không thể tải tin tức. Vui lòng thử lại sau.';
        }
    }

    // Parse date from published_at or created_at
    function parseNewsDate(news) {
        // Try to parse published_at first
        if (news.published_at) {
            // Try to parse various date formats
            const dateStr = news.published_at.trim();
            
            // Try DD/MM/YYYY HH:mm format (e.g., "23/12/2025 07:18 (GMT + 7)")
            const ddmmyyyyMatch = dateStr.match(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})/);
            if (ddmmyyyyMatch) {
                const [, day, month, year, hour, minute] = ddmmyyyyMatch;
                return new Date(year, month - 1, day, hour, minute);
            }
            
            // Try YYYY-MM-DD HH:mm format
            const yyyymmddMatch = dateStr.match(/(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})/);
            if (yyyymmddMatch) {
                const [, year, month, day, hour, minute] = yyyymmddMatch;
                return new Date(year, month - 1, day, hour, minute);
            }
            
            // Try ISO format or standard date string
            const parsed = new Date(dateStr);
            if (!isNaN(parsed.getTime())) {
                return parsed;
            }
        }
        
        // Fallback to created_at
        if (news.created_at) {
            return new Date(news.created_at);
        }
        
        // Last resort: return very old date
        return new Date(0);
    }

    // Sort news by time (newest first)
    function sortNewsByTime(newsArray) {
        return newsArray.sort((a, b) => {
            const dateA = parseNewsDate(a);
            const dateB = parseNewsDate(b);
            return dateB - dateA; // Descending order (newest first)
        });
    }

    // Render news
    function renderNews() {
        if (allNews.length === 0) return;

        // Data is already sorted by backend (sort_by=time&sort_order=desc)
        // No need to sort again on frontend

        // Featured news (first item - newest)
        const featured = allNews[0];
        renderFeaturedNews(featured);

        // Sidebar news (items 2-5)
        const sidebarItems = allNews.slice(1, 5);
        renderSidebarNews(sidebarItems);

        // Grid news (items 6-13, max 8 items = 2 rows)
        const gridItems = allNews.slice(5, 13);
        renderGridNews(gridItems);
    }

    // Render featured news
    function renderFeaturedNews(news) {
        const container = document.getElementById('featured-news');
        const imageUrl = extractFirstImage(news.description) || 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800';
        const descPreview = stripHtml(news.description).substring(0, 200);
        
        container.innerHTML = `
            <article class="bg-white rounded-2xl border border-game-border overflow-hidden hover:shadow-xl transition-all group cursor-pointer" onclick="viewNews(${news.id})">
                <div class="relative aspect-[16/9] overflow-hidden">
                    <img src="${imageUrl}" 
                         alt="${escapeHtml(news.title)}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <span class="inline-block px-3 py-1 bg-game-accent text-white text-xs font-semibold rounded-full mb-3">
                            Tin nổi bật
                        </span>
                        <h2 class="font-heading text-2xl md:text-3xl font-bold text-white mb-3 line-clamp-2">
                            ${escapeHtml(news.title)}
                        </h2>
                        <p class="text-slate-200 text-sm mb-4 line-clamp-2">
                            ${escapeHtml(descPreview)}...
                        </p>
                        <div class="flex items-center text-slate-300 text-sm">
                            <span class="font-medium">${escapeHtml(news.author)}</span>
                            <span class="mx-2">•</span>
                            <span>${formatUnifiedDateTime(news)}</span>
                        </div>
                    </div>
                </div>
            </article>
        `;
    }

    // Render sidebar news
    function renderSidebarNews(newsList) {
        const container = document.getElementById('sidebar-news');
        
        if (newsList.length === 0) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = newsList.map(news => {
            const imageUrl = extractFirstImage(news.description) || 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=200';
            return `
                <article class="bg-white rounded-xl border border-game-border p-4 hover:shadow-lg transition-all cursor-pointer group" onclick="viewNews(${news.id})">
                    <div class="flex gap-4">
                        <div class="w-24 h-24 flex-shrink-0 rounded-lg overflow-hidden">
                            <img src="${imageUrl}" 
                                 alt="${escapeHtml(news.title)}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-game-accent text-xs font-semibold">Công nghệ</span>
                            <h3 class="font-heading font-bold text-slate-800 text-sm line-clamp-2 group-hover:text-game-accent transition-colors mt-1 mb-2">
                                ${escapeHtml(news.title)}
                            </h3>
                            <p class="text-slate-500 text-xs">${formatUnifiedDateTime(news)}</p>
                        </div>
                    </div>
                </article>
            `;
        }).join('');
    }

    // Render grid news
    function renderGridNews(newsList) {
        const container = document.getElementById('news-grid');
        
        if (newsList.length === 0) {
            container.innerHTML = '<p class="col-span-full text-center text-slate-500 py-8">Không có tin tức khác</p>';
            return;
        }

        container.innerHTML = newsList.map(news => {
            const imageUrl = extractFirstImage(news.description) || 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=400';
            return `
                <article class="bg-white rounded-xl border border-game-border overflow-hidden hover:shadow-lg transition-all cursor-pointer group" onclick="viewNews(${news.id})">
                    <div class="relative aspect-[4/3] overflow-hidden">
                        <img src="${imageUrl}" 
                             alt="${escapeHtml(news.title)}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-4">
                        <span class="text-game-accent text-xs font-semibold">Công nghệ</span>
                        <h3 class="font-heading font-bold text-slate-800 text-sm line-clamp-2 group-hover:text-game-accent transition-colors mt-1 mb-2">
                            ${escapeHtml(news.title)}
                        </h3>
                        <p class="text-slate-500 text-xs">${news.published_at ? escapeHtml(news.published_at) : formatTimeAgo(news.created_at)}</p>
                    </div>
                </article>
            `;
        }).join('');
    }
    
    // Strip HTML tags for preview
    function stripHtml(html) {
        if (!html) return '';
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }

    // Render pagination
    function renderPagination() {
        const container = document.getElementById('pagination-nav');
        let html = '';

        // Previous button
        if (currentPage > 1) {
            html += `
                <button onclick="loadNews(${currentPage - 1})" 
                        class="px-4 py-2 bg-white border border-game-border rounded-lg text-slate-600 hover:border-game-accent hover:text-game-accent transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            `;
        }

        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(lastPage, currentPage + 2);

        if (startPage > 1) {
            html += `<button onclick="loadNews(1)" class="px-4 py-2 bg-white border border-game-border rounded-lg text-slate-600 hover:border-game-accent hover:text-game-accent transition-colors">1</button>`;
            if (startPage > 2) {
                html += `<span class="px-2 text-slate-400">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += `<button class="px-4 py-2 bg-gradient-to-r from-game-accent to-game-purple text-white rounded-lg font-semibold">${i}</button>`;
            } else {
                html += `<button onclick="loadNews(${i})" class="px-4 py-2 bg-white border border-game-border rounded-lg text-slate-600 hover:border-game-accent hover:text-game-accent transition-colors">${i}</button>`;
            }
        }

        if (endPage < lastPage) {
            if (endPage < lastPage - 1) {
                html += `<span class="px-2 text-slate-400">...</span>`;
            }
            html += `<button onclick="loadNews(${lastPage})" class="px-4 py-2 bg-white border border-game-border rounded-lg text-slate-600 hover:border-game-accent hover:text-game-accent transition-colors">${lastPage}</button>`;
        }

        // Next button
        if (currentPage < lastPage) {
            html += `
                <button onclick="loadNews(${currentPage + 1})" 
                        class="px-4 py-2 bg-white border border-game-border rounded-lg text-slate-600 hover:border-game-accent hover:text-game-accent transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            `;
        }

        container.innerHTML = html;
    }

    // View news detail - Navigate to detail page
    function viewNews(id) {
        window.location.href = `{{ url('/news') }}/${id}`;
    }

    // Format time ago
    function formatTimeAgo(dateString) {
        if (!dateString) return '';
        
        // Check if it's a standard datetime format from database (created_at)
        const isStandardFormat = typeof dateString === 'string' && (
            dateString.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/) || // "YYYY-MM-DD HH:mm:ss"
            dateString.includes('T') || // ISO format
            dateString.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/) // "YYYY-MM-DD HH:mm"
        );
        
        // If it's NOT a standard format, it's likely a custom text from published_at - display as is
        if (!isStandardFormat) {
            return dateString;
        }
        
        // For standard format, try to parse and format
        let date;
        if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/)) {
            // Format: "YYYY-MM-DD HH:mm:ss" - convert to ISO for parsing
            const dateTimeStr = dateString.replace(' ', 'T');
            date = new Date(dateTimeStr);
        } else if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/)) {
            // Format: "YYYY-MM-DD HH:mm" - convert to ISO for parsing
            const dateTimeStr = dateString.replace(' ', 'T') + ':00';
            date = new Date(dateTimeStr);
        } else {
            date = new Date(dateString);
        }
        
        // Check if date is valid
        if (isNaN(date.getTime())) {
            // If invalid, return original string
            return dateString;
        }
        
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'Vừa xong';
        if (diffMins < 60) return `${diffMins} phút trước`;
        if (diffHours < 24) return `${diffHours} giờ trước`;
        if (diffDays < 7) return `${diffDays} ngày trước`;
        
        return date.toLocaleDateString('vi-VN', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    // Format date to unified format: DD/MM/YYYY HH:mm (GMT + 7)
    function formatUnifiedDateTime(news) {
        let date;
        
        // Try to parse published_at first
        if (news.published_at) {
            const dateStr = news.published_at.trim();
            
            // Check if already in DD/MM/YYYY HH:mm format
            const ddmmyyyyMatch = dateStr.match(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})/);
            if (ddmmyyyyMatch) {
                // Already in correct format, return as is (with GMT + 7 if not present)
                if (dateStr.includes('GMT')) {
                    return dateStr;
                } else {
                    const [, day, month, year, hour, minute] = ddmmyyyyMatch;
                    return `${day}/${month}/${year} ${hour}:${minute} (GMT + 7)`;
                }
            }
            
            // Try YYYY-MM-DD HH:mm format
            const yyyymmddMatch = dateStr.match(/(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})/);
            if (yyyymmddMatch) {
                const [, year, month, day, hour, minute] = yyyymmddMatch;
                date = new Date(year, month - 1, day, hour, minute);
            } else {
                // Try ISO format or standard date string
                date = new Date(dateStr);
            }
        } else if (news.created_at) {
            // Fallback to created_at
            date = new Date(news.created_at);
        } else {
            return '';
        }
        
        // Check if date is valid
        if (isNaN(date.getTime())) {
            return news.published_at || '';
        }
        
        // Format to DD/MM/YYYY HH:mm (GMT + 7)
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hour = String(date.getHours()).padStart(2, '0');
        const minute = String(date.getMinutes()).padStart(2, '0');
        
        return `${day}/${month}/${year} ${hour}:${minute} (GMT + 7)`;
    }

    // Format date (kept for backward compatibility)
    function formatDate(dateString) {
        if (!dateString) return '';
        
        // Parse date string - handle both text format "YYYY-MM-DD HH:mm" and ISO format
        let date;
        if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/)) {
            // Format: "YYYY-MM-DD HH:mm" or "YYYY-MM-DD HH:mm:ss"
            // Convert to ISO format for Date parsing
            const dateTimeStr = dateString.replace(' ', 'T');
            date = new Date(dateTimeStr);
        } else {
            date = new Date(dateString);
        }
        
        // Check if date is valid
        if (isNaN(date.getTime())) {
            // If invalid, try to parse as text format
            if (typeof dateString === 'string') {
                const match = dateString.match(/(\d{4}-\d{2}-\d{2})[T ](\d{2}):(\d{2})/);
                if (match) {
                    date = new Date(`${match[1]}T${match[2]}:${match[3]}:00`);
                } else {
                    return dateString; // Return original string if can't parse
                }
            } else {
                return '';
            }
        }
        
        return date.toLocaleDateString('vi-VN', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadCategories();
        loadNews();
    });
</script>
@endpush
