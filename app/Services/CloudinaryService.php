<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary = null;

    /**
     * Get Cloudinary instance (lazy loading)
     */
    protected function getCloudinary()
    {
        if ($this->cloudinary === null) {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ]
            ]);
        }
        return $this->cloudinary;
    }

    /**
     * Upload image to Cloudinary
     */
    public function uploadImage($filePath, $folder = 'laravel_images')
    {
        try {
            // Check if Cloudinary is configured
            if (empty(env('CLOUDINARY_CLOUD_NAME'))) {
                Log::warning('Cloudinary not configured. Please set CLOUDINARY_CLOUD_NAME in .env file');
                return null;
            }

            $cloudinary = $this->getCloudinary();
            $result = $cloudinary->uploadApi()->upload($filePath, [
                'folder' => $folder,
            ]);

            // Trả về URL của ảnh đã upload
            return $result['secure_url'];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract public_id from Cloudinary URL
     */
    public function getPublicId($url)
    {
        try {
            // Extract the path from URL like: https://res.cloudinary.com/cloud_name/image/upload/v1234567890/folder/image.jpg
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '';
            
            // Remove /image/upload/ and version number
            $pathParts = explode('/', trim($path, '/'));
            $uploadIndex = array_search('upload', $pathParts);
            
            if ($uploadIndex !== false && isset($pathParts[$uploadIndex + 2])) {
                // Skip version number and get the rest as public_id
                $publicIdParts = array_slice($pathParts, $uploadIndex + 2);
                return implode('/', $publicIdParts);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to extract public_id: ' . $e->getMessage());
            return null;
        }
    }
}
