@extends('layouts.app')

@section('title', 'Create Product')

@section('max-width', 'max-w-4xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 md:mb-8 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">Create New Product</h1>
            <p class="text-sm sm:text-base text-gray-600">Thêm sản phẩm mới vào database (API)</p>
        </div>
        <a href="{{ url('/database/products') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
            ← Back to Products
        </a>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Success Message -->
    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <form id="create-product-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input type="text" id="title" name="title" required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-title" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                        <input type="text" id="price" name="price" required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                               placeholder="e.g., $29.99">
                        <p id="error-price" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <input type="text" id="category" name="category" required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                               placeholder="e.g., Action, RPG, Strategy">
                        <p id="error-category" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
                        <input type="url" id="image" name="image" 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                               placeholder="https://example.com/image.jpg">
                        <p id="error-image" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Additional Information</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <input type="text" id="tags" name="tags" 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                               placeholder="action, adventure, multiplayer">
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Separate tags with commas (will be converted to array)</p>
                        <p id="error-tags" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">View Count</label>
                        <input type="number" id="view_count" name="view_count" min="0" value="0"
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-view_count" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                </div>
            </div>

            <!-- Descriptions -->
            <div class="mt-6 space-y-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Descriptions</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                    <textarea id="short_description" name="short_description" rows="3" 
                              class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                              placeholder="Brief description of the product"></textarea>
                    <p id="error-short_description" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Detail Description</label>
                    <!-- Quill Editor Container -->
                    <div id="detail_description_editor" class="bg-white border border-gray-300 rounded-md"></div>
                    <!-- Hidden textarea to store HTML content -->
                    <textarea id="detail_description" name="detail_description" class="hidden"></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        Nhập nội dung mô tả chi tiết sản phẩm hoặc paste từ Word...
                    </p>
                    <p id="error-detail_description" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row justify-end gap-2 sm:gap-4">
                <a href="{{ url('/database/products') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
                    Cancel
                </a>
                <button type="submit" id="submit-btn" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition duration-200 text-sm sm:text-base">
                    <span id="submit-text">Create Product</span>
                    <span id="submit-loading" class="hidden">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang tạo sản phẩm...
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
    #detail_description_editor {
        height: 400px;
    }
    #detail_description_editor .ql-editor {
        font-size: 14px;
        line-height: 1.6;
    }
    /* Improved image styling for balanced display */
    #detail_description_editor .ql-editor img {
        display: block;
        max-width: 100%;
        width: auto;
        height: auto;
        margin: 16px auto;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    #detail_description_editor .ql-editor img:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    /* Image container for better layout */
    #detail_description_editor .ql-editor p img {
        display: inline-block;
        max-width: 48%;
        margin: 8px 1%;
        vertical-align: top;
    }
    #detail_description_editor .ql-editor p img:only-child {
        display: block;
        max-width: 100%;
        margin: 16px auto;
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
    const API_BASE_URL = '{{ url("/api/products") }}';
    
    // Initialize Quill Editor
    let quillEditor = null;
    
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Quill editor with extended toolbar (same as News)
        quillEditor = new Quill('#detail_description_editor', {
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
            placeholder: 'Nhập nội dung mô tả chi tiết sản phẩm hoặc paste từ Word...'
        });
        
        // Update hidden textarea when editor content changes
        quillEditor.on('text-change', function() {
            let html = quillEditor.root.innerHTML;
            // Auto-detect and convert image URLs to img tags
            html = autoDetectImages(html);
            document.getElementById('detail_description').value = html;
        });
        
        // Handle paste event to detect image URLs
        quillEditor.root.addEventListener('paste', function(e) {
            setTimeout(() => {
                let html = quillEditor.root.innerHTML;
                html = autoDetectImages(html);
                if (html !== quillEditor.root.innerHTML) {
                    quillEditor.root.innerHTML = html;
                    document.getElementById('detail_description').value = html;
                }
            }, 100);
        });
    });
    
    // Auto-detect image URLs and convert to img tags with balanced display
    function autoDetectImages(html) {
        // Regex patterns for different image URL formats
        const patterns = [
            // Standard image extensions
            /(?<!src=["'])(?<!href=["'])(https?:\/\/[^\s<>"]+\.(?:jpg|jpeg|png|gif|webp|bmp|svg|ico))(?:\?[^\s<>"]*)?(?![^<]*>)/gi,
            // Steam CDN images
            /(?<!src=["'])(?<!href=["'])(https?:\/\/(?:cdn\.cloudflare\.steamstatic\.com|steamcdn-a\.akamaihd\.net|store\.steampowered\.com)[^\s<>"]+)(?![^<]*>)/gi,
            // Imgur images (without extension)
            /(?<!src=["'])(?<!href=["'])(https?:\/\/(?:i\.)?imgur\.com\/[a-zA-Z0-9]+)(?![^<]*>)/gi,
            // Google Drive images
            /(?<!src=["'])(?<!href=["'])(https?:\/\/drive\.google\.com\/[^\s<>"]+)(?![^<]*>)/gi,
            // Discord CDN
            /(?<!src=["'])(?<!href=["'])(https?:\/\/cdn\.discordapp\.com\/[^\s<>"]+)(?![^<]*>)/gi,
            // Generic image hosting patterns
            /(?<!src=["'])(?<!href=["'])(https?:\/\/[^\s<>"]*(?:image|img|photo|pic|media|upload)[^\s<>"]*\.(?:jpg|jpeg|png|gif|webp))(?:[^\s<>"]*)?(?![^<]*>)/gi
        ];
        
        let result = html;
        
        patterns.forEach(regex => {
            result = result.replace(regex, function(url) {
                // Check if URL is already inside an img tag
                const beforeMatch = result.substring(0, result.indexOf(url));
                if (beforeMatch.lastIndexOf('<img') > beforeMatch.lastIndexOf('>')) {
                    return url; // Already in img tag, don't replace
                }
                // Create balanced, centered image with proper styling
                return `<img src="${url}" alt="Image" class="auto-detected-image" style="display: block; max-width: 100%; height: auto; margin: 16px auto; border-radius: 8px;">`;
            });
        });
        
        return result;
    }
    
    // Function to manually insert image URL
    function insertImageFromUrl() {
        const url = prompt('Nhập URL ảnh:');
        if (url && quillEditor) {
            const range = quillEditor.getSelection(true);
            quillEditor.insertEmbed(range.index, 'image', url);
            quillEditor.setSelection(range.index + 1);
        }
    }
    
    // DOM Elements
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const createProductForm = document.getElementById('create-product-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');

    // Create product via API
    async function createProduct(formData) {
        try {
            // Get Quill editor HTML content before converting FormData
            if (quillEditor) {
                const htmlContent = quillEditor.root.innerHTML;
                // Update hidden textarea
                document.getElementById('detail_description').value = htmlContent;
            }
            
            // Convert FormData to object
            const data = {};
            formData.forEach((value, key) => {
                if (key === 'tags' && value) {
                    const tagsArray = value.split(',').map(tag => tag.trim()).filter(tag => tag);
                    data[key] = tagsArray;
                } else if (key === 'view_count') {
                    data[key] = value ? parseInt(value) : 0;
                } else {
                    data[key] = value || null;
                }
            });

            // Handle tags separately
            const tagsInput = document.getElementById('tags').value;
            if (tagsInput) {
                data.tags = tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag);
            } else {
                data.tags = [];
            }
            
            // Get detail_description from hidden textarea (updated by Quill)
            data.detail_description = document.getElementById('detail_description').value;

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
                throw new Error(result.message || 'Failed to create product');
            }

            // Success
            showSuccess('Tạo sản phẩm thành công! Đang chuyển hướng...');
            clearValidationErrors();
            
            // Redirect after 1.5 seconds
            setTimeout(() => {
                window.location.href = '{{ url("/database/products") }}';
            }, 1500);

        } catch (error) {
            console.error('Error creating product:', error);
            if (error.message !== 'Validation failed') {
                showError('Lỗi khi tạo sản phẩm: ' + error.message);
            }
        }
    }

    // Display validation errors
    function displayValidationErrors(errors) {
        // Clear previous errors
        clearValidationErrors();

        // Display each error
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(`error-${field}`);
            if (errorElement) {
                errorElement.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                errorElement.classList.remove('hidden');
                
                // Highlight input field
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

        // Remove error styling from inputs
        document.querySelectorAll('input, textarea').forEach(element => {
            element.classList.remove('border-red-500');
        });
    }

    // Utility functions
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
        // Scroll to top to show error
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hideError() {
        errorMessage.classList.add('hidden');
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.classList.remove('hidden');
        // Scroll to top to show success
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hideSuccess() {
        successMessage.classList.add('hidden');
    }

    // Form submission handler
    createProductForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Validate detail_description
        const detailDescription = document.getElementById('detail_description').value;
        if (detailDescription && detailDescription === '<p><br></p>') {
            // Empty editor is OK - not required
        }
        
        // Clear previous errors
        clearValidationErrors();
        hideError();
        hideSuccess();

        // Disable submit button
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');

        // Get form data
        const formData = new FormData(createProductForm);

        // Create product
        await createProduct(formData);

        // Re-enable submit button
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
    });
</script>
@endpush

