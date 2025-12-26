<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;

class AppUserManagerController extends Controller
{
    // 1. List Users

    public function index(Request $request)
    {
        // 1. Add withCount(['posts', 'comments'])
        // This adds 'posts_count' and 'comments_count' to each user object
        $query = AppUser::withCount(['posts', 'comments']);

        // A. Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('device_uuid', 'like', "%{$search}%");
            });
        }

        // B. Filter by Type
        if ($request->filled('type')) {
            if ($request->type === 'guest')
                $query->where('is_guest', true);
            elseif ($request->type === 'registered')
                $query->where('is_guest', false);
        }

        // C. Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // D. NEW: Filter by Activity (Has Posts / Has Comments)
        if ($request->filled('activity')) {
            if ($request->activity === 'has_posts')
                $query->has('posts');
            elseif ($request->activity === 'has_comments')
                $query->has('comments');
            elseif ($request->activity === 'inactive')
                $query->where('updated_at', '<', now()->subDays(30));
        }

        // E. Sorting (Updated)
        // We rely on 'updated_at' as "Last Connected" because we update the user row on every login
        $sort = $request->get('sort', 'updated_at');
        $dir = $request->get('dir', 'desc');

        $allowedSorts = [
            'created_at',
            'updated_at', // Last Connected
            'posts_count',
            'comments_count',
            'hate_speech_violation_count'
        ];

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $dir);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users_index', compact('users'));
    }

    // 2. Ban/Unban Action
    public function toggleBan($id)
    {
        $user = AppUser::findOrFail($id);

        if ($user->status === 'banned') {
            $user->update(['status' => 'active']);
            $msg = 'User activated successfully.';
        } else {
            $user->update(['status' => 'banned']);
            $msg = 'User has been banned.';
        }

        return back()->with('success', $msg);
    }

    // 3. Delete User
    public function destroy($id)
    {
        $user = AppUser::findOrFail($id);
        $user->delete(); // Soft Delete

        return back()->with('success', 'User deleted successfully.');
    }

    // 4. Show Details Modal (Device Info & JSON)
    public function show($id)
    {
        $user = AppUser::findOrFail($id);
        return view('admin.partials.users-details-modal', compact('user'))->render();
    }
}