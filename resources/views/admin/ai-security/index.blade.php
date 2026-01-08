@extends('layouts.master')

@section('title') AI Security @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 font-size-18">
                    <i class="ri-shield-check-line"></i> AI Security Dashboard
                </h4>
                <p class="text-muted mt-2">Monitor and manage AI system security</p>
            </div>
        </div>
    </div>

    <!-- Security Metrics -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Total Requests</p>
                            <h4 class="mb-0">{{ number_format($metrics['total_requests']) }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary text-primary rounded-3">
                                <i class="ri-bar-chart-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Blocked Requests</p>
                            <h4 class="mb-0 text-danger">{{ $metrics['blocked_requests'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-danger text-danger rounded-3">
                                <i class="ri-shield-cross-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Rate Limit Hits</p>
                            <h4 class="mb-0 text-warning">{{ $metrics['rate_limit_hits'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning text-warning rounded-3">
                                <i class="ri-time-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Suspicious Activities</p>
                            <h4 class="mb-0 text-warning">{{ $metrics['suspicious_activities'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning text-warning rounded-3">
                                <i class="ri-alert-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Recommendations -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-lightbulb-line text-warning"></i> Security Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($recommendations as $rec)
                        <div class="col-md-6">
                            <div class="alert alert-{{ $rec['priority'] === 'high' ? 'danger' : 'warning' }} mb-0">
                                <h6 class="alert-heading">{{ $rec['title'] }}</h6>
                                <p class="mb-0">{{ $rec['description'] }}</p>
                                <small class="text-muted">Priority: <strong>{{ ucfirst($rec['priority']) }}</strong></small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Tools -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-shield-check-line text-success"></i> Input Validator
                    </h5>
                </div>
                <div class="card-body">
                    <form id="validateForm">
                        <div class="mb-3">
                            <label class="form-label">Test Input</label>
                            <textarea class="form-control" name="data" rows="3" placeholder="Enter text to validate..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line"></i> Validate & Sanitize
                        </button>
                    </form>

                    <div id="validateResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-timer-line text-info"></i> Rate Limit Checker
                    </h5>
                </div>
                <div class="card-body">
                    <p>Check if a user is within rate limits for AI actions.</p>
                    <button type="button" class="btn btn-info" id="checkRateLimit">
                        <i class="ri-refresh-line"></i> Check My Rate Limit
                    </button>

                    <div id="rateLimitResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Best Practices -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-information-line text-primary"></i> Security Best Practices
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="ri-check-line text-success"></i> All inputs are sanitized and validated</li>
                        <li class="mb-2"><i class="ri-check-line text-success"></i> Rate limiting is enabled (60 requests/minute)</li>
                        <li class="mb-2"><i class="ri-check-line text-success"></i> Permission-based access control (RBAC)</li>
                        <li class="mb-2"><i class="ri-check-line text-success"></i> Full audit logging via Spatie Activity Log</li>
                        <li class="mb-2"><i class="ri-check-line text-success"></i> CSRF protection enabled (Laravel default)</li>
                        <li class="mb-2"><i class="ri-check-line text-success"></i> Suspicious activity detection</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Validate Input
$('#validateForm').submit(function(e) {
    e.preventDefault();
    
    const data = $('[name="data"]').val();
    
    axios.post('{{ route("ai.security.validate-input") }}', { data })
        .then(response => {
            if (response.data.success) {
                const alertClass = response.data.suspicious ? 'danger' : 'success';
                const icon = response.data.suspicious ? 'ri-alert-line' : 'ri-check-line';
                
                let html = `
                    <div class="alert alert-${alertClass}">
                        <h6><i class="${icon}"></i> ${response.data.suspicious ? 'Suspicious Input Detected!' : 'Input is Clean'}</h6>
                        <p><strong>Sanitized:</strong></p>
                        <pre class="mb-0">${response.data.sanitized}</pre>
                    </div>
                `;
                
                $('#validateResult').html(html).show();
            }
        })
        .catch(error => {
            toastr.error('Validation failed');
        });
});

// Check Rate Limit
$('#checkRateLimit').click(function() {
    $(this).prop('disabled', true);
    
    axios.get('{{ route("ai.security.check-rate-limit") }}')
        .then(response => {
            if (response.data.success) {
                const allowed = response.data.allowed;
                const alertClass = allowed ? 'success' : 'warning';
                const icon = allowed ? 'ri-check-line' : 'ri-time-line';
                const message = allowed ? 'You are within rate limits' : 'Rate limit exceeded. Please wait.';
                
                $('#rateLimitResult').html(`
                    <div class="alert alert-${alertClass}">
                        <i class="${icon}"></i> ${message}
                    </div>
                `).show();
            }
        })
        .catch(error => {
            toastr.error('Rate limit check failed');
        })
        .finally(() => {
            $(this).prop('disabled', false);
        });
});
</script>
@endsection
