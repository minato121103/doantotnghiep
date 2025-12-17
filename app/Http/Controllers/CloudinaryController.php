<?php
namespace App\Http\Controllers;

use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CloudinaryController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Upload image to Cloudinary
     */
    public function upload(Request $request)
    {
        // Kiểm tra nếu ảnh đã được upload
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $folder = $request->input('folder', 'laravel_images');

            // Gọi service để upload ảnh lên Cloudinary
            $imageUrl = $this->cloudinaryService->uploadImage($image->getRealPath(), $folder);

            if ($imageUrl) {
                return response()->json([
                    'success' => true,
                    'url' => $imageUrl,
                ]);
            } else {
                return response()->json(['error' => 'Upload failed'], 400);
            }
        }

        return response()->json(['error' => 'No image provided'], 400);
    }

    /**
     * Upload all images from public/image folder
     */
    public function uploadAll(Request $request)
    {
        $folder = $request->input('folder', 'laravel_images');
        $imagePath = public_path('image');
        
        if (!File::exists($imagePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Image folder not found'
            ], 404);
        }

        $files = File::files($imagePath);
        $uploaded = [];
        $failed = [];

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                try {
                    $url = $this->cloudinaryService->uploadImage($file->getRealPath(), $folder);
                    if ($url) {
                        $uploaded[] = [
                            'filename' => $file->getFilename(),
                            'url' => $url,
                            'public_id' => $this->cloudinaryService->getPublicId($url)
                        ];
                    } else {
                        $failed[] = [
                            'filename' => $file->getFilename(),
                            'error' => 'Upload failed'
                        ];
                    }
                } catch (\Exception $e) {
                    $failed[] = [
                        'filename' => $file->getFilename(),
                        'error' => $e->getMessage()
                    ];
                }
            }
        }

        // Store results in session for later retrieval
        session([
            'upload_results' => [
                'timestamp' => now(),
                'total_processed' => count($files),
                'uploaded' => $uploaded,
                'failed' => $failed
            ]
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'total_processed' => count($files),
                'uploaded' => $uploaded,
                'failed' => $failed
            ]
        ]);
    }

    /**
     * Get upload results
     */
    public function results()
    {
        $results = session('upload_results');
        
        if (!$results) {
            return response()->json([
                'success' => false,
                'message' => 'No results found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}
