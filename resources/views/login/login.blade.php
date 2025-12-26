@extends('layouts.main')

@section('content')
    <div class="col-md-4 col-lg-3">
        <div class="card-modern p-4 p-md-5">
            <div class="text-center mb-4">
                <h4 class="fw-bold">Welcome Back</h4>
                <p class="text-muted small">Sign in to manage the community</p>
            </div>

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@admin.com" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary-modern shadow-sm">
                    Sign In
                </button>
            </form>

            @if($errors->any())
                <div class="alert alert-danger mt-4 small border-0 bg-danger-subtle text-danger">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>
    </div>
@endsection