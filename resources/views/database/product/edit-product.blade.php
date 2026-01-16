@extends('layouts.app')

@section('title', 'Edit Product')

@section('max-width', 'max-w-4xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 md:mb-8 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">Edit Product</h1>
            <p class="text-sm sm:text-base text-gray-600">Chỉnh sửa thông tin sản phẩm (API)</p>
        </div>
        <a href="{{ url('/database/products') }}" id="back-link" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
            ← Back to Products
        </a>
    </div>

    <!-- Loading Indicator -->
    <div id="loading-indicator" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Đang tải dữ liệu sản phẩm...</span>
        </div>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Success Message -->
    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Edit Form -->
    <div id="edit-form-container" class="bg-white rounded-lg shadow-md p-4 sm:p-6 hidden">
        <form id="edit-product-form">
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
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-price" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <input type="text" id="category" name="category" required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-category" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select id="type" name="type" 
                                class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                            <option value="">-- Chọn Type --</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                        <p id="error-type" class="text-red-500 text-sm mt-1 hidden"></p>
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
                               placeholder="tag1, tag2, tag3">
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Separate tags with commas (will be converted to array)</p>
                        <p id="error-tags" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">View Count</label>
                        <input type="number" id="view_count" name="view_count" min="0"
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
                              class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"></textarea>
                    <p id="error-short_description" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Detail Description</label>
                    <!-- Quill Editor Container -->
                    <div id="detail_description_editor" style="height: 200px;" class="mb-2"></div>
                    <!-- Hidden textarea to store HTML content -->
                    <textarea id="detail_description" name="detail_description" class="hidden"></textarea>
                    <p class="text-xs text-gray-500 mb-2">Sử dụng thanh công cụ để định dạng văn bản (in đậm, in nghiêng, danh sách, v.v.)</p>
                    <p id="error-detail_description" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row justify-end gap-2 sm:gap-4">
                <a href="{{ url('/database/products') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
                    Cancel
                </a>
                <button type="submit" id="submit-btn" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200 text-sm sm:text-base">
                    <span id="submit-text">Update Product</span>
                    <span id="submit-loading" class="hidden">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang cập nhật...
                    </span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
        // Initialize Quill editor
        quillEditor = new Quill('#detail_description_editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            },
            placeholder: 'Nhập mô tả chi tiết sản phẩm...'
        });
        
        // Update hidden textarea when editor content changes
        quillEditor.on('text-change', function() {
            const html = quillEditor.root.innerHTML;
            document.getElementById('detail_description').value = html;
        });
    });
    
    // Get product ID from URL: /database/products/{id}/edit
    const urlParts = window.location.pathname.split('/').filter(part => part);
    let productId = null;
    
    // Extract from URL pattern: database/products/{id}/edit
    const productsIndex = urlParts.indexOf('products');
    if (productsIndex !== -1 && productsIndex + 1 < urlParts.length) {
        productId = urlParts[productsIndex + 1];
    }
    
    if (!productId) {
        console.error('Could not extract product ID from URL:', window.location.pathname);
        showError('Không tìm thấy ID sản phẩm từ URL');
        hideLoading();
    }
    
    // Save current list URL from query params for redirect after update
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || '1';
    const sortBy = urlParams.get('sort_by') || 'id';
    const sortOrder = urlParams.get('sort_order') || 'asc';
    const listUrl = `{{ url("/database/products") }}?page=${page}&sort_by=${sortBy}&sort_order=${sortOrder}`;
    localStorage.setItem('productsListUrl', listUrl);
    console.log('Edit page: Saved list URL to localStorage:', listUrl);
    console.log('Edit page: Query params - page:', page, 'sort_by:', sortBy, 'sort_order:', sortOrder);
    
    // DOM Elements
    const loadingIndicator = document.getElementById('loading-indicator');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const editFormContainer = document.getElementById('edit-form-container');
    const editProductForm = document.getElementById('edit-product-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    const backLink = document.getElementById('back-link');

    // Load product data from API
    async function loadProduct() {
        if (!productId) {
            return;
        }

        try {
            showLoading();
            hideError();
            hideSuccess();

            const response = await fetch(`${API_BASE_URL}/${productId}`);
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to load product');
            }

            const product = result.data;
            
            // Populate form fields
            document.getElementById('title').value = product.title || '';
            document.getElementById('price').value = product.price || '';
            document.getElementById('category').value = product.category || '';
            document.getElementById('type').value = product.type || '';
            document.getElementById('image').value = product.image || '';
            document.getElementById('short_description').value = product.short_description || '';
            
            // Set Quill editor content
            if (quillEditor && product.detail_description) {
                quillEditor.root.innerHTML = product.detail_description;
                document.getElementById('detail_description').value = product.detail_description;
            } else {
                document.getElementById('detail_description').value = '';
            }
            document.getElementById('view_count').value = product.view_count || 0;
            
            // Handle tags - convert array to comma-separated string
            if (Array.isArray(product.tags)) {
                document.getElementById('tags').value = product.tags.join(', ');
            } else if (product.tags) {
                document.getElementById('tags').value = product.tags;
            } else {
                document.getElementById('tags').value = '';
            }

            // Show form
            hideLoading();
            editFormContainer.classList.remove('hidden');
        } catch (error) {
            console.error('Error loading product:', error);
            showError('Lỗi khi tải dữ liệu sản phẩm: ' + error.message);
            hideLoading();
        }
    }

    // Update product via API
    async function updateProduct(formData) {
        try {
            // Convert tags from comma-separated string to array
            const tagsValue = formData.get('tags');
            if (tagsValue) {
                formData.delete('tags');
                const tagsArray = tagsValue.split(',').map(tag => tag.trim()).filter(tag => tag);
                if (tagsArray.length > 0) {
                    // API expects array, so we need to send it properly
                    // Since FormData doesn't handle arrays well, we'll convert to JSON
                }
            }

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

            const response = await fetch(`${API_BASE_URL}/${productId}`, {
                method: 'PUT',
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
                throw new Error(result.message || 'Failed to update product');
            }

            // Success
            showSuccess('Cập nhật sản phẩm thành công! Đang chuyển hướng...');
            clearValidationErrors();
            
            // Redirect after 1.5 seconds - get saved URL from localStorage
            setTimeout(() => {
                const savedUrl = localStorage.getItem('productsListUrl');
                console.log('Saved URL from localStorage:', savedUrl);
                if (savedUrl) {
                    window.location.href = savedUrl;
                    localStorage.removeItem('productsListUrl'); // Clean up after use
                } else {
                    // Fallback: try to get from URL params or default to first page
                    const urlParams = new URLSearchParams(window.location.search);
                    const page = urlParams.get('page') || '1';
                    const sortBy = urlParams.get('sort_by') || 'id';
                    const sortOrder = urlParams.get('sort_order') || 'asc';
                    const fallbackUrl = `{{ url("/database/products") }}?page=${page}&sort_by=${sortBy}&sort_order=${sortOrder}`;
                    console.log('Using fallback URL:', fallbackUrl);
                    window.location.href = fallbackUrl;
                }
            }, 1500);

        } catch (error) {
            console.error('Error updating product:', error);
            if (error.message !== 'Validation failed') {
                showError('Lỗi khi cập nhật sản phẩm: ' + error.message);
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
    function showLoading() {
        loadingIndicator.classList.remove('hidden');
    }

    function hideLoading() {
        loadingIndicator.classList.add('hidden');
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
    }

    function hideError() {
        errorMessage.classList.add('hidden');
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.classList.remove('hidden');
    }

    function hideSuccess() {
        successMessage.classList.add('hidden');
    }

    // Form submission handler
    editProductForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Clear previous errors
        clearValidationErrors();
        hideError();
        hideSuccess();

        // Disable submit button
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');

        // Get form data
        const formData = new FormData(editProductForm);

        // Update product
        await updateProduct(formData);

        // Re-enable submit button
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadProduct();
    });
</script>
@endpush
