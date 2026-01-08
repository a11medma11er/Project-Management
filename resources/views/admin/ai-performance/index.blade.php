@extends('layouts.master')

@section('title') AI Performance @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 font-size-18">
                    <i class="ri-dashboard-line"></i> AI Performance Dashboard
                </h4>
                <p class="text-muted mt-2">Monitor and optimize AI system performance</p>
            </div>
        </div>
    </div>

    <!-- Performance Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Avg Response Time</p>
                            <h4 class="mb-0">{{ $perfStats['avg_duration'] ?? 0 }}ms</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary text-primary rounded-3">
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
                            <p class="text-muted mb-2">Operations</p>
                            <h4 class="mb-0">{{ $perfStats['count'] ?? 0 }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success text-success rounded-3">
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
                            <p class="text-muted mb-2">Memory Usage</p>
                            <h4 class="mb-0">{{ $systemMetrics['memory_usage'] ?? 0 }} MB</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-info text-info rounded-3">
                                <i class="ri-database-2-line font-size-24"></i>
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
                            <p class="text-muted mb-2">Cache Driver</p>
                            <h4 class="mb-0">{{ ucfirst($cacheStats['driver'] ?? 'file') }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning text-warning rounded-3">
                                <i class="ri-fire-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cache Management -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-database-line text-success"></i> Cache Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p><strong>Driver:</strong> {{ $cacheStats['driver'] ?? 'N/A' }}</p>
                        <p><strong>TTL:</strong> {{ $cacheStats['ttl'] ?? 0 }} seconds</p>
                        <p><strong>Prefix:</strong> {{ $cacheStats['prefix'] ?? 'N/A' }}</p>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger btn-sm" id="clearCache">
                            <i class="ri-delete-bin-line"></i> Clear All Cache
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="warmUpCache">
                            <i class="ri-fire-line"></i> Warm Up Cache
                        </button>
                    </div>

                    <div id="cacheResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-cpu-line text-info"></i> System Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tr>
                                <td>Memory Usage:</td>
                                <td><strong>{{ $systemMetrics['memory_usage'] ?? 0 }} MB</strong></td>
                            </tr>
                            <tr>
                                <td>Peak Memory:</td>
                                <td><strong>{{ $systemMetrics['memory_peak'] ?? 0 }} MB</strong></td>
                            </tr>
                            <tr>
                                <td>Memory Limit:</td>
                                <td><strong>{{ $systemMetrics['memory_limit'] ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td>CPU Load (1m):</td>
                                <td><strong>{{ $systemMetrics['cpu_load'][0] ?? 0 }}</strong></td>
                            </tr>
                        </table>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm" id="refreshMetrics">
                        <i class="ri-refresh-line"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Slow Operations -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-alert-line text-warning"></i> Slow Operations (> 1000ms)
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($slowOperations) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Operation</th>
                                        <th>Duration (ms)</th>
                                        <th>Memory (MB)</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slowOperations as $op)
                                    <tr>
                                        <td>{{ $op['operation'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $op['duration_ms'] > 2000 ? 'danger' : 'warning' }}">
                                                {{ $op['duration_ms'] }}
                                            </span>
                                        </td>
                                        <td>{{ $op['memory_used_mb'] }}</td>
                                        <td><small>{{ $op['timestamp'] }}</small></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="ri-check-line"></i> No slow operations detected in the last 24 hours
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Suggested Indexes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-settings-3-line text-primary"></i> Optimization Suggestions
                    </h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary btn-sm" id="getSuggestions">
                        <i class="ri-search-line"></i> Get Index Suggestions
                    </button>
                    
                    <div id="suggestionsResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Clear Cache
$('#clearCache').click(function() {
    if (!confirm('Are you sure you want to clear all AI caches?')) {
        return;
    }

    $(this).prop('disabled', true);
    
    axios.post('{{ route("ai.performance.clear-cache") }}')
        .then(response => {
            const msg = response.data.message;
            $('#cacheResult').html(`<div class="alert alert-success">${msg}</div>`).show();
            toastr.success(msg);
        })
        .catch(error => {
            const msg = error.response?.data?.message || 'Failed to clear cache';
            $('#cacheResult').html(`<div class="alert alert-danger">${msg}</div>`).show();
            toastr.error(msg);
        })
        .finally(() => {
            $(this).prop('disabled', false);
        });
});

// Warm Up Cache
$('#warmUpCache').click(function() {
    $(this).prop('disabled', true).html('<i class="ri-loader-4-line spinner-border-sm"></i> Warming...');
    
    axios.post('{{ route("ai.performance.warm-cache") }}')
        .then(response => {
            if (response.data.success) {
                const msg = response.data.message;
                $('#cacheResult').html(`<div class="alert alert-success">${msg}<br>Warmed: ${response.data.warmed.join(', ')}</div>`).show();
                toastr.success(msg);
            }
        })
        .catch(error => {
            const msg = error.response?.data?.message || 'Failed to warm cache';
            $('#cacheResult').html(`<div class="alert alert-danger">${msg}</div>`).show();
            toastr.error(msg);
        })
        .finally(() => {
            $(this).prop('disabled', false).html('<i class="ri-fire-line"></i> Warm Up Cache');
        });
});

// Refresh Metrics
$('#refreshMetrics').click(function() {
    $(this).prop('disabled', true);
    
    axios.get('{{ route("ai.performance.system-metrics") }}')
        .then(response => {
            if (response.data.success) {
                location.reload();
            }
        })
        .catch(error => {
            toastr.error('Failed to refresh metrics');
        })
        .finally(() => {
            $(this).prop('disabled', false);
        });
});

// Get Suggestions
$('#getSuggestions').click(function() {
    $(this).prop('disabled', true);
    
    axios.get('{{ route("ai.performance.suggested-indexes") }}')
        .then(response => {
            if (response.data.success) {
                const indexes = response.data.indexes;
                
                let html = '<div class="alert alert-info"><h6>Suggested Indexes:</h6><ul>';
                
                Object.keys(indexes).forEach(table => {
                    html += `<li><strong>${table}:</strong><ul>`;
                    Object.keys(indexes[table]).forEach(indexName => {
                        const columns = Array.isArray(indexes[table][indexName]) 
                            ? indexes[table][indexName].join(', ') 
                            : indexes[table][indexName];
                        html += `<li>${indexName}: ${columns}</li>`;
                    });
                    html += '</ul></li>';
                });
                
                html += '</ul></div>';
                
                $('#suggestionsResult').html(html).show();
                toastr.success('Suggestions loaded');
            }
        })
        .catch(error => {
            toastr.error('Failed to get suggestions');
        })
        .finally(() => {
            $(this).prop('disabled', false);
        });
});
</script>
@endsection
