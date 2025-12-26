<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\Post;
use App\Models\ContentReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Fetch Real Stats
        $stats = [
            'total_users' => AppUser::count(),
            'active_posts' => Post::where('status', 'published')->count(),
            'pending_reports' => ContentReport::where('status', 'open')->count(),
            'banned_users' => AppUser::where('status', 'banned')->count(),
        ];

        // 2. Fetch Recent Reports (for the bottom table)
        $recentReports = ContentReport::with(['reporter', 'reported'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports'));
    }
}