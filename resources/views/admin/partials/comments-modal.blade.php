<div class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Comments for Post #{{ $post->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($comments->count() > 0)
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light small text-uppercase">
                            <tr>
                                <th class="px-4">User</th>
                                <th class="px-4">Comment</th>
                                <th class="px-4">Date</th>
                                <th class="px-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $comment)
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold">{{ $comment->author->username ?? 'Guest' }}</div>
                                    </td>
                                    <td class="px-4 text-wrap">
                                        {{ $comment->content }}
                                    </td>
                                    <td class="px-4 text-muted small">
                                        {{ $comment->created_at->format('M d, H:i') }}
                                    </td>
                                    <td class="px-4 text-end">
                                        <button
                                            onclick="deleteItem('{{ route('admin.comments.delete', $comment->id) }}', '{{ request()->fullUrl() }}')"
                                            class="btn btn-sm btn-light text-danger hover-bg-danger" title="Delete Comment">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-3">
                        {{ $comments->links() }}
                    </div>
                @else
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-comment-slash fa-2x mb-3"></i>
                        <p>No comments found on this post.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>