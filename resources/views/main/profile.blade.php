@extends('layouts.main')

@section('title', 'Trang cá nhân')

@push('styles')
<style>
    /* Cover & Profile Header */
    .profile-cover {
        height: 350px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        border-radius: 0 0 16px 16px;
        overflow: hidden;
    }
    .profile-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .cover-edit-btn {
        position: absolute;
        bottom: 16px;
        right: 16px;
        background: rgba(0,0,0,0.6);
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }
    .cover-edit-btn:hover { background: rgba(0,0,0,0.8); }

    .profile-header {
        max-width: 940px;
        margin: -80px auto 0;
        padding: 0 16px;
        position: relative;
        z-index: 10;
    }
    .profile-header-inner {
        display: flex;
        align-items: flex-end;
        gap: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e4e6eb;
    }
    .profile-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }
    .profile-avatar {
        width: 168px;
        height: 168px;
        border-radius: 50%;
        border: 5px solid #fff;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        background: #e4e6eb;
    }
    .avatar-edit-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: #e4e6eb;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background 0.2s;
    }
    .avatar-edit-btn:hover { background: #d8dadf; }

    .profile-info {
        flex: 1;
        padding-bottom: 8px;
    }
    .profile-name {
        font-size: 32px;
        font-weight: 700;
        color: #050505;
        margin: 0;
    }
    .profile-friends-count {
        color: #65676b;
        font-size: 15px;
        margin-top: 4px;
    }
    .profile-actions {
        display: flex;
        gap: 8px;
        margin-left: auto;
        padding-bottom: 8px;
    }
    .profile-btn {
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-primary-profile {
        background: #6366f1;
        color: #fff;
    }
    .btn-primary-profile:hover { background: #4f46e5; }
    .btn-secondary-profile {
        background: #e4e6eb;
        color: #050505;
    }
    .btn-secondary-profile:hover { background: #d8dadf; }
    .btn-danger-profile {
        background: #fee2e2;
        color: #dc2626;
    }
    .btn-danger-profile:hover { background: #fecaca; }
    .btn-success-profile {
        background: #d1fae5;
        color: #059669;
    }
    .btn-success-profile:hover { background: #a7f3d0; }

    /* Profile Content */
    .profile-content {
        max-width: 940px;
        margin: 16px auto;
        padding: 0 16px;
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 16px;
    }

    /* Sidebar Cards */
    .profile-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 16px;
    }
    .profile-card h3 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #050505;
    }
    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
        color: #333;
        font-size: 15px;
    }
    .info-item .material-icons-outlined {
        color: #65676b;
        font-size: 22px;
    }

    /* Friends - grid preview (style like common social feed) */
    .friends-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .friends-card-header h3 { margin: 0; }
    .friends-card-header-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .friends-total-text {
        color: #65676b;
        font-size: 15px;
    }
    .friends-see-all {
        color: #216fdb;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
    }
    .friends-see-all:hover { text-decoration: underline; }
    .friends-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    .friend-item {
        display: block;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
        transition: background 0.15s;
        background: #f0f2f5;
    }
    .friend-item:hover { background: #e4e6eb; }
    .friend-item-avatar {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        display: block;
        background: #e4e6eb;
    }
    .friend-item-name {
        padding: 8px 10px;
        font-size: 13px;
        font-weight: 600;
        color: #050505;
        line-height: 1.3;
        text-align: center;
    }

    /* Modal: all friends */
    .all-friends-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .all-friends-overlay.active { display: flex; }
    .all-friends-modal {
        background: #fff;
        border-radius: 12px;
        width: 560px;
        max-width: 95%;
        max-height: 85vh;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
    }
    .all-friends-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e4e6eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .all-friends-modal-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #050505;
    }
    .all-friends-modal-close {
        background: #e4e6eb;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #050505;
        transition: background 0.2s;
    }
    .all-friends-modal-close:hover { background: #d8dadf; }
    .all-friends-modal-body {
        padding: 16px;
        overflow-y: auto;
        flex: 1;
    }
    .all-friends-table {
        width: 100%;
        border-collapse: collapse;
    }
    .all-friends-table th {
        text-align: left;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #65676b;
        border-bottom: 1px solid #e4e6eb;
    }
    .all-friends-table td {
        padding: 12px;
        border-bottom: 1px solid #e4e6eb;
        vertical-align: middle;
    }
    .all-friends-table tr:hover td { background: #f0f2f5; }
    .all-friends-row-link {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
    }
    .all-friends-row-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        background: #e4e6eb;
    }
    .all-friends-row-name { font-weight: 600; font-size: 15px; color: #050505; }
    .all-friends-row-action {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        background: #e4e6eb;
        color: #050505;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .all-friends-row-action:hover { background: #d8dadf; }

    /* Edit Modal */
    .edit-modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .edit-modal-overlay.active { display: flex; }
    .edit-modal {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        width: 500px;
        max-width: 95%;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .edit-modal h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e4e6eb;
    }
    .form-group {
        margin-bottom: 14px;
    }
    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        display: block;
        margin-bottom: 4px;
    }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccd0d5;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        font-family: inherit;
        transition: border 0.2s;
        box-sizing: border-box;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.1);
    }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 16px;
    }

    /* Friend Requests */
    .friend-request-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        border-radius: 8px;
        transition: background 0.15s;
    }
    .friend-request-item:hover { background: #f0f2f5; }
    .friend-request-item img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
    .friend-request-info { flex: 1; }
    .friend-request-info p { font-weight: 600; font-size: 15px; }
    .friend-request-info small { color: #65676b; }
    .friend-request-actions { display: flex; gap: 6px; }
    .fr-btn { padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; }
    .fr-accept { background: #6366f1; color: #fff; }
    .fr-accept:hover { background: #4f46e5; }
    .fr-reject { background: #e4e6eb; color: #050505; }
    .fr-reject:hover { background: #d8dadf; }

    /* Post cards (same style as community) */
    .post-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 16px;
    }
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
    .post-user-name:hover { text-decoration: underline; }
    .post-time {
        font-size: 13px;
        color: #65676b;
    }
    .post-content {
        padding: 0 16px 12px;
        font-size: 15px;
        line-height: 1.5;
        color: #050505;
    }
    .see-more-btn {
        color: #65676b;
        font-weight: 600;
        cursor: pointer;
    }
    .see-more-btn:hover { text-decoration: underline; }
    .media-container {
        position: relative;
        background: #f0f2f5;
        border-radius: 8px;
        overflow: hidden;
    }
    .media-grid-1 { display: grid; grid-template-columns: 1fr; }
    .media-grid-1 .media-item { max-height: 500px; }
    .media-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2px; }
    .media-grid-2 .media-item { aspect-ratio: 1; }
    .media-grid-3 { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: auto auto; gap: 2px; }
    .media-grid-3 .media-item:first-child { grid-column: 1 / -1; aspect-ratio: 16/9; }
    .media-grid-3 .media-item:not(:first-child) { aspect-ratio: 4/3; }
    .media-grid-4 { display: grid; grid-template-columns: 1fr 1fr; gap: 2px; }
    .media-grid-4 .media-item { aspect-ratio: 1; }
    .media-grid-5plus { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: auto auto; gap: 2px; }
    .media-grid-5plus .media-item:first-child { grid-column: 1 / -1; aspect-ratio: 16/9; }
    .media-grid-5plus .media-item:not(:first-child) { aspect-ratio: 4/3; }
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
    .media-item:hover img, .media-item:hover video { transform: scale(1.02); }
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
    .video-play-btn {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 60px; height: 60px;
        background: rgba(0,0,0,0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
    }
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
        width: 18px; height: 18px;
        background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .like-icon svg { width: 12px; height: 12px; fill: #fff; }
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
    .action-btn:hover { background: #f0f2f5; }
    .action-btn.liked { color: #6366f1; }
    .action-btn.liked svg { fill: #6366f1; }
    .game-tag {
        padding: 6px 12px;
        margin: 0 16px 12px;
        background: #f0f2f5;
        border-radius: 8px;
        font-size: 13px;
        color: #65676b;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .comments-section {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        border-top: 1px solid #e4e6eb;
    }
    .comments-section.expanded { max-height: 2000px; overflow-y: auto; }
    .comments-wrapper { padding: 12px 16px; }
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
    .comment-input:focus { background: #e4e6eb; }
    .send-btn {
        background: #6366f1;
        color: #fff;
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        font-weight: 600;
        cursor: pointer;
    }
    .send-btn:hover { background: #4f46e5; }
    .comment-item { display: flex; gap: 8px; margin-bottom: 4px; }
    .comment-item img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .comment-body { flex: 1; min-width: 0; }
    .comment-bubble {
        background: #f0f2f5;
        border-radius: 18px;
        padding: 8px 12px;
        display: inline-block;
        max-width: 100%;
    }
    .comment-name { font-weight: 600; font-size: 13px; color: #050505; }
    .comment-text { font-size: 15px; color: #050505; word-wrap: break-word; }
    .comment-actions { display: flex; gap: 12px; padding: 2px 12px 4px; font-size: 12px; font-weight: 600; }
    .comment-action-btn { color: #65676b; cursor: pointer; }
    .comment-action-btn:hover { text-decoration: underline; }
    .comment-time { color: #65676b; font-weight: 400; }
    .lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.95);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .lightbox.active { display: flex; }
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

    @media (max-width: 768px) {
        .profile-content {
            grid-template-columns: 1fr;
        }
        .profile-header-inner {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .profile-avatar { width: 120px; height: 120px; }
        .profile-actions { margin-left: 0; }
        .profile-cover { height: 200px; }
        .profile-header { margin-top: -60px; }
    }
</style>
@endpush

@section('content')
<div id="profile-app">
    <!-- Cover Photo -->
    <div class="profile-cover" id="cover-section">
        <img id="cover-image" src="" style="display:none">
        <button class="cover-edit-btn" id="cover-edit-btn" style="display:none" onclick="document.getElementById('cover-input').click()">
            <span class="material-icons-outlined" style="font-size:18px">camera_alt</span> Chỉnh sửa ảnh bìa
        </button>
        <input type="file" id="cover-input" accept="image/*" style="display:none" onchange="uploadCover(this)">
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-header-inner">
            <div class="profile-avatar-wrapper">
                <img id="profile-avatar" class="profile-avatar" src="">
                <button class="avatar-edit-btn" id="avatar-edit-btn" style="display:none" onclick="document.getElementById('avatar-input').click()">
                    <span class="material-icons-outlined" style="font-size:20px">camera_alt</span>
                </button>
                <input type="file" id="avatar-input" accept="image/*" style="display:none" onchange="uploadAvatar(this)">
            </div>
            <div class="profile-info">
                <h1 class="profile-name" id="profile-name"></h1>
                <p class="profile-friends-count" id="profile-friends-count"></p>
            </div>
            <div class="profile-actions" id="profile-actions"></div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <!-- Left Sidebar -->
        <div class="profile-left">
            <!-- About -->
            <div class="profile-card" id="about-card">
                <h3>Giới thiệu</h3>
                <p id="profile-bio" style="text-align:center;color:#333;margin-bottom:12px;font-size:15px"></p>
                <div id="profile-details"></div>
            </div>

            <!-- Friends -->
            <div class="profile-card" id="friends-card">
                <div class="friends-card-header">
                    <h3>Bạn bè</h3>
                    <div class="friends-card-header-right">
                        <span id="friends-total" class="friends-total-text"></span>
                        <button type="button" class="friends-see-all" id="friends-see-all-btn" style="display:none">Xem tất cả</button>
                    </div>
                </div>
                <div class="friends-grid" id="friends-grid"></div>
            </div>

            <!-- Friend Requests (only for self) -->
            <div class="profile-card" id="friend-requests-card" style="display:none">
                <h3>Lời mời kết bạn</h3>
                <div id="friend-requests-list"></div>
            </div>
        </div>

        <!-- Right Content (Posts) -->
        <div class="profile-right">
            <div class="profile-card">
                <h3>Bài viết</h3>
                <div id="profile-posts"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="edit-modal-overlay" id="edit-modal">
    <div class="edit-modal">
        <h2>Chỉnh sửa trang cá nhân</h2>
        <div class="form-group">
            <label>Tên</label>
            <input type="text" id="edit-name">
        </div>
        <div class="form-group">
            <label>Tiểu sử</label>
            <textarea id="edit-bio" rows="3" placeholder="Mô tả bản thân..."></textarea>
        </div>
        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" id="edit-phone">
        </div>
        <div class="form-group">
            <label>Địa chỉ</label>
            <input type="text" id="edit-address">
        </div>
        <div class="form-group">
            <label>Ngày sinh</label>
            <input type="date" id="edit-birthday">
        </div>
        <div class="form-group">
            <label>Giới tính</label>
            <select id="edit-gender">
                <option value="">Chọn</option>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="other">Khác</option>
            </select>
        </div>
        <div class="modal-actions">
            <button class="profile-btn btn-secondary-profile" onclick="closeEditModal()">Hủy</button>
            <button class="profile-btn btn-primary-profile" onclick="saveProfile()">Lưu thay đổi</button>
        </div>
    </div>
</div>

<!-- Modal: Xem tất cả bạn bè -->
<div class="all-friends-overlay" id="all-friends-modal">
    <div class="all-friends-modal">
        <div class="all-friends-modal-header">
            <h2>Bạn bè</h2>
            <button type="button" class="all-friends-modal-close" id="all-friends-close" aria-label="Đóng">&times;</button>
        </div>
        <div class="all-friends-modal-body">
            <table class="all-friends-table">
                <thead>
                    <tr>
                        <th>Người dùng</th>
                        <th style="text-align:right">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="all-friends-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Lightbox for post images -->
<div class="lightbox" id="lightbox" onclick="if(event.target===this)closeLightbox()">
    <img class="lightbox-content" id="lightbox-img" src="" alt="">
    <button type="button" class="lightbox-close" onclick="closeLightbox()" aria-label="Đóng">&times;</button>
</div>

@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("/") }}';
    const API_URL = BASE_URL + '/api';
    const COMMUNITY_API = BASE_URL + '/api/community';
    let currentUser = null;
    let profileUser = null;
    let profileData = null;

    // Get profile ID from URL
    function getProfileId() {
        const path = window.location.pathname;
        const parts = path.split('/');
        const id = parts[parts.length - 1];
        return id && !isNaN(id) ? parseInt(id) : null;
    }

    // Initialize
    async function init() {
        const token = localStorage.getItem('auth_token');
        if (token) {
            try {
                const res = await fetch(API_URL + '/user', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.id) currentUser = data;
            } catch(e) {}
        }

        let profileId = getProfileId();
        if (!profileId && currentUser) profileId = currentUser.id;
        if (!profileId) {
            window.location.href = BASE_URL + '/login';
            return;
        }

        await loadProfile(profileId);
    }

    // Load profile
    async function loadProfile(userId) {
        try {
            const token = localStorage.getItem('auth_token');
            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await fetch(`${API_URL}/profile/${userId}`, { headers });
            const result = await res.json();

            if (!result.success) {
                document.getElementById('profile-app').innerHTML = '<div style="text-align:center;padding:100px 20px"><h2>Không tìm thấy người dùng</h2></div>';
                return;
            }

            profileData = result.data;
            profileUser = result.data.user;
            renderProfile();
        } catch (e) {
            console.error(e);
        }
    }

    // Render profile
    function renderProfile() {
        const d = profileData;
        const u = d.user;
        const isSelf = d.friend_status === 'self';

        // Cover
        if (u.cover_image) {
            const coverSrc = u.cover_image.startsWith('/storage') ? BASE_URL + u.cover_image : u.cover_image;
            document.getElementById('cover-image').src = coverSrc;
            document.getElementById('cover-image').style.display = 'block';
        }
        if (isSelf) document.getElementById('cover-edit-btn').style.display = 'flex';

        // Avatar
        let av = u.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=6366f1&color=fff&size=168`;
        if (av && av.startsWith('/storage')) av = BASE_URL + av;
        document.getElementById('profile-avatar').src = av;
        if (isSelf) document.getElementById('avatar-edit-btn').style.display = 'flex';

        // Name & friends count
        document.getElementById('profile-name').textContent = u.name;
        document.getElementById('profile-friends-count').textContent = `${d.friends_count} bạn bè`;

        // Actions
        const actionsEl = document.getElementById('profile-actions');
        if (isSelf) {
            actionsEl.innerHTML = `
                <button class="profile-btn btn-primary-profile" onclick="openEditModal()">
                    <span class="material-icons-outlined" style="font-size:18px">edit</span> Chỉnh sửa
                </button>
            `;
        } else if (d.friend_status === 'friends') {
            actionsEl.innerHTML = `
                <button class="profile-btn btn-primary-profile" id="btn-open-chat">
                    <span class="material-icons-outlined" style="font-size:18px">chat</span> Nhắn tin
                </button>
                <button class="profile-btn btn-danger-profile" onclick="removeFriend(${d.friendship_id})">
                    <span class="material-icons-outlined" style="font-size:18px">person_remove</span> Hủy kết bạn
                </button>
            `;
            document.getElementById('btn-open-chat').addEventListener('click', function() {
                if (typeof gOpenChat === 'function') {
                    gOpenChat(u.id, u.name, u.avatar || '');
                }
            });
        } else if (d.friend_status === 'sent') {
            actionsEl.innerHTML = `
                <button class="profile-btn btn-secondary-profile" onclick="removeFriend(${d.friendship_id})">
                    <span class="material-icons-outlined" style="font-size:18px">schedule</span> Đã gửi lời mời
                </button>
            `;
        } else if (d.friend_status === 'received') {
            actionsEl.innerHTML = `
                <button class="profile-btn btn-success-profile" onclick="acceptFriend(${d.friendship_id})">
                    <span class="material-icons-outlined" style="font-size:18px">check</span> Chấp nhận
                </button>
                <button class="profile-btn btn-secondary-profile" onclick="removeFriend(${d.friendship_id})">Từ chối</button>
            `;
        } else {
            actionsEl.innerHTML = `
                <button class="profile-btn btn-primary-profile" onclick="sendFriendRequest(${u.id})">
                    <span class="material-icons-outlined" style="font-size:18px">person_add</span> Kết bạn
                </button>
            `;
        }

        // Bio
        const bioEl = document.getElementById('profile-bio');
        bioEl.textContent = u.bio || (isSelf ? 'Thêm tiểu sử...' : '');
        if (!u.bio && isSelf) bioEl.style.cursor = 'pointer';

        // Details
        const detailsEl = document.getElementById('profile-details');
        let detailsHTML = '';
        if (u.address) detailsHTML += `<div class="info-item"><span class="material-icons-outlined">home</span> Sống tại <b>${escapeHtml(u.address)}</b></div>`;
        if (u.phone) detailsHTML += `<div class="info-item"><span class="material-icons-outlined">phone</span> ${escapeHtml(u.phone)}</div>`;
        if (u.birthday) detailsHTML += `<div class="info-item"><span class="material-icons-outlined">cake</span> ${new Date(u.birthday).toLocaleDateString('vi-VN')}</div>`;
        if (u.gender) {
            const genderMap = { male: 'Nam', female: 'Nữ', other: 'Khác' };
            detailsHTML += `<div class="info-item"><span class="material-icons-outlined">person</span> ${genderMap[u.gender] || u.gender}</div>`;
        }
        detailsHTML += `<div class="info-item"><span class="material-icons-outlined">calendar_today</span> Tham gia ${new Date(u.created_at).toLocaleDateString('vi-VN')}</div>`;
        detailsEl.innerHTML = detailsHTML;

        // Friends (preview grid + "Xem tất cả" -> modal)
        const friendsTotalEl = document.getElementById('friends-total');
        const friendsSeeAllBtn = document.getElementById('friends-see-all-btn');
        friendsTotalEl.textContent = `${d.friends_count} người bạn`;
        const friendsGrid = document.getElementById('friends-grid');
        if (d.friends.length > 0) {
            const previewCount = 6;
            const previewFriends = d.friends.slice(0, previewCount);
            friendsGrid.innerHTML = previewFriends.map(f => {
                let fav = f.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=6366f1&color=fff&size=100`;
                if (fav && fav.startsWith('/storage')) fav = BASE_URL + fav;
                return `<a class="friend-item" href="${BASE_URL}/profile/${f.id}"><img class="friend-item-avatar" src="${fav}" alt=""><span class="friend-item-name">${escapeHtml(f.name)}</span></a>`;
            }).join('');
            friendsSeeAllBtn.style.display = 'inline';
            friendsSeeAllBtn.onclick = openAllFriendsModal;
        } else {
            friendsGrid.innerHTML = '<p style="color:#65676b;font-size:14px;grid-column:1/-1">Chưa có bạn bè</p>';
            friendsSeeAllBtn.style.display = 'none';
        }

        // Friend requests (self only)
        if (isSelf) {
            document.getElementById('friend-requests-card').style.display = 'block';
            loadFriendRequests();
        }

        // Posts
        renderPosts(d.posts);
    }

    // Render posts (same style as community)
    function renderPosts(posts) {
        const el = document.getElementById('profile-posts');
        if (!posts || posts.length === 0) {
            el.innerHTML = '<p style="text-align:center;color:#65676b;padding:20px">Chưa có bài viết nào</p>';
            return;
        }

        el.innerHTML = posts.map(p => buildPostCard(p)).join('');
    }

    function buildPostCard(p) {
        const userName = p.user_name || '';
        const userAvatar = p.user_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=6366f1&color=fff&size=64`;
        const avatarSrc = userAvatar.startsWith('/storage') ? BASE_URL + userAvatar : userAvatar;

        const images = typeof p.images === 'string' ? (p.images ? JSON.parse(p.images) : []) : (p.images || []);
        const videos = typeof p.videos === 'string' ? (p.videos ? JSON.parse(p.videos) : []) : (p.videos || []);
        const allMedia = [...images, ...videos];

        let mediaHtml = '';
        if (allMedia.length > 0) {
            const gridClass = allMedia.length === 1 ? 'media-grid-1' : allMedia.length === 2 ? 'media-grid-2' : allMedia.length === 3 ? 'media-grid-3' : allMedia.length === 4 ? 'media-grid-4' : 'media-grid-5plus';
            mediaHtml = `<div class="media-container ${gridClass}">`;
            const displayMedia = allMedia.slice(0, 5);
            const extraCount = allMedia.length - 5;
            displayMedia.forEach((url, idx) => {
                const isVideo = url.match(/\.(mp4|webm|ogg|mov)$/i) || (typeof url === 'string' && url.includes('video'));
                const isLast = idx === 4 && extraCount > 0;
                mediaHtml += `<div class="media-item" onclick="${isVideo ? `playVideoProfile(event, '${url}')` : `openLightbox(${p.id}, ${idx})`}">`;
                if (isVideo) {
                    mediaHtml += `<video src="${url}" preload="metadata"></video><div class="video-play-btn">▶</div>`;
                } else {
                    mediaHtml += `<img src="${url}" alt="Media" loading="lazy">`;
                }
                if (isLast) mediaHtml += `<div class="media-overlay">+${extraCount}</div>`;
                mediaHtml += '</div>';
            });
            mediaHtml += '</div>';
        }

        const gameTagHtml = p.game_preference ? `<div class="game-tag"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="width:16px;height:16px"><path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg> Tìm đồng đội: ${escapeHtml(p.game_preference)}</div>` : '';

        const content = p.content || '';
        const maxLen = 200;
        const needsTruncate = content.length > maxLen;
        const displayContent = needsTruncate ? content.substring(0, maxLen) : content;
        const seeMoreHtml = needsTruncate ? `<span class="see-more-btn" onclick="togglePostContent(${p.id})">... Xem thêm</span>` : '';

        const isLiked = !!p.is_liked;
        const mediaJson = JSON.stringify(images);

        return `
        <article class="post-card" id="post-${p.id}" data-media="${escapeHtml(mediaJson)}">
            <div class="post-header">
                <img src="${avatarSrc}" alt="${escapeHtml(userName)}" class="post-avatar" style="cursor:pointer" onclick="window.location.href='${BASE_URL}/profile/${p.user_id}'">
                <div style="flex:1">
                    <p class="post-user-name" style="cursor:pointer" onclick="window.location.href='${BASE_URL}/profile/${p.user_id}'">${escapeHtml(userName)}</p>
                    <p class="post-time">${formatTimeAgo(p.created_at)}</p>
                </div>
            </div>
            <div class="post-content" id="content-${p.id}" data-full="${escapeHtml(content)}" data-truncated="true">
                <span id="text-${p.id}">${escapeHtml(displayContent)}</span>${seeMoreHtml}
            </div>
            ${gameTagHtml}
            ${mediaHtml}
            <div class="post-stats">
                <div class="like-count">
                    <div class="like-icon"><svg viewBox="0 0 16 16"><path d="M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748z"/></svg></div>
                    <span id="likes-count-${p.id}">${p.likes_count || 0}</span>
                </div>
                <span class="comment-action-btn" id="comments-count-${p.id}" onclick="toggleCommentsProfile(${p.id})" style="cursor:pointer">${p.comments_count || 0} bình luận</span>
            </div>
            <div class="post-actions">
                <div class="action-btn ${isLiked ? 'liked' : ''}" id="like-btn-${p.id}" onclick="toggleLikeProfile(${p.id})">
                    <svg class="w-5 h-5" fill="${isLiked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24" style="width:20px;height:20px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Thích
                </div>
                <div class="action-btn" onclick="toggleCommentsProfile(${p.id})">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:20px;height:20px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Bình luận
                </div>
            </div>
            <div class="comments-section" id="comments-section-${p.id}">
                <div class="comments-wrapper">
                    <div class="comment-input-wrapper">
                        <img src="${currentUser ? (currentUser.avatar && currentUser.avatar.startsWith('/storage') ? BASE_URL + currentUser.avatar : currentUser.avatar) || 'https://ui-avatars.com/api/?name=U&background=6366f1&color=fff' : 'https://ui-avatars.com/api/?name=G&background=ccc&color=fff'}" style="width:32px;height:32px;border-radius:50%;object-fit:cover">
                        <input type="text" class="comment-input" id="comment-input-${p.id}" placeholder="Viết bình luận..." onkeypress="if(event.key==='Enter')submitCommentProfile(${p.id})">
                        <button class="send-btn" onclick="submitCommentProfile(${p.id})">Gửi</button>
                    </div>
                    <div id="comments-list-${p.id}"></div>
                </div>
            </div>
        </article>`;
    }

    function togglePostContent(postId) {
        const contentEl = document.getElementById('content-' + postId);
        const textEl = document.getElementById('text-' + postId);
        if (!contentEl || !textEl) return;
        const fullContent = contentEl.dataset.full;
        if (contentEl.dataset.truncated === 'true') {
            textEl.textContent = fullContent;
            const next = textEl.nextElementSibling;
            if (next && next.classList.contains('see-more-btn')) next.remove();
            contentEl.dataset.truncated = 'false';
        }
    }

    let lightboxImagesProfile = [];
    let lightboxIndexProfile = 0;
    function openLightbox(postId, index) {
        const card = document.getElementById('post-' + postId);
        if (!card) return;
        try {
            lightboxImagesProfile = JSON.parse(card.dataset.media || '[]');
        } catch (e) { lightboxImagesProfile = []; }
        lightboxIndexProfile = index;
        if (lightboxImagesProfile.length > 0) {
            const img = lightboxImagesProfile[lightboxIndexProfile];
            if (img) {
                document.getElementById('lightbox-img').src = img;
                document.getElementById('lightbox').classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
    }

    function playVideoProfile(ev, url) {
        const el = ev.currentTarget;
        const video = el.querySelector('video');
        const btn = el.querySelector('.video-play-btn');
        if (!video || !btn) return;
        if (video.paused) {
            video.play();
            btn.style.display = 'none';
        } else {
            video.pause();
            btn.style.display = 'flex';
        }
    }

    async function toggleLikeProfile(postId) {
        if (!currentUser) { window.location.href = BASE_URL + '/login'; return; }
        const btn = document.getElementById('like-btn-' + postId);
        const countEl = document.getElementById('likes-count-' + postId);
        if (!btn || !countEl) return;
        try {
            const token = localStorage.getItem('auth_token');
            const res = await fetch(COMMUNITY_API + '/posts/' + postId + '/like', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const result = await res.json();
            if (result.success) {
                btn.classList.toggle('liked', result.liked);
                btn.querySelector('svg').setAttribute('fill', result.liked ? 'currentColor' : 'none');
                countEl.textContent = result.likes_count;
            }
        } catch (e) { console.error(e); }
    }

    function toggleCommentsProfile(postId) {
        const section = document.getElementById('comments-section-' + postId);
        if (!section) return;
        if (section.classList.contains('expanded')) {
            section.classList.remove('expanded');
        } else {
            section.classList.add('expanded');
            loadCommentsProfile(postId);
        }
    }

    async function loadCommentsProfile(postId) {
        const listEl = document.getElementById('comments-list-' + postId);
        if (!listEl) return;
        try {
            const token = localStorage.getItem('auth_token');
            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = 'Bearer ' + token;
            const res = await fetch(COMMUNITY_API + '/posts/' + postId + '/comments', { headers });
            const result = await res.json();
            if (result.success && result.data && result.data.length > 0) {
                listEl.innerHTML = result.data.map(c => renderCommentProfile(c)).join('');
            } else {
                listEl.innerHTML = '<p style="text-align:center;color:#65676b;font-size:14px;padding:8px">Chưa có bình luận</p>';
            }
        } catch (e) {
            listEl.innerHTML = '<p style="text-align:center;color:#dc2626;font-size:14px;padding:8px">Lỗi tải bình luận</p>';
        }
    }

    function renderCommentProfile(c) {
        const avatar = c.user_avatar && c.user_avatar.startsWith('/storage') ? BASE_URL + c.user_avatar : (c.user_avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(c.user_name || '') + '&background=6366f1&color=fff');
        return `<div class="comment-item">
            <img src="${avatar}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;cursor:pointer" onclick="window.location.href='${BASE_URL}/profile/${c.user_id}'">
            <div class="comment-body">
                <div class="comment-bubble">
                    <p class="comment-name" style="cursor:pointer" onclick="window.location.href='${BASE_URL}/profile/${c.user_id}'">${escapeHtml(c.user_name || '')}</p>
                    <p class="comment-text">${escapeHtml(c.content || '')}</p>
                </div>
                <div class="comment-actions"><span class="comment-time">${formatTimeAgo(c.created_at)}</span></div>
            </div>
        </div>`;
    }

    async function submitCommentProfile(postId) {
        if (!currentUser) { window.location.href = BASE_URL + '/login'; return; }
        const input = document.getElementById('comment-input-' + postId);
        if (!input) return;
        const content = input.value.trim();
        if (!content) return;
        try {
            const token = localStorage.getItem('auth_token');
            const res = await fetch(COMMUNITY_API + '/posts/' + postId + '/comments', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                body: JSON.stringify({ content: content })
            });
            const result = await res.json();
            if (result.success) {
                input.value = '';
                const countEl = document.getElementById('comments-count-' + postId);
                if (countEl) countEl.textContent = result.comments_count + ' bình luận';
                loadCommentsProfile(postId);
            }
        } catch (e) { console.error(e); }
    }

    // === PROFILE EDITING ===
    function openEditModal() {
        const u = profileUser;
        document.getElementById('edit-name').value = u.name || '';
        document.getElementById('edit-bio').value = u.bio || '';
        document.getElementById('edit-phone').value = u.phone || '';
        document.getElementById('edit-address').value = u.address || '';
        document.getElementById('edit-birthday').value = u.birthday ? u.birthday.split('T')[0] : '';
        document.getElementById('edit-gender').value = u.gender || '';
        document.getElementById('edit-modal').classList.add('active');
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.remove('active');
    }

    async function saveProfile() {
        const token = localStorage.getItem('auth_token');
        try {
            const res = await fetch(`${API_URL}/profile/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
                body: JSON.stringify({
                    name: document.getElementById('edit-name').value,
                    bio: document.getElementById('edit-bio').value,
                    phone: document.getElementById('edit-phone').value,
                    address: document.getElementById('edit-address').value,
                    birthday: document.getElementById('edit-birthday').value || null,
                    gender: document.getElementById('edit-gender').value || null,
                })
            });
            const result = await res.json();
            if (result.success) {
                closeEditModal();
                loadProfile(profileUser.id);
            }
        } catch(e) { console.error(e); }
    }

    async function uploadAvatar(input) {
        if (!input.files[0]) return;
        const file = input.files[0];
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ảnh đại diện không được vượt quá 5MB');
            input.value = '';
            return;
        }
        const form = new FormData();
        form.append('avatar', file);
        const token = localStorage.getItem('auth_token');
        const btn = document.getElementById('avatar-edit-btn');
        if (btn) btn.style.opacity = '0.5';
        try {
            const res = await fetch(`${API_URL}/profile/avatar`, {
                method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, body: form
            });
            const r = await res.json();
            if (r.success) {
                loadProfile(profileUser.id);
            } else {
                alert('Không thể cập nhật ảnh đại diện: ' + (r.message || 'Lỗi không xác định'));
            }
        } catch(e) {
            console.error(e);
            alert('Đã xảy ra lỗi khi tải ảnh đại diện. Vui lòng thử lại.');
        } finally {
            if (btn) btn.style.opacity = '1';
            input.value = '';
        }
    }

    async function uploadCover(input) {
        if (!input.files[0]) return;
        const file = input.files[0];
        // Validate file size (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('Ảnh bìa không được vượt quá 10MB');
            input.value = '';
            return;
        }
        const form = new FormData();
        form.append('cover', file);
        const token = localStorage.getItem('auth_token');
        const btn = document.getElementById('cover-edit-btn');
        const originalText = btn ? btn.innerHTML : '';
        if (btn) btn.innerHTML = '<span class="material-icons-outlined" style="font-size:18px">hourglass_empty</span> Đang tải...';
        try {
            const res = await fetch(`${API_URL}/profile/cover`, {
                method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, body: form
            });
            const r = await res.json();
            if (r.success) {
                loadProfile(profileUser.id);
            } else {
                alert('Không thể cập nhật ảnh bìa: ' + (r.message || 'Lỗi không xác định'));
            }
        } catch(e) {
            console.error(e);
            alert('Đã xảy ra lỗi khi tải ảnh bìa. Vui lòng thử lại.');
        } finally {
            if (btn) btn.innerHTML = originalText;
            input.value = '';
        }
    }

    // === FRIEND SYSTEM ===
    async function sendFriendRequest(userId) {
        const token = localStorage.getItem('auth_token');
        if (!token) { window.location.href = BASE_URL + '/login'; return; }
        try {
            const res = await fetch(`${API_URL}/friends/request/${userId}`, {
                method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const r = await res.json();
            if (r.success) loadProfile(profileUser.id);
        } catch(e) { console.error(e); }
    }

    async function acceptFriend(friendshipId) {
        const token = localStorage.getItem('auth_token');
        try {
            const res = await fetch(`${API_URL}/friends/accept/${friendshipId}`, {
                method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const r = await res.json();
            if (r.success) loadProfile(profileUser.id);
        } catch(e) { console.error(e); }
    }

    async function removeFriend(friendshipId) {
        if (!confirm('Bạn có chắc chắn?')) return;
        const token = localStorage.getItem('auth_token');
        try {
            const res = await fetch(`${API_URL}/friends/remove/${friendshipId}`, {
                method: 'DELETE', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const r = await res.json();
            if (r.success) loadProfile(profileUser.id);
        } catch(e) { console.error(e); }
    }

    async function loadFriendRequests() {
        const token = localStorage.getItem('auth_token');
        try {
            const res = await fetch(`${API_URL}/friends/requests`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const r = await res.json();
            const el = document.getElementById('friend-requests-list');
            if (r.success && r.data.length > 0) {
                el.innerHTML = r.data.map(f => {
                    let av = f.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=6366f1&color=fff`;
                    if (av && av.startsWith('/storage')) av = BASE_URL + av;
                    return `
                        <div class="friend-request-item">
                            <img src="${av}" onclick="window.location.href='${BASE_URL}/profile/${f.id}'" style="cursor:pointer">
                            <div class="friend-request-info">
                                <p>${escapeHtml(f.name)}</p>
                                <small>${formatTimeAgo(f.created_at)}</small>
                            </div>
                            <div class="friend-request-actions">
                                <button class="fr-btn fr-accept" onclick="acceptFriend(${f.friendship_id})">Chấp nhận</button>
                                <button class="fr-btn fr-reject" onclick="removeFriend(${f.friendship_id})">Từ chối</button>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                el.innerHTML = '<p style="color:#65676b;font-size:14px;text-align:center">Không có lời mời</p>';
            }
        } catch(e) { console.error(e); }
    }



    // === UTILITIES ===
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTimeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const seconds = Math.floor((now - date) / 1000);
        if (seconds < 60) return 'Vừa xong';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' phút trước';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' giờ trước';
        if (seconds < 604800) return Math.floor(seconds / 86400) + ' ngày trước';
        return date.toLocaleDateString('vi-VN');
    }

    // Modal: Xem tất cả bạn bè
    function openAllFriendsModal() {
        if (!profileData || !profileData.friends || profileData.friends.length === 0) return;
        const tbody = document.getElementById('all-friends-tbody');
        tbody.innerHTML = profileData.friends.map(f => {
            let av = f.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(f.name)}&background=6366f1&color=fff&size=48`;
            if (av && av.startsWith('/storage')) av = BASE_URL + av;
            const profileUrl = BASE_URL + '/profile/' + f.id;
            return `<tr>
                <td><a class="all-friends-row-link" href="${profileUrl}"><img class="all-friends-row-avatar" src="${av}" alt=""><span class="all-friends-row-name">${escapeHtml(f.name)}</span></a></td>
                <td style="text-align:right"><a class="all-friends-row-action" href="${profileUrl}">Xem trang</a></td>
            </tr>`;
        }).join('');
        document.getElementById('all-friends-modal').classList.add('active');
    }
    function closeAllFriendsModal() {
        document.getElementById('all-friends-modal').classList.remove('active');
    }
    document.getElementById('all-friends-close').addEventListener('click', closeAllFriendsModal);
    document.getElementById('all-friends-modal').addEventListener('click', function(e) {
        if (e.target === this) closeAllFriendsModal();
    });

    // Close modal on overlay click
    document.getElementById('edit-modal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    // Start
    init();
</script>
@endpush
