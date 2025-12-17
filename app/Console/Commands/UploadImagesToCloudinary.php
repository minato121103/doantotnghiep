<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\File;

class UploadImagesToCloudinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:upload-to-cloudinary {--folder=laravel_images : Folder name on Cloudinary}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload all images from public/image folder to Cloudinary';

    protected $cloudinaryService;

    /**
     * Create a new command instance.
     */
    public function __construct(CloudinaryService $cloudinaryService)
    {
        parent::__construct();
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $folder = $this->option('folder');
        $imagePath = public_path('image');
        
        $this->info("Starting upload to Cloudinary folder: {$folder}");
        
        if (!File::exists($imagePath)) {
            $this->error("Image folder not found at: {$imagePath}");
            return Command::FAILURE;
        }

        $files = File::files($imagePath);
        $imageFiles = array_filter($files, function($file) {
            $extension = strtolower($file->getExtension());
            return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });

        if (empty($imageFiles)) {
            $this->warn("No image files found in {$imagePath}");
            return Command::SUCCESS;
        }

        $this->info("Found " . count($imageFiles) . " image files to upload");
        
        $progressBar = $this->output->createProgressBar(count($imageFiles));
        $progressBar->start();

        $uploaded = 0;
        $failed = 0;

        foreach ($imageFiles as $file) {
            try {
                $url = $this->cloudinaryService->uploadImage($file->getRealPath(), $folder);
                if ($url) {
                    $uploaded++;
                    $this->line("\n✓ Uploaded: " . $file->getFilename());
                } else {
                    $failed++;
                    $this->line("\n✗ Failed: " . $file->getFilename());
                }
            } catch (\Exception $e) {
                $failed++;
                $this->line("\n✗ Error uploading " . $file->getFilename() . ": " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Upload completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Successfully Uploaded', $uploaded],
                ['Failed', $failed],
                ['Total Processed', count($imageFiles)]
            ]
        );

        return Command::SUCCESS;
    }
}
