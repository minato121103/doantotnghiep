# API Endpoints Documentation

## Tổng Quan
Tài liệu này mô tả tất cả các API endpoints đã được tạo cho **Phần 1 (Users)** và **Phần 2 (Module Bán Tài Khoản Steam)** theo thiết kế trong `DATABASE_DESIGN.md`.

---

## 1. USER API ENDPOINTS

### Base URL: `/api/users`

#### 1.1. Lấy danh sách users
- **Method:** `GET`
- **URL:** `/api/users`
- **Query Parameters:**
  - `role` (optional): Filter by role (admin, buyer, editor)
  - `status` (optional): Filter by status (active, inactive, banned)
  - `search` (optional): Search by name or email
  - `sort_by` (optional): Sort field (id, name, email, role, status, balance, total_orders, total_spent, created_at)
  - `sort_order` (optional): Sort direction (asc, desc) - default: desc
  - `per_page` (optional): Items per page (1-100) - default: 15
- **Response:** JSON với danh sách users và pagination

#### 1.2. Tạo user mới
- **Method:** `POST`
- **URL:** `/api/users`
- **Body:**
  ```json
  {
    "name": "string (required)",
    "email": "string (required, unique)",
    "password": "string (required, min:8)",
    "phone": "string (optional)",
    "avatar": "string (optional)",
    "role": "admin|buyer|editor (optional, default: buyer)",
    "status": "active|inactive|banned (optional, default: active)",
    "balance": "number (optional, min:0)",
    "address": "string (optional)",
    "birthday": "date (optional)",
    "gender": "male|female|other (optional)"
  }
  ```

#### 1.3. Lấy thông tin user theo ID
- **Method:** `GET`
- **URL:** `/api/users/{id}`
- **Response:** JSON với thông tin user và relationships (orders, transactions, reviews)

#### 1.4. Cập nhật user
- **Method:** `PUT` hoặc `PATCH`
- **URL:** `/api/users/{id}`
- **Body:** Tương tự như tạo user (tất cả fields optional)

#### 1.5. Xóa user
- **Method:** `DELETE`
- **URL:** `/api/users/{id}`
- **Note:** Soft delete

### Authenticated User Endpoints

#### 1.6. Lấy profile của user hiện tại
- **Method:** `GET`
- **URL:** `/api/user/profile`
- **Authentication:** Required (Sanctum)
- **Response:** JSON với thông tin user hiện tại

#### 1.7. Cập nhật profile của user hiện tại
- **Method:** `PUT` hoặc `PATCH`
- **URL:** `/api/user/profile`
- **Authentication:** Required (Sanctum)
- **Body:** Tương tự như cập nhật user

---

## 2. STEAM ACCOUNT API ENDPOINTS

### Base URL: `/api/steam-accounts`

**Lưu ý:** Các endpoints này nên được bảo vệ bởi middleware admin (chưa được implement).

#### 2.1. Lấy danh sách steam accounts
- **Method:** `GET`
- **URL:** `/api/steam-accounts`
- **Query Parameters:**
  - `status` (optional): Filter by status (available, sold, pending, suspended)
  - `game_id` (optional): Filter by game ID
  - `search` (optional): Search by username or email
  - `sort_by` (optional): Sort field
  - `sort_order` (optional): Sort direction
  - `per_page` (optional): Items per page
- **Response:** JSON với danh sách steam accounts (password và email_password bị ẩn)

#### 2.2. Tạo steam account mới
- **Method:** `POST`
- **URL:** `/api/steam-accounts`
- **Body:**
  ```json
  {
    "username": "string (required)",
    "password": "string (required)",
    "email": "string (required, email)",
    "email_password": "string (required)",
    "status": "available|sold|pending|suspended (optional, default: available)",
    "games": [1, 2, 3], // Array of product_simple IDs (required, min:1)
    "is_highlighted": {
      "1": true,  // game_id => boolean
      "2": false
    }
  }
  ```
- **Note:** Password và email_password sẽ được mã hóa tự động

#### 2.3. Tìm steam account có sẵn theo game
- **Method:** `GET`
- **URL:** `/api/steam-accounts/find-by-game/{gameId}`
- **Response:** JSON với thông tin steam account có sẵn (không trả về credentials)

#### 2.4. Lấy thông tin steam account theo ID
- **Method:** `GET`
- **URL:** `/api/steam-accounts/{id}`
- **Response:** JSON với thông tin steam account (password và email_password bị ẩn)

#### 2.5. Cập nhật steam account
- **Method:** `PUT` hoặc `PATCH`
- **URL:** `/api/steam-accounts/{id}`
- **Body:** Tương tự như tạo steam account (tất cả fields optional)

#### 2.6. Xóa steam account
- **Method:** `DELETE`
- **URL:** `/api/steam-accounts/{id}`
- **Note:** Soft delete

---

## 3. ORDER API ENDPOINTS

### Base URL: `/api/orders`

**Authentication:** Required (Sanctum)

#### 3.1. Lấy danh sách orders
- **Method:** `GET`
- **URL:** `/api/orders`
- **Authentication:** Required
- **Query Parameters:**
  - `buyer_id` (optional, admin only): Filter by buyer ID
  - `status` (optional): Filter by status (pending, processing, completed, cancelled, refunded)
  - `game_id` (optional): Filter by game ID
  - `search` (optional): Search by order code
  - `sort_by` (optional): Sort field
  - `sort_order` (optional): Sort direction
  - `per_page` (optional): Items per page
- **Response:** 
  - User: Chỉ thấy orders của chính mình
  - Admin: Thấy tất cả orders

#### 3.2. Tạo order mới
- **Method:** `POST`
- **URL:** `/api/orders`
- **Authentication:** Required
- **Body:**
  ```json
  {
    "product_simple_id": "integer (required)",
    "payment_method": "balance|banking|momo|zalopay (required)",
    "notes": "string (optional)"
  }
  ```
- **Flow:**
  1. Hệ thống tự động tìm steam account có game đó
  2. Tính toán amount từ giá game
  3. Nếu payment_method = "balance", kiểm tra và trừ balance
  4. Tạo order và order_item với credentials đã mã hóa
  5. Nếu thanh toán bằng balance, order tự động chuyển sang "processing"

#### 3.3. Lấy thông tin order theo ID
- **Method:** `GET`
- **URL:** `/api/orders/{id}`
- **Authentication:** Required
- **Response:** 
  - Nếu order status = "completed", credentials sẽ được giải mã và trả về
  - User chỉ thấy orders của chính mình
  - Admin thấy tất cả orders

#### 3.4. Cập nhật status của order (Admin only)
- **Method:** `PUT` hoặc `PATCH`
- **URL:** `/api/orders/{id}/status`
- **Authentication:** Required (Admin)
- **Body:**
  ```json
  {
    "status": "pending|processing|completed|cancelled|refunded (required)"
  }
  ```
- **Logic:**
  - Nếu status = "completed": Đánh dấu steam account là "sold"
  - Nếu status = "cancelled": Hoàn lại steam account về "available" và refund nếu đã thanh toán bằng balance

---

## 4. REVIEW API ENDPOINTS

### Base URL: `/api/reviews`

#### 4.1. Lấy danh sách reviews (Public)
- **Method:** `GET`
- **URL:** `/api/reviews`
- **Query Parameters:**
  - `product_simple_id` (optional): Filter by product/game
  - `buyer_id` (optional): Filter by buyer
  - `rating` (optional): Filter by rating (1-5)
  - `verified_only` (optional): Chỉ lấy reviews từ verified purchases
  - `sort_by` (optional): Sort field
  - `sort_order` (optional): Sort direction
  - `per_page` (optional): Items per page

#### 4.2. Lấy reviews theo product (Public)
- **Method:** `GET`
- **URL:** `/api/reviews/product/{productId}`
- **Query Parameters:** Tương tự như trên

#### 4.3. Lấy thông tin review theo ID (Public)
- **Method:** `GET`
- **URL:** `/api/reviews/{id}`

### Authenticated Review Endpoints

#### 4.4. Tạo review mới
- **Method:** `POST`
- **URL:** `/api/reviews`
- **Authentication:** Required
- **Body:**
  ```json
  {
    "order_id": "integer (required)",
    "rating": "integer (required, 1-5)",
    "comment": "string (optional)",
    "images": ["url1", "url2"] // Array of image URLs (optional)
  }
  ```
- **Validation:**
  - Order phải thuộc về user hiện tại
  - Order phải có status = "completed"
  - Mỗi order chỉ được review 1 lần
- **Note:** Tự động cập nhật rating statistics của product

#### 4.5. Cập nhật review
- **Method:** `PUT` hoặc `PATCH`
- **URL:** `/api/reviews/{id}`
- **Authentication:** Required
- **Body:** Tương tự như tạo review
- **Validation:** Chỉ có thể cập nhật review của chính mình

#### 4.6. Xóa review
- **Method:** `DELETE`
- **URL:** `/api/reviews/{id}`
- **Authentication:** Required
- **Validation:** User có thể xóa review của chính mình, Admin có thể xóa bất kỳ review nào

---

## 5. TRANSACTION API ENDPOINTS

### Base URL: `/api/transactions`

**Authentication:** Required (Sanctum)

#### 5.1. Lấy danh sách transactions
- **Method:** `GET`
- **URL:** `/api/transactions`
- **Authentication:** Required
- **Query Parameters:**
  - `user_id` (optional, admin only): Filter by user ID
  - `type` (optional): Filter by type (deposit, withdraw, purchase, refund, fee)
  - `status` (optional): Filter by status (pending, completed, failed, cancelled)
  - `payment_method` (optional): Filter by payment method
  - `search` (optional): Search by transaction code
  - `date_from` (optional): Filter from date (YYYY-MM-DD)
  - `date_to` (optional): Filter to date (YYYY-MM-DD)
  - `sort_by` (optional): Sort field
  - `sort_order` (optional): Sort direction
  - `per_page` (optional): Items per page
- **Response:**
  - User: Chỉ thấy transactions của chính mình
  - Admin: Thấy tất cả transactions

#### 5.2. Lấy thông tin transaction theo ID
- **Method:** `GET`
- **URL:** `/api/transactions/{id}`
- **Authentication:** Required

#### 5.3. Nạp tiền (Deposit)
- **Method:** `POST`
- **URL:** `/api/transactions/deposit`
- **Authentication:** Required
- **Body:**
  ```json
  {
    "user_id": "integer (optional, admin only)",
    "amount": "number (required, min:0.01)",
    "payment_method": "string (required)",
    "description": "string (optional)"
  }
  ```
- **Logic:**
  - User có thể nạp tiền cho chính mình
  - Admin có thể nạp tiền cho bất kỳ user nào
  - Tự động cập nhật balance của user

#### 5.4. Rút tiền (Withdraw Request)
- **Method:** `POST`
- **URL:** `/api/transactions/withdraw`
- **Authentication:** Required
- **Body:**
  ```json
  {
    "amount": "number (required, min:0.01)",
    "payment_method": "string (required)",
    "description": "string (optional)"
  }
  ```
- **Logic:**
  - Kiểm tra balance đủ
  - Tạo transaction với status = "pending"
  - Chờ admin phê duyệt (balance chưa bị trừ)

#### 5.5. Cập nhật status của transaction (Admin only)
- **Method:** `PUT` hoặc `PATCH`
- **URL:** `/api/transactions/{id}/status`
- **Authentication:** Required (Admin)
- **Body:**
  ```json
  {
    "status": "pending|completed|failed|cancelled (required)"
  }
  ```
- **Logic:**
  - Nếu approve withdraw (pending → completed): Trừ balance của user
  - Nếu cancel withdraw: Không làm gì (balance chưa bị trừ)

#### 5.6. Lấy thống kê transactions
- **Method:** `GET`
- **URL:** `/api/transactions/statistics`
- **Authentication:** Required
- **Query Parameters:**
  - `user_id` (optional, admin only): Filter by user
  - `date_from` (optional): Filter from date
  - `date_to` (optional): Filter to date
- **Response:**
  ```json
  {
    "total_deposits": 1000000,
    "total_withdraws": 500000,
    "total_purchases": 2000000,
    "total_refunds": 100000,
    "total_transactions": 50
  }
  ```

---

## 6. AUTHENTICATION

Tất cả các endpoints yêu cầu authentication sử dụng **Laravel Sanctum**.

### Lấy token (nếu chưa có):
```bash
POST /api/login
# Hoặc sử dụng Laravel's default authentication
```

### Sử dụng token:
```
Authorization: Bearer {token}
```

---

## 7. RESPONSE FORMAT

### Success Response:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response:
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... } // Validation errors
}
```

### Pagination Response:
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

---

## 8. NOTES

1. **Bảo mật:**
   - Tất cả passwords (steam account, email) được mã hóa bằng Laravel Crypt
   - Credentials chỉ được giải mã khi order status = "completed"
   - User chỉ thấy dữ liệu của chính mình (trừ admin)

2. **Validation:**
   - Tất cả inputs đều được validate
   - Error messages rõ ràng và chi tiết

3. **Database Transactions:**
   - Các operations quan trọng (tạo order, cập nhật balance) sử dụng DB transactions
   - Đảm bảo data consistency

4. **Soft Deletes:**
   - Users và Steam Accounts sử dụng soft deletes
   - Có thể khôi phục nếu cần

5. **Relationships:**
   - Tất cả models đã được setup relationships đầy đủ
   - Eager loading được sử dụng để optimize queries

---

## 9. TESTING

Có thể test các endpoints bằng:
- Postman
- cURL
- Laravel HTTP Client
- Frontend application

---

**Tài liệu này mô tả đầy đủ các API endpoints cho Phần 1 và Phần 2 của hệ thống.**

