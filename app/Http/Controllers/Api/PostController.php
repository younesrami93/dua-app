<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // 1. Get the Feed (Public or Protected)
    public function index(Request $request)
    {
        // Validation: Allow filtering by category
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'app_user_id' => 'nullable|exists:app_users,id',
        ]);

        // Start the query
        $userId = $request->user()->id;


        $query = Post::with(['author:id,username,avatar_url', 'category:id,name,icon_url'])
            ->where('status', 'published')
            ->withExists([
                'interactions as is_liked' => function ($q) use ($userId) {
                    $q->where('app_user_id', $userId)
                        ->where('type', 'like');
                }
            ]);


        if ($request->has('app_user_id')) {
            $query->where('app_user_id', $request->app_user_id);
        }


        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Order by newest
        $posts = $query->latest()->cursorPaginate(15);

        return response()->json($posts);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string|max:1000', // Limit to 1000 chars
            'is_anonymous' => 'boolean',
            'device_info' => 'nullable|array', // JSON from Android
        ]);

        $aiResult = $this->analyzeHateSpeech($validated['content']);

        // C. Determine Status
        // If score is high (> 0.8), we ban it immediately.
        // If score is medium (> 0.5), we set to 'pending' for review.
        $status = 'published';
        if ($aiResult['score'] > 0.8)
            $status = 'banned';
        elseif ($aiResult['score'] > 0.5)
            $status = 'pending';

        // D. Create the Post
        $post = Post::create([
            'app_user_id' => $request->user()->id, // The logged-in user
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'device_info' => $validated['device_info'] ?? null,

            // Safety Fields
            'status' => $status,
            'is_ai_checked' => true,
            'hate_speech_score' => $aiResult['score'],
            'safety_label' => $aiResult['label'],
        ]);

        // E. Return the created post
        return response()->json([
            'message' => 'Dua posted successfully',
            'post' => $post
        ], 201);
    }

    // --- Private Helper for AI ---
    // Later, we will connect this to the real Google Cloud API
    private function analyzeHateSpeech(string $text): array
    {
        // TODO: Replace with real Google Cloud Language API call
        // For now, we return a "Safe" dummy result to let you test
        return [
            'score' => 0.0,      // 0.0 = Safe, 1.0 = Toxic
            'label' => 'Clean'
        ];
    }
}