@extends('layouts.app')

@section('title', 'Database Management')

@section('max-width', 'max-w-6xl')

@section('content')
    <!-- Header -->
    <div class="text-center mb-6 md:mb-8">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">Database Management</h1>
        <p class="text-sm sm:text-base text-gray-600">Quản lý và chỉnh sửa dữ liệu trong database</p>
    </div>

    <!-- Navigation -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6 md:mb-8">
        <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800">Quick Navigation</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <a href="{{ route('database.users') }}" class="bg-blue-500 text-white p-4 rounded-lg hover:bg-blue-600 transition duration-200 text-center">
                <div class="text-2xl font-bold">{{ $stats['users_count'] }}</div>
                <div class="text-sm sm:text-base">Users</div>
            </a>
            <a href="{{ route('database.products') }}" class="bg-green-500 text-white p-4 rounded-lg hover:bg-green-600 transition duration-200 text-center">
                <div class="text-2xl font-bold" id="products-count">
                    <span id="products-count-text" class="inline-block animate-pulse">...</span>
                </div>
                <div class="text-sm sm:text-base">Products</div>
            </a>
            <a href="{{ route('home') }}" class="bg-purple-500 text-white p-4 rounded-lg hover:bg-purple-600 transition duration-200 text-center sm:col-span-2 md:col-span-1">
                <div class="text-2xl font-bold">←</div>
                <div class="text-sm sm:text-base">Back to Home</div>
            </a>
        </div>
    </div>

    <!-- Database Tables -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
        <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800">Database Tables</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 sm:px-6 py-3 border-b border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            Table Name
                        </th>
                        <th class="px-4 sm:px-6 py-3 border-b border-gray-300 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($stats['tables'] as $table)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 sm:px-6 py-4 whitespace-no-wrap border-b border-gray-300">
                            <div class="text-sm leading-5 font-medium text-gray-900">{{ $table }}</div>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-no-wrap border-b border-gray-300 text-sm leading-5 text-gray-500">
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                                <a href="{{ route('database.table-structure', $table) }}" class="text-blue-600 hover:text-blue-900">View Structure</a>
                                @if($table === 'users')
                                    <a href="{{ route('database.users') }}" class="text-green-600 hover:text-green-900">Manage</a>
                                @elseif($table === 'product_simple')
                                    <a href="{{ route('database.products') }}" class="text-green-600 hover:text-green-900">Manage</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-semibold mb-4 text-gray-800">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('database.create-product') }}" class="bg-green-500 text-white p-4 rounded-lg hover:bg-green-600 transition duration-200 text-center">
                <div class="text-base sm:text-lg font-semibold">Add New Product</div>
                <div class="text-xs sm:text-sm opacity-90">Create a new product entry</div>
            </a>
            <a href="{{ route('database.products') }}" class="bg-blue-500 text-white p-4 rounded-lg hover:bg-blue-600 transition duration-200 text-center">
                <div class="text-base sm:text-lg font-semibold">Manage Products</div>
                <div class="text-xs sm:text-sm opacity-90">Edit, delete, or view products</div>
            </a>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // API Configuration
    const API_BASE_URL = '{{ url("/api/products") }}';
    
    // Load products count from API
    async function loadProductsCount() {
        try {
            // Get products with minimal data (per_page=1 to minimize data transfer)
            const response = await fetch(`${API_BASE_URL}?per_page=1`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            const productsCountText = document.getElementById('products-count-text');
            
            if (result.success && result.pagination && productsCountText) {
                productsCountText.textContent = result.pagination.total;
                productsCountText.classList.remove('animate-pulse');
            } else {
                // Fallback to 0 if API fails
                if (productsCountText) {
                    productsCountText.textContent = '0';
                    productsCountText.classList.remove('animate-pulse');
                }
            }
        } catch (error) {
            console.error('Error loading products count:', error);
            console.error('API_BASE_URL:', API_BASE_URL);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack
            });
            // Fallback to 0 on error
            const productsCountText = document.getElementById('products-count-text');
            if (productsCountText) {
                productsCountText.textContent = '0';
                productsCountText.classList.remove('animate-pulse');
            }
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadProductsCount();
    });
</script>
@endpush
