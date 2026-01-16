@extends('layouts.app')

@section('title', 'Create News')

@section('max-width', 'max-w-4xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 md:mb-8 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">Create New News</h1>
            <p class="text-sm sm:text-base text-gray-600">Thêm tin tức công nghệ mới vào database</p>
        </div>
        <a href="{{ url('/database/news') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
            ← Back to News
        </a>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Success Message -->
    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <form id="create-news-form">
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
                    <input type="text" id="title" name="title" required 
                           class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                           placeholder="Nhập tiêu đề tin tức">
                    <p id="error-title" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <!-- Description with Rich Text Editor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung *</label>
                    <!-- Quill Editor Container -->
                    <div id="description_editor" class="bg-white border border-gray-300 rounded-md"></div>
                    <!-- Hidden textarea to store HTML content -->
                    <textarea id="description" name="description" class="hidden" required></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        Nhập nội dung mô tả tin tức
                    </p>
                    <p id="error-description" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <!-- Author -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tác giả *</label>
                    <input type="text" id="author" name="author" required 
                           class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                           placeholder="Nhập tên tác giả">
                    <p id="error-author" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <!-- Published At -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian đăng bài</label>
                    <input type="text" id="published_at" name="published_at" 
                           class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                           placeholder="Nhập thời gian đăng bài (ví dụ: 2026-01-13 20:59 hoặc để trống)">
                    <p class="text-xs text-gray-500 mt-2">
                        Nhập thời gian theo định dạng: YYYY-MM-DD HH:mm (ví dụ: 2026-01-13 20:59). Nếu không nhập, hệ thống sẽ sử dụng thời gian tạo tin tức. Có thể nhập thời gian trong quá khứ.
                    </p>
                    <p id="error-published_at" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thể loại</label>
                    <input type="text" id="category" name="category" 
                           class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                           placeholder="Nhập thể loại tin tức (ví dụ: Công nghệ, Game, Tin tức)">
                    <p class="text-xs text-gray-500 mt-2">
                        Nhập thể loại để phân loại tin tức
                    </p>
                    <p id="error-category" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row justify-end gap-2 sm:gap-4">
                <a href="{{ url('/database/news') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
                    Cancel
                </a>
                <button type="submit" id="submit-btn" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition duration-200 text-sm sm:text-base">
                    <span id="submit-text">Create News</span>
                    <span id="submit-loading" class="hidden">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang tạo tin tức...
                    </span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    #description_editor {
        height: 400px;
    }
    #description_editor .ql-editor {
        font-size: 14px;
        line-height: 1.6;
    }
    #description_editor .ql-editor img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 10px 0;
    }
    .ql-toolbar.ql-snow {
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        border-color: #d1d5db;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 6px;
        border-bottom-right-radius: 6px;
        border-color: #d1d5db;
    }
</style>
@endpush

@push('scripts')
<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    // API Configuration
    const API_BASE_URL = '{{ url("/api/news") }}';
    
    // Initialize Quill Editor
    let quillEditor = null;
    
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Quill editor with extended toolbar
        quillEditor = new Quill('#description_editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'font': [] }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['blockquote', 'code-block'],
                    ['clean']
                ],
                clipboard: {
                    matchVisual: false // Better paste from Word
                }
            },
            placeholder: 'Nhập nội dung tin tức hoặc paste từ Word...'
        });
        
        // Update hidden textarea when editor content changes
        quillEditor.on('text-change', function() {
            let html = quillEditor.root.innerHTML;
            // Auto-detect and convert image URLs to img tags
            html = autoDetectImages(html);
            document.getElementById('description').value = html;
        });
        
        // Handle paste event to detect image URLs
        quillEditor.root.addEventListener('paste', function(e) {
            setTimeout(() => {
                let html = quillEditor.root.innerHTML;
                html = autoDetectImages(html);
                if (html !== quillEditor.root.innerHTML) {
                    quillEditor.root.innerHTML = html;
                    document.getElementById('description').value = html;
                }
            }, 100);
        });
    });
    
    // Auto-detect image URLs and convert to img tags
    function autoDetectImages(html) {
        // Regex to match image URLs that are not already in img tags
        const imageUrlRegex = /(?<!src=["'])(?<!href=["'])(https?:\/\/[^\s<>"]+\.(?:jpg|jpeg|png|gif|webp|bmp|svg))(?![^<]*>)/gi;
        
        return html.replace(imageUrlRegex, function(url) {
            return `<img src="${url}" alt="Image" style="max-width: 100%; height: auto;">`;
        });
    }
    
    // DOM Elements
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const createNewsForm = document.getElementById('create-news-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');

    // Create news via API
    async function createNews() {
        try {
            // Get data from form
            const data = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                author: document.getElementById('author').value,
                published_at: document.getElementById('published_at').value || null,
                category: document.getElementById('category').value || null
            };

            const response = await fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                // Handle validation errors
                if (result.errors) {
                    displayValidationErrors(result.errors);
                    throw new Error('Validation failed');
                }
                throw new Error(result.message || 'Failed to create news');
            }

            // Success
            showSuccess('Tạo tin tức thành công! Đang chuyển hướng...');
            clearValidationErrors();
            
            // Redirect after 1.5 seconds
            setTimeout(() => {
                window.location.href = '{{ url("/database/news") }}';
            }, 1500);

        } catch (error) {
            console.error('Error creating news:', error);
            if (error.message !== 'Validation failed') {
                showError('Lỗi khi tạo tin tức: ' + error.message);
            }
        }
    }

    // Display validation errors
    function displayValidationErrors(errors) {
        clearValidationErrors();

        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(`error-${field}`);
            if (errorElement) {
                errorElement.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                errorElement.classList.remove('hidden');
                
                const inputElement = document.getElementById(field);
                if (inputElement) {
                    inputElement.classList.add('border-red-500');
                }
            }
        });
    }

    // Clear validation errors
    function clearValidationErrors() {
        document.querySelectorAll('[id^="error-"]').forEach(element => {
            element.classList.add('hidden');
            element.textContent = '';
        });
        
        document.querySelectorAll('input, textarea').forEach(input => {
            input.classList.remove('border-red-500');
        });
    }

    // Show error message
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
        successMessage.classList.add('hidden');
    }

    // Show success message
    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.classList.remove('hidden');
        errorMessage.classList.add('hidden');
    }

    function hideError() {
        errorMessage.classList.add('hidden');
    }

    function hideSuccess() {
        successMessage.classList.add('hidden');
    }

    // Form submit handler
    createNewsForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Validate description
        const description = document.getElementById('description').value;
        if (!description || description === '<p><br></p>') {
            showError('Vui lòng nhập nội dung tin tức');
            return;
        }
        
        // Disable submit button
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
        
        // Clear previous errors
        clearValidationErrors();
        hideError();
        hideSuccess();
        
        // Create news
        await createNews();
        
        // Re-enable submit button
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
    });
</script>
@endpush
