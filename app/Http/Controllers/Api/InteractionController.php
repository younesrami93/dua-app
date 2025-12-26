<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interaction;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InteractionController extends Controller
{
    // 1. Toggle Like (Like / Unlike)
    public function toggleLike(Request $request, $postId)
    {
        $user = $request->user();
        $post = Post::findOrFail($postId);

        // Check if already liked
        $existingLike = Interaction::where('app_user_id', $user->id)
            ->where('post_id', $post->id)
            ->where('type', 'like')
            ->first();

        // Use Transaction to keep count accurate
        DB::transaction(function () use ($existingLike, $user, $post) {
            if ($existingLike) {
                // UNLIKE: Delete record & decrement count
                $existingLike->delete();
                $post->decrement('likes_count');
                $message = 'Unliked';
            } else {
                // LIKE: Create record & increment count
                Interaction::create([
                    'app_user_id' => $user->id,
                    'post_id' => $post->id,
                    'type' => 'like'
                ]);
                $post->increment('likes_count');
                $message = 'Liked';
            }
        });

        // Return the new count so the App updates the UI immediately
        return response()->json([
            'message' => 'Success',
            'likes_count' => $post->fresh()->likes_count,
            'liked' => $existingLike ? false : true // True if we just liked it
        ]);
    }

    // 2. Track Share
    public function share(Request $request, $postId)
    {
        $user = $request->user();
        $post = Post::findOrFail($postId);

        $exists = Interaction::where('app_user_id', $user->id)
            ->where('post_id', $post->id)
            ->where('type', 'share')
            ->exists();

        if (!$exists) {
            DB::transaction(function () use ($user, $post) {
                Interaction::create([
                    'app_user_id' => $user->id,
                    'post_id' => $post->id,
                    'type' => 'share'
                ]);
                $post->increment('shares_count');
            });
        }

        return response()->json([
            'message' => 'Share tracked',
            'shares_count' => $post->fresh()->shares_count
        ]);
    }
}