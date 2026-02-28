@extends('layouts.main')

@section('title', 'Cộng đồng - Tìm bạn chơi game')

@push('styles')
<style>
    /* Facebook-style Post Card */
    .post-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 16px;
    }
    
    /* Post Header */
    .post-header {
        display: flex;
        align-items: center;
        padding: 12px 16px;
    }
    .post-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 12px;
    }
    .post-user-name {
        font-weight: 600;
        color: #050505;
        font-size: 15px;
    }
    .post-user-name:hover {
        text-decoration: underline;
    }
    .post-time {
        font-size: 13px;
        color: #65676b;
    }
    
    /* Post Content */
    .post-content {
        padding: 0 16px 12px;
        font-size: 15px;
        line-height: 1.5;
        color: #050505;
    }
    .post-content.truncated {
        max-height: 100px;
        overflow: hidden;
    }
    .see-more-btn {
        color: #65676b;
        font-weight: 600;
        cursor: pointer;
    }
    .see-more-btn:hover {
        text-decoration: underline;
    }
    
    /* Facebook-style Media Grid */
    .media-container {
        position: relative;
        background: #f0f2f5;
        border-radius: 8px;
        overflow: hidden;
    }
    .media-grid-1 {
        display: grid;
        grid-template-columns: 1fr;
    }
    .media-grid-1 .media-item {
        max-height: 500px;
    }
    .media-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2px;
    }
    .media-grid-2 .media-item {
        aspect-ratio: 1;
    }
    .media-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto;
        gap: 2px;
    }
    .media-grid-3 .media-item:first-child {
        grid-column: 1 / -1;
        aspect-ratio: 16/9;
    }
    .media-grid-3 .media-item:not(:first-child) {
        aspect-ratio: 4/3;
    }
    .media-grid-4 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2px;
    }
    .media-grid-4 .media-item {
        aspect-ratio: 1;
    }
    .media-grid-5plus {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto;
        gap: 2px;
    }
    .media-grid-5plus .media-item:first-child {
        grid-column: 1 / -1;
        aspect-ratio: 16/9;
    }
    .media-grid-5plus .media-item:not(:first-child) {
        aspect-ratio: 4/3;
    }
    
    .media-item {
        position: relative;
        overflow: hidden;
        cursor: pointer;
        background: #e4e6eb;
    }
    .media-item img, .media-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s;
    }
    .media-item:hover img, .media-item:hover video {
        transform: scale(1.02);
    }
    .media-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 28px;
        font-weight: 600;
    }
    
    /* Video Play Button */
    .video-play-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: rgba(0,0,0,0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
    }
    
    /* Post Stats */
    .post-stats {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        border-bottom: 1px solid #e4e6eb;
    }
    .like-count {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #65676b;
        font-size: 15px;
    }
    .like-icon {
        width: 18px;
        height: 18px;
        background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .like-icon svg {
        width: 12px;
        height: 12px;
        fill: #fff;
    }
    
    /* Post Actions */
    .post-actions {
        display: flex;
        padding: 4px 16px;
    }
    .action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 0;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 600;
        color: #65676b;
        cursor: pointer;
        transition: background 0.2s;
    }
    .action-btn:hover {
        background: #f0f2f5;
    }
    .action-btn.liked {
        color: #6366f1;
    }
    .action-btn.liked svg {
        fill: #6366f1;
    }
    
    .comments-count-link {
        transition: all 0.15s ease;
    }
    .comments-count-link:hover {
        text-decoration: underline;
        transform: translateY(-1px);
        display: inline-block;
    }

    /* Comments Section */
    .comments-section {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        border-top: 1px solid #e4e6eb;
    }
    .comments-section.expanded {
        max-height: 2000px;
        overflow-y: auto;
    }
    .comments-wrapper {
        padding: 12px 16px;
    }
    .comment-input-wrapper {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }
    .comment-input-wrapper img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }
    .comment-input {
        flex: 1;
        background: #f0f2f5;
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 14px;
        outline: none;
    }
    .comment-input:focus {
        background: #e4e6eb;
    }
    .send-btn {
        background: #6366f1;
        color: #fff;
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        font-weight: 600;
        cursor: pointer;
    }
    .send-btn:hover {
        background: #4f46e5;
    }
    
    .comment-item {
        display: flex;
        gap: 8px;
        margin-bottom: 4px;
    }
    .comment-item img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .comment-body {
        flex: 1;
        min-width: 0;
    }
    .comment-bubble {
        background: #f0f2f5;
        border-radius: 18px;
        padding: 8px 12px;
        display: inline-block;
        max-width: 100%;
    }
    .comment-name {
        font-weight: 600;
        font-size: 13px;
        color: #050505;
    }
    .comment-text {
        font-size: 15px;
        color: #050505;
        word-wrap: break-word;
    }
    .comment-actions {
        display: flex;
        gap: 12px;
        padding: 2px 12px 4px;
        font-size: 12px;
        font-weight: 600;
    }
    .comment-action-btn {
        color: #65676b;
        cursor: pointer;
        transition: color 0.15s;
    }
    .comment-action-btn:hover {
        text-decoration: underline;
    }
    .comment-action-btn.liked {
        color: #6366f1;
    }
    .comment-likes-count {
        color: #65676b;
        font-weight: 400;
    }
    .comment-time {
        color: #65676b;
        font-weight: 400;
    }
    
    /* Reply Section */
    .replies-container {
        margin-left: 40px;
        margin-top: 2px;
    }
    .reply-input-wrapper {
        display: flex;
        gap: 8px;
        margin-left: 40px;
        margin-bottom: 8px;
        margin-top: 4px;
    }
    .reply-input-wrapper img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
    }
    .reply-input {
        flex: 1;
        background: #f0f2f5;
        border: none;
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 13px;
        outline: none;
    }
    .reply-input:focus {
        background: #e4e6eb;
    }
    .reply-item {
        display: flex;
        gap: 8px;
        margin-bottom: 4px;
    }
    .reply-item img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .reply-bubble {
        background: #f0f2f5;
        border-radius: 18px;
        padding: 6px 12px;
        display: inline-block;
    }
    .view-replies-btn {
        color: #65676b;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-left: 40px;
        margin-bottom: 4px;
        padding: 2px 0;
    }
    .view-replies-btn:hover {
        text-decoration: underline;
    }
    
    /* Image Lightbox */
    .lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.95);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .lightbox.active {
        display: flex;
    }
    .lightbox-content {
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
    }
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        cursor: pointer;
        font-size: 24px;
    }
    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        cursor: pointer;
        font-size: 20px;
    }
    .lightbox-prev { left: 20px; }
    .lightbox-next { right: 20px; }

    /* Game Preference Tag */
    .game-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(139,92,246,0.1));
        border: 1px solid rgba(99,102,241,0.3);
        color: #6366f1;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        margin: 0 16px 12px;
    }
    
    /* Create Post Box */
    .create-post-box {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        padding: 12px 16px;
        margin-bottom: 16px;
    }
    .create-post-input {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .create-post-input img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .create-post-btn {
        flex: 1;
        background: #f0f2f5;
        border: none;
        border-radius: 20px;
        padding: 10px 16px;
        text-align: left;
        color: #65676b;
        font-size: 16px;
        cursor: pointer;
    }
    .create-post-btn:hover {
        background: #e4e6eb;
    }
    .create-post-actions {
        display: flex;
        border-top: 1px solid #e4e6eb;
        padding-top: 12px;
    }
    .create-action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 0;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #65676b;
        cursor: pointer;
    }
    .create-action-btn:hover {
        background: #f0f2f5;
    }

    /* Sidebar Game Groups */
    .sidebar {
        position: sticky;
        top: 140px;
        height: fit-content;
        max-height: calc(100vh - 160px);
        overflow-y: auto;
    }
    .game-group-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .game-group-header {
        padding: 14px 16px;
        font-weight: 600;
        font-size: 17px;
        color: #050505;
        border-bottom: 1px solid #e4e6eb;
    }
    .game-group-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .game-group-item:hover {
        background: #f0f2f5;
    }
    .game-group-item.active {
        background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(139,92,246,0.08));
        border-left: 3px solid #6366f1;
    }
    .game-group-item img {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        object-fit: cover;
    }
    .game-group-name {
        font-weight: 600;
        font-size: 14px;
        color: #050505;
        line-height: 1.3;
    }
    .game-group-meta {
        font-size: 12px;
        color: #65676b;
    }
    .game-group-empty {
        padding: 20px 16px;
        text-align: center;
        color: #65676b;
        font-size: 14px;
    }
    .game-group-all {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px;
        cursor: pointer;
        transition: background 0.15s;
        font-weight: 600;
        font-size: 14px;
        color: #6366f1;
    }
    .game-group-all:hover {
        background: #f0f2f5;
    }
    .game-group-all.active {
        background: rgba(99,102,241,0.08);
    }
    .game-group-all-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Community Layout */
    .community-layout {
        display: flex;
        gap: 20px;
        max-width: 1100px;
        margin: 0 auto;
    }
    .community-sidebar {
        width: 300px;
        flex-shrink: 0;
    }
    .community-feed {
        flex: 1;
        max-width: 680px;
    }
    @media (max-width: 1024px) {
        .community-sidebar {
            display: none;
        }
        .community-feed {
            max-width: 100%;
        }
    }

    /* @Mention Links */
    .mention-link {
        color: #6366f1;
        font-weight: 600;
        cursor: pointer;
        position: relative;
        text-decoration: none;
    }
    .mention-link:hover {
        text-decoration: underline;
    }

    /* User Hover Card */
    .user-hover-card {
        position: fixed;
        width: 340px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.18), 0 2px 8px rgba(0,0,0,0.08);
        z-index: 2000;
        overflow: hidden;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.2s ease, transform 0.2s ease;
        pointer-events: none;
    }
    .user-hover-card.visible {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
    .hover-card-cover {
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
    }
    .hover-card-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .hover-card-main {
        padding: 0 16px 14px;
        position: relative;
    }
    .hover-card-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        border: 4px solid #fff;
        object-fit: cover;
        margin-top: -36px;
        background: #e4e6eb;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }
    .hover-card-name {
        font-size: 17px;
        font-weight: 700;
        color: #050505;
        margin-top: 6px;
        cursor: pointer;
    }
    .hover-card-name:hover {
        text-decoration: underline;
    }
    .hover-card-bio {
        font-size: 13px;
        color: #65676b;
        margin-top: 2px;
        line-height: 1.3;
    }
    .hover-card-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        font-size: 13px;
        color: #65676b;
    }
    .hover-card-meta .material-icons-outlined {
        font-size: 16px;
    }
    .hover-card-friends {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 8px;
    }
    .hover-card-friends img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #fff;
        object-fit: cover;
        margin-left: -6px;
    }
    .hover-card-friends img:first-child {
        margin-left: 0;
    }
    .hover-card-friends-text {
        font-size: 13px;
        color: #65676b;
        margin-left: 4px;
    }
    .hover-card-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }
    .hc-btn {
        flex: 1;
        padding: 7px 0;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: background 0.15s;
    }
    .hc-btn-primary {
        background: #6366f1;
        color: #fff;
    }
    .hc-btn-primary:hover { background: #4f46e5; }
    .hc-btn-secondary {
        background: #e4e6eb;
        color: #050505;
    }
    .hc-btn-secondary:hover { background: #d8dadf; }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="relative pt-32 md:pt-36 pb-4 overflow-hidden bg-gradient-to-br from-slate-50 via-purple-50/30 to-indigo-50/30">
        <div class="container mx-auto px-4 relative z-20">
            <div class="text-center">
                <h1 class="font-display text-3xl md:text-4xl font-bold mb-2">
                    <span class="gradient-text">Cộng đồng Game</span>
                </h1>
                <p class="text-slate-600 text-base max-w-xl mx-auto">
                    Kết nối với game thủ, chia sẻ trải nghiệm và tìm kiếm đồng đội
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-6 bg-[#f0f2f5] min-h-screen">
        <div class="container mx-auto px-4">
            <div class="community-layout">
                <!-- Left Sidebar - Game Groups -->
                <aside class="community-sidebar">
                    <div class="sidebar">
                        <div class="game-group-card">
                            <div class="game-group-header">
                                🎮 Nhóm game của bạn
                            </div>
                            
                            <!-- All Posts Button -->
                            <div class="game-group-all active" id="group-all" onclick="filterByGame(null)">
                                <div class="game-group-all-icon">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div>Tất cả bài viết</div>
                                </div>
                            </div>
                            
                            <!-- Game Groups List -->
                            <div id="game-groups-list">
                                <div class="game-group-empty">
                                    <div class="animate-spin w-6 h-6 border-2 border-game-accent border-t-transparent rounded-full mx-auto mb-2"></div>
                                    Đang tải...
                                </div>
                            </div>
                        </div>

                        <!-- Login prompt for sidebar -->
                        <div id="sidebar-login-prompt" class="hidden game-group-card mt-4">
                            <div class="p-4 text-center">
                                <p class="text-sm text-slate-600 mb-3">Đăng nhập để xem nhóm game của bạn</p>
                                <a href="{{ url('/login') }}" class="inline-block px-4 py-2 bg-gradient-to-r from-game-accent to-game-purple text-white text-sm font-semibold rounded-lg hover:opacity-90">Đăng nhập</a>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Feed -->
                <div class="community-feed">
                <!-- Create Post Card -->
                <div class="create-post-box" id="create-post-section">
                    <div class="create-post-input">
                        <img src="" alt="Avatar" id="user-avatar">
                        <button onclick="openCreatePostModal()" class="create-post-btn">
                            Bạn đang nghĩ gì, <span id="user-name-display">bạn</span>?
                        </button>
                    </div>
                    <div class="create-post-actions">
                        <div onclick="openCreatePostModal()" class="create-action-btn">
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            <span>Ảnh/Video</span>
                        </div>
                        <div onclick="openCreatePostModal()" class="create-action-btn">
                            <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                            </svg>
                            <span>Tìm đồng đội</span>
                        </div>
                    </div>
                </div>

                <!-- Guest Message -->
                <div id="guest-message" class="hidden create-post-box text-center py-8">
                    <svg class="w-16 h-16 text-game-accent mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Đăng nhập để tham gia</h3>
                    <p class="text-slate-600 mb-4">Đăng nhập để chia sẻ bài viết và kết nối với cộng đồng</p>
                    <a href="{{ url('/login') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-game-accent to-game-purple text-white font-semibold rounded-lg hover:opacity-90">
                        Đăng nhập ngay
                    </a>
                </div>

                <!-- Posts Loading -->
                <div id="posts-loading" class="text-center py-12">
                    <div class="inline-flex flex-col items-center">
                        <div class="animate-spin w-10 h-10 border-4 border-game-accent border-t-transparent rounded-full mb-3"></div>
                        <p class="text-slate-500">Đang tải bài viết...</p>
                    </div>
                </div>

                <!-- Posts Container -->
                <div id="posts-container"></div>

                <!-- Empty State -->
                <div id="empty-state" class="hidden create-post-box text-center py-12">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Chưa có bài viết</h3>
                    <p class="text-slate-600 mb-4">Hãy là người đầu tiên chia sẻ!</p>
                    <button onclick="openCreatePostModal()" class="px-6 py-3 bg-gradient-to-r from-game-accent to-game-purple text-white font-semibold rounded-lg">
                        Tạo bài viết đầu tiên
                    </button>
                </div>

                <!-- Load More -->
                <div id="load-more-container" class="hidden text-center py-4">
                    <button onclick="loadMorePosts()" id="load-more-btn" class="px-8 py-3 bg-white border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50">
                        Xem thêm bài viết
                    </button>
                </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Lightbox -->
    <div id="lightbox" class="lightbox">
        <div class="lightbox-close" onclick="closeLightbox()">&times;</div>
        <div class="lightbox-nav lightbox-prev" onclick="prevImage()">&#10094;</div>
        <img id="lightbox-img" src="" class="lightbox-content">
        <div class="lightbox-nav lightbox-next" onclick="nextImage()">&#10095;</div>
    </div>

    <!-- Delete post confirmation modal -->
    <div id="delete-post-modal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-2xl">
            <h3 class="font-bold text-lg text-slate-800 mb-2">Xác nhận xóa</h3>
            <p class="text-slate-600 mb-6">Bạn có chắc chắn muốn xóa bài viết này?</p>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeDeletePostModal()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 font-medium">Hủy</button>
                <button type="button" id="delete-post-confirm-btn" onclick="doDeletePost()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium">Xóa</button>
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div id="create-post-modal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="sticky top-0 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
                <div></div>
                <h2 id="create-post-modal-title" class="font-bold text-xl text-slate-800">Tạo bài viết</h2>
                <button onclick="closeCreatePostModal()" class="w-9 h-9 bg-slate-100 hover:bg-slate-200 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-4">
                <div class="flex items-center gap-3 mb-4">
                    <img src="" alt="" id="modal-user-avatar" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <p class="font-semibold text-slate-800" id="modal-user-name">User</p>
                        <select id="post-privacy" class="text-sm text-slate-600 bg-slate-100 px-2 py-1 rounded-md">
                            <option value="public">🌍 Công khai</option>
                            <option value="friends">👥 Bạn bè</option>
                            <option value="private">🔒 Chỉ mình tôi</option>
                        </select>
                    </div>
                </div>

                <textarea id="post-content" rows="5" placeholder="Bạn đang nghĩ gì?" class="w-full p-3 border-0 focus:outline-none resize-none text-lg text-slate-700 placeholder-slate-400"></textarea>

                <div class="mt-3">
                    <input type="text" id="game-preference" placeholder="🎮 Game bạn muốn chơi (tùy chọn)" class="w-full px-4 py-3 bg-slate-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-game-accent/30">
                </div>

                <div class="mt-3">
                    <label class="block text-sm font-medium text-slate-700 mb-2">📷 Thêm ảnh hoặc video</label>
                    
                    <!-- File Upload Area -->
                    <div id="media-upload-area" class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center hover:border-game-accent transition-colors cursor-pointer" onclick="document.getElementById('media-file-input').click()">
                        <svg class="w-10 h-10 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-slate-600 text-sm">Nhấn để chọn ảnh hoặc video</p>
                        <p class="text-slate-400 text-xs mt-1">Hỗ trợ: JPG, PNG, GIF, WEBP, MP4, WEBM (tối đa 50MB)</p>
                    </div>
                    
                    <input type="file" id="media-file-input" multiple accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime" class="hidden" onchange="handleFileSelect(event)">
                    
                    <!-- Media Preview -->
                    <div id="media-preview-container" class="grid grid-cols-3 gap-2 mt-3"></div>
                </div>

                <button onclick="submitPost()" id="submit-post-btn" class="w-full mt-4 px-6 py-3 bg-gradient-to-r from-game-accent to-game-purple text-white font-semibold rounded-lg hover:opacity-90">
                    <span id="submit-post-btn-text">Đăng</span>
                </button>
            </div>
        </div>
    </div>

    <!-- User Hover Card -->
    <div class="user-hover-card" id="user-hover-card">
        <div class="hover-card-cover" id="hc-cover">
            <img id="hc-cover-img" src="" style="display:none">
        </div>
        <div class="hover-card-main">
            <img class="hover-card-avatar" id="hc-avatar" src="">
            <div class="hover-card-name" id="hc-name" onclick=""></div>
            <div class="hover-card-bio" id="hc-bio"></div>
            <div class="hover-card-meta" id="hc-meta"></div>
            <div class="hover-card-friends" id="hc-friends"></div>
            <div class="hover-card-actions" id="hc-actions"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_BASE_URL = '{{ url("/api/community") }}';
    const BASE_URL = '{{ url("/") }}';
    
    let currentPage = 1;
    let lastPage = 1;
    let isLoading = false;
    let currentUser = null;
    let lightboxImages = [];
    let lightboxIndex = 0;
    let activeGameFilter = null;

    // Check authentication
    function checkAuth() {
        const token = localStorage.getItem('auth_token');
        const userStr = localStorage.getItem('user');
        
        if (token && userStr) {
            try {
                currentUser = JSON.parse(userStr);
                document.getElementById('create-post-section').classList.remove('hidden');
                document.getElementById('guest-message').classList.add('hidden');
                
                const defaultAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(currentUser.name)}&background=6366f1&color=fff&size=64`;
                document.getElementById('user-avatar').src = currentUser.avatar || defaultAvatar;
                document.getElementById('user-name-display').textContent = currentUser.name;
                document.getElementById('modal-user-avatar').src = currentUser.avatar || defaultAvatar;
                document.getElementById('modal-user-name').textContent = currentUser.name;
            } catch (e) {
                showGuestUI();
            }
        } else {
            showGuestUI();
        }
    }

    function showGuestUI() {
        currentUser = null;
        document.getElementById('create-post-section').classList.add('hidden');
        document.getElementById('guest-message').classList.remove('hidden');
        
        // Show login prompt in sidebar
        document.getElementById('game-groups-list').innerHTML = '';
        document.getElementById('sidebar-login-prompt').classList.remove('hidden');
    }

    // Load posts
    async function loadPosts(page = 1) {
        if (isLoading) return;
        isLoading = true;

        const loadingEl = document.getElementById('posts-loading');
        const postsContainer = document.getElementById('posts-container');
        const emptyState = document.getElementById('empty-state');
        const loadMoreContainer = document.getElementById('load-more-container');

        try {
            loadingEl.classList.remove('hidden');
            
            let url = `${API_BASE_URL}/posts?page=${page}&per_page=10`;
            if (activeGameFilter) {
                url += `&game_filter=${encodeURIComponent(activeGameFilter)}`;
            }
            const response = await fetch(url);
            const result = await response.json();

            if (!result.success || !result.data || result.data.length === 0) {
                if (page === 1) {
                    postsContainer.innerHTML = '';
                    emptyState.classList.remove('hidden');
                }
                loadMoreContainer.classList.add('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            
            if (page === 1) {
                postsContainer.innerHTML = '';
            }

            result.data.forEach(post => {
                postsContainer.appendChild(createPostCard(post));
            });

            currentPage = result.pagination.current_page;
            lastPage = result.pagination.last_page;

            if (currentPage < lastPage) {
                loadMoreContainer.classList.remove('hidden');
            } else {
                loadMoreContainer.classList.add('hidden');
            }

        } catch (error) {
            console.error('Error loading posts:', error);
            if (page === 1) {
                emptyState.classList.remove('hidden');
            }
        } finally {
            loadingEl.classList.add('hidden');
            isLoading = false;
        }
    }

    function loadMorePosts() {
        if (currentPage < lastPage) {
            loadPosts(currentPage + 1);
        }
    }

    // Cache post for edit (author)
    window._communityPostCache = window._communityPostCache || {};
    
    // Create Facebook-style post card
    function createPostCard(post) {
        _communityPostCache[post.id] = post;
        const card = document.createElement('article');
        card.className = 'post-card';
        card.id = `post-${post.id}`;
        
        const defaultAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(post.user.name)}&background=6366f1&color=fff&size=64`;
        const userAvatar = post.user.avatar || defaultAvatar;
        
        // Build media HTML (Facebook-style grid)
        let mediaHtml = '';
        const allMedia = [...(post.images || []), ...(post.videos || [])];
        
        if (allMedia.length > 0) {
            const gridClass = allMedia.length === 1 ? 'media-grid-1' : 
                             allMedia.length === 2 ? 'media-grid-2' : 
                             allMedia.length === 3 ? 'media-grid-3' : 
                             allMedia.length === 4 ? 'media-grid-4' : 'media-grid-5plus';
            
            mediaHtml = `<div class="media-container ${gridClass}">`;
            
            const displayMedia = allMedia.slice(0, 5);
            const extraCount = allMedia.length - 5;
            
            displayMedia.forEach((url, idx) => {
                const isVideo = url.match(/\.(mp4|webm|ogg)$/i) || url.includes('video');
                const isLast = idx === 4 && extraCount > 0;
                
                mediaHtml += `<div class="media-item" onclick="${isVideo ? `playVideo(event, '${url}')` : `openLightbox(${post.id}, ${idx})`}">`;
                
                if (isVideo) {
                    mediaHtml += `
                        <video src="${url}" preload="metadata"></video>
                        <div class="video-play-btn">▶</div>
                    `;
                } else {
                    mediaHtml += `<img src="${url}" alt="Media" loading="lazy">`;
                }
                
                if (isLast) {
                    mediaHtml += `<div class="media-overlay">+${extraCount}</div>`;
                }
                
                mediaHtml += `</div>`;
            });
            
            mediaHtml += '</div>';
        }
        
        // Game preference tag
        const gameTagHtml = post.game_preference 
            ? `<div class="game-tag">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                </svg>
                Tìm đồng đội: ${escapeHtml(post.game_preference)}
            </div>` : '';
        
        // Chỉnh sửa (chỉ người đăng), Xóa (người đăng hoặc admin/editor)
        const canEdit = currentUser && currentUser.id === post.user_id;
        const canDelete = currentUser && (currentUser.id === post.user_id || currentUser.role === 'admin' || currentUser.role === 'editor');
        const menuHtml = (canEdit || canDelete) ? `
            <div class="flex items-center gap-1">
                ${canEdit ? `<button onclick="openEditPostModal(${post.id})" class="p-2 hover:bg-slate-100 rounded-full" title="Chỉnh sửa">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>` : ''}
                ${canDelete ? `<button onclick="confirmDeletePost(${post.id})" class="p-2 hover:bg-slate-100 rounded-full" title="Xóa">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>` : ''}
            </div>` : '';
        
        // Truncate content
        const maxLen = 200;
        const content = post.content || '';
        const needsTruncate = content.length > maxLen;
        const displayContent = needsTruncate ? content.substring(0, maxLen) : content;
        const seeMoreHtml = needsTruncate ? `<span class="see-more-btn" onclick="toggleContent(${post.id})">... Xem thêm</span>` : '';
        
        const isLiked = post.is_liked || false;
        
        // Store media for lightbox
        card.dataset.media = JSON.stringify(allMedia.filter(url => !url.match(/\.(mp4|webm|ogg)$/i) && !url.includes('video')));
        
        card.innerHTML = `
            <!-- Header -->
            <div class="post-header">
                <img src="${userAvatar}" alt="${escapeHtml(post.user.name)}" class="post-avatar" style="cursor:pointer" onclick="window.location.href='${BASE_URL}/profile/${post.user_id}'" onmouseenter="showHoverCard(event, ${post.user_id})" onmouseleave="scheduleHideHoverCard()">
                <div class="flex-1">
                    <p class="post-user-name" style="cursor:pointer" onclick="window.location.href='${BASE_URL}/profile/${post.user_id}'" onmouseenter="showHoverCard(event, ${post.user_id})" onmouseleave="scheduleHideHoverCard()">${escapeHtml(post.user.name)}</p>
                    <p class="post-time">${formatTimeAgo(post.created_at)}</p>
                </div>
                ${menuHtml}
            </div>
            
            <!-- Content -->
            <div class="post-content" id="content-${post.id}" data-full="${escapeHtml(content)}" data-truncated="true">
                <span id="text-${post.id}">${escapeHtml(displayContent)}</span>${seeMoreHtml}
            </div>
            
            <!-- Game Tag -->
            ${gameTagHtml}
            
            <!-- Media -->
            ${mediaHtml}
            
            <!-- Stats -->
            <div class="post-stats">
                <div class="like-count">
                    <div class="like-icon">
                        <svg viewBox="0 0 16 16"><path d="M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748z"/></svg>
                    </div>
                    <span id="likes-count-${post.id}">${post.likes_count || 0}</span>
                </div>
                <span class="text-slate-500 text-sm comments-count-link" id="comments-count-${post.id}" onclick="toggleComments(${post.id})" style="cursor:pointer">${post.comments_count || 0} bình luận</span>
            </div>
            
            <!-- Actions -->
            <div class="post-actions">
                <div class="action-btn ${isLiked ? 'liked' : ''}" id="like-btn-${post.id}" onclick="toggleLike(${post.id})">
                    <svg class="w-5 h-5" fill="${isLiked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    Thích
                </div>
                <div class="action-btn" onclick="toggleComments(${post.id})">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Bình luận
                </div>
            </div>
            
            <!-- Comments Section -->
            <div class="comments-section" id="comments-section-${post.id}">
                <div class="comments-wrapper">
                    <div class="comment-input-wrapper">
                        <img src="${currentUser ? (currentUser.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentUser.name)}&background=6366f1&color=fff`) : 'https://ui-avatars.com/api/?name=G&background=ccc&color=fff'}">
                        <input type="text" class="comment-input" id="comment-input-${post.id}" placeholder="Viết bình luận..." onkeypress="if(event.key==='Enter')submitComment(${post.id})">
                        <button class="send-btn" onclick="submitComment(${post.id})">Gửi</button>
                    </div>
                    <div id="comments-list-${post.id}"></div>
                </div>
            </div>
        `;
        
        return card;
    }

    // Toggle content expand/collapse
    function toggleContent(postId) {
        const contentEl = document.getElementById(`content-${postId}`);
        const textEl = document.getElementById(`text-${postId}`);
        const fullContent = contentEl.dataset.full;
        const isTruncated = contentEl.dataset.truncated === 'true';
        
        if (isTruncated) {
            textEl.textContent = fullContent;
            textEl.nextElementSibling?.remove();
            contentEl.dataset.truncated = 'false';
        }
    }

    // Lightbox functions
    function openLightbox(postId, index) {
        const card = document.getElementById(`post-${postId}`);
        lightboxImages = JSON.parse(card.dataset.media || '[]');
        lightboxIndex = index;
        
        if (lightboxImages.length > 0) {
            document.getElementById('lightbox-img').src = lightboxImages[lightboxIndex];
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
    }

    function nextImage() {
        lightboxIndex = (lightboxIndex + 1) % lightboxImages.length;
        document.getElementById('lightbox-img').src = lightboxImages[lightboxIndex];
    }

    function prevImage() {
        lightboxIndex = (lightboxIndex - 1 + lightboxImages.length) % lightboxImages.length;
        document.getElementById('lightbox-img').src = lightboxImages[lightboxIndex];
    }

    function playVideo(event, url) {
        event.stopPropagation();
        const video = event.currentTarget.querySelector('video');
        const playBtn = event.currentTarget.querySelector('.video-play-btn');
        
        if (video.paused) {
            video.play();
            playBtn.style.display = 'none';
        } else {
            video.pause();
            playBtn.style.display = 'flex';
        }
    }

    // Toggle like
    async function toggleLike(postId) {
        if (!currentUser) {
            window.location.href = BASE_URL + '/login';
            return;
        }
        
        const btn = document.getElementById(`like-btn-${postId}`);
        const countEl = document.getElementById(`likes-count-${postId}`);
        
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(`${API_BASE_URL}/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            if (result.success) {
                if (result.liked) {
                    btn.classList.add('liked');
                    btn.querySelector('svg').setAttribute('fill', 'currentColor');
                } else {
                    btn.classList.remove('liked');
                    btn.querySelector('svg').setAttribute('fill', 'none');
                }
                countEl.textContent = result.likes_count;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Toggle comments
    function toggleComments(postId) {
        const section = document.getElementById(`comments-section-${postId}`);
        const isExpanded = section.classList.contains('expanded');
        
        if (isExpanded) {
            section.classList.remove('expanded');
        } else {
            section.classList.add('expanded');
            loadComments(postId);
        }
    }

    // Load comments
    async function loadComments(postId) {
        const listEl = document.getElementById(`comments-list-${postId}`);
        
        try {
            const token = localStorage.getItem('auth_token');
            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = `Bearer ${token}`;
            
            const response = await fetch(`${API_BASE_URL}/posts/${postId}/comments`, { headers });
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                listEl.innerHTML = result.data.map(c => renderCommentHTML(c, postId)).join('');
                
                // Bind reply buttons via data attributes
                listEl.querySelectorAll('[data-reply-for]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        showReplyInput(
                            parseInt(btn.dataset.replyFor),
                            postId,
                            btn.dataset.replyName
                        );
                    });
                });
            } else {
                listEl.innerHTML = '<p class="text-center text-slate-500 text-sm py-2">Chưa có bình luận</p>';
            }
        } catch (error) {
            listEl.innerHTML = '<p class="text-center text-red-500 text-sm py-2">Lỗi tải bình luận</p>';
        }
    }

    // Render a single comment with replies (recursive, unlimited depth)
    function renderCommentHTML(c, postId, depth = 0) {
        const avatar = c.user_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(c.user_name)}&background=6366f1&color=fff`;
        const likesText = c.likes_count > 0 ? `<span class="comment-likes-count"> · ${c.likes_count}</span>` : '';
        const timeText = `<span class="comment-time">${formatTimeAgo(c.created_at)}</span>`;
        const isReply = depth > 0;
        const imgSize = isReply ? 28 : 32;
        const bubbleClass = isReply ? 'reply-bubble' : 'comment-bubble';
        const itemClass = isReply ? 'reply-item' : 'comment-item';
        
        // Cap visual indent at 3 levels max
        const indentPx = Math.min(depth, 3) * 40;
        const marginStyle = indentPx > 0 ? `margin-left:${indentPx}px;` : '';
        
        let repliesHTML = '';
        if (c.replies && c.replies.length > 0) {
            // Render each reply recursively
            const repliesContent = c.replies.map(r => renderCommentHTML(r, postId, depth + 1)).join('');
            
            repliesHTML = `
                <div class="view-replies-btn" style="${marginStyle}" onclick="toggleReplies(${c.id})">
                    ↳ ${countAllReplies(c)} phản hồi
                </div>
                <div id="replies-${c.id}" style="display:none">
                    ${repliesContent}
                </div>
            `;
        }
        
        const userAvatar = currentUser ? (currentUser.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentUser.name)}&background=6366f1&color=fff`) : '';
        
        return `
            <div class="${itemClass}" style="${marginStyle}">
                <img src="${avatar}" style="width:${imgSize}px;height:${imgSize}px;cursor:pointer" onclick="window.location.href='${BASE_URL}/profile/${c.user_id}'" onmouseenter="showHoverCard(event, ${c.user_id})" onmouseleave="scheduleHideHoverCard()">
                <div class="comment-body">
                    <div class="${bubbleClass}">
                        <p class="comment-name" style="cursor:pointer" onclick="window.location.href='${BASE_URL}/profile/${c.user_id}'" onmouseenter="showHoverCard(event, ${c.user_id})" onmouseleave="scheduleHideHoverCard()">${escapeHtml(c.user_name)}</p>
                        <p class="comment-text">${parseMentions(c.content)}</p>
                    </div>
                    <div class="comment-actions">
                        <span class="comment-action-btn ${c.is_liked ? 'liked' : ''}" id="clike-${c.id}" onclick="toggleCommentLike(${c.id}, ${postId})">Thích${likesText}</span>
                        <span class="comment-action-btn" data-reply-for="${c.id}" data-reply-name="${escapeHtml(c.user_name)}">Trả lời</span>
                        ${timeText}
                    </div>
                </div>
            </div>
            ${repliesHTML}
            <div id="reply-input-${c.id}" class="reply-input-wrapper" style="display:none;margin-left:${Math.min(depth + 1, 3) * 40}px;">
                <img src="${userAvatar}" style="width:${imgSize}px;height:${imgSize}px;">
                <input type="text" class="reply-input" id="reply-text-${c.id}" placeholder="Viết phản hồi..." onkeypress="if(event.key==='Enter')submitReply(${postId}, ${c.id})">
            </div>
        `;
    }

    // Count all replies recursively
    function countAllReplies(comment) {
        let count = 0;
        if (comment.replies && comment.replies.length > 0) {
            count += comment.replies.length;
            comment.replies.forEach(r => { count += countAllReplies(r); });
        }
        return count;
    }

    // Toggle replies visibility
    function toggleReplies(commentId) {
        const el = document.getElementById(`replies-${commentId}`);
        const btn = el.previousElementSibling;
        if (el.style.display === 'none') {
            el.style.display = 'block';
            btn.style.display = 'none';
        } else {
            el.style.display = 'none';
        }
    }

    // Show reply input with @mention
    function showReplyInput(commentId, postId, userName) {
        if (!currentUser) {
            window.location.href = BASE_URL + '/login';
            return;
        }
        const el = document.getElementById(`reply-input-${commentId}`);
        const input = el.querySelector('input');
        
        if (el.style.display === 'none' || el.style.display === '') {
            el.style.display = 'flex';
            // Pre-fill with @username
            if (userName && !input.value) {
                input.value = `@${userName} `;
            }
            input.focus();
            // Move cursor to end
            input.setSelectionRange(input.value.length, input.value.length);
        } else {
            el.style.display = 'none';
        }
    }

    // Submit reply
    async function submitReply(postId, parentId) {
        if (!currentUser) {
            window.location.href = BASE_URL + '/login';
            return;
        }
        
        const input = document.getElementById(`reply-text-${parentId}`);
        const content = input.value.trim();
        if (!content) return;
        
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(`${API_BASE_URL}/posts/${postId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content, parent_id: parentId })
            });
            
            const result = await response.json();
            if (result.success) {
                input.value = '';
                document.getElementById(`comments-count-${postId}`).textContent = `${result.comments_count} bình luận`;
                loadComments(postId);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Toggle like on comment
    async function toggleCommentLike(commentId, postId) {
        if (!currentUser) {
            window.location.href = BASE_URL + '/login';
            return;
        }
        
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(`${API_BASE_URL}/comments/${commentId}/like`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            if (result.success) {
                const btn = document.getElementById(`clike-${commentId}`);
                if (btn) {
                    btn.classList.toggle('liked', result.liked);
                    const countText = result.likes_count > 0 ? ` · ${result.likes_count}` : '';
                    btn.innerHTML = `Thích${countText ? `<span class="comment-likes-count">${countText}</span>` : ''}`;
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Submit comment
    async function submitComment(postId) {
        if (!currentUser) {
            window.location.href = BASE_URL + '/login';
            return;
        }
        
        const input = document.getElementById(`comment-input-${postId}`);
        const content = input.value.trim();
        if (!content) return;
        
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(`${API_BASE_URL}/posts/${postId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content })
            });
            
            const result = await response.json();
            if (result.success) {
                input.value = '';
                document.getElementById(`comments-count-${postId}`).textContent = `${result.comments_count} bình luận`;
                loadComments(postId);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    let postIdToDelete = null;
    let editingPostId = null;

    function confirmDeletePost(postId) {
        postIdToDelete = postId;
        document.getElementById('delete-post-modal').classList.remove('hidden');
        document.getElementById('delete-post-modal').classList.add('flex');
    }

    function closeDeletePostModal() {
        postIdToDelete = null;
        document.getElementById('delete-post-modal').classList.add('hidden');
        document.getElementById('delete-post-modal').classList.remove('flex');
    }

    async function doDeletePost() {
        if (!postIdToDelete) return;
        const postId = postIdToDelete;
        closeDeletePostModal();
        postIdToDelete = null;
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(`${API_BASE_URL}/posts/${postId}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) {
                document.getElementById(`post-${postId}`)?.remove();
            } else {
                alert(result.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            alert('Có lỗi xảy ra');
        }
    }

    function openEditPostModal(postId) {
        const post = window._communityPostCache && window._communityPostCache[postId];
        if (!post) return;
        editingPostId = postId;
        document.getElementById('create-post-modal-title').textContent = 'Chỉnh sửa bài viết';
        document.getElementById('submit-post-btn-text').textContent = 'Lưu';
        document.getElementById('post-content').value = post.content || '';
        document.getElementById('game-preference').value = post.game_preference || '';
        document.getElementById('create-post-modal').classList.remove('hidden');
        document.getElementById('create-post-modal').classList.add('flex');
    }

    // Modal functions
    function openCreatePostModal() {
        if (!currentUser) {
            window.location.href = BASE_URL + '/login';
            return;
        }
        document.getElementById('create-post-modal').classList.remove('hidden');
        document.getElementById('create-post-modal').classList.add('flex');
        
        // Auto-fill game preference when inside a game group
        if (activeGameFilter) {
            document.getElementById('game-preference').value = activeGameFilter;
        }
    }

    function closeCreatePostModal() {
        editingPostId = null;
        document.getElementById('create-post-modal-title').textContent = 'Tạo bài viết';
        document.getElementById('submit-post-btn-text').textContent = 'Đăng';
        document.getElementById('create-post-modal').classList.add('hidden');
        document.getElementById('create-post-modal').classList.remove('flex');
        document.getElementById('post-content').value = '';
        document.getElementById('game-preference').value = '';
        clearMediaFiles();
    }

    // Media file handling
    let selectedFiles = [];

    function handleFileSelect(event) {
        const files = Array.from(event.target.files);
        const maxSize = 50 * 1024 * 1024; // 50MB
        
        files.forEach(file => {
            if (file.size > maxSize) {
                alert(`File "${file.name}" vượt quá 50MB`);
                return;
            }
            
            // Check if already added
            if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                return;
            }
            
            selectedFiles.push(file);
        });
        
        renderMediaPreviews();
        event.target.value = ''; // Reset input để có thể chọn lại cùng file
    }

    function renderMediaPreviews() {
        const container = document.getElementById('media-preview-container');
        container.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'relative aspect-square rounded-lg overflow-hidden bg-slate-100';
            
            const isVideo = file.type.startsWith('video/');
            const url = URL.createObjectURL(file);
            
            if (isVideo) {
                div.innerHTML = `
                    <video src="${url}" class="w-full h-full object-cover"></video>
                    <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                `;
            } else {
                div.innerHTML = `<img src="${url}" class="w-full h-full object-cover">`;
            }
            
            // Remove button
            div.innerHTML += `
                <button onclick="removeMediaFile(${index})" class="absolute top-1 right-1 w-6 h-6 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white text-sm">
                    ✕
                </button>
            `;
            
            container.appendChild(div);
        });
    }

    function removeMediaFile(index) {
        selectedFiles.splice(index, 1);
        renderMediaPreviews();
    }

    function clearMediaFiles() {
        selectedFiles = [];
        document.getElementById('media-preview-container').innerHTML = '';
        document.getElementById('media-file-input').value = '';
    }

    // Submit post with file upload
    async function submitPost() {
        if (!currentUser) {
            window.location.href = BASE_URL + '/login';
            return;
        }

        const content = document.getElementById('post-content').value.trim();
        if (!content) {
            alert('Vui lòng nhập nội dung');
            return;
        }

        const submitBtn = document.getElementById('submit-post-btn');
        const btnText = document.getElementById('submit-post-btn-text');
        submitBtn.disabled = true;
        btnText.textContent = editingPostId ? 'Đang lưu...' : 'Đang đăng...';

        try {
            const token = localStorage.getItem('auth_token');

            if (editingPostId) {
                const response = await fetch(`${API_BASE_URL}/posts/${editingPostId}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        content,
                        game_preference: document.getElementById('game-preference').value.trim() || ''
                    })
                });
                const result = await response.json();
                if (result.success && result.data) {
                    const post = result.data;
                    window._communityPostCache[post.id] = post;
                    const contentEl = document.getElementById('content-' + post.id);
                    const textEl = document.getElementById('text-' + post.id);
                    if (contentEl && textEl) {
                        const safe = (s) => (s || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        contentEl.dataset.full = safe(post.content);
                        const maxLen = 200;
                        const needsTruncate = (post.content || '').length > maxLen;
                        textEl.textContent = needsTruncate ? (post.content || '').substring(0, maxLen) : (post.content || '');
                        const seeMore = contentEl.querySelector('.see-more-btn');
                        if (seeMore) seeMore.remove();
                        if (needsTruncate) {
                            const span = document.createElement('span');
                            span.className = 'see-more-btn';
                            span.onclick = () => toggleContent(post.id);
                            span.textContent = '... Xem thêm';
                            contentEl.appendChild(span);
                        }
                    }
                    const card = document.getElementById('post-' + post.id);
                    if (card) {
                        let gameTag = card.querySelector('.game-tag');
                        if (post.game_preference) {
                            const txt = (post.game_preference || '').replace(/</g, '&lt;');
                            if (gameTag) gameTag.innerHTML = `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg> Tìm đồng đội: ${txt}`;
                            else {
                                const div = document.createElement('div');
                                div.className = 'game-tag';
                                div.innerHTML = `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg> Tìm đồng đội: ${txt}`;
                                contentEl?.after(div);
                            }
                        } else if (gameTag) gameTag.remove();
                    }
                    closeCreatePostModal();
                } else {
                    alert(result.message || 'Có lỗi xảy ra');
                }
            } else {
                const formData = new FormData();
                formData.append('content', content);
                formData.append('game_preference', document.getElementById('game-preference').value.trim() || '');
                formData.append('privacy', document.getElementById('post-privacy').value);
                selectedFiles.forEach((file, index) => formData.append(`media[${index}]`, file));

                const response = await fetch(`${API_BASE_URL}/posts`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    closeCreatePostModal();
                    loadPosts(1);
                } else {
                    alert(result.message || 'Có lỗi xảy ra');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert(editingPostId ? 'Có lỗi khi lưu' : 'Có lỗi xảy ra khi đăng bài');
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = editingPostId ? 'Lưu' : 'Đăng';
        }
    }

    // Utility functions
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTimeAgo(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'Vừa xong';
        if (diffMins < 60) return `${diffMins} phút`;
        if (diffHours < 24) return `${diffHours} giờ`;
        if (diffDays < 7) return `${diffDays} ngày`;
        
        return date.toLocaleDateString('vi-VN');
    }

    // Load game groups for sidebar
    async function loadGameGroups() {
        const token = localStorage.getItem('auth_token');
        if (!token) return;
        
        try {
            const response = await fetch(`${API_BASE_URL}/game-groups`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            const listEl = document.getElementById('game-groups-list');
            
            if (result.success && result.data && result.data.length > 0) {
                listEl.innerHTML = result.data.map(game => `
                    <div class="game-group-item" id="group-${game.id}" data-game-title="${escapeHtml(game.title)}" data-game-id="${game.id}">
                        <img src="${game.image}" alt="${escapeHtml(game.title)}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(game.title)}&background=6366f1&color=fff&size=48'">
                        <div>
                            <div class="game-group-name">${escapeHtml(game.title.replace(/ –.*$/, ''))}</div>
                            <div class="game-group-meta">${game.member_count} thành viên · ${game.post_count} bài viết</div>
                        </div>
                    </div>
                `).join('');
                
                // Add click handlers
                listEl.querySelectorAll('.game-group-item').forEach(item => {
                    item.addEventListener('click', () => {
                        filterByGame(item.dataset.gameTitle, parseInt(item.dataset.gameId));
                    });
                });
            } else {
                listEl.innerHTML = `
                    <div class="game-group-empty">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                        </svg>
                        <p>Bạn chưa mua game online nào</p>
                        <a href="${BASE_URL}/store" class="inline-block mt-2 text-game-accent font-semibold text-sm hover:underline">Khám phá cửa hàng →</a>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading game groups:', error);
            document.getElementById('game-groups-list').innerHTML = '';
        }
    }

    // Filter posts by game group
    function filterByGame(gameName, gameId = null) {
        activeGameFilter = gameName;
        
        // Update sidebar active state
        document.querySelectorAll('.game-group-item, .game-group-all').forEach(el => el.classList.remove('active'));
        
        if (gameName === null) {
            document.getElementById('group-all').classList.add('active');
        } else if (gameId) {
            const el = document.getElementById(`group-${gameId}`);
            if (el) el.classList.add('active');
        }
        
        // Reload posts with filter
        currentPage = 1;
        document.getElementById('posts-container').innerHTML = '';
        loadPosts(1);

        // Scroll to top of feed
        const feed = document.querySelector('.community-feed');
        if (feed) feed.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // === HOVER CARD - @MENTION SYSTEM ===
    
    // Parse @mentions in comment text → clickable/hoverable links
    function parseMentions(text) {
        // First escape HTML
        const escaped = escapeHtml(text);
        // Replace @Name (supports Vietnamese characters with diacritics, spaces after @)
        return escaped.replace(/@([A-Za-zÀ-ỹ\s]+?)(?=\s{2}|$|[.,!?;:\)])/g, (match, name) => {
            const trimName = name.trim();
            if (!trimName) return match;
            return `<span class="mention-link" data-mention-name="${trimName}" onmouseenter="showHoverCardByName(event, '${trimName}')" onmouseleave="scheduleHideHoverCard()">@${trimName}</span>`;
        });
    }

    // Hover card state
    let hoverCardTimer = null;
    let hoverCardHideTimer = null;
    let hoverCardCache = {}; // cache profile data by user id
    let hoverCardNameCache = {}; // cache user id by name
    const hoverCard = document.getElementById('user-hover-card');

    // Position hover card near the trigger element
    function positionHoverCard(event) {
        const card = hoverCard;
        const rect = event.target.getBoundingClientRect();
        let top = rect.bottom + 8;
        let left = rect.left;

        // Adjust if card goes below viewport
        if (top + 320 > window.innerHeight) {
            top = rect.top - 320;
        }
        // Adjust if card goes right of viewport
        if (left + 340 > window.innerWidth) {
            left = window.innerWidth - 356;
        }
        if (left < 8) left = 8;

        card.style.top = top + 'px';
        card.style.left = left + 'px';
    }

    // Show hover card by user ID
    async function showHoverCard(event, userId) {
        clearTimeout(hoverCardHideTimer);
        positionHoverCard(event);

        // Show loading state immediately
        hoverCard.classList.add('visible');

        try {
            let data;
            if (hoverCardCache[userId]) {
                data = hoverCardCache[userId];
            } else {
                const token = localStorage.getItem('auth_token');
                const headers = { 'Accept': 'application/json' };
                if (token) headers['Authorization'] = `Bearer ${token}`;

                const res = await fetch(`${API_BASE_URL.replace('/community', '')}/profile/${userId}`, { headers });
                const result = await res.json();
                if (!result.success) return;
                data = result.data;
                hoverCardCache[userId] = data;
            }
            renderHoverCard(data);
        } catch(e) {
            console.error('Hover card error:', e);
        }
    }

    // Show hover card by @name (search user by name first)
    async function showHoverCardByName(event, name) {
        clearTimeout(hoverCardHideTimer);
        positionHoverCard(event);
        hoverCard.classList.add('visible');

        try {
            // Check name cache first
            if (hoverCardNameCache[name]) {
                const userId = hoverCardNameCache[name];
                if (hoverCardCache[userId]) {
                    renderHoverCard(hoverCardCache[userId]);
                    return;
                }
            }

            // Search for user by name via profile API
            const token = localStorage.getItem('auth_token');
            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            // Try to find user - search API
            const searchRes = await fetch(`${API_BASE_URL.replace('/community', '')}/users/search?name=${encodeURIComponent(name)}`, { headers });
            const searchResult = await searchRes.json();
            
            if (searchResult.success && searchResult.data && searchResult.data.id) {
                const userId = searchResult.data.id;
                hoverCardNameCache[name] = userId;
                
                // Now get full profile
                const res = await fetch(`${API_BASE_URL.replace('/community', '')}/profile/${userId}`, { headers });
                const result = await res.json();
                if (result.success) {
                    hoverCardCache[userId] = result.data;
                    renderHoverCard(result.data);
                }
            } else {
                hideHoverCard();
            }
        } catch(e) {
            console.error('Hover card by name error:', e);
            hideHoverCard();
        }
    }

    // Render hover card content
    function renderHoverCard(data) {
        const u = data.user;
        const profileUrl = `${BASE_URL}/profile/${u.id}`;

        // Cover
        const coverImg = document.getElementById('hc-cover-img');
        if (u.cover_image) {
            coverImg.src = u.cover_image;
            coverImg.style.display = 'block';
        } else {
            coverImg.style.display = 'none';
        }

        // Avatar
        const av = u.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=6366f1&color=fff&size=72`;
        document.getElementById('hc-avatar').src = av;

        // Name
        const nameEl = document.getElementById('hc-name');
        nameEl.textContent = u.name;
        nameEl.onclick = () => { window.location.href = profileUrl; };

        // Bio
        const bioEl = document.getElementById('hc-bio');
        bioEl.textContent = u.bio || '';
        bioEl.style.display = u.bio ? 'block' : 'none';

        // Meta
        let metaHTML = '';
        if (u.address) metaHTML += `<div class="hover-card-meta"><span class="material-icons-outlined">home</span> ${escapeHtml(u.address)}</div>`;
        if (data.friends_count !== undefined) {
            metaHTML += `<div class="hover-card-meta"><span class="material-icons-outlined">people</span> ${data.friends_count} bạn bè</div>`;
        }
        document.getElementById('hc-meta').innerHTML = metaHTML;

        // Friends avatars
        const friendsEl = document.getElementById('hc-friends');
        if (data.friends && data.friends.length > 0) {
            let fHTML = data.friends.slice(0, 5).map(f => {
                const fav = f.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=6366f1&color=fff&size=28`;
                return `<img src="${fav}" title="${escapeHtml(f.name)}">`;
            }).join('');
            if (data.friends_count > 5) {
                fHTML += `<span class="hover-card-friends-text">+${data.friends_count - 5}</span>`;
            }
            friendsEl.innerHTML = fHTML;
            friendsEl.style.display = 'flex';
        } else {
            friendsEl.style.display = 'none';
        }

        // Actions
        const actionsEl = document.getElementById('hc-actions');
        if (data.friend_status === 'self') {
            actionsEl.innerHTML = `<button class="hc-btn hc-btn-primary" onclick="window.location.href='${profileUrl}'"><span class="material-icons-outlined" style="font-size:16px">person</span> Xem trang cá nhân</button>`;
        } else if (data.friend_status === 'friends') {
            actionsEl.innerHTML = `
                <button class="hc-btn hc-btn-primary" id="hc-btn-chat"><span class="material-icons-outlined" style="font-size:16px">chat</span> Nhắn tin</button>
                <button class="hc-btn hc-btn-secondary" onclick="window.location.href='${profileUrl}'"><span class="material-icons-outlined" style="font-size:16px">person</span> Trang cá nhân</button>
            `;
            document.getElementById('hc-btn-chat').addEventListener('click', function() {
                if (typeof gOpenChat === 'function') {
                    gOpenChat(u.id, u.name, u.avatar || '');
                }
            });
        } else if (data.friend_status === 'sent') {
            actionsEl.innerHTML = `<button class="hc-btn hc-btn-secondary"><span class="material-icons-outlined" style="font-size:16px">schedule</span> Đã gửi lời mời</button>`;
        } else {
            actionsEl.innerHTML = `
                <button class="hc-btn hc-btn-primary" onclick="sendFriendFromCard(${u.id})"><span class="material-icons-outlined" style="font-size:16px">person_add</span> Kết bạn</button>
                <button class="hc-btn hc-btn-secondary" onclick="window.location.href='${profileUrl}'"><span class="material-icons-outlined" style="font-size:16px">person</span> Trang cá nhân</button>
            `;
        }
    }

    // Send friend request from hover card
    async function sendFriendFromCard(userId) {
        const token = localStorage.getItem('auth_token');
        if (!token) { window.location.href = BASE_URL + '/login'; return; }
        try {
            await fetch(`${API_BASE_URL.replace('/community', '')}/friends/request/${userId}`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            // Clear cache and update button
            delete hoverCardCache[userId];
            document.getElementById('hc-actions').innerHTML = `<button class="hc-btn hc-btn-secondary"><span class="material-icons-outlined" style="font-size:16px">schedule</span> Đã gửi lời mời</button>`;
        } catch(e) { console.error(e); }
    }

    // Schedule hide hover card (with delay so user can mouse into card)
    function scheduleHideHoverCard() {
        hoverCardHideTimer = setTimeout(() => {
            hideHoverCard();
        }, 300);
    }

    function hideHoverCard() {
        hoverCard.classList.remove('visible');
    }

    // Keep card visible when hovering on the card itself
    hoverCard.addEventListener('mouseenter', () => {
        clearTimeout(hoverCardHideTimer);
    });
    hoverCard.addEventListener('mouseleave', () => {
        hideHoverCard();
    });

    // Scroll to and highlight specific post
    function scrollToPost(postId) {
        const postElement = document.getElementById(`post-${postId}`);
        if (postElement) {
            // Scroll to the post
            postElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Add highlight effect
            postElement.style.transition = 'all 0.3s ease';
            postElement.style.boxShadow = '0 0 0 3px #6366f1, 0 4px 20px rgba(99,102,241,0.3)';
            postElement.style.transform = 'scale(1.01)';
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                postElement.style.boxShadow = '';
                postElement.style.transform = '';
            }, 3000);
        }
    }
    
    // Check URL for post parameter and load specific post
    async function loadPostFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const targetPostId = urlParams.get('post');
        
        if (targetPostId) {
            // First, load posts
            await loadPosts();
            
            // Try to find the post in current page
            let postElement = document.getElementById(`post-${targetPostId}`);
            
            // If not found, try loading more until we find it or no more pages
            while (!postElement && currentPage < lastPage) {
                await loadPosts(currentPage + 1);
                postElement = document.getElementById(`post-${targetPostId}`);
            }
            
            // Scroll to and highlight the post
            if (postElement) {
                setTimeout(() => scrollToPost(targetPostId), 300);
            }
        } else {
            // No specific post, just load normally
            loadPosts();
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();
        loadPostFromUrl();
        loadGameGroups();
    });

    // Close modal on outside click
    document.getElementById('create-post-modal').addEventListener('click', (e) => {
        if (e.target.id === 'create-post-modal') closeCreatePostModal();
    });

    // Close lightbox on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
    });
</script>
@endpush
