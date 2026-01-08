@extends('layouts.master')

@section('title') AI Learning Analytics @endsection

@section('css')
<style>
.metric-card {
    transition: all 0.3s ease;
    border-radius: 10px;
}
.metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
.improvement-badge {
    font-size: 0.9rem;
    padding: 0.4rem 0.8rem;
}
.accuracy-trend {
    height: 300px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 font-size-18">
                    <i class="ri-brain-line"></i> AI Learning Analytics
                </h4>
                <p class="text-muted mt-2">Track how AI improves over time based on your feedback</p>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card metric-card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-2 opacity-75">Acceptance Rate</p>
                            <h2 class="mb-0">{{ number_format($metrics['acceptance_rate'], 1) }}%</h2>
                        </div>
                        <div>
                            <i class="ri-thumb-up-line font-size-48 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card metric-card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-2 opacity-75">Learning Progress</p>
                            <h2 class="mb-0">
                                @if($metrics['learning_progress']['is_improving'])
                                    +{{ number_format($metrics['learning_progress']['improvement'], 1) }}%
                                @else
                                    {{ number_format($metrics['learning_progress']['improvement'], 1) }}%
                                @endif
                            </h2>
                        </div>
                        <div>
                            <i class="ri-line-chart-line font-size-48 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card metric-card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-2 opacity-75">Reviewed Decisions</p>
                            <h2 class="mb-0">{{ number_format($metrics['reviewed_decisions']) }}</h2>
                        </div>
                        <div>
                            <i class="ri-checkbox-multiple-line font-size-48 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card metric-card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-2 opacity-75">Modification Rate</p>
                            <h2 class="mb-0">{{ number_format($metrics['modification_rate'], 1) }}%</h2>
                        </div>
                        <div>
                            <i class="ri-edit-line font-size-48 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accuracy Trend Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-line-chart-line text-primary"></i> Accuracy Trend (Last 30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accuracy-trend">
                        <canvas id="accuracyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Improvements & Calibration Suggestions -->
    <div class="row">
        <!-- Recent Improvements -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-arrow-up-circle-line text-success"></i> Recent Improvements
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($recentImprovements as $improvement)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $improvement['decision_type'])) }}</h6>
                            <small class="text-muted">
                                {{ $improvement['old_accuracy'] }}% → {{ $improvement['new_accuracy'] }}%
                            </small>
                        </div>
                        <div>
                            <span class="improvement-badge badge bg-{{ $improvement['is_positive'] ? 'success' : 'danger' }}">
                                {{ $improvement['is_positive'] ? '+' : '' }}{{ $improvement['improvement'] }}%
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">No improvements detected yet. Keep providing feedback!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Calibration Suggestions -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-settings-3-line text-warning"></i> Calibration Suggestions
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($calibrationSuggestions as $suggestion)
                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading">{{ ucfirst(str_replace('_', ' ', $suggestion['decision_type'])) }}</h6>
                        <p class="mb-2">
                            Current avg confidence: <strong>{{ $suggestion['current_avg_confidence'] }}</strong><br>
                            Suggested confidence: <strong>{{ $suggestion['suggested_confidence'] }}</strong>
                        </p>
                        <small class="text-muted">
                            Adjustment: {{ $suggestion['difference'] > 0 ? '+' : '' }}{{ $suggestion['difference'] }}
                        </small>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">AI confidence is well-calibrated. No adjustments needed.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Patterns -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-checkbox-circle-line text-success"></i> Most Accepted Types
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Decision Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patterns['most_accepted_types'] as $type)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $type->decision_type)) }}</td>
                                    <td><span class="badge bg-success">{{ $type->count }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No data yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-close-circle-line text-danger"></i> Most Rejected Types
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Decision Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patterns['most_rejected_types'] as $type)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $type->decision_type)) }}</td>
                                    <td><span class="badge bg-danger">{{ $type->count }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No data yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Accuracy Trend Chart
const ctx = document.getElementById('accuracyTrendChart').getContext('2d');
const accuracyData = @json($metrics['accuracy_trend']);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: accuracyData.map(d => d.date),
        datasets: [{
            label: 'Accuracy (%)',
            data: accuracyData.map(d => d.accuracy),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Accuracy: ' + context.parsed.y.toFixed(1) + '%';
                    }
                }
            }
        }
    }
});
</script>
@endsection
