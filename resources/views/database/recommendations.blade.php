@extends('layouts.app')

@section('title', 'AI Recommendation System')

@section('max-width', 'max-w-7xl')

@section('content')
    <!-- Header -->
    <div class="text-center mb-6 md:mb-8">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">
            🤖 AI Recommendation System
        </h1>
        <p class="text-sm sm:text-base text-gray-600">
            Quản lý và training hệ thống gợi ý sản phẩm thông minh
        </p>
    </div>

    <!-- Navigation -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('database.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-sm">
            ← Quay lại
        </a>
        <a href="{{ route('home') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 transition duration-200 text-sm">
            🏠 Trang chủ
        </a>
    </div>

    @if(!$stats['tables_exist'])
    <!-- Migration Required Alert -->
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-bold">Chưa tạo database tables!</p>
                <p class="text-sm">Vui lòng chạy migration để tạo các bảng cần thiết:</p>
                <code class="bg-yellow-200 px-2 py-1 rounded text-sm mt-1 inline-block">php artisan migrate</code>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- Data Stats -->
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['data_stats']['total_users']) }}</div>
            <div class="text-sm text-gray-600">👥 Total Users</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="text-3xl font-bold text-green-600">{{ number_format($stats['data_stats']['total_products']) }}</div>
            <div class="text-sm text-gray-600">📦 Total Products</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="text-3xl font-bold text-purple-600">{{ number_format($stats['data_stats']['total_orders']) }}</div>
            <div class="text-sm text-gray-600">🛒 Completed Orders</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="text-3xl font-bold text-orange-600">{{ number_format($stats['data_stats']['total_reviews']) }}</div>
            <div class="text-sm text-gray-600">⭐ Total Reviews</div>
        </div>
    </div>

    <!-- Recommendation Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg shadow-md p-4 text-white">
            <div class="text-3xl font-bold">{{ number_format($stats['users_with_recommendations']) }}</div>
            <div class="text-sm opacity-90">🎯 Users with Recommendations</div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-teal-500 rounded-lg shadow-md p-4 text-white">
            <div class="text-3xl font-bold">{{ number_format($stats['products_with_recommendations']) }}</div>
            <div class="text-sm opacity-90">🔗 Products with Similar</div>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg shadow-md p-4 text-white">
            <div class="text-3xl font-bold">{{ number_format($stats['total_recommendations']) }}</div>
            <div class="text-sm opacity-90">📊 Total Recommendations</div>
        </div>
        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg shadow-md p-4 text-white">
            <div class="text-3xl font-bold">{{ number_format($stats['total_interactions']) }}</div>
            <div class="text-sm opacity-90">📈 User Interactions</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Training Controls -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                <span class="mr-2">🚀</span> Training Controls
            </h2>
            
            <div class="space-y-4">
                <!-- Train Button -->
                <button 
                    id="train-btn"
                    onclick="startTraining(false)"
                    class="w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white py-3 px-4 rounded-lg hover:from-green-600 hover:to-emerald-600 transition duration-200 font-semibold flex items-center justify-center"
                    {{ !$stats['tables_exist'] ? 'disabled' : '' }}
                >
                    <svg id="train-icon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span id="train-text">Start Training</span>
                </button>

                <!-- Force Train Button -->
                <button 
                    onclick="startTraining(true)"
                    class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-2 px-4 rounded-lg hover:from-orange-600 hover:to-red-600 transition duration-200 text-sm flex items-center justify-center"
                    {{ !$stats['tables_exist'] ? 'disabled' : '' }}
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Force Retrain
                </button>

                <!-- Clear Cache Button -->
                <button 
                    onclick="clearCache()"
                    class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200 text-sm flex items-center justify-center"
                    {{ !$stats['tables_exist'] ? 'disabled' : '' }}
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Clear Cache
                </button>
            </div>

            <!-- Training Progress -->
            <div id="training-progress" class="hidden mt-4">
                <div class="bg-blue-100 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <div class="animate-spin w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full mr-3"></div>
                        <span class="text-blue-700 font-medium">Đang training...</span>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full animate-pulse" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Training Result -->
            <div id="training-result" class="hidden mt-4"></div>
        </div>

        <!-- Last Training Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                <span class="mr-2">📊</span> Last Training
            </h2>
            
            @if($stats['last_training'])
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-2 py-1 rounded text-sm font-medium
                        @if($stats['last_training']->status == 'success') bg-green-100 text-green-800
                        @elseif($stats['last_training']->status == 'running') bg-blue-100 text-blue-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($stats['last_training']->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Time:</span>
                    <span class="text-gray-800">{{ \Carbon\Carbon::parse($stats['last_training']->created_at)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Duration:</span>
                    <span class="text-gray-800">{{ $stats['last_training']->duration_seconds ?? 'N/A' }}s</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Users Processed:</span>
                    <span class="text-gray-800">{{ number_format($stats['last_training']->users_processed) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Products Processed:</span>
                    <span class="text-gray-800">{{ number_format($stats['last_training']->products_processed) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Recommendations:</span>
                    <span class="text-gray-800">{{ number_format($stats['last_training']->recommendations_created) }}</span>
                </div>
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <p>Chưa có training nào</p>
                <p class="text-sm">Click "Start Training" để bắt đầu</p>
            </div>
            @endif
        </div>

        <!-- Algorithm Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                <span class="mr-2">🧠</span> Algorithms Used
            </h2>
            
            <div class="space-y-3">
                <div class="border-l-4 border-blue-500 pl-3">
                    <h4 class="font-medium text-gray-800">Collaborative Filtering</h4>
                    <p class="text-sm text-gray-600">Gợi ý dựa trên hành vi của người dùng tương tự</p>
                </div>
                <div class="border-l-4 border-green-500 pl-3">
                    <h4 class="font-medium text-gray-800">Content-Based</h4>
                    <p class="text-sm text-gray-600">Gợi ý dựa trên category, tags và type</p>
                </div>
                <div class="border-l-4 border-purple-500 pl-3">
                    <h4 class="font-medium text-gray-800">Hybrid</h4>
                    <p class="text-sm text-gray-600">Kết hợp cả hai phương pháp trên</p>
                </div>
                <div class="border-l-4 border-orange-500 pl-3">
                    <h4 class="font-medium text-gray-800">Popularity-Based</h4>
                    <p class="text-sm text-gray-600">Sản phẩm phổ biến cho người dùng mới</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Training History -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
            <span class="mr-2">📜</span> Training History
        </h2>
        
        @if(count($stats['training_history']) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recommendations</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($stats['training_history'] as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">#{{ $log->id }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if($log->status == 'success') bg-green-100 text-green-800
                                @elseif($log->status == 'running') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $log->duration_seconds ?? '-' }}s</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($log->users_processed) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($log->products_processed) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($log->recommendations_created) }}</td>
                        <td class="px-4 py-3 text-sm">
                            <button onclick="deleteLog({{ $log->id }})" class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <p>Chưa có lịch sử training</p>
        </div>
        @endif
    </div>

    <!-- API Endpoints Info -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
            <span class="mr-2">🔌</span> API Endpoints
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded mr-2">GET</span>
                    <code class="text-sm">/api/recommendations/for-me</code>
                </div>
                <p class="text-sm text-gray-600">Lấy recommendations cho user hiện tại (cần auth)</p>
            </div>
            <div class="border rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded mr-2">GET</span>
                    <code class="text-sm">/api/recommendations/products/{'{id}'}/similar</code>
                </div>
                <p class="text-sm text-gray-600">Lấy các sản phẩm tương tự (public)</p>
            </div>
            <div class="border rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded mr-2">POST</span>
                    <code class="text-sm">/api/recommendations/interaction</code>
                </div>
                <p class="text-sm text-gray-600">Ghi nhận tương tác user (cần auth)</p>
            </div>
            <div class="border rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded mr-2">CLI</span>
                    <code class="text-sm">php artisan recommendation:train</code>
                </div>
                <p class="text-sm text-gray-600">Chạy training qua command line</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const BASE_URL = '{{ url("") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    async function startTraining(force = false) {
        const trainBtn = document.getElementById('train-btn');
        const trainIcon = document.getElementById('train-icon');
        const trainText = document.getElementById('train-text');
        const progressDiv = document.getElementById('training-progress');
        const resultDiv = document.getElementById('training-result');

        // Show loading
        trainBtn.disabled = true;
        trainIcon.classList.add('animate-spin');
        trainText.textContent = 'Đang training...';
        progressDiv.classList.remove('hidden');
        resultDiv.classList.add('hidden');

        try {
            const response = await fetch(`${BASE_URL}/database/recommendations/train`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ force: force })
            });

            const result = await response.json();

            progressDiv.classList.add('hidden');
            resultDiv.classList.remove('hidden');

            if (result.success) {
                resultDiv.innerHTML = `
                    <div class="bg-green-100 rounded-lg p-4">
                        <div class="flex items-center text-green-700 font-medium mb-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Training thành công!
                        </div>
                        <div class="text-sm text-green-600 space-y-1">
                            <p>👥 Users: ${result.data.users_processed}</p>
                            <p>📦 Products: ${result.data.products_processed}</p>
                            <p>🎯 Recommendations: ${result.data.recommendations_created}</p>
                            <p>⏱️ Duration: ${result.data.duration}s</p>
                        </div>
                    </div>
                `;
                // Reload page after 2 seconds to show updated stats
                setTimeout(() => location.reload(), 2000);
            } else {
                resultDiv.innerHTML = `
                    <div class="bg-red-100 rounded-lg p-4">
                        <div class="flex items-center text-red-700 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            ${result.message}
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            progressDiv.classList.add('hidden');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = `
                <div class="bg-red-100 rounded-lg p-4">
                    <div class="flex items-center text-red-700 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Lỗi: ${error.message}
                    </div>
                </div>
            `;
        } finally {
            trainBtn.disabled = false;
            trainIcon.classList.remove('animate-spin');
            trainText.textContent = 'Start Training';
        }
    }

    async function clearCache() {
        if (!confirm('Bạn có chắc muốn xóa cache?')) return;

        try {
            const response = await fetch(`${BASE_URL}/database/recommendations/clear-cache`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });

            const result = await response.json();
            alert(result.message);
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }

    async function deleteLog(id) {
        if (!confirm('Bạn có chắc muốn xóa log này?')) return;

        try {
            const response = await fetch(`${BASE_URL}/database/recommendations/logs/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
        }
    }
</script>
@endpush
