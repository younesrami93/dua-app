<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\ContentReport;
use App\Models\Post;
use Illuminate\Http\Request;

class PostManagerController extends Controller
{
    // 1. List All Posts

    public function index(Request $request)
    {
        $query = Post::with(['author', 'category']);

        // A. Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // B. Filter by User
        if ($request->filled('user_id')) {
            $query->where('app_user_id', $request->user_id);
        }

        // C. Search (Content or Username)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%");
                    });
            });
        }

        // D. Sorting
        $sortField = $request->get('sort', 'created_at'); // Default: Date
        $sortDir = $request->get('dir', 'desc');

        // Allow sorting by metrics too
        $allowedSorts = ['created_at', 'likes_count', 'reports_count', 'hate_speech_score'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir);
        }

        $posts = $query->paginate(20)->withQueryString(); // Keep filters when paginating

        return view('admin.posts_index', compact('posts'));
    }

    // 2. Fetch Comments for Modal (Returns HTML Partial)
    public function comments(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $comments = Comment::with('author')
            ->where('post_id', $id)
            ->latest()
            ->paginate(10); // Pagination inside modal

        // We return a "Partial View" (just the table rows), not a full page
        return view('admin.partials.comments-modal', compact('post', 'comments'))->render();
    }

    // 3. Delete a Comment
    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $post = $comment->post;


        $comment->delete(); // Soft delete


        if ($post && $post->comments_count > 0) {
            $post->decrement('comments_count');
        }

        return response()->json(['message' => 'Comment deleted successfully.']);
    }


    // 2. Ban a Post
    public function ban($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['status' => 'banned']);

        ContentReport::where('reported_type', Post::class)
            ->where('reported_id', $id)
            ->where('status', 'open') // Only close the open ones
            ->update([
                'status' => 'resolved',
                'admin_notes' => 'System: Automatically resolved because the content was banned.'
            ]);

        return back()->with('success', 'Post has been banned.');
    }

    // 3. Delete a Post
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete(); // Soft Delete

        return back()->with('success', 'Post deleted successfully.');
    }
}