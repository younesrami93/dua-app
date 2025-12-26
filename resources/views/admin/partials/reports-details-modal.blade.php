<div class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header {{ $report->status == 'open' ? 'bg-danger-subtle text-danger' : 'bg-light' }}">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-flag me-2"></i> Report #{{ $report->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-4 border-end">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Report Details</h6>

                        <div class="mb-3">
                            <label class="small text-muted">Reason</label>
                            <div class="fw-bold text-danger text-uppercase">{{ $report->reason }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Reporter Note</label>
                            <div class="bg-light p-2 rounded small border">
                                {{ $report->details ?? 'No details provided.' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted">Reporter</label>
                            <div>{{ $report->reporter->username ?? 'Unknown' }}</div>
                            <div class="small text-muted">{{ $report->reporter->email ?? 'Guest' }}</div>
                        </div>
                    </div>

                    <div class="col-md-8 ps-md-4">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Reported Content</h6>

                        @if($report->reported)
                        <div class="card card-body bg-light border-0 mb-3">
                            <p class="mb-0 lead fs-6">{{ $report->reported->content }}</p>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="small text-muted">
                                Posted by <span class="fw-bold text-dark">{{ $report->reported->author->username ??
                                    'Guest' }}</span>
                                • {{ $report->reported->created_at->format('M d, Y H:i') }}
                            </div>

                            @if($report->reported_type == 'App\Models\Post')
                            <span class="badge bg-primary">Post</span>
                            @else
                            <span class="badge bg-info">Comment</span>
                            @endif
                        </div>

                        @if($report->reported->status !== 'banned')
                        <div class="alert alert-warning d-flex align-items-center justify-content-between p-2">
                            <small><i class="fas fa-exclamation-triangle me-1"></i> Content is currently
                                visible.</small>

                            @php
                            // Determine Ban Route based on type
                            $banRoute = ($report->reported_type == 'App\Models\Post')
                            ? route('admin.posts.ban', $report->reported_id)
                            : null;
                            // Note: We didn't make a dedicated 'ban comment' route, just delete.
                            // You can add that later. For now, let's offer Delete for comments.
                            @endphp

                            @if($report->reported_type == 'App\Models\Post')
                            <form action="{{ $banRoute }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-danger">Ban Post Now</button>
                            </form>
                            @else
                            <button
                                onclick="deleteItem('{{ route('admin.comments.delete', $report->reported_id) }}', '{{ request()->fullUrl() }}')"
                                class="btn btn-sm btn-danger">Delete Comment</button>
                            @endif
                        </div>
                        @else
                        <div class="alert alert-danger p-2 small">
                            <i class="fas fa-ban me-1"></i> Content is already BANNED.
                        </div>
                        @endif

                        @else
                        <div class="alert alert-secondary">
                            <i class="fas fa-trash me-2"></i> This content has been deleted.
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <form action="{{ route('admin.reports.update', $report->id) }}" method="POST"
                    class="d-flex gap-2 w-100 justify-content-end">
                    @csrf

                    @if($report->status !== 'dismissed')
                    <button type="submit" name="status" value="dismissed" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Dismiss Report
                    </button>
                    @endif

                    @if($report->status !== 'resolved')
                    <button type="submit" name="status" value="resolved" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Mark Resolved
                    </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>