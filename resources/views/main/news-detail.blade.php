@extends('layouts.main')

@section('title', 'Chi tiết tin tức')

@push('styles')
<style>
    /* News Content Styles */
    .news-content {
        font-size: 17px;
        line-height: 1.9;
        color: #374151;
    }
    .news-content h1 {
        font-size: 2em;
        font-weight: 700;
        color: #1e293b;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }
    .news-content h2 {
        font-size: 1.5em;
        font-weight: 700;
        color: #1e293b;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }
    .news-content h3 {
        font-size: 1.25em;
        font-weight: 600;
        color: #1e293b;
        margin-top: 1.25em;
        margin-bottom: 0.5em;
    }
    .news-content p {
        margin-bottom: 1.25em;
    }
    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 1.5em 0;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    .news-content ul, .news-content ol {
        margin-left: 1.5em;
        margin-bottom: 1.25em;
    }
    .news-content li {
        margin-bottom: 0.5em;
    }
    .news-content a {
        color: #6366f1;
        text-decoration: underline;
        text-underline-offset: 2px;
    }
    .news-content a:hover {
        color: #4f46e5;
    }
    .news-content blockquote {
        border-left: 4px solid #6366f1;
        padding-left: 1.5em;
        margin: 1.5em 0;
        color: #64748b;
        font-style: italic;
        background: linear-gradient(to right, rgba(99, 102, 241, 0.05), transparent);
        padding: 1em 1.5em;
        border-radius: 0 8px 8px 0;
    }
    .news-content strong, .news-content b {
        font-weight: 700;
        color: #1e293b;
    }
    .news-content code {
        background: #f1f5f9;
        padding: 0.2em 0.4em;
        border-radius: 4px;
        font-size: 0.9em;
    }
    .news-content pre {
        background: #1e293b;
        color: #e2e8f0;
        padding: 1em;
        border-radius: 8px;
        overflow-x: auto;
        margin: 1.5em 0;
    }
    
    /* Decorative Elements */
    .decoration-blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.5;
        pointer-events: none;
    }
    .decoration-grid {
        background-image: 
            linear-gradient(rgba(99, 102, 241, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
        background-size: 40px 40px;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb Section -->
    <section class="pt-36 pb-4 bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="decoration-blob w-96 h-96 bg-game-accent/20 -top-48 -right-48"></div>
        <div class="decoration-blob w-64 h-64 bg-game-purple/20 top-20 -left-32"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <nav class="flex items-center text-sm text-slate-500 flex-wrap gap-1">
                <a href="{{ url('/') }}" class="hover:text-game-accent transition-colors">Trang chủ</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ url('/news') }}" class="hover:text-game-accent transition-colors">Tin tức</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span id="breadcrumb-category-container" class="hidden">
                    <a href="#" id="breadcrumb-category" class="hover:text-game-accent transition-colors"></a>
                    <svg class="w-4 h-4 mx-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
                <span class="text-slate-800 font-medium" id="breadcrumb-title">Đang tải...</span>
            </nav>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="py-8 bg-gradient-to-b from-slate-50/50 to-white relative">
        <!-- Background Grid Pattern -->
        <div class="absolute inset-0 decoration-grid"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Article Content -->
                <article class="lg:col-span-8">
                    <!-- Loading State -->
                    <div id="news-loading" class="animate-pulse">
                        <div class="bg-slate-200 h-8 w-3/4 rounded mb-4"></div>
                        <div class="bg-slate-200 h-6 w-1/2 rounded mb-6"></div>
                        <div class="bg-slate-200 h-64 rounded-2xl mb-6"></div>
                        <div class="space-y-3">
                            <div class="bg-slate-200 h-4 w-full rounded"></div>
                            <div class="bg-slate-200 h-4 w-full rounded"></div>
                            <div class="bg-slate-200 h-4 w-2/3 rounded"></div>
                        </div>
                    </div>

                    <!-- News Content -->
                    <div id="news-content" class="hidden">
                        <!-- Category Badge -->
                        <div class="mb-4" id="news-category-badge">
                            <a href="#" id="news-category-link" class="inline-flex items-center px-4 py-1.5 bg-gradient-to-r from-game-accent to-game-purple text-white text-sm font-semibold rounded-full hover:opacity-90 transition-opacity">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                                <span id="news-category-text">Tin tức</span>
                            </a>
                        </div>

                        <!-- Title -->
                        <h1 id="news-title" class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-slate-800 leading-tight mb-6"></h1>

                        <!-- Meta Info -->
                        <div class="flex flex-wrap items-center gap-4 pb-6 mb-8 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-game-accent to-game-purple rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    <span id="author-avatar">A</span>
                                </div>
                                <div>
                                    <p id="news-author" class="font-semibold text-slate-800"></p>
                                    <p id="news-date" class="text-sm text-slate-500"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 ml-auto">
                                <!-- Share Buttons -->
                                <button onclick="shareNews('facebook')" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" title="Chia sẻ Facebook">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                                    </svg>
                                </button>
                                <button onclick="shareNews('twitter')" class="p-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors" title="Chia sẻ Twitter">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.44-.67.2-1.37.2-2.06.08.58 1.8 2.26 3.12 4.25 3.16C5.78 18.1 3.37 18.74 1 18.46c2 1.3 4.4 2.04 6.97 2.04 8.35 0 12.92-6.92 12.92-12.93 0-.2 0-.4-.02-.6.9-.63 1.96-1.22 2.56-2.14z"/>
                                    </svg>
                                </button>
                                <button onclick="copyLink()" class="p-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors" title="Sao chép link">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div id="featured-image-container" class="mb-8 hidden">
                            <img id="featured-image" src="" alt="" class="w-full rounded-2xl shadow-lg">
                        </div>

                        <!-- Article Content -->
                        <div id="news-body" class="news-content"></div>

                        <!-- Tags -->
                        <div class="mt-8 pt-6 border-t border-slate-200">
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-sm rounded-full hover:bg-game-accent/10 hover:text-game-accent transition-colors cursor-pointer">#CôngNghệ</span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-sm rounded-full hover:bg-game-accent/10 hover:text-game-accent transition-colors cursor-pointer">#TinTức</span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-sm rounded-full hover:bg-game-accent/10 hover:text-game-accent transition-colors cursor-pointer">#Game</span>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="lg:col-span-4">
                    <div class="sticky top-24 space-y-6">
                        <!-- Related News -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                            <h3 class="font-heading text-lg font-bold text-slate-800 mb-4 flex items-center">
                                <span class="w-1 h-5 bg-gradient-to-b from-game-accent to-game-purple rounded-full mr-3"></span>
                                Tin tức liên quan
                            </h3>
                            <div id="related-news" class="space-y-4">
                                <!-- Loading -->
                                <div class="animate-pulse space-y-4">
                                    <div class="flex gap-3">
                                        <div class="w-20 h-16 bg-slate-200 rounded-lg flex-shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="bg-slate-200 h-4 w-full rounded"></div>
                                            <div class="bg-slate-200 h-3 w-2/3 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="w-20 h-16 bg-slate-200 rounded-lg flex-shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="bg-slate-200 h-4 w-full rounded"></div>
                                            <div class="bg-slate-200 h-3 w-2/3 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="bg-gradient-to-br from-game-accent/5 to-game-purple/5 rounded-2xl border border-game-accent/20 p-6">
                            <h3 class="font-heading text-lg font-bold text-slate-800 mb-4">Khám phá thêm</h3>
                            <div class="space-y-3">
                                <a href="{{ url('/store') }}" class="flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all group">
                                    <div class="w-10 h-10 bg-game-accent/10 rounded-lg flex items-center justify-center text-game-accent group-hover:bg-game-accent group-hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 group-hover:text-game-accent transition-colors">Cửa hàng Game</p>
                                        <p class="text-xs text-slate-500">Khám phá game mới</p>
                                    </div>
                                </a>
                                <a href="{{ url('/news') }}" class="flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all group">
                                    <div class="w-10 h-10 bg-game-purple/10 rounded-lg flex items-center justify-center text-game-purple group-hover:bg-game-purple group-hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 group-hover:text-game-purple transition-colors">Tất cả tin tức</p>
                                        <p class="text-xs text-slate-500">Xem thêm bài viết</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                </aside>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    const API_BASE_URL = '{{ url("/api/news") }}';
    const NEWS_ID = {{ $newsId }};
    
    let newsData = null;

    // Load news detail
    async function loadNewsDetail() {
        try {
            const response = await fetch(`${API_BASE_URL}/${NEWS_ID}`);
            const result = await response.json();

            if (!result.success || !result.data) {
                throw new Error('Không tìm thấy tin tức');
            }

            newsData = result.data;
            renderNewsDetail(newsData);
            loadRelatedNews();
            
        } catch (error) {
            console.error('Error loading news:', error);
            document.getElementById('news-loading').innerHTML = `
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Không tìm thấy tin tức</h3>
                    <p class="text-slate-500 mb-4">Tin tức này có thể đã bị xóa hoặc không tồn tại</p>
                    <a href="{{ url('/news') }}" class="inline-flex items-center px-6 py-3 bg-game-accent text-white rounded-full hover:bg-game-accent-hover transition-colors">
                        Quay lại trang tin tức
                    </a>
                </div>
            `;
        }
    }

    // Render news detail
    function renderNewsDetail(news) {
        // Update breadcrumb with category
        if (news.category && news.category.trim()) {
            const categoryContainer = document.getElementById('breadcrumb-category-container');
            const categoryLink = document.getElementById('breadcrumb-category');
            categoryLink.textContent = news.category;
            categoryLink.href = `{{ url('/news') }}?category=${encodeURIComponent(news.category)}`;
            categoryContainer.classList.remove('hidden');
            
            // Update category badge
            document.getElementById('news-category-text').textContent = news.category;
            document.getElementById('news-category-link').href = `{{ url('/news') }}?category=${encodeURIComponent(news.category)}`;
        } else {
            // Default category text
            document.getElementById('news-category-text').textContent = 'Tin tức';
            document.getElementById('news-category-link').href = `{{ url('/news') }}`;
        }
        document.getElementById('breadcrumb-title').textContent = truncateText(news.title, 50);
        
        // Update page title
        document.title = news.title + ' - Tin tức';

        // Render content
        document.getElementById('news-title').textContent = news.title;
        document.getElementById('news-author').textContent = news.author;
        document.getElementById('author-avatar').textContent = news.author.charAt(0).toUpperCase();
        // Use published_at if available (display directly), otherwise use created_at (format it)
        if (news.published_at) {
            document.getElementById('news-date').textContent = news.published_at;
        } else {
            document.getElementById('news-date').textContent = formatDate(news.created_at);
        }
        document.getElementById('news-body').innerHTML = news.description;

        // Extract and show featured image
        const firstImage = extractFirstImage(news.description);
        if (firstImage) {
            document.getElementById('featured-image').src = firstImage;
            document.getElementById('featured-image').alt = news.title;
            document.getElementById('featured-image-container').classList.remove('hidden');
        }

        // Show content, hide loading
        document.getElementById('news-loading').classList.add('hidden');
        document.getElementById('news-content').classList.remove('hidden');
    }

    // Load related news (random)
    async function loadRelatedNews() {
        try {
            // Fetch more news to have better random selection
            const response = await fetch(`${API_BASE_URL}?per_page=50`);
            const result = await response.json();

            if (!result.success || !result.data) return;

            // Filter out current news
            let otherNews = result.data.filter(n => n.id !== NEWS_ID);
            
            // Shuffle array (Fisher-Yates algorithm)
            for (let i = otherNews.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [otherNews[i], otherNews[j]] = [otherNews[j], otherNews[i]];
            }
            
            // Take 4 random news
            const relatedNews = otherNews.slice(0, 4);
            renderRelatedNews(relatedNews);
            
        } catch (error) {
            console.error('Error loading related news:', error);
        }
    }

    // Render related news
    function renderRelatedNews(newsList) {
        const container = document.getElementById('related-news');
        
        if (newsList.length === 0) {
            container.innerHTML = '<p class="text-slate-500 text-sm">Chưa có tin tức liên quan</p>';
            return;
        }

        container.innerHTML = newsList.map(news => {
            const imageUrl = extractFirstImage(news.description) || 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=200';
            return `
                <a href="{{ url('/news') }}/${news.id}" class="flex gap-3 group">
                    <div class="w-20 h-16 flex-shrink-0 rounded-lg overflow-hidden">
                        <img src="${imageUrl}" alt="${escapeHtml(news.title)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-slate-800 text-sm line-clamp-2 group-hover:text-game-accent transition-colors">
                            ${escapeHtml(news.title)}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1">${formatUnifiedDateTime(news)}</p>
                    </div>
                </a>
            `;
        }).join('');
    }

    // Share functions
    function shareNews(platform) {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(newsData?.title || 'Tin tức');
        
        let shareUrl = '';
        switch (platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                break;
        }
        
        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    }

    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Đã sao chép link!');
        }).catch(() => {
            alert('Không thể sao chép link');
        });
    }

    // Utility functions
    function extractFirstImage(html) {
        if (!html) return null;
        const imgMatch = html.match(/<img[^>]+src=["']([^"']+)["']/i);
        return imgMatch ? imgMatch[1] : null;
    }

    function truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateString) {
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
        
        return date.toLocaleDateString('vi-VN', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

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
        
        return date.toLocaleDateString('vi-VN');
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

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadNewsDetail();
    });
</script>
@endpush
