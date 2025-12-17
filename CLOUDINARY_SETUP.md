# Cloudinary Setup Guide

## Cấu hình Cloudinary

Để sử dụng tính năng upload ảnh lên Cloudinary, bạn cần cấu hình các biến môi trường sau trong file `.env`:

```env
# Cloudinary Configuration
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

### Cách lấy thông tin Cloudinary:

1. Đăng ký tài khoản tại [Cloudinary](https://cloudinary.com/)
2. Vào Dashboard và copy thông tin:
   - Cloud Name
   - API Key
   - API Secret

## Sử dụng

### 1. Web Interface
Truy cập: `http://your-domain/cloudinary/upload`

### 2. Command Line
```bash
# Upload tất cả ảnh trong thư mục public/image
php artisan images:upload-to-cloudinary

# Upload với folder tùy chỉnh
php artisan images:upload-to-cloudinary --folder=game-store
```

### 3. API Endpoints
- `POST /cloudinary/upload` - Upload single image
- `POST /cloudinary/upload-all` - Upload all images from public/image
- `GET /cloudinary/results` - Get upload results

## Cấu trúc thư mục

```
public/
  └── image/          # Thư mục chứa ảnh để upload
      ├── game1.jpg
      ├── game2.png
      └── ...
```

## Tính năng

- ✅ Upload ảnh đơn lẻ
- ✅ Upload hàng loạt từ thư mục
- ✅ Hỗ trợ folder tùy chỉnh
- ✅ Hiển thị kết quả upload
- ✅ Command line interface
- ✅ Error handling
- ✅ Progress tracking 