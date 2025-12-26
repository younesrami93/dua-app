@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold text-dark">Dashboard</h2>
                <p class="text-muted">Overview of platform activity</p>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3 fw-medium">{{ Auth::user()->name }}</span>
                <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card-modern p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small fw-bold uppercase">Total Users</span>
                        <i class="fas fa-users text-primary opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_users']) }}</h3>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +12% this week</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small fw-bold uppercase">Active Duas</span>
                        <i class="fas fa-praying-hands text-warning opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['active_posts']) }}</h3>
                    <small class="text-muted">Since launch</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small fw-bold uppercase">Pending Reports</span>
                        <i class="fas fa-exclamation-circle text-danger opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($stats['pending_reports']) }}</h3>
                    <small class="text-danger fw-bold">Action required</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-modern p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small fw-bold uppercase">Server Load</span>
                        <i class="fas fa-server text-info opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-0">12%</h3>
                    <small class="text-success">System healthy</small>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3">Recent Reports</h5>
        <div class="card-modern p-0 overflow-hidden">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted small border-0">Reported Content</th>
                        <th class="px-4 py-3 text-muted small border-0">Reason</th>
                        <th class="px-4 py-3 text-muted small border-0">Date</th>
                        <th class="px-4 py-3 text-muted small border-0">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-3">"Please help me..."</td>
                        <td class="px-4 py-3"><span
                                class="badge bg-danger-subtle text-danger border border-danger-subtle">Hate Speech</span>
                        </td>
                        <td class="px-4 py-3 text-muted">2 mins ago</td>
                        <td class="px-4 py-3"><button class="btn btn-sm btn-outline-dark">Review</button></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3">"I want to ask..."</td>
                        <td class="px-4 py-3"><span
                                class="badge bg-warning-subtle text-warning border border-warning-subtle">Spam</span></td>
                        <td class="px-4 py-3 text-muted">1 hour ago</td>
                        <td class="px-4 py-3"><button class="btn btn-sm btn-outline-dark">Review</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection