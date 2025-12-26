@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <div class="card-modern p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0">Manage Posts</h3>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-light btn-sm text-danger">Reset Filters</a>
            </div>

            <form method="GET" action="{{ route('admin.posts.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search content or user..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Newest First
                        </option>
                        <option value="likes_count" {{ request('sort') == 'likes_count' ? 'selected' : '' }}>Most Liked
                        </option>
                        <option value="reports_count" {{ request('sort') == 'reports_count' ? 'selected' : '' }}>Most Reported
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary-modern w-100">Filter</button>
                </div>
            </form>

            @if(request('user_id'))
                <div class="mt-3">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                        Filtering by User ID: {{ request('user_id') }}
                        <a href="{{ route('admin.posts.index', request()->except('user_id')) }}" class="ms-2 text-primary"><i
                                class="fas fa-times"></i></a>
                    </span>
                </div>
            @endif
        </div>

        <div class="card-modern p-0 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted small">User</th>
                        <th class="px-4 py-3 text-muted small">Content</th>
                        <th class="px-4 py-3 text-muted small text-center">Metrics</th>
                        <th class="px-4 py-3 text-muted small">Status</th>
                        <th class="px-4 py-3 text-muted small text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $post)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold">
                                    <a href="{{ route('admin.posts.index', ['user_id' => $post->app_user_id]) }}"
                                        class="text-dark text-decoration-none" title="Filter by this user">
                                        {{ $post->author->username ?? 'Guest' }}
                                    </a>
                                </div>
                                <div class="small text-muted">{{ $post->created_at->diffForHumans() }}</div>
                            </td>

                            <td class="px-4 py-3" style="width: 40%;">
                                <p class="mb-0 text-truncate" style="max-width: 350px;">{{ $post->content }}</p>
                                <div class="mt-1">
                                    <span class="badge bg-light text-secondary border">{{ $post->category->name }}</span>
                                    @if($post->hate_speech_score > 0.5)
                                        <span class="badge bg-danger-subtle text-danger">Toxic:
                                            {{ $post->hate_speech_score }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-3">
                                    <span class="text-muted" title="Likes"><i class="fas fa-heart me-1 text-danger"></i>
                                        {{ $post->likes_count }}</span>

                                    <a href="#"
                                        onclick="event.preventDefault(); loadModal('{{ route('admin.posts.comments', $post->id) }}')"
                                        class="text-decoration-none text-dark" title="View Comments">
                                        <i class="fas fa-comment me-1 text-primary"></i> {{ $post->comments_count }}
                                    </a>

                                    <span class="text-muted" title="Shares"><i class="fas fa-share me-1 text-success"></i>
                                        {{ $post->shares_count }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                @if($post->status == 'published')
                                    <span class="badge bg-success-subtle text-success">Published</span>
                                @elseif($post->status == 'banned')
                                    <span class="badge bg-danger">Banned</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ ucfirst($post->status) }}</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    @if($post->status !== 'banned')
                                        <form action="{{ route('admin.posts.ban', $post->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" title="Ban Post"><i
                                                    class="fas fa-ban"></i></button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.posts.delete', $post->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger" title="Delete"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-3 border-top">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection