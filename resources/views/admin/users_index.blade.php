@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <div class="card-modern p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0">App Users Manager</h3>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm text-danger">Reset Filters</a>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search name, email, or UUID..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="registered" {{ request('type') == 'registered' ? 'selected' : '' }}>Registered</option>
                        <option value="guest" {{ request('type') == 'guest' ? 'selected' : '' }}>Guests</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="activity" class="form-select" onchange="this.form.submit()">
                        <option value="">All Activity</option>
                        <option value="has_posts" {{ request('activity') == 'has_posts' ? 'selected' : '' }}>Has Posted
                        </option>
                        <option value="has_comments" {{ request('activity') == 'has_comments' ? 'selected' : '' }}>Has
                            Commented</option>
                        <option value="inactive" {{ request('activity') == 'inactive' ? 'selected' : '' }}>Inactive (>30 days)
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="updated_at" {{ request('sort') == 'updated_at' ? 'selected' : '' }}>Last Connected
                        </option>
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Joined Date
                        </option>
                        <option value="posts_count" {{ request('sort') == 'posts_count' ? 'selected' : '' }}>Top Posters
                        </option>
                        <option value="comments_count" {{ request('sort') == 'comments_count' ? 'selected' : '' }}>Top
                            Commenters</option>
                        <option value="hate_speech_violation_count" {{ request('sort') == 'hate_speech_violation_count' ? 'selected' : '' }}>Highest Risk</option>
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
                        <th class="px-4 py-3 text-muted small">Identity</th>
                        <th class="px-4 py-3 text-muted small text-center">Stats</th>
                        <th class="px-4 py-3 text-muted small">Risk Metrics</th>
                        <th class="px-4 py-3 text-muted small">Device Info</th>
                        <th class="px-4 py-3 text-muted small">Status</th>
                        <th class="px-4 py-3 text-muted small text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 border"
                                        style="width: 40px; height: 40px;">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" class="rounded-circle" width="40" height="40">
                                        @else
                                            <i class="fas fa-user text-muted"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">
                                            {{ $user->username ?? 'Unknown Guest' }}
                                        </div>
                                        <div class="small text-muted">
                                            @if($user->is_guest)
                                                <span class="badge bg-secondary-subtle text-secondary border">Guest</span>
                                            @else
                                                {{ $user->email }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2 mb-1">
                                    @if($user->posts_count > 0)
                                        <a href="{{ route('admin.posts.index', ['user_id' => $user->id]) }}"
                                            class="badge bg-primary-subtle text-primary text-decoration-none border border-primary-subtle"
                                            title="View Posts">
                                            {{ $user->posts_count }} P
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted border">0 P</span>
                                    @endif

                                    @if($user->comments_count > 0)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle"
                                            title="Comments">{{ $user->comments_count }} C</span>
                                    @else
                                        <span class="badge bg-light text-muted border">0 C</span>
                                    @endif
                                </div>
                                <div class="small text-muted" style="font-size: 0.7rem;">
                                    {{ $user->updated_at->diffForHumans() }}
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="d-flex flex-column gap-1">
                                    @if($user->hate_speech_violation_count > 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            {{ $user->hate_speech_violation_count }} Hate Flags
                                        </span>
                                    @endif
                                    @if($user->banned_posts_count > 0)
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                            <i class="fas fa-ban me-1"></i> {{ $user->banned_posts_count }} Banned Posts
                                        </span>
                                    @endif
                                    @if($user->hate_speech_violation_count == 0 && $user->banned_posts_count == 0)
                                        <span class="small text-success"><i class="fas fa-check-circle"></i> Clean</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <a href="#"
                                    onclick="event.preventDefault(); loadModal('{{ route('admin.users.show', $user->id) }}')"
                                    class="text-decoration-none text-dark">
                                    <div class="small fw-bold"><i class="fas fa-mobile-alt me-1"></i>
                                        {{ $user->last_device_info['model'] ?? 'Unknown' }}</div>
                                    <div class="small text-muted text-uppercase">{{ $user->country_code ?? 'N/A' }} •
                                        {{ $user->last_ip_address }}</div>
                                </a>
                            </td>

                            <td class="px-4 py-3">
                                @if($user->status == 'active')
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Banned</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                                        @csrf
                                        @if($user->status == 'active')
                                            <button class="btn btn-sm btn-outline-danger" title="Ban User"><i
                                                    class="fas fa-user-slash"></i></button>
                                        @else
                                            <button class="btn btn-sm btn-outline-success" title="Activate User"><i
                                                    class="fas fa-user-check"></i></button>
                                        @endif
                                    </form>

                                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                        onsubmit="return confirm('This will delete the user and all their posts. Continue?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger" title="Delete Account"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-3 border-top">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection