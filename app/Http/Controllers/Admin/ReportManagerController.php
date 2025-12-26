<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentReport;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class ReportManagerController extends Controller
{
    // 1. List Reports
    public function index(Request $request)
    {
        // Eager load the reporter and the reported content (morph)
        $query = ContentReport::with(['reporter', 'reported']);

        // A. Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: Show 'open' reports first
            $query->orderByRaw("FIELD(status, 'open', 'resolved', 'dismissed')");
        }

        // B. Filter by Reason
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $reports = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reports-index', compact('reports'));
    }

    // 2. Show Details Modal (Polymorphic)
    public function show($id)
    {
        $report = ContentReport::with(['reporter', 'reported'])->findOrFail($id);
        // We pass the report to the view. The view will handle if it's a Post or Comment.
        return view('admin.partials.reports-details-modal', compact('report'))->render();
    }

    // 3. Update Status (Resolve / Dismiss)
    public function updateStatus(Request $request, $id)
    {
        $report = ContentReport::findOrFail($id);

        $request->validate([
            'status' => 'required|in:resolved,dismissed,open',
            'admin_notes' => 'nullable|string'
        ]);

        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        return back()->with('success', 'Report status updated.');
    }
}