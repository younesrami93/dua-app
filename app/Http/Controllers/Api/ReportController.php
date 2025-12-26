<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\ContentReport;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    // Submit a Report
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['post', 'comment'])], // What are we reporting?
            'id' => 'required|integer', // The ID of the post/comment
            'reason' => 'required|string|max:50', // e.g. "spam", "hate_speech"
            'details' => 'nullable|string|max:500', // Optional description
        ]);

        // 1. Find the content based on Type
        $modelClass = $validated['type'] === 'post' ? Post::class : Comment::class;
        $content = $modelClass::findOrFail($validated['id']);

        // 2. Create the Report
        ContentReport::create([
            'reporter_id' => $request->user()->id,
            'reported_id' => $content->id,
            'reported_type' => $modelClass, // Polymorphic magic
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'status' => 'open'
        ]);

        // 3. Increment the counter on the content (For Admin visibility)
        // This lets you quickly see "This post has 50 reports!"
        $content->increment('reports_count');

        return response()->json(['message' => 'Report submitted. Thank you for helping keep the community safe.']);
    }
}