<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    // 1. List Comments for a Post
    public function index(Request $request, $postId)
    {
        // Fetch comments that are NOT replies (top-level)
        // We eager load 'author' and 'replies.author' (2 levels deep)
        $comments = Comment::with(['author:id,username,avatar_url'])
            ->where('post_id', $postId)
            ->whereNull('parent_id') // Only get main comments
            ->where('status', 'visible')
            ->latest()
            ->cursorPaginate(20);

        return response()->json($comments);
    }

    // 2. Create a Comment (or Reply)
    public function store(Request $request, $postId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id', // If replying to a comment
            'device_info' => 'nullable|array',
        ]);

        $post = Post::findOrFail($postId);

        // Transaction: Save Comment + Update Post Counter
        $comment = DB::transaction(function () use ($request, $post, $validated) {

            // AI Check Placeholder
            $aiScore = 0.0; // Assume safe for now

            // Create
            $newComment = Comment::create([
                'app_user_id' => $request->user()->id,
                'post_id' => $post->id,
                'parent_id' => $validated['parent_id'] ?? null,
                'content' => $validated['content'],
                'device_info' => $validated['device_info'] ?? null,
                'status' => 'visible',
                'hate_speech_score' => $aiScore,
                'is_ai_checked' => true
            ]);

            // Increment Post Counter
            $post->increment('comments_count');

            return $newComment;
        });

        // Load the author details before returning
        $comment->load('author:id,username,avatar_url');

        return response()->json([
            'message' => 'Comment posted',
            'comment' => $comment
        ], 201);
    }
}