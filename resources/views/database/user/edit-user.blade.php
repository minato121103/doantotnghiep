@extends('layouts.app')

@section('title', 'Edit User')

@section('max-width', 'max-w-4xl')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 md:mb-8 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">Edit User</h1>
            <p class="text-sm sm:text-base text-gray-600">Chỉnh sửa thông tin người dùng (API)</p>
        </div>
        <a href="{{ url('/database/users') }}" id="back-link" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
            ← Back to Users
        </a>
    </div>

    <!-- Loading Indicator -->
    <div id="loading-indicator" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Đang tải dữ liệu người dùng...</span>
        </div>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Success Message -->
    <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden"></div>

    <!-- Edit Form -->
    <div id="edit-form-container" class="bg-white rounded-lg shadow-md p-4 sm:p-6 hidden">
        <form id="edit-user-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                        <input type="text" id="name" name="name" required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-name" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-email" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" minlength="8"
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                               placeholder="Leave blank to keep current password">
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Leave blank to keep current password. Minimum 8 characters if changing.</p>
                        <p id="error-password" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" id="phone" name="phone" 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-phone" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Avatar URL</label>
                        <input type="url" id="avatar" name="avatar" 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-avatar" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Additional Information</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="role" name="role" 
                                class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                            <option value="buyer">Buyer</option>
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                        </select>
                        <p id="error-role" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" 
                                class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="banned">Banned</option>
                        </select>
                        <p id="error-status" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Balance</label>
                        <input type="number" id="balance" name="balance" min="0" step="0.01"
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-balance" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input type="text" id="address" name="address" 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-address" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Birthday</label>
                        <input type="date" id="birthday" name="birthday" 
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <p id="error-birthday" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select id="gender" name="gender" 
                                class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                            <option value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <p id="error-gender" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row justify-end gap-2 sm:gap-4">
                <a href="{{ url('/database/users') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-center text-sm sm:text-base">
                    Cancel
                </a>
                <button type="submit" id="submit-btn" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200 text-sm sm:text-base">
                    <span id="submit-text">Update User</span>
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

@push('scripts')
<script>
    // API Configuration
    const API_BASE_URL = '{{ url("/api/users") }}';
    
    // Get user ID from URL (format: /database/users/{id}/edit)
    const pathParts = window.location.pathname.split('/').filter(part => part);
    const userId = pathParts[pathParts.length - 2]; // Get second to last part (id) before 'edit'
    
    // Validate user ID
    if (!userId || isNaN(userId)) {
        console.error('Invalid user ID:', userId);
        document.getElementById('error-message').textContent = 'Invalid user ID';
        document.getElementById('error-message').classList.remove('hidden');
        document.getElementById('loading-indicator').classList.add('hidden');
    }
    
    // DOM Elements
    const loadingIndicator = document.getElementById('loading-indicator');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const editFormContainer = document.getElementById('edit-form-container');
    const editUserForm = document.getElementById('edit-user-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    const backLink = document.getElementById('back-link');

    // Load user data from API
    async function loadUser() {
        // Check if userId is valid
        if (!userId || isNaN(userId)) {
            showError('Invalid user ID');
            hideLoading();
            return;
        }

        try {
            showLoading();
            hideError();

            console.log('Loading user with ID:', userId);
            console.log('API URL:', `${API_BASE_URL}/${userId}`);
            
            const response = await fetch(`${API_BASE_URL}/${userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Get content type first
            const contentType = response.headers.get('content-type') || '';
            console.log('Response Content-Type:', contentType);
            console.log('Response Status:', response.status);

            // Check if response is JSON
            if (!contentType.includes('application/json')) {
                // Response is not JSON, likely an HTML error page
                const text = await response.text();
                console.error('Non-JSON response received:', text.substring(0, 500));
                
                if (response.status === 404) {
                    throw new Error('User not found. Please check if the user ID is correct.');
                } else if (response.status === 500) {
                    throw new Error('Server error. Please check the server logs.');
                } else {
                    throw new Error(`Server returned HTML instead of JSON (Status: ${response.status}). This usually means the API route is not found or there's a server error.`);
                }
            }

            // Parse JSON response
            const result = await response.json();
            console.log('API Response:', result);

            // Check response status
            if (!response.ok) {
                throw new Error(result.message || `HTTP ${response.status}: ${response.statusText}`);
            }

            if (!result.success) {
                throw new Error(result.message || 'Failed to load user');
            }

            const user = result.data;
            
            // Populate form fields
            document.getElementById('name').value = user.name || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('phone').value = user.phone || '';
            document.getElementById('avatar').value = user.avatar || '';
            document.getElementById('role').value = user.role || 'buyer';
            document.getElementById('status').value = user.status || 'active';
            document.getElementById('balance').value = user.balance || 0;
            document.getElementById('address').value = user.address || '';
            document.getElementById('birthday').value = user.birthday ? user.birthday.split('T')[0] : '';
            document.getElementById('gender').value = user.gender || '';

            // Update back link with page parameter
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page');
            if (page) {
                backLink.href = '{{ url("/database/users") }}?page=' + page;
            }

            hideLoading();
            editFormContainer.classList.remove('hidden');
        } catch (error) {
            console.error('Error loading user:', error);
            showError('Lỗi khi tải dữ liệu người dùng: ' + error.message);
            hideLoading();
        }
    }

    // Update user via API
    async function updateUser(formData) {
        try {
            // Convert FormData to object
            const data = {};
            formData.forEach((value, key) => {
                if (key === 'password' && !value) {
                    // Skip empty password
                } else if (key === 'balance') {
                    data[key] = value ? parseFloat(value) : 0;
                } else if (key === 'birthday' && !value) {
                    // Skip empty birthday
                } else if (key === 'gender' && !value) {
                    // Skip empty gender
                } else {
                    data[key] = value || null;
                }
            });

            const response = await fetch(`${API_BASE_URL}/${userId}`, {
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
                throw new Error(result.message || 'Failed to update user');
            }

            // Success
            showSuccess('Cập nhật người dùng thành công! Đang chuyển hướng...');
            clearValidationErrors();
            
            // Redirect after 1.5 seconds
            setTimeout(() => {
                const urlParams = new URLSearchParams(window.location.search);
                const page = urlParams.get('page');
                if (page) {
                    window.location.href = '{{ url("/database/users") }}?page=' + page;
                } else {
                    window.location.href = '{{ url("/database/users") }}';
                }
            }, 1500);

        } catch (error) {
            console.error('Error updating user:', error);
            if (error.message !== 'Validation failed') {
                showError('Lỗi khi cập nhật người dùng: ' + error.message);
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

        document.querySelectorAll('input, select').forEach(element => {
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
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hideError() {
        errorMessage.classList.add('hidden');
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hideSuccess() {
        successMessage.classList.add('hidden');
    }

    // Form submission handler
    editUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        clearValidationErrors();
        hideError();
        hideSuccess();

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');

        const formData = new FormData(editUserForm);
        await updateUser(formData);

        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadUser();
    });
</script>
@endpush

