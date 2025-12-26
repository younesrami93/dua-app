@extends('layouts.main')

@section('content')
<div class="container-fluid">
    
    <div class="card-modern p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">Moderation Queue</h3>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-light btn-sm text-danger">Reset Filters</a>
        </div>

        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open (Needs Action)</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="reason" class="form-select" onchange="this.form.submit()">
                    <option value="">All Reasons</option>
                    <option value="spam" {{ request('reason') == 'spam' ? 'selected' : '' }}>Spam</option>
                    <option value="hate_speech" {{ request('reason') == 'hate_speech' ? 'selected' : '' }}>Hate Speech</option>
                    <option value="harassment" {{ request('reason') == 'harassment' ? 'selected' : '' }}>Harassment</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-modern w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="card-modern p-0 overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small">Reported Content</th>
                    <th class="px-4 py-3 text-muted small">Reason</th>
                    <th class="px-4 py-3 text-muted small">Reporter</th>
                    <th class="px-4 py-3 text-muted small">Date</th>
                    <th class="px-4 py-3 text-muted small">Status</th>
                    <th class="px-4 py-3 text-muted small text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                <tr>
                    <td class="px-4 py-3" style="max-width: 300px;">
                        <div class="d-flex align-items-center mb-1">
                            @if($report->reported_type == 'App\Models\Post')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-2" style="font-size: 0.7rem;">POST</span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle me-2" style="font-size: 0.7rem;">COMMENT</span>
                            @endif
                            <small class="text-muted">ID: {{ $report->reported_id }}</small>
                        </div>
                        
                        @if($report->reported)
                            <div class="text-truncate fw-medium text-dark">
                                {{ Str::limit($report->reported->content, 60) }}
                            </div>
                        @else
                            <div class="text-danger small fst-italic"><i class="fas fa-trash me-1"></i> Content already deleted</div>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <span class="badge bg-light text-dark border border-secondary-subtle text-uppercase">{{ $report->reason }}</span>
                        @if($report->details)
                            <div class="small text-muted mt-1 fst-italic">"{{ Str::limit($report->details, 30) }}"</div>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="small fw-bold">{{ $report->reporter->username ?? 'Guest' }}</div>
                    </td>

                    <td class="px-4 py-3 text-muted small">
                        {{ $report->created_at->diffForHumans() }}
                    </td>

                    <td class="px-4 py-3">
                        @if($report->status == 'open')
                            <span class="badge bg-danger">Open</span>
                        @elseif($report->status == 'resolved')
                            <span class="badge bg-success-subtle text-success">Resolved</span>
                        @else
                            <span class="badge bg-secondary">Dismissed</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-end">
                        <button onclick="loadModal('{{ route('admin.reports.show', $report->id) }}')" class="btn btn-sm btn-dark shadow-sm">
                            Review
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-3 border-top">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection