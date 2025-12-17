# Thiết Kế Database - Web Bán Tài Khoản Game & Tin Tức Công Nghệ

## Tổng Quan
Hệ thống bao gồm 2 module chính:
1. **E-commerce**: Bán tài khoản game
2. **CMS**: Quản lý và hiển thị tin tức công nghệ

---

## 1. BẢNG USERS (Mở rộng)

### 1.1. Bảng `users` (Cập nhật)
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('phone', 20)->nullable();
    $table->string('avatar')->nullable();
    $table->enum('role', ['admin', 'buyer', 'editor'])->default('buyer');
    $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
    $table->decimal('balance', 15, 2)->default(0); // Số dư tài khoản
    $table->integer('total_orders')->default(0);
    $table->integer('total_spent')->default(0);
    $table->string('address')->nullable();
    $table->date('birthday')->nullable();
    $table->enum('gender', ['male', 'female', 'other'])->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});
```

**Các trường mới:**
- `phone`: Số điện thoại
- `avatar`: Ảnh đại diện
- `role`: Vai trò (admin, buyer, editor)
- `status`: Trạng thái tài khoản
- `balance`: Số dư ví
- `total_orders`: Tổng số đơn hàng
- `total_spent`: Tổng số tiền đã chi
- `address`: Địa chỉ
- `birthday`: Ngày sinh
- `gender`: Giới tính


---

## 2. MODULE BÁN TÀI KHOẢN STEAM

**Định hướng:** Người dùng chọn mua game (từ `product_simple`), sau đó hệ thống sẽ gửi tài khoản Steam chứa game đó. Tài khoản Steam không có view/display, chỉ là sản phẩm ẩn mà người dùng nhận được khi mua hàng.

### 2.1. Bảng `steam_accounts` (Tài khoản Steam - Sản phẩm ẩn)
```php
Schema::create('steam_accounts', function (Blueprint $table) {
    $table->id();
    
    // Thông tin đăng nhập tài khoản Steam
    $table->string('username', 100); // Tên tài khoản Steam
    $table->text('password'); // Mật khẩu tài khoản Steam (sẽ được mã hóa)
    $table->string('email', 255); // Gmail liên kết với tài khoản
    $table->text('email_password'); // Mật khẩu Gmail (sẽ được mã hóa)
    
    // Trạng thái tài khoản
    $table->enum('status', ['available', 'sold', 'pending', 'suspended'])->default('available');
    
    $table->timestamp('sold_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('status');
});
```

**Giải thích:**
- `username`: Tên tài khoản Steam (để đăng nhập)
- `password`: Mật khẩu tài khoản Steam (sẽ được mã hóa bằng Laravel Encryption)
- `email`: Gmail liên kết với tài khoản Steam
- `email_password`: Mật khẩu Gmail (sẽ được mã hóa bằng Laravel Encryption)
- `status`: Trạng thái tài khoản (có sẵn, đã bán, đang chờ, bị treo)
- **Lưu ý:** Tài khoản Steam không có view/display, chỉ là sản phẩm ẩn được gửi cho người mua

**Lưu ý bảo mật:**
- Tất cả mật khẩu (password và email_password) phải được mã hóa trước khi lưu vào database
- Sử dụng Laravel Encryption: `Crypt::encryptString($password)`
- Chỉ hiển thị thông tin đăng nhập cho người mua sau khi đơn hàng đã hoàn thành

### 2.2. Bảng `steam_account_games` (Game trong tài khoản Steam - Pivot Table)
```php
Schema::create('steam_account_games', function (Blueprint $table) {
    $table->id();
    $table->foreignId('steam_account_id')->constrained('steam_accounts')->onDelete('cascade');
    $table->foreignId('product_simple_id')->constrained('product_simple')->onDelete('cascade'); // Sử dụng product_simple thay vì games
 // Danh sách achievement đã đạt được
    $table->boolean('is_highlighted')->default(false); // Game được highlight
    $table->timestamps();
    
    $table->unique(['steam_account_id', 'product_simple_id']); // Mỗi game chỉ xuất hiện 1 lần trong 1 tài khoản
    $table->index('product_simple_id'); // Để tìm tài khoản có game cụ thể
});
```

**Giải thích:**
- Bảng này liên kết tài khoản Steam với các game có trong thư viện
- Sử dụng `product_simple_id` thay vì `game_id` (vì game được lưu trong bảng `product_simple`)
- `is_highlighted`: Game được highlight (game chính trong tài khoản)
- **Quan trọng:** Khi người mua chọn mua game, hệ thống sẽ query bảng này để tìm tài khoản Steam có game đó và tự động gán vào đơn hàng

### 2.3. Bảng `orders`
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_code', 20)->unique(); // Mã đơn hàng
    $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('steam_account_id')->constrained('steam_accounts')->onDelete('cascade');
    $table->foreignId('product_simple_id')->constrained('product_simple')->onDelete('cascade'); // Game mà người mua muốn mua
    $table->decimal('amount', 15, 2);
    $table->decimal('fee', 15, 2)->default(0); // Phí giao dịch
    $table->enum('payment_method', ['balance', 'banking', 'momo', 'zalopay'])->default('balance');
    $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'refunded'])->default('pending');
    $table->text('notes')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->timestamps();
    
    $table->index(['buyer_id', 'status']);
    $table->index('order_code');
    $table->index('product_simple_id'); // Để tìm đơn hàng theo game
});
```

**Giải thích:**
- `product_simple_id`: Game mà người mua muốn mua (người mua chọn game từ product_simple)
- `steam_account_id`: Tài khoản Steam được tự động gửi cho người mua (hệ thống tự động tìm tài khoản Steam có game đó từ bảng `steam_account_games`)
- **Luồng hoạt động:** Người mua chọn game → Hệ thống tìm tài khoản Steam có game đó → Tự động tạo đơn hàng và gửi tài khoản

### 2.4. Bảng `order_items` (Chi tiết đơn hàng - tài khoản đã mua)
```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
    $table->foreignId('steam_account_id')->constrained('steam_accounts');
    $table->foreignId('product_simple_id')->constrained('product_simple'); // Game được mua
    $table->json('steam_credentials'); // Thông tin đăng nhập Steam (username, password, email, email_password - đã mã hóa)
    $table->decimal('price', 15, 2);
    $table->timestamps();
});
```

**Giải thích:**
- `steam_credentials`: Thông tin đăng nhập Steam (username, password, email, email_password - tất cả đã được mã hóa)
- `product_simple_id`: Game được mua (sử dụng bảng product_simple)
- Thông tin này được lưu tại thời điểm đơn hàng hoàn thành để gửi cho người mua

### 2.5. Bảng `reviews` (Đánh giá sản phẩm)
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
    $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('product_simple_id')->constrained('product_simple')->onDelete('cascade'); // Game được đánh giá
    $table->integer('rating')->unsigned(); // 1-5 sao
    $table->text('comment')->nullable();
    $table->json('images')->nullable(); // Ảnh đính kèm
    $table->boolean('is_verified_purchase')->default(true);
    $table->timestamps();
    
    $table->unique('order_id'); // Mỗi đơn hàng chỉ được đánh giá 1 lần
});
```

**Giải thích:**
- Đánh giá dựa trên game (product_simple) chứ không phải tài khoản Steam
- Người mua đánh giá game sau khi mua, không đánh giá tài khoản Steam cụ thể

### 2.6. Bảng `transactions` (Lịch sử giao dịch)
```php
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('transaction_code', 50)->unique();
    $table->enum('type', ['deposit', 'withdraw', 'purchase', 'refund', 'fee'])->default('deposit');
    $table->decimal('amount', 15, 2);
    $table->decimal('balance_before', 15, 2);
    $table->decimal('balance_after', 15, 2);
    $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
    $table->string('payment_method')->nullable();
    $table->text('description')->nullable();
    $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
    $table->timestamps();
    
    $table->index(['user_id', 'type', 'status']);
});
```

---

## 3. MODULE TIN TỨC CÔNG NGHỆ

### 3.1. Bảng `news_categories`
```php
Schema::create('news_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('icon')->nullable();
    $table->string('color', 20)->nullable(); // Màu sắc cho category
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### 3.2. Bảng `articles` (Tin tức)
```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('category_id')->constrained('news_categories')->onDelete('cascade');
    $table->string('title', 255);
    $table->string('slug')->unique();
    $table->string('excerpt', 500)->nullable(); // Tóm tắt
    $table->text('content'); // Nội dung HTML
    $table->string('featured_image')->nullable();
    $table->json('images')->nullable(); // Mảng ảnh trong bài viết
    $table->json('tags')->nullable(); // Mảng tags
    $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
    $table->boolean('is_featured')->default(false);
    $table->boolean('allow_comments')->default(true);
    $table->integer('view_count')->default(0);
    $table->integer('like_count')->default(0);
    $table->integer('comment_count')->default(0);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['category_id', 'status']);
    $table->index(['author_id', 'status']);
    $table->index('is_featured');
    $table->index('published_at');
});
```

### 3.3. Bảng `article_comments` (Bình luận tin tức)
```php
Schema::create('article_comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('parent_id')->nullable()->constrained('article_comments')->onDelete('cascade'); // Reply comment
    $table->text('content');
    $table->integer('like_count')->default(0);
    $table->enum('status', ['approved', 'pending', 'spam', 'deleted'])->default('pending');
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['article_id', 'status']);
    $table->index('parent_id');
});
```

### 3.4. Bảng `article_likes` (Like bài viết)
```php
Schema::create('article_likes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->timestamps();
    
    $table->unique(['article_id', 'user_id']);
});
```

### 3.5. Bảng `comment_likes` (Like bình luận)
```php
Schema::create('comment_likes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('comment_id')->constrained('article_comments')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->timestamps();
    
    $table->unique(['comment_id', 'user_id']);
});
```

---

## 4. BẢNG HỖ TRỢ CHUNG

### 4.1. Bảng `notifications` (Thông báo)
```php
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('type', 50); // order_placed, order_completed, new_comment, etc.
    $table->string('title', 255);
    $table->text('message');
    $table->json('data')->nullable(); // Dữ liệu bổ sung
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'is_read']);
});
```

### 4.2. Bảng `settings` (Cài đặt hệ thống)
```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->string('type', 50)->default('text'); // text, number, boolean, json
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### 4.3. Bảng `contact_messages` (Liên hệ)
```php
Schema::create('contact_messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('name', 100);
    $table->string('email', 100);
    $table->string('phone', 20)->nullable();
    $table->string('subject', 255);
    $table->text('message');
    $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
    $table->text('reply')->nullable();
    $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('replied_at')->nullable();
    $table->timestamps();
    
    $table->index('status');
});
```

---

## 5. QUAN HỆ GIỮA CÁC BẢNG (Relationships)

### Users Model
```php
// User.php
public function orders() {
    return $this->hasMany(Order::class, 'buyer_id');
}

public function articles() {
    return $this->hasMany(Article::class, 'author_id');
}

public function transactions() {
    return $this->hasMany(Transaction::class);
}

public function reviews() {
    return $this->hasMany(Review::class, 'buyer_id');
}
```

### SteamAccount Model
```php
// SteamAccount.php
public function games() {
    return $this->belongsToMany(ProductSimple::class, 'steam_account_games', 'steam_account_id', 'product_simple_id')
                ->withPivot('is_highlighted')
                ->withTimestamps();
}

public function orders() {
    return $this->hasMany(Order::class);
}

// Scope để tìm tài khoản có game cụ thể
public function scopeHasGame($query, $productSimpleId) {
    return $query->whereHas('games', function($q) use ($productSimpleId) {
        $q->where('product_simple.id', $productSimpleId);
    });
}
```

### ProductSimple Model
```php
// ProductSimple.php
public function steamAccounts() {
    return $this->belongsToMany(SteamAccount::class, 'steam_account_games', 'product_simple_id', 'steam_account_id')
                ->withPivot('is_highlighted')
                ->withTimestamps();
}

public function orders() {
    return $this->hasMany(Order::class, 'product_simple_id');
}

public function reviews() {
    return $this->hasMany(Review::class, 'product_simple_id');
}
```

### Order Model
```php
// Order.php
public function buyer() {
    return $this->belongsTo(User::class, 'buyer_id');
}

public function steamAccount() {
    return $this->belongsTo(SteamAccount::class);
}

public function game() {
    return $this->belongsTo(ProductSimple::class, 'product_simple_id');
}

public function items() {
    return $this->hasMany(OrderItem::class);
}

public function review() {
    return $this->hasOne(Review::class);
}

public function transactions() {
    return $this->hasMany(Transaction::class);
}
```

### Article Model
```php
// Article.php
public function author() {
    return $this->belongsTo(User::class, 'author_id');
}

public function category() {
    return $this->belongsTo(NewsCategory::class, 'category_id');
}

public function comments() {
    return $this->hasMany(ArticleComment::class);
}

public function likes() {
    return $this->hasMany(ArticleLike::class);
}
```

---

## 6. INDEXES VÀ OPTIMIZATION

### Indexes quan trọng:
- `users`: email, role, status
- `steam_accounts`: status (tài khoản ẩn, không cần index cho view/display)
- `steam_account_games`: product_simple_id (để tìm tài khoản có game cụ thể)
- `orders`: buyer_id, status, order_code, product_simple_id
- `articles`: category_id, author_id, status, published_at, is_featured
- `transactions`: user_id, type, status

---

## 7. GỢI Ý BỔ SUNG

### 7.1. Bảo mật
- Mã hóa thông tin tài khoản game trong `order_items.account_credentials`
- Sử dụng Laravel Encryption để mã hóa password tài khoản
- Log mọi thay đổi quan trọng (audit trail)

### 7.2. Performance
- Cache các danh mục, bài viết nổi bật
- Sử dụng Redis cho session và cache
- Optimize queries với Eager Loading

### 7.3. Tính năng mở rộng
- Hệ thống voucher/coupon
- Chương trình affiliate
- Hệ thống báo cáo (report)

---

## 8. MIGRATION FILES CẦN TẠO

1. `2025_01_27_000001_update_users_table_add_fields.php` - Cập nhật bảng users
2. `2025_01_27_000003_create_steam_accounts_table.php` - Tạo bảng steam_accounts (sản phẩm ẩn)
3. `2025_01_27_000004_create_steam_account_games_table.php` - Tạo bảng pivot steam_account_games (sử dụng product_simple)
4. `2025_01_27_000004_create_orders_table.php`
5. `2025_01_27_000005_create_order_items_table.php`
6. `2025_01_27_000006_create_reviews_table.php`
7. `2025_01_27_000007_create_transactions_table.php`
8. `2025_01_27_000011_create_news_categories_table.php`
9. `2025_01_27_000012_create_articles_table.php`
10. `2025_01_27_000013_create_article_comments_table.php`
11. `2025_01_27_000014_create_article_likes_table.php`
12. `2025_01_27_000015_create_comment_likes_table.php`
13. `2025_01_27_000016_create_notifications_table.php`
14. `2025_01_27_000017_create_settings_table.php`
15. `2025_01_27_000018_create_contact_messages_table.php`

**Lưu ý:** 
- Sử dụng bảng `product_simple` có sẵn để lưu thông tin game, không cần tạo bảng `games` riêng.
- Tài khoản Steam là sản phẩm ẩn, không có view/display. Người dùng chọn mua game, hệ thống tự động gửi tài khoản Steam chứa game đó.

---

## 9. LƯU Ý QUAN TRỌNG

1. **Soft Deletes**: Sử dụng cho users, steam_accounts, articles để có thể khôi phục
2. **Timestamps**: Tất cả bảng đều có created_at và updated_at
3. **Foreign Keys**: Sử dụng onDelete('cascade') hoặc onDelete('set null') phù hợp
4. **JSON Fields**: Sử dụng cho dữ liệu linh hoạt (tags, achievements, steam_credentials, etc.)
5. **Enum Fields**: Sử dụng cho status, type để đảm bảo tính nhất quán
6. **Unique Constraints**: Email, slug, order_code, transaction_code
7. **Indexes**: Tạo index cho các cột thường xuyên query, đặc biệt là product_simple_id trong steam_account_games
8. **Pivot Table**: steam_account_games để liên kết nhiều-nhiều giữa Steam accounts và Games (product_simple)
9. **Tài khoản Steam ẩn**: Tài khoản Steam không có view/display, chỉ là sản phẩm được gửi cho người mua sau khi chọn game

---

## 10. VÍ DỤ DỮ LIỆU MẪU

### steam_credentials (JSON) trong order_items:
```json
{
  "username": "encrypted_steam_username",
  "password": "encrypted_password",
  "email": "encrypted_email",
  "email_password": "encrypted_email_password"
}
```

**Lưu ý:** Tất cả thông tin đăng nhập đã được mã hóa bằng Laravel Encryption trước khi lưu vào database.

---

**Tài liệu này cung cấp thiết kế database hoàn chỉnh cho hệ thống của bạn. Bạn có thể điều chỉnh theo nhu cầu cụ thể!**

