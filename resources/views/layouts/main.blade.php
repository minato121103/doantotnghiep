<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'eStrix') - Game Store</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Round" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'game-dark': '#f8fafc',
                        'game-darker': '#f1f5f9',
                        'game-card': '#ffffff',
                        'game-border': '#e2e8f0',
                        'game-accent': '#6366f1',
                        'game-accent-hover': '#4f46e5',
                        'game-purple': '#8b5cf6',
                        'game-pink': '#ec4899',
                        'game-green': '#10b981',
                        'game-orange': '#f97316',
                    },
                    fontFamily: {
                        'display': ['Be Vietnam Pro', 'sans-serif'],
                        'heading': ['Be Vietnam Pro', 'sans-serif'],
                        'body': ['Be Vietnam Pro', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        .gradient-text {
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glow-effect {
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.25);
        }
        
        .glow-effect:hover {
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.35);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 4px 20px rgba(99, 102, 241, 0.25); }
            50% { box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4); }
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-game-dark text-slate-800 min-h-screen">
    <!-- Header -->
    @include('main.partials.header')
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('main.partials.footer')

    <!-- Global Chat System - Messenger Style -->
    <style>
        /* ===== Messenger Chat Style ===== */
        :root {
            --msg-blue: #0068FF;
            --msg-blue-hover: #0054cc;
            --msg-blue-light: #E5F0FF;
            --msg-bg: #f0f2f5;
            --msg-sidebar-bg: #ffffff;
            --msg-chat-bg: #e8ecf1;
            --msg-text: #081c36;
            --msg-text-secondary: #7589a3;
            --msg-border: #dfe2e7;
            --msg-sent-bg: #E5EFFF;
            --msg-received-bg: #ffffff;
            --msg-online: #2ecc71;
        }

        /* Float Button */
        .msg-float {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--msg-blue);
            color: #fff;
            border: none;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0, 104, 255, 0.4);
            z-index: 998;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .msg-float:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 24px rgba(0, 104, 255, 0.5);
        }
        .msg-float .material-icons-round { font-size: 26px; }
        .msg-float-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #ff3b30;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        /* Main Messenger Panel */
        .msg-panel {
            position: fixed;
            bottom: 96px;
            right: 28px;
            width: 720px;
            height: 520px;
            background: var(--msg-sidebar-bg);
            border-radius: 16px;
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.18), 0 2px 8px rgba(0, 0, 0, 0.06);
            display: none;
            flex-direction: row;
            z-index: 999;
            overflow: hidden;
            animation: msgSlideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .msg-panel.active { display: flex; }

        @keyframes msgSlideUp {
            from { opacity: 0; transform: translateY(16px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Sidebar (Conversation List) */
        .msg-sidebar {
            width: 280px;
            min-width: 280px;
            border-right: 1px solid var(--msg-border);
            display: flex;
            flex-direction: column;
            background: var(--msg-sidebar-bg);
        }

        .msg-sidebar-header {
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--msg-border);
        }
        .msg-sidebar-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--msg-text);
            margin: 0;
            flex: 1;
        }
        .msg-sidebar-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--msg-text-secondary);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }
        .msg-sidebar-close:hover {
            background: var(--msg-bg);
            color: var(--msg-text);
        }

        .msg-search {
            padding: 8px 16px 12px;
        }
        .msg-search-input {
            width: 100%;
            padding: 9px 12px 9px 36px;
            border: none;
            background: var(--msg-bg);
            border-radius: 8px;
            font-size: 13px;
            color: var(--msg-text);
            outline: none;
            font-family: inherit;
            transition: background 0.2s;
        }
        .msg-search-input:focus {
            background: #e4e6eb;
        }
        .msg-search-icon {
            position: absolute;
            left: 28px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--msg-text-secondary);
            font-size: 18px;
            pointer-events: none;
        }

        .msg-conv-list {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c4c9d0 transparent;
        }
        .msg-conv-list::-webkit-scrollbar { width: 6px; }
        .msg-conv-list::-webkit-scrollbar-track { background: transparent; }
        .msg-conv-list::-webkit-scrollbar-thumb { background: #c4c9d0; border-radius: 3px; }

        .msg-conv-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            cursor: pointer;
            transition: background 0.15s;
            border-left: 3px solid transparent;
        }
        .msg-conv-item:hover { background: var(--msg-blue-light); }
        .msg-conv-item.active {
            background: var(--msg-blue-light);
            border-left-color: var(--msg-blue);
        }
        .msg-conv-avatar {
            position: relative;
            flex-shrink: 0;
        }
        .msg-conv-avatar img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--msg-bg);
        }
        .msg-conv-avatar .online-dot {
            position: absolute;
            bottom: 1px;
            right: 1px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--msg-online);
            border: 2px solid #fff;
        }
        .msg-conv-info {
            flex: 1;
            min-width: 0;
        }
        .msg-conv-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .msg-conv-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--msg-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .msg-conv-time {
            font-size: 11px;
            color: var(--msg-text-secondary);
            flex-shrink: 0;
        }
        .msg-conv-last {
            font-size: 13px;
            color: var(--msg-text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }
        .msg-conv-item.unread .msg-conv-name { color: var(--msg-text); }
        .msg-conv-item.unread .msg-conv-last {
            color: var(--msg-text);
            font-weight: 600;
        }
        .msg-conv-unread-badge {
            background: var(--msg-blue);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Chat Area */
        .msg-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--msg-chat-bg);
        }
        .msg-chat-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: var(--msg-text-secondary);
        }
        .msg-chat-empty .material-icons-round {
            font-size: 64px;
            color: #c4c9d0;
        }
        .msg-chat-empty p {
            font-size: 14px;
            margin: 0;
        }

        .msg-chat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #fff;
            border-bottom: 1px solid var(--msg-border);
        }
        .msg-chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            transition: opacity 0.15s;
        }
        .msg-chat-header img:hover { opacity: 0.85; }
        .msg-chat-header-info {
            flex: 1;
            min-width: 0;
        }
        .msg-chat-header-name {
            font-weight: 700;
            font-size: 15px;
            color: var(--msg-text);
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .msg-chat-header-name:hover { color: var(--msg-blue); }
        .msg-chat-header-status {
            font-size: 12px;
            color: var(--msg-online);
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .msg-chat-header-status::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--msg-online);
        }

        .msg-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 3px;
            scrollbar-width: thin;
            scrollbar-color: #b8bfc7 transparent;
        }
        .msg-messages::-webkit-scrollbar { width: 6px; }
        .msg-messages::-webkit-scrollbar-track { background: transparent; }
        .msg-messages::-webkit-scrollbar-thumb { background: #b8bfc7; border-radius: 3px; }

        .msg-msg-group {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-bottom: 4px;
        }
        .msg-msg-group.sent { align-items: flex-end; }
        .msg-msg-group.received { align-items: flex-start; }

        .msg-msg {
            max-width: 70%;
            padding: 10px 14px;
            font-size: 14px;
            line-height: 1.45;
            word-wrap: break-word;
            position: relative;
        }
        .msg-msg.sent {
            background: var(--msg-sent-bg);
            color: var(--msg-text);
            border-radius: 18px 18px 4px 18px;
        }
        .msg-msg.received {
            background: var(--msg-received-bg);
            color: var(--msg-text);
            border-radius: 18px 18px 18px 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.06);
        }
        
        /* System/Support Messages */
        .msg-msg.system-msg {
            max-width: 85%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 16px;
            padding: 16px 18px;
            line-height: 1.6;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .msg-msg.system-msg.sent {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .msg-msg-time {
            font-size: 11px;
            color: var(--msg-text-secondary);
            margin-top: 3px;
            padding: 0 4px;
        }
        .msg-msg-time.sent { text-align: right; }

        .msg-date-divider {
            text-align: center;
            padding: 12px 0;
        }
        .msg-date-divider span {
            font-size: 12px;
            color: var(--msg-text-secondary);
            background: var(--msg-chat-bg);
            padding: 4px 14px;
            border-radius: 12px;
            font-weight: 500;
        }

        /* Input Area */
        .msg-input-area {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            padding: 12px 16px;
            background: #fff;
            border-top: 1px solid var(--msg-border);
        }
        .msg-input-wrapper {
            flex: 1;
            position: relative;
        }
        .msg-input {
            width: 100%;
            border: none;
            background: var(--msg-bg);
            border-radius: 20px;
            padding: 10px 16px;
            font-size: 14px;
            color: var(--msg-text);
            outline: none;
            font-family: inherit;
            resize: none;
            max-height: 100px;
            line-height: 1.4;
            transition: background 0.2s;
        }
        .msg-input:focus { background: #e4e6eb; }
        .msg-input::placeholder { color: var(--msg-text-secondary); }

        .msg-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--msg-blue);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .msg-send-btn:hover {
            background: var(--msg-blue-hover);
            transform: scale(1.05);
        }
        .msg-send-btn .material-icons-round { font-size: 20px; }

        /* Empty conversation state */
        .msg-conv-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--msg-text-secondary);
        }
        .msg-conv-empty .material-icons-round {
            font-size: 48px;
            color: #d0d5dc;
            margin-bottom: 8px;
        }
        .msg-conv-empty p {
            font-size: 13px;
            margin: 4px 0 0;
        }

        /* Responsive - small screens: show only sidebar or chat */
        @media (max-width: 768px) {
            .msg-panel {
                width: calc(100vw - 24px);
                height: calc(100vh - 140px);
                bottom: 92px;
                right: 12px;
                border-radius: 12px;
            }
            .msg-sidebar { width: 100%; min-width: 100%; }
            .msg-panel.chat-open .msg-sidebar { display: none; }
            .msg-panel:not(.chat-open) .msg-chat { display: none; }
            .msg-chat-back { display: flex !important; }
        }

        @media (min-width: 769px) {
            .msg-chat-back { display: none !important; }
        }

        .msg-chat-back {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--msg-text-secondary);
            padding: 6px;
            border-radius: 8px;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }
        .msg-chat-back:hover {
            background: var(--msg-bg);
            color: var(--msg-text);
        }

        /* Typing indicator animation */
        .msg-typing { display: flex; gap: 4px; padding: 10px 14px; }
        .msg-typing span {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #a0a8b4;
            animation: msgBounce 1.4s infinite ease-in-out;
        }
        .msg-typing span:nth-child(2) { animation-delay: 0.16s; }
        .msg-typing span:nth-child(3) { animation-delay: 0.32s; }
        @keyframes msgBounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40% { transform: scale(1); opacity: 1; }
        }
        
        /* ============================================
           AI CHATBOT STYLES
           ============================================ */
        .chatbot-float {
            position: fixed;
            bottom: 100px;
            right: 28px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            z-index: 9998;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .chatbot-float:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        .chatbot-float .material-icons-round {
            font-size: 26px;
        }
        
        .chatbot-panel {
            position: fixed;
            bottom: 80px;
            right: 28px;
            width: 700px;
            height: 550px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            z-index: 9999;
            display: none;
            flex-direction: row;
            overflow: hidden;
        }
        .chatbot-panel.open {
            display: flex;
        }
        
        /* Sidebar - History */
        .chatbot-sidebar {
            width: 220px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        .chatbot-sidebar-header {
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }
        .chatbot-sidebar-title {
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .chatbot-sidebar-title .material-icons-round {
            font-size: 20px;
        }
        .chatbot-new-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .chatbot-new-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .chatbot-user-info {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.9);
            font-size: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .chatbot-guest-notice {
            padding: 8px 12px;
            background: rgba(255,200,0,0.15);
            color: rgba(255,255,255,0.9);
            font-size: 11px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .chatbot-guest-notice a {
            color: #ffd700;
            text-decoration: underline;
        }
        .chatbot-history-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }
        .chatbot-history-item {
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            margin-bottom: 4px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chatbot-history-item:hover {
            background: rgba(255,255,255,0.15);
        }
        .chatbot-history-item.active {
            background: rgba(255,255,255,0.25);
        }
        #chatbot-new-conv-item {
            border: 1px dashed rgba(255,255,255,0.3);
        }
        .chatbot-history-date {
            color: rgba(255,255,255,0.6);
            font-size: 10px;
        }
        .chatbot-history-title {
            color: #fff;
            font-size: 13px;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }
        .chatbot-history-count {
            color: rgba(255,255,255,0.5);
            font-size: 10px;
            white-space: nowrap;
        }
        .chatbot-history-count {
            color: rgba(255,255,255,0.6);
            font-size: 11px;
            margin-top: 2px;
        }
        .chatbot-history-empty {
            color: rgba(255,255,255,0.6);
            text-align: center;
            padding: 20px;
            font-size: 13px;
        }
        
        /* Chat Area */
        .chatbot-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .chatbot-header {
            background: #fff;
            color: #374151;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .chatbot-header .material-icons-round {
            font-size: 26px;
            color: #667eea;
        }
        .chatbot-header-title {
            flex: 1;
            font-weight: 600;
            font-size: 15px;
        }
        .chatbot-header-subtitle {
            font-size: 12px;
            color: #6b7280;
            font-weight: normal;
        }
        .chatbot-header-close {
            background: #f3f4f6;
            border: none;
            color: #6b7280;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .chatbot-header-close:hover {
            background: #e5e7eb;
            color: #374151;
        }
        
        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .chatbot-msg {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
        }
        .chatbot-msg.bot {
            background: #fff;
            color: #374151;
            align-self: flex-start;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .chatbot-msg.user {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            align-self: flex-end;
        }
        .chatbot-msg.bot strong {
            color: #667eea;
            font-weight: 600;
        }
        .chatbot-msg.bot a,
        .chatbot-msg.bot .chatbot-link {
            color: #667eea;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px dotted #667eea;
            padding-bottom: 1px;
        }
        .chatbot-msg.bot a:hover,
        .chatbot-msg.bot .chatbot-link:hover {
            color: #4c51bf;
            text-decoration: none;
            border-bottom: 2px solid #4c51bf;
            background-color: rgba(102, 126, 234, 0.1);
            border-radius: 2px;
        }
        .chatbot-msg.bot a strong,
        .chatbot-msg.bot .chatbot-link strong {
            font-weight: 600;
        }
        
        .chatbot-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        .chatbot-suggestion-btn {
            background: #fff;
            border: 1px solid #667eea;
            color: #667eea;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chatbot-suggestion-btn:hover {
            background: #667eea;
            color: #fff;
        }
        
        .chatbot-feedback {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        .chatbot-feedback-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chatbot-feedback-btn:hover {
            background: #e9ecef;
        }
        .chatbot-feedback-btn.selected {
            background: #667eea;
            color: #fff;
            border-color: #667eea;
        }
        
        .chatbot-input-area {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: #fff;
            border-top: 1px solid #e9ecef;
        }
        .chatbot-input {
            flex: 1;
            border: 1px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 24px;
            padding: 12px 18px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }
        .chatbot-input:focus {
            border-color: #667eea;
            background: #fff;
        }
        .chatbot-send-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        .chatbot-send-btn:hover {
            transform: scale(1.05);
        }
        .chatbot-send-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .chatbot-typing {
            display: flex;
            gap: 4px;
            padding: 12px 16px;
            background: #fff;
            border-radius: 16px;
            align-self: flex-start;
            border: 1px solid #e9ecef;
        }
        .chatbot-typing span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #667eea;
            animation: chatbotBounce 1.4s infinite ease-in-out;
        }
        .chatbot-typing span:nth-child(2) { animation-delay: 0.16s; }
        .chatbot-typing span:nth-child(3) { animation-delay: 0.32s; }
        @keyframes chatbotBounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40% { transform: scale(1); opacity: 1; }
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .chatbot-panel {
                width: calc(100vw - 24px);
                height: calc(100vh - 120px);
                bottom: 100px;
                right: 12px;
            }
            .chatbot-sidebar {
                width: 180px;
            }
        }
        @media (max-width: 540px) {
            .chatbot-panel {
                flex-direction: column;
            }
            .chatbot-sidebar {
                width: 100%;
                height: auto;
                max-height: 120px;
            }
            .chatbot-history-list {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                padding: 8px;
                gap: 8px;
            }
            .chatbot-history-item {
                flex: 0 0 auto;
                min-width: 120px;
                margin-bottom: 0;
            }
            .chatbot-float {
                bottom: 80px;
                right: 16px;
            }
        }
    </style>

    <!-- AI Chatbot Float Button -->
    <button class="chatbot-float" id="chatbot-float" onclick="toggleChatbot()" title="Trợ lý AI">
        <span class="material-icons-round">smart_toy</span>
    </button>

    <!-- AI Chatbot Panel -->
    <div class="chatbot-panel" id="chatbot-panel">
        <!-- Sidebar - History -->
        <div class="chatbot-sidebar">
            <div class="chatbot-sidebar-header">
                <span class="chatbot-sidebar-title">
                    <span class="material-icons-round">history</span>
                    Lịch sử
                </span>
                <button class="chatbot-new-btn" onclick="startNewChatbotConversation()" title="Cuộc trò chuyện mới">
                    <span class="material-icons-round" style="font-size:18px">add</span>
                </button>
            </div>
            <!-- User info -->
            <div class="chatbot-user-info" id="chatbot-user-info" style="display:none">
                <span class="material-icons-round" style="font-size:14px">person</span>
                <span id="chatbot-user-name"></span>
            </div>
            <div class="chatbot-history-list" id="chatbot-history-list">
                <!-- History items loaded here -->
                <div class="chatbot-history-empty">Chưa có lịch sử</div>
            </div>
        </div>
        
        <!-- Main Chat Area -->
        <div class="chatbot-main">
            <div class="chatbot-header">
                <span class="material-icons-round">smart_toy</span>
                <div>
                    <div class="chatbot-header-title">Trợ lý AI GameTech</div>
                    <div class="chatbot-header-subtitle">Sẵn sàng hỗ trợ bạn 24/7</div>
                </div>
                <button class="chatbot-header-close" onclick="toggleChatbot()" title="Đóng">
                    <span class="material-icons-round" style="font-size:18px">close</span>
                </button>
            </div>
            
            <div class="chatbot-messages" id="chatbot-messages">
                <!-- Messages will be loaded here -->
            </div>
            
            <div class="chatbot-input-area">
                <input type="text" class="chatbot-input" id="chatbot-input" placeholder="Hỏi tôi bất cứ điều gì..." onkeypress="if(event.key==='Enter')sendChatbotMessage()">
                <button class="chatbot-send-btn" id="chatbot-send-btn" onclick="sendChatbotMessage()" title="Gửi">
                    <span class="material-icons-round">send</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Messenger Float Button -->
    <button class="msg-float" id="gmsg-float" onclick="gToggleConv()">
        <span class="material-icons-round">chat</span>
        <span class="msg-float-badge" id="gmsg-badge" style="display:none">0</span>
    </button>

    <!-- Messenger Panel -->
    <div class="msg-panel" id="msg-panel">
        <!-- Sidebar -->
        <div class="msg-sidebar">
            <div class="msg-sidebar-header">
                <h3 id="msg-sidebar-title">Bạn bè</h3>
                <button class="msg-sidebar-close" onclick="gToggleConv()" title="Đóng">
                    <span class="material-icons-round" style="font-size:20px">close</span>
                </button>
            </div>
            <div class="msg-search" style="position:relative">
                <span class="msg-search-icon material-icons-round">search</span>
                <input class="msg-search-input" id="msg-search-input" placeholder="Tìm kiếm...">
            </div>
            <div class="msg-conv-list" id="gconv-list">
                <!-- Conversations loaded here -->
            </div>
        </div>

        <!-- Chat Area -->
        <div class="msg-chat" id="msg-chat-area">
            <!-- Empty state (no conversation selected) -->
            <div class="msg-chat-empty" id="msg-chat-empty">
                <span class="material-icons-round">forum</span>
                <p><strong>Chào mừng đến Tin nhắn</strong></p>
                <p style="font-size:13px">Chọn một cuộc trò chuyện để bắt đầu</p>
            </div>

            <!-- Chat header (hidden initially) -->
            <div class="msg-chat-header" id="msg-chat-header" style="display:none">
                <button class="msg-chat-back" onclick="gBackToList()">
                    <span class="material-icons-round">arrow_back</span>
                </button>
                <img id="gchat-avatar" src="" onclick="if(window._gchatPartnerId)window.location.href=gBase+'/profile/'+window._gchatPartnerId">
                <div class="msg-chat-header-info">
                    <div class="msg-chat-header-name" id="gchat-name" onclick="if(window._gchatPartnerId)window.location.href=gBase+'/profile/'+window._gchatPartnerId"></div>
                    <div class="msg-chat-header-status">Đang hoạt động</div>
                </div>
            </div>

            <!-- Messages -->
            <div class="msg-messages" id="gchat-messages" style="display:none"></div>

            <!-- Input area (hidden initially) -->
            <div class="msg-input-area" id="msg-input-area" style="display:none">
                <div class="msg-input-wrapper">
                    <input class="msg-input" id="gchat-input" placeholder="Nhập tin nhắn..." onkeypress="if(event.key==='Enter')gSendMsg()">
                </div>
                <button class="msg-send-btn" onclick="gSendMsg()" title="Gửi">
                    <span class="material-icons-round">send</span>
                </button>
            </div>
        </div>
    </div>

    <script>
    (function(){
        const gBase = '{{ url("/") }}';
        const gApi = gBase + '/api';
        window.gBase = gBase;
        let gUser = null;
        let gPollId = null;
        let gConvPollId = null; // Polling for conversation list (realtime update)
        let gUnreadPollId = null; // Polling for unread count (global)
        window._gchatPartnerId = null;
        let gActiveConvId = null;

        function gEsc(t){ if(!t)return ''; const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }
        function gFormatMsg(t){ if(!t)return ''; return gEsc(t).replace(/\n/g, '<br>'); }

        function gResolveAvatar(av, name) {
            if (!av) return `https://ui-avatars.com/api/?name=${encodeURIComponent(name||'U')}&background=0068FF&color=fff&size=96`;
            if (av.startsWith('/storage')) return gBase + av;
            return av;
        }

        function gFormatTime(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const now = new Date();
            const diff = (now - d) / 1000;
            if (diff < 60) return 'Vừa xong';
            if (diff < 3600) return Math.floor(diff/60) + ' phút';
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const msgDay = new Date(d.getFullYear(), d.getMonth(), d.getDate());
            if (msgDay.getTime() === today.getTime()) {
                return d.toLocaleTimeString('vi-VN', {hour:'2-digit', minute:'2-digit'});
            }
            const yesterday = new Date(today); yesterday.setDate(yesterday.getDate()-1);
            if (msgDay.getTime() === yesterday.getTime()) return 'Hôm qua';
            if (diff < 604800) {
                const days = ['CN','T2','T3','T4','T5','T6','T7'];
                return days[d.getDay()];
            }
            return d.toLocaleDateString('vi-VN', {day:'2-digit', month:'2-digit'});
        }

        function gFormatMsgTime(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleTimeString('vi-VN', {hour:'2-digit', minute:'2-digit'});
        }

        // Init
        let gInitPromise = null;
        async function gInit(){
            const token = localStorage.getItem('auth_token');
            if(!token) {
                console.log('[Chat] No auth token found');
                return;
            }
            try {
                const r = await fetch(gApi+'/user',{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
                const d = await r.json();
                if(d.id) gUser = d;
            } catch(e){
                console.error('[Chat] Failed to get user:', e);
            }
            if(!gUser) {
                console.log('[Chat] No user found');
                return;
            }
            document.getElementById('gmsg-float').style.display = 'flex';
            
            // Initial load
            gLoadUnread();
            
            // Start polling for unread messages every 5 seconds
            if (gUnreadPollId) clearInterval(gUnreadPollId);
            gUnreadPollId = setInterval(gLoadUnread, 5000);
            console.log('[Chat] Initialized, polling every 5s');
        }

        // Unread count - polls to check for new messages
        async function gLoadUnread(){
            const token = localStorage.getItem('auth_token');
            if (!token) return;
            try {
                const r = await fetch(gApi+'/messages/unread',{
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/json'},
                    cache: 'no-store' // Prevent caching
                });
                const d = await r.json();
                const b = document.getElementById('gmsg-badge');
                if(d.success && d.count > 0){ 
                    b.textContent = d.count; 
                    b.style.display = 'flex';
                } else { 
                    b.style.display = 'none'; 
                }
            } catch(e){
                console.error('[Chat] Failed to load unread:', e);
            }
        }

        // Toggle panel
        window.gToggleConv = function(){
            const p = document.getElementById('msg-panel');
            if (p.classList.contains('active')) {
                p.classList.remove('active');
                // Stop conversation list polling when panel is closed
                if (gConvPollId) {
                    clearInterval(gConvPollId);
                    gConvPollId = null;
                }
            } else {
                p.classList.add('active');
                gLoadConv();
                gLoadUnread(); // Refresh unread count immediately
                // Start conversation list polling for realtime updates (every 3 seconds when panel open)
                if (gConvPollId) clearInterval(gConvPollId);
                gConvPollId = setInterval(() => {
                    // Only refresh if not currently searching
                    const searchInput = document.getElementById('msg-search-input');
                    const searchQuery = searchInput ? searchInput.value.trim() : '';
                    if (!searchQuery) {
                        gLoadConv();
                    }
                }, 3000);
            }
        };

        // Check if user is admin
        function gIsAdmin() {
            return gUser && gUser.role === 'admin';
        }

        // Load friends list for sidebar (returns Promise)
        // Admin: loads all users; Regular users: loads friends only
        async function gLoadConv(searchQuery = ''){
            const token = localStorage.getItem('auth_token');
            const isAdmin = gIsAdmin();
            const sidebarTitle = document.getElementById('msg-sidebar-title');
            const searchInput = document.getElementById('msg-search-input');
            
            // Update sidebar title and placeholder based on role
            if (isAdmin) {
                if (sidebarTitle) sidebarTitle.textContent = 'Tất cả người dùng';
                if (searchInput) searchInput.placeholder = 'Tìm người dùng...';
            } else {
                if (sidebarTitle) sidebarTitle.textContent = 'Tin nhắn';
                if (searchInput) searchInput.placeholder = 'Tìm kiếm...';
            }

            try {
                let url = isAdmin ? gApi+'/friends/all-users' : gApi+'/friends/chat-list';
                if (isAdmin && searchQuery) {
                    url += '?search=' + encodeURIComponent(searchQuery);
                }
                
                const r = await fetch(url, {headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
                const d = await r.json();
                const el = document.getElementById('gconv-list');
                
                if(d.success && d.data.length > 0){
                    el.innerHTML = d.data.map(c => {
                        const av = gResolveAvatar(c.avatar, c.name);
                        const unread = c.unread_count > 0;
                        const isActive = gActiveConvId === c.id;
                        const hasMsg = !!c.last_message;
                        const timeStr = hasMsg ? gFormatTime(c.last_message_time) : '';
                        const lastMsg = hasMsg ? c.last_message : 'Bắt đầu trò chuyện';
                        const prefix = (hasMsg && c.last_sender_id == gUser.id) ? 'Bạn: ' : '';
                        const lastClass = hasMsg ? '' : 'color:var(--msg-text-secondary);font-style:italic;';
                        
                        // Show role badge - Admin sees all roles, regular users see "Hỗ trợ" for admin
                        let roleBadge = '';
                        if (c.role === 'admin') {
                            roleBadge = `<span style="font-size:10px;padding:1px 5px;border-radius:4px;margin-left:4px;background:#dcfce7;color:#16a34a">Hỗ trợ</span>`;
                        } else if (isAdmin && c.role) {
                            roleBadge = c.role === 'editor' ? 
                                `<span style="font-size:10px;padding:1px 5px;border-radius:4px;margin-left:4px;background:#dbeafe;color:#2563eb">Editor</span>` :
                                `<span style="font-size:10px;padding:1px 5px;border-radius:4px;margin-left:4px;background:#f1f5f9;color:#64748b">User</span>`;
                        }
                        
                        return `<div class="msg-conv-item ${unread?'unread':''} ${isActive?'active':''}" data-uid="${c.id}" onclick="gSelectChat(${c.id},'${gEsc(c.name)}','${c.avatar||''}')">
                            <div class="msg-conv-avatar">
                                <img src="${av}" alt="">
                            </div>
                            <div class="msg-conv-info">
                                <div class="msg-conv-top">
                                    <span class="msg-conv-name">${gEsc(c.name)}${roleBadge}</span>
                                    ${timeStr ? `<span class="msg-conv-time">${timeStr}</span>` : ''}
                                </div>
                                <div style="display:flex;align-items:center;gap:6px">
                                    <span class="msg-conv-last" style="flex:1;${lastClass}">${prefix}${gEsc(lastMsg)}</span>
                                    ${unread ? `<span class="msg-conv-unread-badge">${c.unread_count}</span>` : ''}
                                </div>
                            </div>
                        </div>`;
                    }).join('');
                } else {
                    const emptyMsg = isAdmin ? 
                        (searchQuery ? 'Không tìm thấy người dùng' : 'Chưa có người dùng') :
                        'Chưa có tin nhắn';
                    const emptySubMsg = isAdmin ?
                        (searchQuery ? 'Thử tìm kiếm với từ khóa khác' : '') :
                        'Kết bạn hoặc gửi yêu cầu hỗ trợ để bắt đầu';
                    
                    el.innerHTML = `<div class="msg-conv-empty">
                        <span class="material-icons-round">people_outline</span>
                        <p>${emptyMsg}</p>
                        ${emptySubMsg ? `<p style="font-size:12px;margin-top:4px">${emptySubMsg}</p>` : ''}
                    </div>`;
                }
            } catch(e){ console.error('[Chat] gLoadConv error:', e); }
        }

        // Select a friend's chat (click on sidebar item)
        window.gSelectChat = async function(userId, name, avatar){
            window._gchatPartnerId = userId;
            gActiveConvId = userId;
            const av = gResolveAvatar(avatar, name);

            // Update chat header
            document.getElementById('gchat-avatar').src = av;
            document.getElementById('gchat-name').textContent = name;
            document.getElementById('msg-chat-empty').style.display = 'none';
            document.getElementById('msg-chat-header').style.display = 'flex';
            document.getElementById('gchat-messages').style.display = 'flex';
            document.getElementById('msg-input-area').style.display = 'flex';

            // Mobile: show chat view
            document.getElementById('msg-panel').classList.add('chat-open');

            // Highlight active in sidebar
            document.querySelectorAll('.msg-conv-item').forEach(item => {
                item.classList.remove('active');
            });
            const activeItem = document.querySelector(`.msg-conv-item[data-uid="${userId}"]`);
            if (activeItem) {
                activeItem.classList.remove('unread');
                activeItem.classList.add('active');
            }

            // Load messages
            await gLoadMsgs(userId);
            gLoadUnread(); // Update badge after messages are marked as read
            document.getElementById('gchat-input').focus();
            if(gPollId) clearInterval(gPollId);
            gPollId = setInterval(() => gLoadMsgs(userId), 5000);
        };

        // Open chat from profile/community pages = open panel + auto-select friend
        window.gOpenChat = async function(userId, name, avatar){
            try {
                // Prevent document click handler from immediately closing
                window._gJustOpened = true;

                // 1. Open panel
                const panel = document.getElementById('msg-panel');
                panel.classList.add('active');

                // 2. Load friends list and wait for it
                await gLoadConv();

                // 3. Auto-select the friend's chat
                await gSelectChat(userId, name, avatar);
            } catch(err) {
                console.error('[gOpenChat] ERROR:', err);
            }
        };

        // Search filter
        let gSearchTimeout = null;
        const searchInput = document.getElementById('msg-search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                // Clear previous timeout
                if (gSearchTimeout) clearTimeout(gSearchTimeout);
                
                if (gIsAdmin()) {
                    // Admin: search via API with debounce
                    gSearchTimeout = setTimeout(() => {
                        gLoadConv(query);
                    }, 300);
                } else {
                    // Regular users: filter in DOM
                    const items = document.querySelectorAll('.msg-conv-item');
                    items.forEach(item => {
                        const name = item.querySelector('.msg-conv-name');
                        if (!query || (name && name.textContent.toLowerCase().includes(query.toLowerCase()))) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }
            });
        }



        // Back to list (mobile)
        window.gBackToList = function() {
            document.getElementById('msg-panel').classList.remove('chat-open');
        };

        // Close chat
        window.gCloseChat = function(){
            document.getElementById('msg-chat-empty').style.display = 'flex';
            document.getElementById('msg-chat-header').style.display = 'none';
            document.getElementById('gchat-messages').style.display = 'none';
            document.getElementById('msg-input-area').style.display = 'none';
            document.getElementById('msg-panel').classList.remove('chat-open');
            if(gPollId) clearInterval(gPollId);
            window._gchatPartnerId = null;
            gActiveConvId = null;
        };

        // Load messages
        async function gLoadMsgs(userId){
            const token = localStorage.getItem('auth_token');
            try {
                const r = await fetch(`${gApi}/messages/${userId}`,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
                const d = await r.json();
                const el = document.getElementById('gchat-messages');
                if(d.success && d.data){
                    let html = '';
                    let lastSender = null;
                    let lastDate = null;

                    d.data.forEach((m, i) => {
                        const isSent = m.sender_id == gUser.id;
                        const msgDate = m.created_at ? new Date(m.created_at).toLocaleDateString('vi-VN') : '';
                        const nextMsg = d.data[i+1];
                        const nextIsSame = nextMsg && nextMsg.sender_id === m.sender_id;

                        // Date divider
                        if (msgDate && msgDate !== lastDate) {
                            html += `<div class="msg-date-divider"><span>${msgDate}</span></div>`;
                            lastDate = msgDate;
                        }

                        // Check if this is a system/support message
                        const isSystemMsg = m.content && (m.content.includes('🎫') || m.content.includes('📬') || m.content.includes('Mã ticket:') || m.content.includes('ticket #'));
                        const systemClass = isSystemMsg ? ' system-msg' : '';

                        html += `<div class="msg-msg-group ${isSent?'sent':'received'}">
                            <div class="msg-bubble ${isSent?'sent':'received'}${systemClass}">${gFormatMsg(m.content)}</div>
                            ${!nextIsSame ? `<div class="msg-time ${isSent?'sent':''}">${gFormatMsgTime(m.created_at)}</div>` : ''}
                        </div>`;
                    });

                    el.innerHTML = html;
                    el.scrollTop = el.scrollHeight;
                }
            } catch(e){}
        }

        // Send message
        window.gSendMsg = async function(){
            if(!window._gchatPartnerId) return;
            const input = document.getElementById('gchat-input');
            const content = input.value.trim();
            if(!content) return;
            const token = localStorage.getItem('auth_token');
            input.value = '';

            // Optimistic update
            const el = document.getElementById('gchat-messages');
            const now = new Date();
            el.innerHTML += `<div class="msg-msg-group sent">
                <div class="msg-bubble sent">${gFormatMsg(content)}</div>
                <div class="msg-time sent">${now.toLocaleTimeString('vi-VN',{hour:'2-digit',minute:'2-digit'})}</div>
            </div>`;
            el.scrollTop = el.scrollHeight;

            try {
                await fetch(`${gApi}/messages/${window._gchatPartnerId}`,{
                    method:'POST',
                    headers:{'Content-Type':'application/json','Authorization':'Bearer '+token,'Accept':'application/json'},
                    body:JSON.stringify({content})
                });
                // Refresh conversation list
                gLoadConv();
            } catch(e){}
        };

        // Close panel when clicking outside
        document.addEventListener('click', function(e) {
            // Skip if panel was just opened by gOpenChat (same click event)
            if (window._gJustOpened) {
                window._gJustOpened = false;
                return;
            }
            const panel = document.getElementById('msg-panel');
            const floatBtn = document.getElementById('gmsg-float');
            if (panel.classList.contains('active') &&
                !panel.contains(e.target) &&
                !floatBtn.contains(e.target)) {
                panel.classList.remove('active');
            }
        });

        // Refresh unread when page becomes visible (user switches back to tab)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible' && gUser) {
                gLoadUnread();
            }
        });

        // Also refresh on window focus
        window.addEventListener('focus', function() {
            if (gUser) {
                gLoadUnread();
            }
        });

        gInitPromise = gInit();
    })();
    </script>

    <!-- AI Chatbot JavaScript -->
    <script>
    (function(){
        const cbBase = '{{ url("/") }}';
        const cbApi = cbBase + '/api/chatbot';
        let cbLastRecordId = null;
        
        // Conversation ID management - persisted in localStorage
        // A conversation is like a chat with a friend - all messages belong to one conversation
        // until user explicitly creates a new one
        const CB_CONV_KEY = 'chatbot_conv_id';
        
        function getCbConversationId() {
            // Migrate old key if exists
            const oldKey = localStorage.getItem('chatbot_conversation_id');
            if (oldKey) {
                localStorage.setItem(CB_CONV_KEY, oldKey);
                localStorage.removeItem('chatbot_conversation_id');
            }
            return localStorage.getItem(CB_CONV_KEY) || null;
        }
        function setCbConversationId(id) {
            if (id) {
                localStorage.setItem(CB_CONV_KEY, id);
            } else {
                localStorage.removeItem(CB_CONV_KEY);
            }
        }
        function generateConversationId() {
            return crypto.randomUUID ? crypto.randomUUID() : 
                'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
        }
        
        // Current active conversation ID - loaded from localStorage
        let cbCurrentConversationId = getCbConversationId();
        
        // Generate or get session ID for guest users
        function getChatbotSessionId() {
            let sessionId = localStorage.getItem('chatbot_session_id');
            if (!sessionId) {
                sessionId = 'cb_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
                localStorage.setItem('chatbot_session_id', sessionId);
            }
            return sessionId;
        }

        // Update user info display
        async function updateChatbotUserInfo() {
            const userInfoEl = document.getElementById('chatbot-user-info');
            const userNameEl = document.getElementById('chatbot-user-name');
            const historyList = document.getElementById('chatbot-history-list');
            
            // Check if user is logged in via token
            const token = localStorage.getItem('auth_token');
            
            if (token) {
                try {
                    // Fetch user data from API
                    const res = await fetch('{{ url("/api/user") }}', {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    });
                    const userData = await res.json();
                    
                    if (userData && userData.id) {
                        // User is logged in
                        userInfoEl.style.display = 'flex';
                        userNameEl.textContent = userData.name || userData.email;
                        
                        // Remove guest notice if exists
                        const guestNotice = document.querySelector('.chatbot-guest-notice');
                        if (guestNotice) guestNotice.remove();
                        
                        // Store user data for later use
                        window.cbUserData = userData;
                        return;
                    }
                } catch(e) {
                    console.log('Failed to get user data:', e);
                }
            }
            
            // Guest user
            userInfoEl.style.display = 'none';
            window.cbUserData = null;
            
            // Show guest notice if not already shown
            if (!document.querySelector('.chatbot-guest-notice')) {
                const notice = document.createElement('div');
                notice.className = 'chatbot-guest-notice';
                notice.innerHTML = '💡 <a href="{{ url("/login") }}">Đăng nhập</a> để lưu lịch sử chat';
                historyList.parentNode.insertBefore(notice, historyList);
            }
        }

        // Toggle chatbot panel
        window.toggleChatbot = async function() {
            const panel = document.getElementById('chatbot-panel');
            panel.classList.toggle('open');
            
            if (panel.classList.contains('open')) {
                // Update user info display first (to get user data)
                await updateChatbotUserInfo();
                
                // Load history
                await loadChatbotHistory();
                
                // Load saved conversation messages or show welcome
                const messages = document.getElementById('chatbot-messages');
                if (messages.innerHTML.trim() === '') {
                    if (cbCurrentConversationId) {
                        // Load existing conversation
                        loadChatbotConversation(cbCurrentConversationId);
                    } else {
                        // Show welcome message for new users
                        loadWelcomeMessage();
                    }
                }
                
                // Focus input
                setTimeout(() => document.getElementById('chatbot-input').focus(), 100);
            }
        };

        // Load conversation history
        window.loadChatbotHistory = async function() {
            const historyList = document.getElementById('chatbot-history-list');
            
            try {
                const token = localStorage.getItem('auth_token');
                const headers = { 'Accept': 'application/json' };
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }
                
                const res = await fetch(`${cbApi}/history?session_id=${getChatbotSessionId()}`, { headers });
                const data = await res.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    historyList.innerHTML = data.data.map((conv) => {
                        const isActive = cbCurrentConversationId === conv.conversation_id ? 'active' : '';
                        
                        return `
                            <div class="chatbot-history-item ${isActive}" onclick="loadChatbotConversation('${conv.conversation_id}')" data-conv-id="${conv.conversation_id}">
                                <span class="material-icons-round" style="color:rgba(255,255,255,0.6);font-size:16px;flex-shrink:0">chat_bubble_outline</span>
                                <div style="flex:1;min-width:0;overflow:hidden">
                                    <div class="chatbot-history-title">${escapeHtml(conv.title)}</div>
                                    <div style="display:flex;gap:8px;margin-top:2px">
                                        <span class="chatbot-history-date">${escapeHtml(conv.time)}</span>
                                        <span class="chatbot-history-count">${conv.message_count} tin nhắn</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    historyList.innerHTML = '<div class="chatbot-history-empty">Chưa có lịch sử</div>';
                }
            } catch(e) {
                console.error('Failed to load history:', e);
            }
        }

        // Load messages for a specific conversation
        window.loadChatbotConversation = async function(conversationId) {
            const messages = document.getElementById('chatbot-messages');
            cbCurrentConversationId = conversationId;
            setCbConversationId(conversationId);
            
            // Remove new conversation placeholder if exists
            const newConvItem = document.getElementById('chatbot-new-conv-item');
            if (newConvItem) newConvItem.remove();
            
            // Update active state in sidebar
            document.querySelectorAll('.chatbot-history-item').forEach(item => {
                item.classList.toggle('active', item.dataset.convId === conversationId);
            });
            
            messages.innerHTML = '<div style="text-align:center;padding:20px;color:#6b7280">Đang tải...</div>';
            
            try {
                const token = localStorage.getItem('auth_token');
                const headers = { 'Accept': 'application/json' };
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }
                
                const url = `${cbApi}/messages?session_id=${getChatbotSessionId()}&conversation_id=${conversationId}`;
                
                const res = await fetch(url, { headers });
                const data = await res.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    messages.innerHTML = data.data.map(msg => `
                        <div class="chatbot-msg user">${escapeHtml(msg.question)}</div>
                        <div class="chatbot-msg bot">${formatMarkdown(msg.answer)}</div>
                    `).join('');
                    messages.scrollTop = messages.scrollHeight;
                } else {
                    // Conversation is empty - show welcome but keep the same conversation
                    loadWelcomeMessage();
                }
            } catch(e) {
                console.error('Failed to load conversation:', e);
                loadWelcomeMessage();
            }
        };

        // Start new conversation - ONLY called when user clicks "New Conversation"
        window.startNewChatbotConversation = async function() {
            // Generate a brand new conversation ID
            cbCurrentConversationId = generateConversationId();
            setCbConversationId(cbCurrentConversationId);
            
            // Remove active state from all history items
            document.querySelectorAll('.chatbot-history-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add a "new conversation" placeholder at the top of history
            const historyList = document.getElementById('chatbot-history-list');
            const existingNew = document.getElementById('chatbot-new-conv-item');
            if (existingNew) existingNew.remove();
            
            const newItem = document.createElement('div');
            newItem.id = 'chatbot-new-conv-item';
            newItem.className = 'chatbot-history-item active';
            newItem.dataset.convId = cbCurrentConversationId;
            newItem.innerHTML = `
                <span class="material-icons-round" style="color:#667eea;font-size:16px">add_circle</span>
                <span>Cuộc trò chuyện mới</span>
            `;
            historyList.insertBefore(newItem, historyList.firstChild);
            
            // Remove empty message if exists
            const emptyMsg = historyList.querySelector('.chatbot-history-empty');
            if (emptyMsg) emptyMsg.remove();
            
            loadWelcomeMessage();
            document.getElementById('chatbot-input').focus();
        };

        // Load welcome message with suggestions
        window.loadWelcomeMessage = async function() {
            const messages = document.getElementById('chatbot-messages');
            
            // Personalized greeting based on user data
            let greeting, saveNote;
            if (window.cbUserData && window.cbUserData.name) {
                greeting = `👋 <strong style="color:#667eea">Xin chào ${window.cbUserData.name}!</strong>`;
                saveNote = '<div style="font-size:11px;color:#6b7280;margin-top:8px">✅ Lịch sử chat của bạn được lưu tự động</div>';
            } else {
                greeting = '👋 <strong style="color:#667eea">Xin chào!</strong>';
                saveNote = '<div style="font-size:11px;color:#f59e0b;margin-top:8px">💡 <a href="{{ url("/login") }}" style="color:#f59e0b;text-decoration:underline">Đăng nhập</a> để lưu lịch sử chat</div>';
            }
            
            // Add welcome message
            messages.innerHTML = `
                <div class="chatbot-msg bot">
                    <div style="font-size:20px;margin-bottom:8px">${greeting}</div>
                    <div style="margin-bottom:12px">Tôi là trợ lý AI của <strong style="color:#667eea">GameTech</strong>.</div>
                    <div style="margin-bottom:8px">🎮 Tôi có thể giúp bạn:</div>
                    <div style="padding-left:8px;line-height:1.8">
                        • Tìm game theo thể loại<br>
                        • Tra cứu giá & thông tin game<br>
                        • Kiểm tra đơn hàng<br>
                        • Trả lời mọi câu hỏi
                    </div>
                    <div style="margin-top:12px;color:#6b7280;font-size:13px">💬 Hãy chọn câu hỏi bên dưới hoặc gõ tin nhắn!</div>
                    ${saveNote}
                </div>
            `;
            
            // Load suggestions
            try {
                const res = await fetch(`${cbApi}/suggestions`);
                const data = await res.json();
                
                if (data.success && data.data) {
                    let suggestionsHtml = '<div class="chatbot-suggestions">';
                    data.data.forEach(s => {
                        suggestionsHtml += `<button class="chatbot-suggestion-btn" onclick="sendSuggestion('${escapeHtml(s)}', event)">${escapeHtml(s)}</button>`;
                    });
                    suggestionsHtml += '</div>';
                    messages.innerHTML += suggestionsHtml;
                }
            } catch(e) {
                console.error('Failed to load suggestions:', e);
            }
        }

        // Send a suggestion
        window.sendSuggestion = function(text, event) {
            // Prevent panel from closing
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            document.getElementById('chatbot-input').value = text;
            sendChatbotMessage();
        };

        // Send message to chatbot
        window.sendChatbotMessage = async function() {
            const input = document.getElementById('chatbot-input');
            const sendBtn = document.getElementById('chatbot-send-btn');
            const messages = document.getElementById('chatbot-messages');
            const message = input.value.trim();
            
            if (!message) return;
            
            // Use existing conversation or create one for first-time users
            if (!cbCurrentConversationId) {
                cbCurrentConversationId = generateConversationId();
                setCbConversationId(cbCurrentConversationId);
            }
            
            // Clear input and disable button
            input.value = '';
            sendBtn.disabled = true;
            
            // Remove suggestions if present
            const suggestions = messages.querySelector('.chatbot-suggestions');
            if (suggestions) suggestions.remove();
            
            // Add user message
            messages.innerHTML += `<div class="chatbot-msg user">${escapeHtml(message)}</div>`;
            
            // Add typing indicator
            messages.innerHTML += `<div class="chatbot-typing" id="chatbot-typing"><span></span><span></span><span></span></div>`;
            messages.scrollTop = messages.scrollHeight;
            
            try {
                const token = localStorage.getItem('auth_token');
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }
                
                const res = await fetch(`${cbApi}/message`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        message: message,
                        session_id: getChatbotSessionId(),
                        conversation_id: cbCurrentConversationId
                    })
                });
                
                const data = await res.json();
                
                // Remove typing indicator
                const typing = document.getElementById('chatbot-typing');
                if (typing) typing.remove();
                
                if (data.success) {
                    cbLastRecordId = data.data.record_id;
                    
                    // Format and add bot response
                    let responseHtml = formatMarkdown(data.data.answer);
                    
                    // Add feedback buttons (use record_id for feedback)
                    let feedbackHtml = '';
                    if (cbLastRecordId) {
                        feedbackHtml = `
                            <div class="chatbot-feedback" data-record-id="${cbLastRecordId}">
                                <button class="chatbot-feedback-btn" onclick="submitFeedback(${cbLastRecordId}, 'good', this)">👍 Hữu ích</button>
                                <button class="chatbot-feedback-btn" onclick="submitFeedback(${cbLastRecordId}, 'bad', this)">👎 Chưa tốt</button>
                            </div>
                        `;
                    }
                    
                    messages.innerHTML += `<div class="chatbot-msg bot">${responseHtml}${feedbackHtml}</div>`;
                } else {
                    messages.innerHTML += `<div class="chatbot-msg bot">Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.</div>`;
                }
                
            } catch(e) {
                // Remove typing indicator
                const typing = document.getElementById('chatbot-typing');
                if (typing) typing.remove();
                
                messages.innerHTML += `<div class="chatbot-msg bot">Không thể kết nối. Vui lòng kiểm tra kết nối mạng và thử lại.</div>`;
                console.error('Chatbot error:', e);
            }
            
            // Re-enable button and scroll
            sendBtn.disabled = false;
            messages.scrollTop = messages.scrollHeight;
            
            // Refresh history sidebar
            loadChatbotHistory();
        };

        // Submit feedback
        window.submitFeedback = async function(conversationId, feedback, btn) {
            try {
                const feedbackDiv = btn.parentElement;
                const buttons = feedbackDiv.querySelectorAll('.chatbot-feedback-btn');
                buttons.forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                
                const token = localStorage.getItem('auth_token');
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                };
                if (token) {
                    headers['Authorization'] = 'Bearer ' + token;
                }
                
                await fetch(`${cbApi}/feedback`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        conversation_id: conversationId,
                        feedback: feedback
                    })
                });
                
                // Disable buttons after feedback
                buttons.forEach(b => {
                    b.disabled = true;
                    b.style.cursor = 'default';
                });
                
            } catch(e) {
                console.error('Failed to submit feedback:', e);
            }
        };

        // Format markdown to HTML
        function formatMarkdown(text) {
            if (!text) return '';
            
            let html = text;
            
            // Handle escaped newlines first (from JSON)
            html = html.replace(/\\n/g, '\n');
            
            // Extract and preserve links first (before HTML escaping)
            const links = [];
            html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, (match, text, url) => {
                const index = links.length;
                // Check if it's an internal link - include same hostname, relative paths, or known paths
                const currentHost = window.location.hostname;
                const isInternal = url.startsWith(cbBase) || 
                                   url.startsWith('/') || 
                                   url.includes(currentHost) ||
                                   url.includes('/game/') ||
                                   url.includes('/store') ||
                                   url.includes('/orders') ||
                                   url.includes('/support') ||
                                   url.includes('/community') ||
                                   url.includes('/wallet') ||
                                   url.includes('/news') ||
                                   !url.startsWith('http');
                const target = isInternal ? '' : ' target="_blank"';
                links.push(`<a href="${url}"${target} class="chatbot-link">${text}</a>`);
                return `__LINK_${index}__`;
            });
            
            // Escape HTML special characters
            html = html.replace(/&/g, '&amp;')
                       .replace(/</g, '&lt;')
                       .replace(/>/g, '&gt;');
            
            // Restore links
            links.forEach((link, index) => {
                html = html.replace(`__LINK_${index}__`, link);
            });
            
            // Bold: **text** or __text__ (but not our link placeholders)
            html = html.replace(/\*\*(.+?)\*\*/g, '<strong style="color:#667eea">$1</strong>');
            html = html.replace(/(?<!_)__([^_]+)__(?!_)/g, '<strong style="color:#667eea">$1</strong>');
            
            // Italic: *text* or _text_
            html = html.replace(/\*([^*]+)\*/g, '<em>$1</em>');
            html = html.replace(/(?<![_a-zA-Z])_([^_]+)_(?![_a-zA-Z])/g, '<em>$1</em>');
            
            // Bullet points: - item or • item
            html = html.replace(/^- (.+)$/gm, '• $1');
            
            // Numbered lists: 1. item
            html = html.replace(/^(\d+)\. (.+)$/gm, '<strong style="color:#667eea">$1.</strong> $2');
            
            // Line breaks (must be last)
            html = html.replace(/\n/g, '<br>');
            
            return html;
        }

        // Escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Close panel when clicking outside
        document.addEventListener('click', function(e) {
            const panel = document.getElementById('chatbot-panel');
            const floatBtn = document.getElementById('chatbot-float');
            if (panel.classList.contains('open') &&
                !panel.contains(e.target) &&
                !floatBtn.contains(e.target)) {
                panel.classList.remove('open');
            }
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>

