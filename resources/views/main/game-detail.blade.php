@extends('layouts.main')

@section('title', 'Chi tiết sản phẩm')

@section('content')
    <!-- Breadcrumb -->
    <section class="pt-36 pb-4 bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30">
        <div class="container mx-auto px-4">
            <nav class="flex items-center text-sm text-slate-500" id="breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-game-accent transition-colors">Trang chủ</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ url('/store') }}" class="hover:text-game-accent transition-colors">Khám phá</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-800 font-medium" id="breadcrumb-title">Đang tải...</span>
            </nav>
        </div>
    </section>

    <!-- Main Product Section -->
    <section class="py-8 bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30">
        <div class="container mx-auto px-4">
            <!-- Loading State -->
            <div id="product-loading" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="animate-pulse">
                    <div class="bg-slate-200 rounded-2xl h-96"></div>
                </div>
                <div class="animate-pulse space-y-4">
                    <div class="bg-slate-200 h-8 w-3/4 rounded"></div>
                    <div class="bg-slate-200 h-6 w-1/2 rounded"></div>
                    <div class="bg-slate-200 h-4 w-full rounded"></div>
                    <div class="bg-slate-200 h-4 w-2/3 rounded"></div>
                    <div class="bg-slate-200 h-12 w-1/3 rounded"></div>
                </div>
            </div>

            <!-- Product Content -->
            <div id="product-content" class="hidden grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                <!-- Left: Product Image -->
                <div class="space-y-4">
                    <div class="relative bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-xl">
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 z-10 flex flex-col gap-2" id="product-badges"></div>
                        
                        <!-- Main Image -->
                        <img id="product-image" src="" alt="" 
                             class="w-full h-auto max-h-[500px] object-contain bg-gradient-to-br from-slate-100 to-slate-50">
                    </div>
                    
                    <!-- Image Gallery Thumbnails (if multiple images) -->
                    <div id="image-gallery" class="hidden grid grid-cols-4 gap-2">
                        <!-- Thumbnails will be loaded here -->
                    </div>
                </div>

                <!-- Right: Product Info -->
                <div class="space-y-6">
                    <!-- Category & Title -->
                    <div>
                        <!-- Dòng 1: Thể loại -->
                        <div class="flex items-center gap-2 mb-1">
                            <span id="product-category" class="px-3 py-1 bg-game-accent/10 text-game-accent text-sm font-medium rounded-full"></span>
                        </div>
                        <!-- Dòng 2: Tags -->
                        <div class="flex items-center gap-2 mb-3 text-slate-600 text-sm flex-wrap" id="product-tags-wrapper">
                            <span id="product-tags-main" class="flex flex-wrap gap-1"></span>
                            <button id="product-tags-more-btn"
                                    type="button"
                                    class="hidden px-2 py-0.5 text-xs font-medium rounded-full border border-slate-300 hover:border-game-accent hover:text-game-accent transition-colors">
                                +0
                            </button>
                        </div>
                        <h1 id="product-title" class="font-display text-2xl md:text-3xl lg:text-4xl font-bold text-slate-800 leading-tight"></h1>
                    </div>

                    <!-- Rating & Stats -->
                    <div class="flex flex-wrap items-center gap-4 pb-4 border-b border-slate-200">
                        <div class="flex items-center gap-1">
                            <div id="product-stars" class="flex text-yellow-400"></div>
                            <span id="product-rating" class="text-slate-600 font-medium ml-1"></span>
                        </div>
                        <div class="flex items-center gap-1 text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span id="product-views">0</span> lượt xem
                        </div>
                        <div class="flex items-center gap-1 text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span id="product-sold">0</span> đã bán
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div id="product-short-desc" class="prose prose-slate max-w-none text-slate-600 leading-relaxed"></div>

                    <!-- Price -->
                    <div class="bg-gradient-to-r from-slate-50 to-indigo-50/50 rounded-xl p-6 border border-slate-200">
                        <div class="flex items-end gap-3">
                            <span id="product-original-price" class="text-slate-400 line-through text-lg hidden"></span>
                            <span id="product-current-price" class="text-game-accent font-bold text-3xl md:text-4xl"></span>
                            <span id="product-discount" class="px-2 py-1 bg-game-green text-white text-sm font-bold rounded hidden"></span>
                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white rounded-xl p-4 border border-slate-200 text-center">
                            <div class="text-game-accent mb-2">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-xs text-slate-500">Giao hàng</div>
                            <div class="text-sm font-semibold text-slate-800">5-15 phút</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-slate-200 text-center">
                            <div class="text-game-green mb-2">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="text-xs text-slate-500">Bảo hành</div>
                            <div class="text-sm font-semibold text-slate-800">Trọn đời</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-slate-200 text-center">
                            <div class="text-game-purple mb-2">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="text-xs text-slate-500">Gửi qua</div>
                            <div class="text-sm font-semibold text-slate-800">Email</div>
                        </div>
                    </div>

                    <!-- Quantity & Add to Cart -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-white">
                            <button id="qty-minus" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number" id="qty-input" value="1" min="1" 
                                   class="w-16 h-12 text-center border-x border-slate-200 font-semibold text-slate-800 focus:outline-none">
                            <button id="qty-plus" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                        <button id="add-to-cart-btn" 
                                class="flex-1 px-8 py-4 bg-gradient-to-r from-game-accent to-game-purple text-white font-bold rounded-xl hover:opacity-90 transition-all glow-effect flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Thêm vào giỏ hàng
                        </button>
                        <button id="buy-now-btn" 
                                class="px-8 py-4 bg-game-orange text-white font-bold rounded-xl hover:bg-orange-600 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Mua ngay
                        </button>
                    </div>

                    <!-- Wishlist & Share -->
                    <div class="flex items-center gap-4 pt-4 border-t border-slate-200">
                        <button class="flex items-center gap-2 text-slate-600 hover:text-game-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Yêu thích
                        </button>
                        <button class="flex items-center gap-2 text-slate-600 hover:text-game-accent transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Chia sẻ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Description Tabs -->
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4">
            <!-- Tabs -->
            <div class="flex border-b border-slate-200 mb-8">
                <button class="tab-btn active px-6 py-4 font-heading font-semibold text-lg border-b-2 border-game-accent text-game-accent transition-colors" data-tab="description">
                    Mô tả sản phẩm
                </button>
                <button class="tab-btn px-6 py-4 font-heading font-semibold text-lg border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors" data-tab="reviews">
                    Đánh giá (<span id="review-count">0</span>)
                </button>
            </div>

            <!-- Tab Content -->
            <div id="tab-description" class="tab-content">
                <div id="product-detail-desc-wrapper" class="relative">
                    <div id="product-detail-desc" class="prose prose-slate max-w-none overflow-hidden transition-all duration-300" style="max-height: 600px;">
                        <!-- Detail description will be loaded here -->
                    </div>
                    <!-- Gradient overlay khi thu gọn - giảm độ mờ -->
                    <div id="description-fade" class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white via-white/30 to-transparent pointer-events-none hidden"></div>
                    <!-- Nút Xem thêm / Thu gọn -->
                    <div id="description-toggle-container" class="text-center mt-4 hidden">
                        <button id="description-toggle-btn" class="px-6 py-2 bg-game-accent text-white rounded-full hover:bg-game-accent-hover transition-colors font-medium shadow-sm">
                            Xem thêm
                        </button>
                    </div>
                </div>
            </div>

            <div id="tab-reviews" class="tab-content hidden">
                <!-- Reviews Section -->
                <div class="space-y-6">
                    <!-- Write Review Form (only for authenticated users who purchased) -->
                    <!-- Review form container -->
                    <div id="review-form-container" class="bg-white rounded-2xl border border-slate-200 p-6 hidden">
                        <h3 class="font-heading text-xl font-bold text-slate-800 mb-4">Viết đánh giá</h3>
                        <form id="review-form">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Đánh giá của bạn</label>
                                <div class="flex items-center gap-2" id="rating-input">
                                    <button type="button" class="rating-star" data-rating="1">
                                        <svg class="w-8 h-8 text-slate-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="rating-star" data-rating="2">
                                        <svg class="w-8 h-8 text-slate-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="rating-star" data-rating="3">
                                        <svg class="w-8 h-8 text-slate-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="rating-star" data-rating="4">
                                        <svg class="w-8 h-8 text-slate-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="rating-star" data-rating="5">
                                        <svg class="w-8 h-8 text-slate-300 hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </div>
                                <input type="hidden" id="selected-rating" name="rating" required>
                                <span id="rating-text" class="text-sm text-slate-500 mt-1 block"></span>
                            </div>
                            <div class="mb-4">
                                <label for="review-comment" class="block text-sm font-medium text-slate-700 mb-2">Nhận xét</label>
                                <textarea id="review-comment" name="comment" rows="4" 
                                          class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-game-accent focus:ring-1 focus:ring-game-accent/30 resize-y"
                                          placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                            </div>
                            <input type="hidden" id="review-order-id" name="order_id" required>
                            <div id="review-error" class="hidden mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"></div>
                            <div class="flex justify-end gap-3">
                                <button type="button" id="cancel-review-btn" class="px-6 py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                                    Hủy
                                </button>
                                <button type="submit" id="submit-review-btn" class="px-6 py-2 bg-game-accent text-white font-semibold rounded-lg hover:bg-game-accent-hover transition-colors">
                                    Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Already reviewed message -->
                    <div id="review-already-container" class="bg-slate-50 rounded-2xl border border-slate-200 p-6 hidden">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-game-green flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3 class="font-heading text-lg font-semibold text-slate-800">Bạn đã đánh giá sản phẩm này</h3>
                                <p class="text-sm text-slate-600 mt-1">Cảm ơn bạn đã đánh giá sản phẩm.</p>
                            </div>
                        </div>
                    </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Rating Summary -->
                    <div class="bg-slate-50 rounded-2xl p-6">
                        <div class="text-center">
                            <div id="avg-rating-display" class="text-5xl font-bold text-slate-800">0</div>
                            <div id="avg-stars-display" class="flex justify-center my-2 text-yellow-400"></div>
                            <div class="text-slate-500"><span id="total-reviews">0</span> đánh giá</div>
                        </div>
                        <div class="mt-6 space-y-2" id="rating-bars">
                            <!-- Rating bars will be generated here -->
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="lg:col-span-2 space-y-6" id="reviews-list">
                        <div id="reviews-loading" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-game-accent"></div>
                            <p class="text-slate-500 mt-2">Đang tải đánh giá...</p>
                    </div>
                        <div id="reviews-empty" class="hidden text-slate-500 text-center py-8">Chưa có đánh giá nào.</div>
                </div>
            </div>
            </div>
        </div>
    </section>

    <!-- Discussion Section (Moved below reviews) -->
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto space-y-4">
                <!-- Discussion Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="font-heading text-xl font-bold text-slate-800">Thảo luận</h4>
                        <p class="text-slate-500 text-sm">Nơi người dùng trao đổi, đặt câu hỏi và chia sẻ kinh nghiệm về game này.</p>
                    </div>
                    </div>

                <!-- New Comment Form -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div id="discussion-avatar"
                                 class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-sm">
                                U
                    </div>
                        </div>
                        <!-- Editor -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                                <div id="discussion-user-label">
                                    Tham gia bình luận ngay
                                </div>
                            </div>
                            <div class="border border-slate-200 rounded-lg overflow-hidden">
                                <!-- Toolbar -->
                                <div class="flex items-center gap-1 bg-slate-50 px-2 py-1 border-b border-slate-200 text-xs">
                                    <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded" data-format="**">
                                        <span class="font-bold">b</span>
                                    </button>
                                    <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded" data-format="_">
                                        <span class="italic">i</span>
                                    </button>
                                    <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded" data-format="[link](url)">
                                        link
                                    </button>
                                    <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded" data-format="> ">
                                        b-quote
                                    </button>
                                    <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded" data-format="- ">
                                        ul
                                    </button>
                                    <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded" data-format="1. ">
                                        ol
                                    </button>
                                    <button type="button" class="px-2 py-1 hover:bg-slate-200 rounded" data-format="`code`">
                                        code
                                    </button>
                                </div>
                                <!-- Form -->
                                <form id="discussion-form" class="space-y-0">
                                    <textarea id="discussion-content"
                                              rows="3"
                                              class="w-full px-3 py-2 text-sm border-0 focus:outline-none focus:ring-0 resize-y"
                                              placeholder="Tham gia bình luận ngay..."
                                              required></textarea>
                                    <div class="flex items-center justify-between px-3 py-2 bg-slate-50 border-t border-slate-200">
                                        <p class="text-[11px] text-slate-400 hidden sm:block">
                                            Vui lòng trao đổi lịch sự, không spam hoặc quảng cáo.
                                        </p>
                                        <button type="submit"
                                                id="discussion-submit-btn"
                                                class="px-4 py-2 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition-colors">
                                            Đăng bình luận
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discussion List -->
                <div id="discussion-list-wrapper" class="space-y-3">
                    <div class="flex items-center justify-between mt-4 border-b border-slate-200 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-heading text-sm font-semibold text-slate-800">
                                <span id="discussion-count">0</span> bình luận
                            </span>
                        </div>
                        <div class="flex items-center gap-1 text-xs text-slate-500">
                            <span>Mới nhất</span>
                        </div>
                    </div>
                    <div id="discussion-empty" class="text-center py-6 text-slate-500 text-sm">
                        Chưa có thảo luận nào. Hãy là người đầu tiên đặt câu hỏi!
                    </div>
                    <div id="discussion-list" class="space-y-3 hidden"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="py-10 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-800">Sản phẩm tương tự</h2>
                <a href="{{ url('/store') }}" class="flex items-center text-game-accent hover:text-game-accent-hover transition-colors">
                    Xem tất cả
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4" id="related-products">
                <!-- Related products will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Modal Mua Ngay -->
    <div id="buy-now-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-slate-800">Xác nhận mua hàng</h3>
                    <button id="close-buy-modal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div id="buy-modal-content">
                    <!-- Product Info -->
                    <div class="flex gap-4 mb-6">
                        <img id="buy-modal-image" src="" alt="" class="w-20 h-20 object-cover rounded-lg border border-slate-200">
                        <div class="flex-1">
                            <h4 id="buy-modal-title" class="font-semibold text-slate-800 mb-1"></h4>
                            <p id="buy-modal-price" class="text-game-accent font-bold text-lg"></p>
                        </div>
                    </div>
                    
                    <!-- Quantity -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Số lượng</label>
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-white">
                            <button id="buy-qty-minus" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number" id="buy-qty-input" value="1" min="1" max="10"
                                   class="w-16 h-12 text-center border-x border-slate-200 font-semibold text-slate-800 focus:outline-none">
                            <button id="buy-qty-plus" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Total -->
                    <div class="bg-slate-50 rounded-xl p-4 mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-slate-600">Số lượng:</span>
                            <span id="buy-modal-quantity" class="font-semibold text-slate-800">1</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                            <span class="text-lg font-semibold text-slate-800">Tổng tiền:</span>
                            <span id="buy-modal-total" class="text-2xl font-bold text-game-accent"></span>
                        </div>
                    </div>
                    
                    <!-- Balance Check -->
                    <div id="buy-balance-info" class="mb-6 p-3 rounded-lg bg-blue-50 border border-blue-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Số dư hiện tại:</span>
                            <span id="buy-current-balance" class="font-semibold text-slate-800">0 đ</span>
                        </div>
                        <div id="buy-balance-after" class="flex items-center justify-between text-sm mt-2 pt-2 border-t border-blue-200">
                            <span class="text-slate-600">Số dư sau khi mua:</span>
                            <span id="buy-remaining-balance" class="font-semibold text-slate-800">0 đ</span>
                        </div>
                    </div>
                    
                    <!-- Error Message -->
                    <div id="buy-error-message" class="hidden mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"></div>
                    
                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button id="cancel-buy-btn" class="flex-1 px-4 py-3 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors font-medium">
                            Hủy
                        </button>
                        <button id="confirm-buy-btn" class="flex-1 px-4 py-3 bg-game-orange text-white rounded-xl hover:bg-orange-600 transition-colors font-semibold">
                            Xác nhận mua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thông báo sau khi mua -->
    <div id="buy-success-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6 text-center">
                <div class="mb-4">
                    <div class="w-16 h-16 bg-game-green/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-game-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Mua hàng thành công!</h3>
                    <p class="text-slate-600">Đơn hàng của bạn đã được tạo thành công.</p>
                </div>
                
                <div class="bg-slate-50 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-slate-600">Mã đơn hàng:</span>
                        <span id="success-order-code" class="font-semibold text-slate-800"></span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                        <span class="text-slate-600">Tổng tiền:</span>
                        <span id="success-order-total" class="font-bold text-game-accent"></span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button id="continue-shopping-btn" class="flex-1 px-4 py-3 border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors font-medium">
                        Mua tiếp
                    </button>
                    <button id="view-order-btn" class="flex-1 px-4 py-3 bg-game-accent text-white rounded-xl hover:bg-game-accent-hover transition-colors font-semibold">
                        Xem đơn hàng
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .prose h2 { @apply text-xl font-bold text-slate-800 mt-6 mb-3; }
    .prose h3 { @apply text-lg font-semibold text-slate-800 mt-4 mb-2; }
    .prose p { @apply text-slate-600 mb-4 leading-relaxed; }
    .prose ul { @apply list-disc list-inside text-slate-600 mb-4 space-y-1; }
    .prose li { @apply text-slate-600; }
    .prose strong { @apply text-slate-800 font-semibold; }
    .prose img { @apply w-full rounded-xl shadow-md my-6; }
    .prose br { @apply block mb-2; }
    /* Đảm bảo các đoạn văn có khoảng cách */
    #product-detail-desc p {
        margin-bottom: 1rem;
        line-height: 1.75;
    }
    /* Đảm bảo hình ảnh hiển thị đúng */
    #product-detail-desc img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1.5rem 0;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    /* Style cho phần mô tả với chức năng Xem thêm */
    #product-detail-desc-wrapper {
        position: relative;
    }
    #product-detail-desc {
        transition: max-height 0.3s ease-in-out;
        overflow: hidden;
    }
    #description-fade {
        transition: opacity 0.3s ease-in-out;
        opacity: 0.6; /* Giảm độ mờ */
    }
    #description-toggle-btn {
        transition: all 0.2s ease-in-out;
    }
</style>
@endpush

@push('scripts')
<script>
    // Cấu hình URL API
    const BASE_URL = '{{ url("/") }}';
    const GAME_BASE_URL = '{{ url("/game") }}';
    const API_BASE_URL = '{{ url("/api/products") }}';
    const REVIEWS_API_BASE_URL = '{{ url("/api/reviews") }}';
    const ORDERS_API_BASE_URL = '{{ url("/api/orders") }}';
    const DISCUSSION_API_BASE_URL = '{{ url("/api/discussions") }}';
    
    const gameId = {{ $gameId }};
    let productData = null;
    let discussionData = [];

    // Hàm tiện ích chung
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function extractPrices(priceStr) {
        if (!priceStr) return { original: null, current: null };
        const priceRegex = /[\d.,]+\s*[₫đ]/gi;
        const prices = priceStr.match(priceRegex);
        if (!prices || prices.length === 0) {
            return { original: null, current: priceStr.replace(/Giá gốc là:|Giá hiện tại là:/gi, '').trim() };
        }
        if (prices.length === 1) {
            return { original: null, current: prices[0].trim() };
        }
        return { original: prices[0].trim(), current: prices[prices.length - 1].trim() };
    }

    // Extract price number from price string
    function extractPriceNumber(priceStr) {
        if (!priceStr) return 0;
        const priceRegex = /[\d.,]+\s*[₫đ]/gi;
        const prices = priceStr.match(priceRegex);
        if (!prices || prices.length === 0) {
            const numberRegex = /[\d.,]+/g;
            const numbers = priceStr.match(numberRegex);
            if (numbers && numbers.length > 0) {
                const lastNumber = numbers[numbers.length - 1];
                return parseFloat(lastNumber.replace(/\./g, '').replace(',', '.')) || 0;
            }
            return 0;
        }
        const lastPrice = prices[prices.length - 1].trim();
        const priceNumber = lastPrice.replace(/[₫đ\s]/gi, '').replace(/\./g, '').replace(',', '.');
        return parseFloat(priceNumber) || 0;
    }

    // Format price (number to currency string)
    function formatPrice(amount) {
        if (!amount && amount !== 0) return '0đ';
        const numAmount = typeof amount === 'string' ? parseFloat(amount.replace(/[^\d.,]/g, '').replace(/\./g, '').replace(',', '.')) : parseFloat(amount);
        if (isNaN(numAmount)) return '0đ';
        return new Intl.NumberFormat('vi-VN').format(numAmount) + 'đ';
    }

    function calculateDiscount(priceStr) {
        if (!priceStr) return 0;
        const priceRegex = /[\d.,]+/g;
        const prices = priceStr.match(priceRegex);
        if (!prices || prices.length < 2) return 0;
        const original = parseFloat(prices[0].replace(/\./g, '').replace(',', '.'));
        const current = parseFloat(prices[prices.length - 1].replace(/\./g, '').replace(',', '.'));
        if (original <= 0 || current >= original) return 0;
        return Math.round((1 - current / original) * 100);
    }

    // --------- Discussion (Thảo luận) ----------

    // Helper functions for like management (using localStorage)
    function getLikedComments() {
        try {
            const liked = localStorage.getItem(`liked_comments_${gameId}`);
            return liked ? JSON.parse(liked) : [];
        } catch (e) {
            return [];
        }
    }

    function isCommentLiked(commentId) {
        const liked = getLikedComments();
        return liked.includes(commentId);
    }

    function toggleLikeComment(commentId) {
        const liked = getLikedComments();
        const index = liked.indexOf(commentId);
        if (index > -1) {
            liked.splice(index, 1);
        } else {
            liked.push(commentId);
        }
        localStorage.setItem(`liked_comments_${gameId}`, JSON.stringify(liked));
        return index === -1; // true if liked, false if unliked
    }

    async function loadDiscussionData() {
        try {
            const response = await fetch(`${DISCUSSION_API_BASE_URL}/product/${gameId}?per_page=100`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            if (result.success && result.data) {
                return result.data;
            }
            return [];
        } catch (e) {
            console.error('Error loading discussion data:', e);
            return [];
        }
    }

    async function saveDiscussion(content, parentId = null, authorName = null) {
        try {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const body = {
                product_simple_id: gameId,
                content: content,
            };

            if (parentId) {
                body.parent_id = parentId;
            }

            if (!token && authorName) {
                body.author_name = authorName;
            }

            const response = await fetch(`${DISCUSSION_API_BASE_URL}`, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(body)
            });

            const result = await response.json();
            
            if (result.success) {
                return result.data;
            } else {
                throw new Error(result.message || 'Không thể gửi bình luận');
            }
        } catch (e) {
            console.error('Error saving discussion:', e);
            throw e;
        }
    }

    async function renderDiscussion() {
        const listEl = document.getElementById('discussion-list');
        const emptyEl = document.getElementById('discussion-empty');
        const countEl = document.getElementById('discussion-count');
        if (!listEl || !emptyEl) return;

        // Show loading state
        listEl.classList.add('hidden');
        emptyEl.classList.remove('hidden');
        emptyEl.innerHTML = '<div class="text-center py-4"><div class="inline-block animate-spin w-6 h-6 border-2 border-game-accent border-t-transparent rounded-full"></div><p class="text-slate-500 text-sm mt-2">Đang tải...</p></div>';

        discussionData = await loadDiscussionData();

        if (!discussionData.length) {
            emptyEl.classList.remove('hidden');
            emptyEl.innerHTML = '<div class="text-center py-6 text-slate-500 text-sm">Chưa có thảo luận nào. Hãy là người đầu tiên đặt câu hỏi!</div>';
            listEl.classList.add('hidden');
            listEl.innerHTML = '';
            if (countEl) countEl.textContent = '0';
            return;
        }

        emptyEl.classList.add('hidden');
        listEl.classList.remove('hidden');

        // Đếm tổng số comment bao gồm cả replies
        const totalComments = discussionData.reduce((sum, item) => {
            return sum + 1 + (item.replies ? item.replies.length : 0);
        }, 0);
        if (countEl) countEl.textContent = totalComments.toString();

        listEl.innerHTML = discussionData
            .map((item) => {
                const name = escapeHtml(item.display_name || item.user?.name || item.author_name || 'Người dùng ẩn danh');
                const content = escapeHtml(item.content || '');
                const date = item.created_at
                    ? new Date(item.created_at).toLocaleString('vi-VN')
                    : '';
                const replies = item.replies || [];
                const commentId = item.id;
                const liked = isCommentLiked(commentId);
                const likeCount = item.like_count || 0;

                return `
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200" data-comment-id="${commentId}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-game-accent/10 flex items-center justify-center text-game-accent text-sm font-bold">
                                    ${item.avatar_initial || name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">${name}</p>
                                    <p class="text-[11px] text-slate-400">${date}</p>
                                </div>
                            </div>
                            <button type="button" 
                                    class="reply-btn text-xs text-game-accent hover:text-game-accent-hover font-medium transition-colors"
                                    data-comment-id="${commentId}">
                                Trả lời
                            </button>
                        </div>
                        <p class="text-sm text-slate-700 whitespace-pre-line mb-3">${content}</p>
                        
                        <!-- Like and Reply Actions -->
                        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-slate-200">
                            <button type="button" 
                                    class="like-btn flex items-center gap-2 text-xs transition-colors ${liked ? 'text-game-accent' : 'text-slate-600 hover:text-game-accent'}"
                                    data-comment-id="${commentId}"
                                    data-liked="${liked}">
                                <svg class="w-4 h-4 ${liked ? 'fill-current' : ''}" fill="${liked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span class="like-count">${likeCount}</span>
                            </button>
                            <button type="button" 
                                    class="reply-btn text-xs text-slate-600 hover:text-game-accent font-medium transition-colors"
                                    data-comment-id="${commentId}">
                                Trả lời
                            </button>
                        </div>
                        
                        <!-- Reply Form (hidden by default) -->
                        <div id="reply-form-${commentId}" class="hidden mt-3 pt-3 border-t border-slate-200">
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <textarea id="reply-content-${commentId}"
                                              rows="2"
                                              class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-game-accent focus:ring-1 focus:ring-game-accent/30 resize-y"
                                              placeholder="Viết phản hồi..."></textarea>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <button type="button"
                                            class="reply-submit-btn px-4 py-2 bg-game-accent text-white text-sm font-semibold rounded-lg hover:bg-game-accent-hover transition-colors"
                                            data-comment-id="${commentId}">
                                        Gửi
                                    </button>
                                    <button type="button"
                                            class="reply-cancel-btn px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-100 transition-colors"
                                            data-comment-id="${commentId}">
                                        Hủy
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Replies List -->
                        ${replies.length > 0 ? `
                            <div class="mt-3 pt-3 border-t border-slate-200 space-y-3">
                                ${replies.map((reply) => {
                                    const replyName = escapeHtml(reply.display_name || reply.user?.name || reply.author_name || 'Người dùng ẩn danh');
                                    const replyContent = escapeHtml(reply.content || '');
                                    const replyDate = reply.created_at
                                        ? new Date(reply.created_at).toLocaleString('vi-VN')
                                        : '';
                                    const replyAvatar = reply.avatar_initial || replyName.charAt(0).toUpperCase();
                                    const replyId = reply.id;
                                    const replyLiked = isCommentLiked(replyId);
                                    const replyLikeCount = reply.like_count || 0;
                                    return `
                                        <div class="flex gap-3 pl-4 border-l-2 border-slate-200" data-reply-id="${replyId}">
                                            <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-xs font-bold flex-shrink-0">
                                                ${replyAvatar}
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <p class="text-xs font-semibold text-slate-800">${replyName}</p>
                                                    <p class="text-[10px] text-slate-400">${replyDate}</p>
                                                </div>
                                                <p class="text-xs text-slate-700 whitespace-pre-line mb-2">${replyContent}</p>
                                                <!-- Like button for reply -->
                                                <button type="button" 
                                                        class="like-btn flex items-center gap-1.5 text-[10px] transition-colors ${replyLiked ? 'text-game-accent' : 'text-slate-500 hover:text-game-accent'}"
                                                        data-comment-id="${replyId}"
                                                        data-liked="${replyLiked}">
                                                    <svg class="w-3.5 h-3.5 ${replyLiked ? 'fill-current' : ''}" fill="${replyLiked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                    <span class="like-count">${replyLikeCount}</span>
                                                </button>
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        ` : ''}
                    </div>
                `;
            }).join('');
        
        // Attach event listeners for reply buttons and like buttons
        attachReplyListeners();
        attachLikeListeners();
    }

    function attachLikeListeners() {
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const commentId = btn.getAttribute('data-comment-id');
                const isLiked = btn.getAttribute('data-liked') === 'true';
                const likeCountEl = btn.querySelector('.like-count');
                
                // Disable button during request
                btn.disabled = true;
                
                try {
                    const endpoint = isLiked ? 'unlike' : 'like';
                    const response = await fetch(`${DISCUSSION_API_BASE_URL}/${commentId}/${endpoint}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        }
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        // Update UI
                        const newLiked = !isLiked;
                        btn.setAttribute('data-liked', newLiked);
                        
                        // Update localStorage
                        toggleLikeComment(parseInt(commentId));
                        
                        // Update button style
                        if (newLiked) {
                            btn.classList.remove('text-slate-600');
                            btn.classList.add('text-game-accent');
                            const svg = btn.querySelector('svg');
                            svg.classList.add('fill-current');
                            svg.setAttribute('fill', 'currentColor');
                        } else {
                            btn.classList.remove('text-game-accent');
                            btn.classList.add('text-slate-600');
                            const svg = btn.querySelector('svg');
                            svg.classList.remove('fill-current');
                            svg.setAttribute('fill', 'none');
                        }
                        
                        // Update count
                        if (likeCountEl) {
                            likeCountEl.textContent = result.data.like_count || 0;
                        }
                    } else {
                        throw new Error(result.message || 'Không thể thực hiện thao tác');
                    }
                } catch (error) {
                    console.error('Error toggling like:', error);
                    alert(error.message || 'Không thể thực hiện thao tác. Vui lòng thử lại.');
                } finally {
                    btn.disabled = false;
                }
            });
        });
    }

    function setupDiscussionForm() {
        const form = document.getElementById('discussion-form');
        const contentInput = document.getElementById('discussion-content');
        const submitBtn = document.getElementById('discussion-submit-btn');

        if (!form || !contentInput || !submitBtn) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const content = (contentInput.value || '').trim();
            // Lấy tên từ localStorage (nếu có user), nếu không sẽ là ẩn danh
            let name = 'Người dùng ẩn danh';
            try {
                const userRaw = localStorage.getItem('user');
                if (userRaw) {
                    const user = JSON.parse(userRaw);
                    if (user && user.name) {
                        name = user.name;
                    }
                }
            } catch (e) {
                console.warn('Cannot parse user from localStorage:', e);
            }

            if (!content) {
                alert('Vui lòng nhập nội dung thảo luận.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full inline-block"></div>';

            // Gửi lên API
            (async () => {
                try {
                    // Lấy tên từ localStorage (nếu có user), nếu không sẽ là ẩn danh
                    let authorName = null;
                    try {
                        const userRaw = localStorage.getItem('user');
                        if (userRaw) {
                            const user = JSON.parse(userRaw);
                            authorName = user.name || null;
                        }
                    } catch (e) {
                        console.error('Error parsing user data:', e);
                    }

                    await saveDiscussion(content, null, authorName);

                    // Reset form
                    contentInput.value = '';

                    // Render lại danh sách
                    await renderDiscussion();

                    // Thông báo thành công
                    showNotification('Bình luận đã được đăng thành công!', 'success');
                } catch (error) {
                    console.error('Error submitting discussion:', error);
                    alert(error.message || 'Không thể gửi bình luận. Vui lòng thử lại.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Gửi thảo luận';
                }
            })();
        });
    }

    // Helper function to show notification
    function showNotification(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-game-green' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        const notification = document.createElement('div');
        notification.className = `fixed top-24 right-4 ${bgColor} text-white rounded-xl shadow-lg p-4 z-50 flex items-center gap-3 animate-slide-in max-w-md`;
        notification.innerHTML = `
            <div class="flex-1">
                <p class="font-semibold text-sm">${escapeHtml(message)}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function attachReplyListeners() {
        // Reply button - show form
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const commentId = btn.getAttribute('data-comment-id');
                const formEl = document.getElementById(`reply-form-${commentId}`);
                if (formEl) {
                    formEl.classList.remove('hidden');
                    const textarea = document.getElementById(`reply-content-${commentId}`);
                    if (textarea) textarea.focus();
                }
            });
        });

        // Reply submit button
        document.querySelectorAll('.reply-submit-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const commentId = btn.getAttribute('data-comment-id');
                const textarea = document.getElementById(`reply-content-${commentId}`);
                if (!textarea) return;

                const content = (textarea.value || '').trim();
                if (!content) {
                    alert('Vui lòng nhập nội dung phản hồi.');
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '<div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full inline-block"></div>';

                try {
                    // Get current user name from localStorage or use default
                    let authorName = null;
                    try {
                        const userData = localStorage.getItem('user');
                        if (userData) {
                            const user = JSON.parse(userData);
                            authorName = user.name || null;
                        }
                    } catch (e) {
                        console.error('Error parsing user data:', e);
                    }

                    // Gửi reply lên API
                    await saveDiscussion(content, commentId, authorName);
                    
                    // Clear textarea and hide form
                    textarea.value = '';
                    const formEl = document.getElementById(`reply-form-${commentId}`);
                    if (formEl) formEl.classList.add('hidden');
                    
                    // Re-render discussion
                    await renderDiscussion();
                    
                    showNotification('Phản hồi đã được đăng thành công!', 'success');
                } catch (error) {
                    console.error('Error submitting reply:', error);
                    alert(error.message || 'Không thể gửi phản hồi. Vui lòng thử lại.');
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Gửi';
                }
            });
        });

        // Reply cancel button
        document.querySelectorAll('.reply-cancel-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const commentId = btn.getAttribute('data-comment-id');
                const formEl = document.getElementById(`reply-form-${commentId}`);
                const textarea = document.getElementById(`reply-content-${commentId}`);
                
                if (formEl) formEl.classList.add('hidden');
                if (textarea) textarea.value = '';
            });
        });
    }

    // Hiển thị modal danh sách đầy đủ tags
    function showAllTagsModal(tags) {
        if (!tags || !tags.length) return;

        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4';
        overlay.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
                <button type="button"
                        class="absolute top-3 right-3 text-slate-400 hover:text-slate-600"
                        onclick="this.closest('.fixed').remove()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <h3 class="font-heading text-lg font-bold text-slate-800 mb-4">Tất cả tag của sản phẩm</h3>
                <div class="flex flex-wrap gap-2 mb-4">
                    ${tags.map(tag => `
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs rounded-full border border-slate-200">
                            ${escapeHtml(tag)}
                        </span>
                    `).join('')}
                </div>
                <button type="button"
                        class="w-full mt-2 px-4 py-2 bg-game-accent text-white rounded-xl hover:bg-game-accent-hover transition-colors font-semibold"
                        onclick="this.closest('.fixed').remove()">
                    Đóng
                </button>
            </div>
        `;

        document.body.appendChild(overlay);
    }

    function renderDetailDescription(product) {
        const raw = product.detail_description;

        // Fallback mặc định khi không có mô tả chi tiết
        if (!raw || !raw.trim()) {
            return `
                <h2>Thông tin sản phẩm</h2>
                <p>${escapeHtml(product.title)} - Tài khoản game chính hãng, bảo hành trọn đời.</p>
                <h3>Đặc điểm nổi bật</h3>
                <ul>
                    <li>Tài khoản chính chủ, full quyền truy cập</li>
                    <li>Bảo hành trọn đời, hỗ trợ 24/7</li>
                    <li>Giao hàng nhanh chóng qua email</li>
                    <li>Hướng dẫn chi tiết cách sử dụng</li>
                </ul>
                <h3>Hướng dẫn sử dụng</h3>
                <p>Sau khi mua, bạn sẽ nhận được thông tin tài khoản qua email. Đăng nhập và tải game để bắt đầu trải nghiệm!</p>
            `;
        }

        // Render HTML trực tiếp từ Quill editor (y hệt như trong admin)
        // Quill editor lưu HTML trực tiếp, không cần xử lý phức tạp
        let html = raw.trim();
        
        // Nếu HTML bị escape (có &lt; thay vì <), unescape nó
        if (html.includes('&lt;') || html.includes('&gt;') || html.includes('&amp;')) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            html = tempDiv.textContent || tempDiv.innerText || html;
            // Nếu vẫn còn escape, thử unescape thủ công
            html = html.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
        }
        
        // Loại bỏ các thuộc tính HTML bị hiển thị như text trong nội dung
        // Sử dụng DOM để parse và clean HTML một cách chính xác
        try {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Hàm để clean text node
            function cleanTextNode(textNode) {
                if (!textNode || !textNode.textContent) return;
                
                let text = textNode.textContent;
                const originalText = text;
                
                // Loại bỏ các thuộc tính HTML trong text
                text = text.replace(/\s+target\s*=\s*["']_blank["']/gi, '');
                text = text.replace(/\s+target\s*=\s*["']_self["']/gi, '');
                text = text.replace(/\s+target\s*=\s*["']_parent["']/gi, '');
                text = text.replace(/\s+target\s*=\s*["']_top["']/gi, '');
                text = text.replace(/\s+rel\s*=\s*["'][^"']*["']/gi, '');
                text = text.replace(/\s+href\s*=\s*["'][^"']*["']/gi, '');
                text = text.replace(/\s+class\s*=\s*["'][^"']*["']/gi, '');
                text = text.replace(/\s+id\s*=\s*["'][^"']*["']/gi, '');
                text = text.replace(/\s+style\s*=\s*["'][^"']*["']/gi, '');
                
                // Loại bỏ các pattern như: " target="_blank""
                text = text.replace(/\s+target\s*=\s*_blank/gi, '');
                text = text.replace(/\s+target\s*=\s*_self/gi, '');
                text = text.replace(/\s+rel\s*=\s*[^\s<>"']+/gi, '');
                
                // Loại bỏ các ký tự còn sót lại từ thuộc tính HTML
                // Pattern: "> hoặc " > hoặc " >" (các phần còn sót lại)
                text = text.replace(/\s*["']\s*>\s*/g, '');
                text = text.replace(/\s*>\s*/g, '');
                text = text.replace(/\s*["']\s*/g, '');
                
                // Loại bỏ các pattern như: " target= hoặc "> hoặc " >
                text = text.replace(/["']\s*target\s*=\s*/gi, '');
                text = text.replace(/["']\s*rel\s*=\s*/gi, '');
                text = text.replace(/["']\s*href\s*=\s*/gi, '');
                text = text.replace(/["']\s*class\s*=\s*/gi, '');
                text = text.replace(/["']\s*id\s*=\s*/gi, '');
                text = text.replace(/["']\s*style\s*=\s*/gi, '');
                
                // Loại bỏ các ký tự đóng mở không hợp lệ
                text = text.replace(/^\s*["']\s*>\s*/g, ''); // Bắt đầu với "> hoặc " >
                text = text.replace(/\s*["']\s*>\s*$/g, ''); // Kết thúc với "> hoặc " >
                
                // Chỉ cập nhật nếu có thay đổi
                if (text !== originalText) {
                    textNode.textContent = text;
                }
            }
            
            // Tìm và clean tất cả text nodes
            const walker = document.createTreeWalker(
                tempDiv,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );
            
            const textNodes = [];
            let node;
            while (node = walker.nextNode()) {
                textNodes.push(node);
            }
            
            // Clean tất cả text nodes
            textNodes.forEach(cleanTextNode);
            
            html = tempDiv.innerHTML;
        } catch (e) {
            console.warn('Error parsing HTML, using regex fallback:', e);
            // Fallback: sử dụng regex mạnh hơn để loại bỏ
            // Loại bỏ trong thẻ <p>
            html = html.replace(/(<p[^>]*>)([^<]*?)\s+target\s*=\s*["']_blank["']([^<]*?)(<\/p>)/gi, '$1$2$3$4');
            html = html.replace(/(<p[^>]*>)([^<]*?)\s+target\s*=\s*["']_self["']([^<]*?)(<\/p>)/gi, '$1$2$3$4');
            html = html.replace(/(<p[^>]*>)([^<]*?)\s+rel\s*=\s*["'][^"']*["']([^<]*?)(<\/p>)/gi, '$1$2$3$4');
            
            // Loại bỏ các thuộc tính HTML đơn lẻ trong text (không trong thẻ HTML)
            // Pattern: text trước + thuộc tính + text sau (không có < > xung quanh)
            html = html.replace(/([^<>"']+?)\s+target\s*=\s*["']_blank["']([^<>"']*?)/gi, '$1$2');
            html = html.replace(/([^<>"']+?)\s+target\s*=\s*["']_self["']([^<>"']*?)/gi, '$1$2');
            html = html.replace(/([^<>"']+?)\s+rel\s*=\s*["'][^"']*["']([^<>"']*?)/gi, '$1$2');
            
            // Loại bỏ các pattern đơn giản hơn
            html = html.replace(/\s+target\s*=\s*["']_blank["']/gi, '');
            html = html.replace(/\s+target\s*=\s*["']_self["']/gi, '');
            html = html.replace(/\s+rel\s*=\s*["'][^"']*["']/gi, '');
        }
        
        // Bước cuối: loại bỏ các thuộc tính HTML còn sót lại bằng regex toàn cục
        // Tìm pattern: khoảng trắng + thuộc tính HTML + giá trị trong text (không trong thẻ)
        html = html.replace(/(?<!<[^>]*)\s+(target|rel|href|class|id|style)\s*=\s*["'][^"']*["'](?![^<]*>)/gi, '');
        html = html.replace(/(?<!<[^>]*)\s+(target|rel|href|class|id|style)\s*=\s*[^\s<>"']+(?![^<]*>)/gi, '');
        
        // Bước clean cuối cùng: loại bỏ các thuộc tính HTML trong text content của các thẻ
        // Pattern: >text có thuộc tính HTML< -> >text đã clean<
        html = html.replace(/>([^<]*?)\s+(target|rel|href|class|id|style)\s*=\s*["'][^"']*["']([^<]*?)</gi, '>$1$3<');
        html = html.replace(/>([^<]*?)\s+(target|rel|href|class|id|style)\s*=\s*[^\s<>"']+([^<]*?)</gi, '>$1$3<');
        
        // Loại bỏ các pattern cụ thể còn sót lại
        html = html.replace(/\s+target\s*=\s*["']_blank["']/gi, '');
        html = html.replace(/\s+target\s*=\s*["']_self["']/gi, '');
        html = html.replace(/\s+target\s*=\s*_blank/gi, '');
        html = html.replace(/\s+target\s*=\s*_self/gi, '');
        html = html.replace(/\s+rel\s*=\s*["'][^"']*["']/gi, '');
        
        // Loại bỏ các ký tự còn sót lại từ thuộc tính HTML: "> hoặc " > hoặc " >"
        html = html.replace(/>([^<]*?)\s*["']\s*>\s*([^<]*?)</gi, '>$1$2<');
        html = html.replace(/([^<>"']+)\s*["']\s*>\s*([^<>"']+)/gi, '$1$2');
        html = html.replace(/^\s*["']\s*>\s*/g, '');
        html = html.replace(/\s*["']\s*>\s*$/g, '');
        html = html.replace(/["']\s*>/g, '');
        html = html.replace(/>\s*["']/g, '');
        
        // Loại bỏ các thẻ <p> chỉ chứa "> hoặc rỗng
        html = html.replace(/<p[^>]*>\s*["']\s*>\s*<\/p>/gi, '');
        html = html.replace(/<p[^>]*>\s*>\s*<\/p>/gi, '');
        html = html.replace(/<p[^>]*>\s*["']\s*<\/p>/gi, '');
        
        // Đảm bảo các thẻ <p> có margin để tạo khoảng cách giữa các đoạn
        // Quill editor tạo <p> tags cho mỗi đoạn, nhưng có thể thiếu styling
        html = html.replace(/<p([^>]*)>/gi, (match, attrs) => {
            // Nếu đã có class, thêm mb-4 nếu chưa có
            if (match.includes('class=')) {
                if (!match.includes('mb-')) {
                    return match.replace(/class="([^"]*)"/, 'class="$1 mb-4"');
                }
                return match;
            }
            // Nếu chưa có class, thêm class="mb-4"
            return `<p${attrs} class="mb-4">`;
        });
        
        // Xử lý các thẻ <p><br></p> (dòng trống trong Quill) để tạo khoảng cách
        html = html.replace(/<p[^>]*>\s*<br\s*\/?>\s*<\/p>/gi, '<p class="mb-4"><br></p>');
        
        // Xử lý hình ảnh - đảm bảo các thẻ <img> có styling và responsive
        html = html.replace(/<img([^>]*?)(?:\s+class="[^"]*")?([^>]*)>/gi, (match, before, after) => {
            if (!match.includes('class=')) {
                return `<img${before}${after} class="w-full rounded-xl shadow-md my-6">`;
            } else if (!match.includes('w-full')) {
                // Thêm w-full nếu chưa có
                return match.replace(/class="([^"]*)"/, 'class="$1 w-full rounded-xl shadow-md my-6"');
            }
            return match;
        });
        
        // Xử lý các URL ảnh đơn lẻ (không nằm trong thẻ <img> hoặc <a>) - chuyển thành thẻ <img>
        // Chỉ xử lý URL ảnh đơn lẻ trong text, không nằm trong thẻ HTML
        // Kiểm tra xem đã có thẻ <img> chưa
        if (!html.includes('<img')) {
            // Nếu chưa có thẻ <img>, tìm và chuyển đổi URL ảnh
            const imgUrlRegex = /(https?:\/\/[^\s<>"']+?\.(jpe?g|png|webp|gif|jpg))/gi;
            html = html.replace(imgUrlRegex, (url) => {
                return `<img src="${url}" alt="${escapeHtml(product.title)}" class="w-full rounded-xl shadow-md my-6" />`;
            });
        }
        
        // Đảm bảo các thẻ <br> có spacing
        html = html.replace(/<br\s*\/?>/gi, '<br class="mb-2">');
        
        // Render HTML trực tiếp - y hệt như trong Quill editor nhưng có thêm styling cơ bản
        return html;
    }

    // Render mô tả ngắn (đơn giản hơn mô tả chi tiết)
    function renderShortDescription(product) {
        const raw = product.short_description;

        // Fallback mặc định khi không có mô tả ngắn
        if (!raw || !raw.trim()) {
            return `<p>Tài khoản ${escapeHtml(product.title)} - Kích hoạt nhanh chóng, bảo hành trọn đời.</p>`;
        }

        // Kiểm tra xem có phải HTML không
        const htmlTagRegex = /<\/?[a-z][\s\S]*>/i;
        const isHTML = htmlTagRegex.test(raw);
        const hasCommonHTMLTags = raw.includes('<p>') || raw.includes('</p>') || 
                                  raw.includes('<strong>') || raw.includes('<br>') ||
                                  raw.includes('<ul>') || raw.includes('<li>');
        
        let html = raw.trim();
        
        if (isHTML || hasCommonHTMLTags) {
            // Nếu là HTML, xử lý giống detail description
            // Nếu HTML bị escape (có &lt; thay vì <), unescape nó
            if (html.includes('&lt;') || html.includes('&gt;') || html.includes('&amp;')) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                html = tempDiv.textContent || tempDiv.innerText || html;
                html = html.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
            }
            
            // Đảm bảo các thẻ <p> có margin để tạo khoảng cách
            html = html.replace(/<p([^>]*)>/gi, (match, attrs) => {
                if (!match.includes('class=')) {
                    return `<p${attrs} class="mb-3">`;
                } else if (!match.includes('mb-')) {
                    return match.replace(/class="([^"]*)"/, 'class="$1 mb-3"');
                }
                return match;
            });
            
            // Xử lý <br> tags
            html = html.replace(/<br\s*\/?>/gi, '<br class="mb-2">');
            
            // Xử lý hình ảnh
            html = html.replace(/<img([^>]*?)(?:\s+class="[^"]*")?([^>]*)>/gi, (match, before, after) => {
                if (!match.includes('class=')) {
                    return `<img${before}${after} class="w-full rounded-xl shadow-md my-4">`;
                }
                return match;
            });
            
            // Xử lý các URL ảnh đơn lẻ
            if (!html.includes('<img')) {
                const imgUrlRegex = /(https?:\/\/[^\s<>"']+?\.(jpe?g|png|webp|gif|jpg))/gi;
                html = html.replace(imgUrlRegex, (url) => {
                    return `<img src="${url}" alt="${escapeHtml(product.title)}" class="w-full rounded-xl shadow-md my-4" />`;
                });
            }
        } else {
            // Nếu là text thuần, xử lý xuống dòng
            // Chia thành các dòng và bọc mỗi dòng trong <p>
            const lines = raw.split(/\r?\n/).filter(line => line.trim());
            if (lines.length > 0) {
                html = lines.map(line => {
                    const trimmed = line.trim();
                    if (!trimmed) return '';
                    return `<p class="mb-3">${escapeHtml(trimmed)}</p>`;
                }).join('\n');
            } else {
                // Nếu không có dòng trống, chia theo dấu chấm hoặc dấu phẩy
                html = `<p class="mb-3">${escapeHtml(raw)}</p>`;
            }
        }
        
        return html;
    }

    // Hàm helper để clean HTML attributes trong một element
    function cleanHTMLAttributes(element) {
        if (!element) return;
        
        const textNodes = [];
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
        
        let node;
        while (node = walker.nextNode()) {
            if (node.textContent) {
                let text = node.textContent;
                const originalText = text;
                
                // Loại bỏ tất cả các thuộc tính HTML trong text
                text = text.replace(/\s+(target|rel|href|class|id|style)\s*=\s*["'][^"']*["']/gi, '');
                text = text.replace(/\s+(target|rel|href|class|id|style)\s*=\s*[^\s<>"']+/gi, '');
                
                // Loại bỏ các ký tự còn sót lại: "> hoặc " > hoặc " >"
                text = text.replace(/\s*["']\s*>\s*/g, '');
                text = text.replace(/^\s*["']\s*>\s*/g, '');
                text = text.replace(/\s*["']\s*>\s*$/g, '');
                text = text.replace(/["']\s*>/g, '');
                text = text.replace(/>\s*["']/g, '');
                text = text.replace(/^\s*["']\s*>\s*/g, '');
                text = text.replace(/\s*["']\s*>\s*$/g, '');
                
                if (text !== originalText) {
                    node.textContent = text;
                }
            }
        }
    }

    function generateStars(rating) {
        let stars = '';
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 >= 0.5;
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                stars += '<svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            } else if (i === fullStars && hasHalf) {
                stars += '<svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><defs><linearGradient id="half"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#e5e7eb"/></linearGradient></defs><path fill="url(#half)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            } else {
                stars += '<svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            }
        }
        return stars;
    }

    // Gọi API lấy dữ liệu sản phẩm
    async function loadProduct() {
        try {
            const response = await fetch(`${API_BASE_URL}/${gameId}`);
            const data = await response.json();

            if (data.success && data.data) {
                productData = data.data;
                renderProduct(productData);
                // Sử dụng AI Recommendation để load sản phẩm tương tự
                loadRelatedProducts(productData.id, productData.category);
                // Ghi nhận view interaction cho AI recommendations (nếu user đã đăng nhập)
                recordViewInteraction(productData.id);
            } else {
                showError('Không tìm thấy sản phẩm');
            }
        } catch (error) {
            console.error('Error loading product:', error);
            showError('Đã xảy ra lỗi khi tải sản phẩm');
        }
    }

    // Ghi nhận view interaction cho AI Recommendation System
    async function recordViewInteraction(productId) {
        const token = localStorage.getItem('auth_token');
        if (!token) return; // Chỉ ghi nhận nếu user đã đăng nhập
        
        try {
            await fetch(`${BASE_URL}/api/recommendations/interaction`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    product_id: productId,
                    type: 'view'
                })
            });
            console.log('🤖 Recorded view interaction for AI recommendations');
        } catch (error) {
            // Silent fail - không ảnh hưởng UX
            console.debug('Could not record view interaction:', error);
        }
    }

    function renderProduct(product) {
        // Cập nhật tiêu đề trang
        document.title = `${product.title} - Game Store`;
        
        // Cập nhật breadcrumb
        document.getElementById('breadcrumb-title').textContent = product.title;

        // Hiển thị badge HOT và % giảm giá
        const badges = document.getElementById('product-badges');
        const discount = calculateDiscount(product.price);
        badges.innerHTML = `
            <span class="px-3 py-1 bg-game-orange text-white text-xs font-bold rounded-full shadow-lg">HOT</span>
            ${discount > 0 ? `<span class="px-3 py-1 bg-game-green text-white text-xs font-bold rounded-full shadow-lg">-${discount}%</span>` : ''}
        `;

        // Ảnh sản phẩm
        document.getElementById('product-image').src = product.image || 'https://via.placeholder.com/600x400?text=Game';
        document.getElementById('product-image').alt = product.title;

        // Danh mục & tiêu đề
        document.getElementById('product-category').textContent = product.category || 'Game';
        document.getElementById('product-title').textContent = product.title;

        // Gắn các thẻ (tags) của sản phẩm – hiển thị 2 dòng:
        // Dòng trên: thể loại (đã hiển thị ở trên)
        // Dòng dưới: danh sách tags, nếu quá nhiều thì thu gọn và hiển thị nút "+N"
        const tagsWrapper = document.getElementById('product-tags-wrapper');
        const tagsMainEl = document.getElementById('product-tags-main');
        const tagsMoreBtn = document.getElementById('product-tags-more-btn');

        tagsMainEl.innerHTML = '';
        tagsMoreBtn.classList.add('hidden');

        let tags = [];
        if (Array.isArray(product.tags)) {
            tags = product.tags;
        } else if (typeof product.tags === 'string' && product.tags.trim() !== '') {
            // Trường hợp API trả về chuỗi tags phân tách bởi dấu phẩy
            tags = product.tags.split(',').map(t => t.trim()).filter(Boolean);
        }

        if (tags.length > 0) {
            const MAX_VISIBLE_TAGS = 3;
            const visibleTags = tags.slice(0, MAX_VISIBLE_TAGS);
            const extraCount = tags.length - MAX_VISIBLE_TAGS;

            tagsMainEl.innerHTML = visibleTags.map(tag =>
                `<span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs rounded">${escapeHtml(tag)}</span>`
            ).join('');

            if (extraCount > 0) {
                tagsMoreBtn.textContent = `+${extraCount}`;
                tagsMoreBtn.classList.remove('hidden');

                tagsMoreBtn.onclick = () => {
                    showAllTagsModal(tags);
                };
            }
        }

        // Điểm đánh giá và số lượt xem / đã bán
        // Rating sẽ được tính từ reviews khi load
        const rating = 0;
        document.getElementById('product-stars').innerHTML = generateStars(rating);
        document.getElementById('product-rating').textContent = rating.toFixed(1);
        document.getElementById('product-views').textContent = (product.view_count || 0).toLocaleString();
        document.getElementById('product-sold').textContent = '0';

        // Mô tả ngắn: render HTML giống phần chi tiết nhưng gọn hơn
        const shortDescElement = document.getElementById('product-short-desc');
        const shortDescHTML = renderShortDescription(product);
        shortDescElement.innerHTML = shortDescHTML;
        
        // Clean short description sau khi render
        cleanHTMLAttributes(shortDescElement);

        // Giá sản phẩm (gốc + sau giảm nếu có)
        const prices = extractPrices(product.price);
        if (prices.original && prices.original !== prices.current) {
            document.getElementById('product-original-price').textContent = prices.original;
            document.getElementById('product-original-price').classList.remove('hidden');
        }
        document.getElementById('product-current-price').textContent = prices.current || 'Liên hệ';
        
        if (discount > 0) {
            document.getElementById('product-discount').textContent = `-${discount}%`;
            document.getElementById('product-discount').classList.remove('hidden');
        }

        // Mô tả chi tiết: render HTML từ dữ liệu trong database
        const detailDescElement = document.getElementById('product-detail-desc');
        const detailDescWrapper = document.getElementById('product-detail-desc-wrapper');
        const fadeOverlay = document.getElementById('description-fade');
        const toggleContainer = document.getElementById('description-toggle-container');
        const toggleBtn = document.getElementById('description-toggle-btn');
        
        let renderedHTML = renderDetailDescription(product);
        
        // Log debug nội dung mô tả chi tiết
        console.log('Detail description raw (first 500 chars):', product.detail_description ? product.detail_description.substring(0, 500) : 'empty');
        console.log('Rendered HTML (first 500 chars):', renderedHTML ? renderedHTML.substring(0, 500) : 'empty');
        
        // Render HTML trực tiếp (không escape) - innerHTML sẽ tự động parse HTML
        detailDescElement.innerHTML = renderedHTML;
        
        // Clean thêm lần nữa các thuộc tính HTML dư sau khi render
        const textNodes = [];
        const walker = document.createTreeWalker(
            detailDescElement,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
        
        let node;
        while (node = walker.nextNode()) {
            if (node.textContent) {
                let text = node.textContent;
                const originalText = text;
                
                // Loại bỏ tất cả các thuộc tính HTML trong text
                text = text.replace(/\s+(target|rel|href|class|id|style)\s*=\s*["'][^"']*["']/gi, '');
                text = text.replace(/\s+(target|rel|href|class|id|style)\s*=\s*[^\s<>"']+/gi, '');
                
                // Loại bỏ các ký tự còn sót lại: "> hoặc " > hoặc " >"
                text = text.replace(/\s*["']\s*>\s*/g, '');
                text = text.replace(/^\s*["']\s*>\s*/g, '');
                text = text.replace(/\s*["']\s*>\s*$/g, '');
                
                // Loại bỏ các pattern như: "> đơn lẻ
                text = text.replace(/["']\s*>/g, '');
                text = text.replace(/>\s*["']/g, '');
                
                // Loại bỏ các ký tự quotes và > đơn lẻ không hợp lệ
                text = text.replace(/^\s*["']\s*>\s*/g, '');
                text = text.replace(/\s*["']\s*>\s*$/g, '');
                
                // Chỉ cập nhật nếu có thay đổi
                if (text !== originalText) {
                    node.textContent = text;
                }
            }
        }
        
        // Nếu mô tả dài, hiển thị nút "Xem thêm / Thu gọn"
        setTimeout(() => {
            const descHeight = detailDescElement.scrollHeight;
            const maxHeight = 600; // Chiều cao tối đa khi thu gọn
            
            if (descHeight > maxHeight) {
                // Hiển thị nút và overlay
                toggleContainer.classList.remove('hidden');
                fadeOverlay.classList.remove('hidden');
                detailDescElement.style.maxHeight = maxHeight + 'px';
                
                // Xử lý click nút toggle
                let isExpanded = false;
                toggleBtn.addEventListener('click', () => {
                    isExpanded = !isExpanded;
                    if (isExpanded) {
                        detailDescElement.style.maxHeight = 'none';
                        fadeOverlay.classList.add('hidden');
                        toggleBtn.textContent = 'Thu gọn';
                    } else {
                        detailDescElement.style.maxHeight = maxHeight + 'px';
                        fadeOverlay.classList.remove('hidden');
                        toggleBtn.textContent = 'Xem thêm';
                        // Scroll về đầu phần mô tả khi thu gọn
                        detailDescElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            }
        }, 100);

        // Thống kê đánh giá tổng quan (sẽ được cập nhật khi load reviews)
        document.getElementById('review-count').textContent = '0';
        document.getElementById('avg-rating-display').textContent = '0.0';
        document.getElementById('avg-stars-display').innerHTML = generateStars(0);
        document.getElementById('total-reviews').textContent = '0';

        // Vẽ thanh tỷ lệ số sao (sẽ được cập nhật khi load reviews)
        const ratingBars = document.getElementById('rating-bars');
        if (ratingBars) {
        ratingBars.innerHTML = [5,4,3,2,1].map(star => `
            <div class="flex items-center gap-2">
                <span class="text-sm w-3">${star}</span>
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-400 rounded-full" style="width: 0%"></div>
                </div>
                    <span class="text-xs text-slate-500 w-8 text-right">0</span>
            </div>
        `).join('');
        }

        // Ẩn phần loading, hiển thị nội dung chính
        document.getElementById('product-loading').classList.add('hidden');
        document.getElementById('product-content').classList.remove('hidden');
        document.getElementById('product-content').classList.add('grid');
    }

    function showError(message) {
        document.getElementById('product-loading').innerHTML = `
            <div class="col-span-2 text-center py-16">
                <div class="text-6xl mb-4">😔</div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">${message}</h2>
                <p class="text-slate-600 mb-6">Sản phẩm này có thể đã bị xóa hoặc không tồn tại.</p>
                <a href="/store" class="inline-flex items-center px-6 py-3 bg-game-accent text-white font-semibold rounded-xl hover:bg-game-accent-hover transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Quay lại cửa hàng
                </a>
            </div>
        `;
    }

    // Tải danh sách sản phẩm liên quan sử dụng AI Recommendation System
    async function loadRelatedProducts(productId, category) {
        const container = document.getElementById('related-products');
        
        // Hiển thị loading skeleton
        container.innerHTML = `
            <div class="animate-pulse bg-white rounded-xl border border-slate-200 p-3 flex">
                <div class="w-24 h-24 bg-slate-200 rounded-lg"></div>
                <div class="flex-1 ml-3 space-y-2">
                    <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                    <div class="h-4 bg-slate-200 rounded w-1/4"></div>
                </div>
            </div>
        `.repeat(4);
        
        try {
            // Thử lấy từ AI Recommendation System trước
            let related = [];
            let isAIRecommendation = false;
            
            try {
                const recommendResponse = await fetch(`${BASE_URL}/api/recommendations/products/${productId}/similar?limit=4`);
                const recommendData = await recommendResponse.json();
                
                if (recommendData.success && recommendData.data && recommendData.data.length > 0) {
                    related = recommendData.data;
                    isAIRecommendation = true;
                    console.log('🤖 Loaded AI recommendations:', related.length, 'products');
                }
            } catch (aiError) {
                console.warn('AI Recommendation not available, falling back to category:', aiError);
            }
            
            // Fallback: Nếu không có AI recommendations, lấy theo category
            if (related.length === 0 && category) {
                const response = await fetch(`${API_BASE_URL}?category=${encodeURIComponent(category)}&per_page=5`);
                const data = await response.json();
                
                if (data.success && data.data && data.data.length > 0) {
                    // Filter out current product
                    related = data.data.filter(p => p.id !== productId).slice(0, 4);
                    console.log('📂 Loaded category-based products:', related.length, 'products');
                }
            }
            
            if (related.length === 0) {
                container.innerHTML = '<p class="col-span-4 text-center text-slate-500 py-8">Không có sản phẩm tương tự.</p>';
                return;
            }

            container.innerHTML = related.map(product => {
                const prices = extractPrices(product.price);
                const discount = calculateDiscount(product.price);

                return `
                    <a href="${GAME_BASE_URL}/${product.id}" class="group bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-game-accent hover:shadow-lg transition-all card-hover flex">
                        <div class="flex-shrink-0 w-24 h-24 overflow-hidden rounded-lg m-3 relative">
                            <img src="${product.image || 'https://via.placeholder.com/150x150?text=Game'}" 
                                 alt="${escapeHtml(product.title)}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            ${discount > 0 ? `<div class="absolute top-1 right-1 px-1.5 py-0.5 bg-game-green text-white text-[10px] font-bold rounded">-${discount}%</div>` : ''}
                        </div>
                        <div class="flex-1 py-3 pr-3 flex flex-col justify-between min-w-0">
                            <div>
                                <h3 class="font-heading font-semibold text-slate-800 text-sm leading-tight line-clamp-2 group-hover:text-game-accent transition-colors">
                                    ${escapeHtml(product.title)}
                                </h3>
                                <div class="flex items-center gap-2 mt-1">
                                    ${product.category ? `<span class="px-2 py-0.5 bg-game-accent/10 text-game-accent text-xs font-medium rounded">${escapeHtml(product.category)}</span>` : ''}
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                ${prices.original && prices.original !== prices.current ? `<span class="text-slate-400 line-through text-xs">${prices.original}</span>` : ''}
                                <span class="text-game-accent font-bold">${prices.current || 'Liên hệ'}</span>
                            </div>
                        </div>
                    </a>
                `;
            }).join('');
            
        } catch (error) {
            console.error('Error loading related products:', error);
            container.innerHTML = '<p class="col-span-4 text-center text-slate-500 py-8">Không thể tải sản phẩm tương tự.</p>';
        }
    }

    // Chuyển tab mô tả / đánh giá / FAQ
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active from all tabs
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'border-game-accent', 'text-game-accent');
                b.classList.add('border-transparent', 'text-slate-500');
            });
            
            // Add active to clicked tab
            btn.classList.add('active', 'border-game-accent', 'text-game-accent');
            btn.classList.remove('border-transparent', 'text-slate-500');
            
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            
            // Show selected tab content (only description and reviews tabs now)
            const tabContent = document.getElementById(`tab-${btn.dataset.tab}`);
            if (tabContent) {
                tabContent.classList.remove('hidden');
            }
            
            // Load reviews when reviews tab is opened
            if (btn.dataset.tab === 'reviews') {
                // Wait a bit for tab to be visible, then load reviews
                setTimeout(() => {
                    // Retry loading reviews if elements not found
                    let retries = 0;
                    const maxRetries = 5;
                    const tryLoadReviews = () => {
                        const reviewsList = document.getElementById('reviews-list');
                        const reviewsLoading = document.getElementById('reviews-loading');
                        const reviewsEmpty = document.getElementById('reviews-empty');
                        
                        if (reviewsList && reviewsLoading && reviewsEmpty) {
                            loadReviews();
                        } else if (retries < maxRetries) {
                            retries++;
                            setTimeout(tryLoadReviews, 100);
                        } else {
                            console.error('Could not find review elements after retries');
                        }
                    };
                    tryLoadReviews();
                }, 150);
            }
        });
    });

    // Điều khiển số lượng mua
    document.getElementById('qty-minus').addEventListener('click', () => {
        const input = document.getElementById('qty-input');
        const value = parseInt(input.value) || 1;
        if (value > 1) input.value = value - 1;
    });

    document.getElementById('qty-plus').addEventListener('click', () => {
        const input = document.getElementById('qty-input');
        const value = parseInt(input.value) || 1;
        input.value = value + 1;
    });

    // Thêm sản phẩm vào giỏ hàng (localStorage)
    document.getElementById('add-to-cart-btn').addEventListener('click', () => {
        if (!productData) return;
        
        const qty = parseInt(document.getElementById('qty-input').value) || 1;
        
        // Lấy giỏ hiện tại hoặc tạo mới
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        
        // Nếu đã có trong giỏ thì cộng dồn số lượng
        const existingIndex = cart.findIndex(item => item.id === productData.id);
        if (existingIndex >= 0) {
            cart[existingIndex].quantity += qty;
        } else {
            cart.push({
                id: productData.id,
                title: productData.title,
                image: productData.image,
                price: productData.price,
                quantity: qty
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Cập nhật số lượng trên icon giỏ hàng
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCountEl.textContent = totalItems;
            cartCountEl.classList.remove('hidden');
        }
        
        // Thông báo thêm giỏ hàng thành công
        alert(`Đã thêm ${qty} "${productData.title}" vào giỏ hàng!`);
    });

    // --------- Buy Now Modal ----------
    const buyNowModal = document.getElementById('buy-now-modal');
    const buySuccessModal = document.getElementById('buy-success-modal');
    let buyQuantity = 1;
    let buyPrice = 0;
    let userBalance = 0;

    // Load user balance
    async function loadUserBalance() {
        try {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            if (!token) return 0;
            
            const response = await fetch(`${BASE_URL}/api/auth/me`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data) {
                    return parseFloat(result.data.balance || 0);
                }
            }
        } catch (e) {
            console.error('Error loading balance:', e);
        }
        return 0;
    }

    // Update buy modal
    function updateBuyModal() {
        if (!productData) return;
        
        buyPrice = extractPriceNumber(productData.price);
        const total = buyPrice * buyQuantity;
        
        document.getElementById('buy-modal-image').src = productData.image || 'https://via.placeholder.com/150';
        document.getElementById('buy-modal-title').textContent = productData.title;
        document.getElementById('buy-modal-price').textContent = formatPrice(buyPrice);
        document.getElementById('buy-qty-input').value = buyQuantity;
        document.getElementById('buy-modal-quantity').textContent = buyQuantity;
        document.getElementById('buy-modal-total').textContent = formatPrice(total);
        
        // Update balance info
        const remaining = userBalance - total;
        document.getElementById('buy-current-balance').textContent = formatPrice(userBalance);
        document.getElementById('buy-remaining-balance').textContent = formatPrice(remaining);
        
        // Show error if insufficient balance
        const errorEl = document.getElementById('buy-error-message');
        if (userBalance < total) {
            errorEl.classList.remove('hidden');
            errorEl.textContent = `Số dư không đủ. Bạn cần thêm ${formatPrice(total - userBalance)} để hoàn tất đơn hàng.`;
            document.getElementById('confirm-buy-btn').disabled = true;
            document.getElementById('confirm-buy-btn').classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            errorEl.classList.add('hidden');
            document.getElementById('confirm-buy-btn').disabled = false;
            document.getElementById('confirm-buy-btn').classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // Open buy modal
    async function openBuyModal() {
        if (!productData) return;
        
        // Check authentication
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (!token) {
            alert('Vui lòng đăng nhập để mua hàng');
            return;
        }
        
        buyQuantity = parseInt(document.getElementById('qty-input').value) || 1;
        userBalance = await loadUserBalance();
        
        updateBuyModal();
        buyNowModal.classList.remove('hidden');
        buyNowModal.classList.add('flex');
    }

    // Close buy modal
    function closeBuyModal() {
        buyNowModal.classList.add('hidden');
        buyNowModal.classList.remove('flex');
    }

    // Buy quantity controls
    document.getElementById('buy-qty-minus')?.addEventListener('click', () => {
        if (buyQuantity > 1) {
            buyQuantity--;
            updateBuyModal();
        }
    });

    document.getElementById('buy-qty-plus')?.addEventListener('click', () => {
        if (buyQuantity < 10) {
            buyQuantity++;
            updateBuyModal();
        }
    });

    document.getElementById('buy-qty-input')?.addEventListener('change', (e) => {
        const val = parseInt(e.target.value) || 1;
        buyQuantity = Math.max(1, Math.min(10, val));
        updateBuyModal();
    });

    // Close buttons
    document.getElementById('close-buy-modal')?.addEventListener('click', closeBuyModal);
    document.getElementById('cancel-buy-btn')?.addEventListener('click', closeBuyModal);

    // Confirm buy
    document.getElementById('confirm-buy-btn')?.addEventListener('click', async () => {
        if (!productData) return;
        
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (!token) {
            alert('Vui lòng đăng nhập để mua hàng');
            return;
        }

        const btn = document.getElementById('confirm-buy-btn');
        btn.disabled = true;
        btn.innerHTML = '<div class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full inline-block"></div> Đang xử lý...';

        try {
            // Create orders using batch API
            const items = [];
            for (let i = 0; i < buyQuantity; i++) {
                items.push({
                    product_simple_id: productData.id,
                    quantity: 1
                });
            }

            const response = await fetch(`${BASE_URL}/api/orders/batch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items: items,
                    payment_method: 'balance'
                })
            });

            const result = await response.json();

            if (result.success) {
                // Close buy modal
                closeBuyModal();
                
                // Show success modal
                const totalAmount = buyPrice * buyQuantity;
                document.getElementById('success-order-code').textContent = result.data[0]?.order_code || 'N/A';
                document.getElementById('success-order-total').textContent = formatPrice(totalAmount);
                
                buySuccessModal.classList.remove('hidden');
                buySuccessModal.classList.add('flex');
                
                // Update user balance in header if exists
                userBalance = await loadUserBalance();
                const balanceEl = document.querySelector('[data-balance]');
                if (balanceEl) {
                    balanceEl.textContent = formatPrice(userBalance);
                }
            } else {
                throw new Error(result.message || 'Không thể tạo đơn hàng');
            }
        } catch (error) {
            console.error('Error creating order:', error);
            alert(error.message || 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Xác nhận mua';
        }
    });

    // Success modal actions
    document.getElementById('continue-shopping-btn')?.addEventListener('click', () => {
        buySuccessModal.classList.add('hidden');
        buySuccessModal.classList.remove('flex');
    });

    document.getElementById('view-order-btn')?.addEventListener('click', () => {
        window.location.href = '/orders';
    });

    // Buy now button
    document.getElementById('buy-now-btn')?.addEventListener('click', openBuyModal);

    // ==================== REVIEWS FUNCTIONALITY ====================
    let selectedRating = 0;
    let userOrders = [];
    let reviewsData = [];

    // Load reviews for current product
    async function loadReviews() {
        // Check if reviews tab exists
        const reviewsTab = document.getElementById('tab-reviews');
        if (!reviewsTab) {
            console.warn('Reviews tab not found');
            return;
        }
        
        const reviewsList = document.getElementById('reviews-list');
        const reviewsLoading = document.getElementById('reviews-loading');
        const reviewsEmpty = document.getElementById('reviews-empty');
        
        // Check if elements exist
        if (!reviewsList) {
            console.error('reviews-list element not found');
            return;
        }
        if (!reviewsLoading) {
            console.error('reviews-loading element not found');
            return;
        }
        if (!reviewsEmpty) {
            console.error('reviews-empty element not found');
            return;
        }
        
        reviewsLoading.classList.remove('hidden');
        reviewsEmpty.classList.add('hidden');
        
        // Clear only the reviews content, not the loading/empty divs
        const existingReviews = reviewsList.querySelectorAll('div:not(#reviews-loading):not(#reviews-empty)');
        existingReviews.forEach(el => {
            if (el.id !== 'reviews-loading' && el.id !== 'reviews-empty') {
                el.remove();
            }
        });

        try {
            // Load reviews with pagination info to get total count
            const response = await fetch(`${REVIEWS_API_BASE_URL}/product/${gameId}?per_page=20&sort_by=created_at&sort_order=desc`);
            const data = await response.json();

            if (data.success && data.data && data.data.length > 0) {
                reviewsData = data.data;
                renderReviews(reviewsData);
                updateRatingBars(reviewsData);
                updateRatingFromReviews(reviewsData, data.pagination?.total || reviewsData.length);
                reviewsLoading.classList.add('hidden');
            } else {
                reviewsData = [];
                reviewsLoading.classList.add('hidden');
                reviewsEmpty.classList.remove('hidden');
                updateRatingBars([]);
                updateRatingFromReviews([], 0);
            }

            // Check if user can review (has purchased this product) and if already reviewed
            await checkUserCanReview();
        } catch (error) {
            console.error('Error loading reviews:', error);
            if (reviewsLoading) reviewsLoading.classList.add('hidden');
            if (reviewsEmpty) {
                reviewsEmpty.classList.remove('hidden');
                reviewsEmpty.textContent = 'Không thể tải đánh giá. Vui lòng thử lại sau.';
            }
            updateRatingFromReviews([], 0);
        }
    }

    // Calculate and update rating from reviews
    function updateRatingFromReviews(reviews, totalCount) {
        let averageRating = 0;
        if (reviews.length > 0) {
            const sum = reviews.reduce((acc, review) => acc + (parseFloat(review.rating) || 0), 0);
            averageRating = sum / reviews.length;
        }
        
        const ratingCount = totalCount || reviews.length;
        
        // Update product rating display (check if elements exist)
        const productStars = document.getElementById('product-stars');
        const productRating = document.getElementById('product-rating');
        const productSold = document.getElementById('product-sold');
        const reviewCount = document.getElementById('review-count');
        const avgRatingDisplay = document.getElementById('avg-rating-display');
        const avgStarsDisplay = document.getElementById('avg-stars-display');
        const totalReviews = document.getElementById('total-reviews');
        
        if (productStars) productStars.innerHTML = generateStars(averageRating);
        if (productRating) productRating.textContent = averageRating.toFixed(1);
        if (productSold) productSold.textContent = ratingCount.toLocaleString();
        if (reviewCount) reviewCount.textContent = ratingCount;
        if (avgRatingDisplay) avgRatingDisplay.textContent = averageRating.toFixed(1);
        if (avgStarsDisplay) avgStarsDisplay.innerHTML = generateStars(averageRating);
        if (totalReviews) totalReviews.textContent = ratingCount;
    }

    // Render reviews list
    function renderReviews(reviews) {
        // Check if reviews tab is visible
        const reviewsTab = document.getElementById('tab-reviews');
        if (!reviewsTab) {
            console.warn('Reviews tab not found');
            return;
        }
        
        if (reviewsTab.classList.contains('hidden')) {
            console.log('Reviews tab is hidden, skipping render');
            return;
        }
        
        const reviewsList = document.getElementById('reviews-list');
        const reviewsLoading = document.getElementById('reviews-loading');
        const reviewsEmpty = document.getElementById('reviews-empty');
        
        // Check if elements exist with detailed logging
        if (!reviewsList) {
            console.error('reviews-list element not found in renderReviews');
            return;
        }
        if (!reviewsLoading) {
            console.error('reviews-loading element not found in renderReviews');
            return;
        }
        if (!reviewsEmpty) {
            console.error('reviews-empty element not found in renderReviews');
            return;
        }
        
        reviewsLoading.classList.add('hidden');
        
        if (reviews.length === 0) {
            reviewsEmpty.classList.remove('hidden');
            // Clear existing reviews but keep loading/empty divs
            const existingReviews = reviewsList.querySelectorAll(':not(#reviews-loading):not(#reviews-empty)');
            existingReviews.forEach(el => el.remove());
            return;
        }

        reviewsEmpty.classList.add('hidden');
        
        // Clear existing reviews before rendering new ones (but keep loading/empty divs)
        const existingReviews = reviewsList.querySelectorAll(':not(#reviews-loading):not(#reviews-empty)');
        existingReviews.forEach(el => el.remove());
        
        // Render reviews
        const reviewsHTML = reviews.map(review => {
            const buyerName = review.buyer ? (review.buyer.name || review.buyer.email || 'Người dùng') : 'Người dùng';
            const buyerInitial = buyerName.charAt(0).toUpperCase();
            const buyerAvatar = review.buyer?.avatar || null;
            const formattedDate = new Date(review.created_at).toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            const formattedTime = new Date(review.created_at).toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const stars = generateStars(review.rating);
            
            return `
                <div class="bg-white rounded-xl border border-slate-200 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        ${buyerAvatar ? 
                            `<img src="${escapeHtml(buyerAvatar)}" alt="${escapeHtml(buyerName)}" class="flex-shrink-0 w-12 h-12 rounded-full object-cover border-2 border-slate-200">` :
                            `<div class="flex-shrink-0 w-12 h-12 rounded-full bg-game-accent/10 flex items-center justify-center text-game-accent font-bold text-lg">
                                ${buyerInitial}
                            </div>`
                        }
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-semibold text-slate-800">${escapeHtml(buyerName)}</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-slate-800">${formattedDate}</div>
                                    <div class="text-xs text-slate-500">${formattedTime}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mb-3">
                                ${stars}
                                <span class="text-sm text-slate-600 font-medium">${review.rating}/5</span>
                            </div>
                            ${review.comment ? `<div class="mt-3"><p class="text-slate-700 whitespace-pre-wrap leading-relaxed">${escapeHtml(review.comment)}</p></div>` : '<p class="text-slate-400 italic text-sm">Không có nhận xét</p>'}
                            ${review.images && review.images.length > 0 ? `
                                <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-2">
                                    ${review.images.map(img => `
                                        <img src="${escapeHtml(img)}" alt="Review image" class="w-full h-32 object-cover rounded-lg border border-slate-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="window.open('${escapeHtml(img)}', '_blank')">
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        // Append reviews to the list (after loading/empty divs)
        reviewsList.insertAdjacentHTML('beforeend', reviewsHTML);
    }

    // Update rating bars based on actual review data
    function updateRatingBars(reviews) {
        const ratingBars = document.getElementById('rating-bars');
        if (!ratingBars) return;

        // Calculate rating distribution
        const distribution = {5: 0, 4: 0, 3: 0, 2: 0, 1: 0};
        reviews.forEach(review => {
            if (review.rating >= 1 && review.rating <= 5) {
                distribution[review.rating]++;
            }
        });

        const total = reviews.length;
        
        ratingBars.innerHTML = [5,4,3,2,1].map(star => {
            const count = distribution[star] || 0;
            const percentage = total > 0 ? (count / total * 100) : 0;
            
            return `
                <div class="flex items-center gap-2">
                    <span class="text-sm w-3">${star}</span>
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-400 rounded-full transition-all duration-300" style="width: ${percentage}%"></div>
                    </div>
                    <span class="text-xs text-slate-500 w-8 text-right">${count}</span>
                </div>
            `;
        }).join('');
    }

    // Check if user can review (has purchased this product)
    async function checkUserCanReview() {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        const formContainer = document.getElementById('review-form-container');
        const alreadyReviewedContainer = document.getElementById('review-already-container');
        
        if (!token) {
            if (formContainer) formContainer.classList.add('hidden');
            if (alreadyReviewedContainer) alreadyReviewedContainer.classList.add('hidden');
            return;
        }

        try {
            // First, check if user has already reviewed this product by checking reviewsData
            let currentUserId = null;
            try {
                const userResponse = await fetch(`${BASE_URL}/api/auth/me`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
                if (userResponse.ok) {
                    const userData = await userResponse.json();
                    if (userData.success && userData.data) {
                        currentUserId = userData.data.id;
                    }
                }
            } catch (e) {
                console.warn('Could not get current user info:', e);
            }

            // Check if user has already reviewed this product
            if (currentUserId && reviewsData && reviewsData.length > 0) {
                const userReview = reviewsData.find(review => review.buyer_id === currentUserId);
                if (userReview) {
                    // User has already reviewed - show message, hide form
                    if (formContainer) formContainer.classList.add('hidden');
                    if (alreadyReviewedContainer) alreadyReviewedContainer.classList.remove('hidden');
                    return;
                }
            }

            // Check if user has purchased this product
            const checkResponse = await fetch(`${REVIEWS_API_BASE_URL}/check/${gameId}`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (checkResponse.ok) {
                const checkData = await checkResponse.json();
                console.log('Check review data:', checkData);
                
                if (checkData.success && checkData.can_review && checkData.eligible_orders && checkData.eligible_orders.length > 0) {
                    // User has purchased this product and hasn't reviewed - show review form
                    userOrders = checkData.eligible_orders;
                    showReviewForm(checkData.eligible_orders);
                    if (alreadyReviewedContainer) alreadyReviewedContainer.classList.add('hidden');
                } else {
                    // User hasn't purchased this product or already reviewed
                    if (formContainer) formContainer.classList.add('hidden');
                    if (alreadyReviewedContainer) alreadyReviewedContainer.classList.add('hidden');
                    if (checkData.reason) {
                        console.log('Cannot review:', checkData.reason);
                    }
                }
            } else {
                const errorData = await checkResponse.json().catch(() => ({}));
                console.error('Error checking review eligibility:', checkResponse.status, errorData);
                if (formContainer) formContainer.classList.add('hidden');
                if (alreadyReviewedContainer) alreadyReviewedContainer.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error checking user can review:', error);
            if (formContainer) formContainer.classList.add('hidden');
            if (alreadyReviewedContainer) alreadyReviewedContainer.classList.add('hidden');
        }
    }

    // Show review form with eligible orders
    function showReviewForm(orders) {
        const formContainer = document.getElementById('review-form-container');
        const orderInput = document.getElementById('review-order-id');
        
        if (!formContainer || !orderInput) {
            console.error('Review form elements not found');
            return;
        }
        
        if (orders && orders.length > 0) {
            // Tự động chọn đơn hàng đầu tiên (hoặc đơn hàng mới nhất)
            const selectedOrder = orders[0]; // Lấy đơn hàng đầu tiên
            orderInput.value = selectedOrder.id;
            formContainer.classList.remove('hidden');
            console.log('Review form shown with order:', selectedOrder.id);
        } else {
            console.log('No eligible orders, hiding form');
            formContainer.classList.add('hidden');
        }
    }

    // Rating star selection
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('click', (e) => {
            const rating = parseInt(e.currentTarget.dataset.rating);
            selectedRating = rating;
            document.getElementById('selected-rating').value = rating;
            
            // Update star display
            document.querySelectorAll('.rating-star').forEach((s, index) => {
                const svg = s.querySelector('svg');
                if (index < rating) {
                    svg.classList.remove('text-slate-300');
                    svg.classList.add('text-yellow-400');
                } else {
                    svg.classList.remove('text-yellow-400');
                    svg.classList.add('text-slate-300');
                }
            });
        
            // Update rating text
            const ratingTexts = {
                1: 'Rất tệ',
                2: 'Tệ',
                3: 'Bình thường',
                4: 'Tốt',
                5: 'Rất tốt'
            };
            document.getElementById('rating-text').textContent = ratingTexts[rating] || '';
        });

        // Hover effect
        star.addEventListener('mouseenter', (e) => {
            const rating = parseInt(e.currentTarget.dataset.rating);
            document.querySelectorAll('.rating-star').forEach((s, index) => {
                const svg = s.querySelector('svg');
                if (index < rating) {
                    svg.classList.add('text-yellow-400');
                    svg.classList.remove('text-slate-300');
                }
            });
        });

        star.addEventListener('mouseleave', () => {
            if (selectedRating === 0) {
                document.querySelectorAll('.rating-star').forEach(s => {
                    const svg = s.querySelector('svg');
                    svg.classList.remove('text-yellow-400');
                    svg.classList.add('text-slate-300');
                });
            } else {
                document.querySelectorAll('.rating-star').forEach((s, index) => {
                    const svg = s.querySelector('svg');
                    if (index < selectedRating) {
                        svg.classList.add('text-yellow-400');
                        svg.classList.remove('text-slate-300');
                    } else {
                        svg.classList.remove('text-yellow-400');
                        svg.classList.add('text-slate-300');
                    }
                });
            }
        });
    });

    // Submit review form
    document.getElementById('review-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (!token) {
            alert('Vui lòng đăng nhập để đánh giá');
            return;
        }

        const rating = parseInt(document.getElementById('selected-rating').value);
        const comment = document.getElementById('review-comment').value.trim();
        const orderId = parseInt(document.getElementById('review-order-id').value);

        if (!rating || !orderId) {
            document.getElementById('review-error').textContent = 'Vui lòng chọn đánh giá và đơn hàng';
            document.getElementById('review-error').classList.remove('hidden');
            return;
        }

        const submitBtn = document.getElementById('submit-review-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full inline-block"></div> Đang gửi...';

        const errorDiv = document.getElementById('review-error');
        errorDiv.classList.add('hidden');

        try {
            const response = await fetch(`${REVIEWS_API_BASE_URL}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    rating: rating,
                    comment: comment || null
                })
            });

            const result = await response.json();

            if (result.success) {
                // Reset form
                document.getElementById('review-form').reset();
                selectedRating = 0;
                document.querySelectorAll('.rating-star').forEach(s => {
                    const svg = s.querySelector('svg');
                    svg.classList.remove('text-yellow-400');
                    svg.classList.add('text-slate-300');
                });
                document.getElementById('rating-text').textContent = '';
                document.getElementById('review-order-id').value = '';
                
                // Hide form immediately
                document.getElementById('review-form-container').classList.add('hidden');
                
                // Reload reviews to update the list, rating summary, and check if user can still review
                await loadReviews();
                
                // Check again to show "already reviewed" message
                await checkUserCanReview();
                
                // Scroll to reviews section to show the new review
                const reviewsTab = document.getElementById('tab-reviews');
                if (reviewsTab) {
                    reviewsTab.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                throw new Error(result.message || 'Không thể gửi đánh giá');
            }
        } catch (error) {
            console.error('Error submitting review:', error);
            errorDiv.textContent = error.message || 'Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.';
            errorDiv.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Gửi đánh giá';
        }
    });

    // Cancel review button
    document.getElementById('cancel-review-btn')?.addEventListener('click', () => {
        document.getElementById('review-form').reset();
        selectedRating = 0;
        document.querySelectorAll('.rating-star').forEach(s => {
            const svg = s.querySelector('svg');
            svg.classList.remove('text-yellow-400');
            svg.classList.add('text-slate-300');
        });
        document.getElementById('rating-text').textContent = '';
        document.getElementById('review-error').classList.add('hidden');
        document.getElementById('review-form-container').classList.add('hidden');
    });

    // ==================== END REVIEWS FUNCTIONALITY ====================

    // Khởi tạo: load sản phẩm, đánh giá & thảo luận khi DOM sẵn sàng
    document.addEventListener('DOMContentLoaded', () => {
        loadProduct();
        // Load reviews immediately when page loads
        loadReviews();
        setupDiscussionForm();
        renderDiscussion();
    });
</script>
@endpush
