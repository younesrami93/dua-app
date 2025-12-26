<div class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Device & User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="small fw-bold text-muted">User ID</label>
                    <div>{{ $user->id }}</div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Device UUID</label>
                    <div class="bg-light p-2 rounded small font-monospace">{{ $user->device_uuid }}</div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Raw Device Data</label>
                    <pre class="bg-light p-2 rounded small border" style="max-height: 200px; overflow-y: auto;">
{{ json_encode($user->last_device_info, JSON_PRETTY_PRINT) }}
                    </pre>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label class="small fw-bold text-muted">Joined</label>
                        <div>{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold text-muted">Last IP</label>
                        <div>{{ $user->last_ip_address }}</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>