# Community Feature - Documentation

## Overview
A Facebook-like community page has been created next to the News section in the header navigation. This feature allows users to post content with images and videos to find gaming partners.

## Features Implemented

### 1. Database Structure
- **Table**: `community_posts`
- **Fields**:
  - `id`: Primary key
  - `user_id`: Foreign key to users table
  - `content`: Text content of the post
  - `images`: JSON array of image URLs
  - `videos`: JSON array of video URLs
  - `game_preference`: The game they want to play together
  - `likes_count`: Number of likes (for future expansion)
  - `comments_count`: Number of comments (for future expansion)
  - `privacy`: public/friends/private
  - `is_active`: Boolean flag
  - `timestamps`: created_at, updated_at

### 2. Backend Components

#### Model
- **CommunityPost.php**: Eloquent model with relationships to User

#### Controller
- **CommunityController.php**:
  - `index()`: Display community page
  - `getAllPosts()`: API endpoint to fetch all posts with pagination
  - `createPost()`: API endpoint to create new post (requires authentication)
  - `deletePost()`: API endpoint to delete post (owner or admin only)

#### Routes
- **Web Routes**:
  - `GET /community`: Display community page
  
- **API Routes**:
  - `GET /api/community/posts`: Get all posts (public)
  - `POST /api/community/posts`: Create new post (authenticated)
  - `DELETE /api/community/posts/{id}`: Delete post (authenticated)

### 3. Frontend Components

#### Community Page (`resources/views/main/community.blade.php`)
Features:
- **Facebook-like Interface**:
  - Post creation card with avatar
  - Modal for creating posts
  - Media upload support (via URLs)
  - Game preference selection
  - Privacy settings (public/friends/private)

- **Post Display**:
  - User avatar and name
  - Timestamp (relative time)
  - Post content
  - Image/video gallery (responsive grid layout)
  - Game preference badge
  - Delete button (for post owner or admin)

- **Authentication**:
  - Guest users see a login prompt
  - Authenticated users can create and delete posts

- **Infinite Loading**:
  - Initial load of 10 posts
  - "Load More" button for pagination

### 4. Design Features

#### Modern UI Elements
- Gradient backgrounds
- Rounded corners and shadows
- Hover effects and transitions
- Responsive grid layouts for media
- Beautiful color scheme matching the site

#### Media Grid System
- Single image: Full width
- Two images: 2-column grid
- Multiple images: 3-column grid
- Support for both images and videos

### 5. Sample Data
Created 10 sample posts with various content types:
- Posts with images
- Posts with multiple images
- Posts with game preferences
- Posts seeking teammates
- Tutorial/tips posts
- Tournament announcements

## Navigation
The "Cộng đồng" (Community) link has been added to the header navigation, positioned right after "Tin tức" (News).

## Security
- Authentication required for creating and deleting posts
- Authorization check: users can only delete their own posts (or admin can delete any)
- CSRF protection via Laravel Sanctum
- Input validation for all fields

## Future Enhancements (Not Implemented Yet)
1. Like/unlike functionality
2. Comment system
3. Real-time updates using WebSockets
4. Image/video upload to server (currently uses URLs)
5. User mentions (@username)
6. Hashtags (#tag)
7. Share functionality
8. Report inappropriate content
9. Search and filter posts
10. Friend system for "friends-only" posts

## Usage

### For Users
1. Navigate to "Cộng đồng" from the header
2. Click on the post creation box or buttons
3. Write content, add media URLs, specify game preference
4. Click "Đăng bài" to post
5. View posts from other users
6. Click delete button on your own posts to remove them

### For Developers
- Migration file: `database/migrations/2026_02_05_084807_create_community_posts_table.php`
- Model: `app/Models/CommunityPost.php`
- Controller: `app/Http/Controllers/CommunityController.php`
- View: `resources/views/main/community.blade.php`
- Seeder: `database/seeders/CommunityPostSeeder.php`
- API Routes: `routes/api.php` (lines 257-270)
- Web Routes: `routes/web.php` (line 38)

## API Endpoints

### GET /api/community/posts
Fetch all community posts with pagination.

**Query Parameters**:
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Posts per page (default: 10)

**Response**:
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 50
  }
}
```

### POST /api/community/posts
Create a new community post (requires authentication).

**Headers**:
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Body**:
```json
{
  "content": "Post content",
  "images": ["url1", "url2"],
  "videos": ["url1"],
  "game_preference": "Game Name",
  "privacy": "public"
}
```

### DELETE /api/community/posts/{id}
Delete a community post (requires authentication, owner or admin only).

**Headers**:
- `Authorization: Bearer {token}`

## Testing
1. Visit `/community` page
2. Login with a user account
3. Create a new post with content and media
4. View the post in the feed
5. Try deleting your own post
6. Test pagination by loading more posts

## Technical Notes
- Uses Laravel Blade templates
- JavaScript for dynamic content loading
- Tailwind-inspired custom CSS
- RESTful API design
- JSON responses for all API endpoints
- Sanctum authentication for API protection
