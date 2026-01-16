<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public function index(Request $request)
    {
        $query = News::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'time');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Special handling for time-based sorting (parse published_at or fallback to created_at)
        if ($sortBy === 'time' || $sortBy === 'published_at') {
            $direction = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
            $query->orderByRaw("
                COALESCE(
                    CASE 
                        WHEN published_at REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}' THEN 
                            STR_TO_DATE(SUBSTRING(published_at, 1, 16), '%d/%m/%Y %H:%i')
                        WHEN published_at REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}' THEN 
                            STR_TO_DATE(SUBSTRING(published_at, 1, 16), '%Y-%m-%d %H:%i')
                        ELSE NULL
                    END,
                    created_at
                ) {$direction}
            ");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $news = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $news->items(),
            'pagination' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
            ]
        ]);
    }

    /**
     * Store a newly created news in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'author' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'published_at' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $news = News::create([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'views' => 0,
            'category' => $request->category,
            'published_at' => $request->published_at ? trim($request->published_at) : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tin tức đã được tạo thành công',
            'data' => $news
        ], 201);
    }

    /**
     * Display the specified news.
     */
    public function show($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tin tức'
            ], 404);
        }

        // Increment views
        $news->increment('views');

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    /**
     * Update the specified news in storage.
     */
    public function update(Request $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tin tức'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'author' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'published_at' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $news->update([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'category' => $request->category,
            'published_at' => $request->published_at ? trim($request->published_at) : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tin tức đã được cập nhật thành công',
            'data' => $news
        ]);
    }

    /**
     * Remove the specified news from storage.
     */
    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tin tức'
            ], 404);
        }

        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tin tức đã được xóa thành công'
        ]);
    }
}
