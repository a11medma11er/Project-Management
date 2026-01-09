@extends('layouts.master')

@section('title') AI Analytics @endsection

@section('content')
<div class="container-fluid">

    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18"><i class="ri-bar-chart-2-line"></i> AI Analytics</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('ai.control.index') }}">AI Control</a></li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Decisions</p>
                            <h4 class="mb-2">{{ number_format($analytics['summary']['total_decisions']) }}</h4>
                            <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><i class="ri-arrow-right-up-line me-1 align-middle"></i>All time</span></p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-stack-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Acceptance Rate</p>
                            <h4 class="mb-2">{{ $analytics['summary']['acceptance_rate'] }}%</h4>
                            <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><i class="ri-checkbox-circle-line me-1 align-middle"></i>Accepted</span></p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-check-double-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Avg. Confidence</p>
                            <h4 class="mb-2">{{ number_format($analytics['summary']['avg_confidence'] * 100, 1) }}%</h4>
                            <p class="text-muted mb-0"><span class="text-info fw-bold font-size-12 me-2"><i class="ri-shield-star-line me-1 align-middle"></i>Reliability</span></p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="ri-contrast-drop-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Tasks Affected</p>
                            <h4 class="mb-2">{{ number_format($analytics['impact']['tasks_affected']) }}</h4>
                            <p class="text-muted mb-0"><span class="text-warning fw-bold font-size-12 me-2"><i class="ri-task-line me-1 align-middle"></i>Impact</span></p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="ri-briefcase-4-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Decision Trend -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Decision Trends (Last 30 Days)</h4>
                </div>
                <div class="card-body">
                    <div id="decision_trend_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <!-- Decision Breakdown -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Decision Breakdown</h4>
                </div>
                <div class="card-body">
                    <div id="decision_breakdown_chart" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats Row -->
    <div class="row">
        <!-- Performance -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0">System Performance</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-1 d-block">Avg Response Time</span>
                            <h4 class="mb-0">{{ $analytics['performance']['avg_response_time_minutes'] }} min</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div id="response_time_spark" class="apex-charts"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="font-size-14">Throughput (Decisions/Day)</h5>
                            <span class="text-muted">{{ number_format($analytics['performance']['decisions_per_day'], 1) }}</span>
                        </div>
                        <div class="progress animated-progess" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $analytics['performance']['decisions_per_day'] * 2) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accuracy -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0">Accuracy Metrics</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-centered table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <td>
                                        <h6 class="font-size-14 mb-1">Overall Accuracy</h6>
                                        <p class="text-muted mb-0 font-size-12">Based on accepted/modified decisions</p>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-{{ $analytics['accuracy']['overall_accuracy'] > 80 ? 'success' : 'warning' }} font-size-12">
                                            {{ $analytics['accuracy']['overall_accuracy'] }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6 class="font-size-14 mb-1">High Confidence Acc.</h6>
                                        <p class="text-muted mb-0 font-size-12">Predictions > 80% confidence</p>
                                    </td>
                                    <td class="text-end fw-bold">{{ $analytics['accuracy']['high_confidence_accuracy'] }}%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6 class="font-size-14 mb-1">Low Confidence Acc.</h6>
                                        <p class="text-muted mb-0 font-size-12">Predictions < 60% confidence</p>
                                    </td>
                                    <td class="text-end fw-bold">{{ $analytics['accuracy']['low_confidence_accuracy'] }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engagement -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0">User Engagement</h4>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="avatar-sm mx-auto mb-4">
                            <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-24">
                                <i class="ri-user-voice-line"></i>
                            </span>
                        </div>
                        <p class="font-16 text-muted mb-2"></p>
                        <h5><span class="text-muted font-size-12">Review Rate:</span> {{ $analytics['user_engagement']['review_rate'] }}%</h5>
                        <p class="text-muted">of total AI suggestions reviewed by humans</p>
                    </div>
                    <div class="row mt-4">
                        <div class="col-6">
                            <div class="social-source text-center mt-3">
                                <div class="avatar-xs mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-success font-size-16">
                                        <i class="ri-chat-1-line text-white"></i>
                                    </span>
                                </div>
                                <h5 class="font-size-15">{{ $analytics['user_engagement']['comment_rate'] }}%</h5>
                                <p class="text-muted mb-0">With Feedback</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="social-source text-center mt-3">
                                <div class="avatar-xs mx-auto mb-3">
                                    <span class="avatar-title rounded-circle bg-info font-size-16">
                                        <i class="ri-group-line text-white"></i>
                                    </span>
                                </div>
                                <h5 class="font-size-15">{{ $analytics['user_engagement']['active_reviewers'] }}</h5>
                                <p class="text-muted mb-0">Active Reviewers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Decision Trends Chart
    var trendOptions = {
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        series: [{
            name: 'Total Decisions',
            data: @json(array_column($analytics['trends'], 'total'))
        }, {
            name: 'Accepted',
            data: @json(array_column($analytics['trends'], 'accepted'))
        }],
        colors: ['#556ee6', '#34c38f'],
        xaxis: {
            categories: @json(array_column($analytics['trends'], 'date')),
            type: 'datetime'
        },
        grid: { borderColor: '#f1f1f1' },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.9,
                stops: [0, 90, 100]
            }
        }
    };
    new ApexCharts(document.querySelector("#decision_trend_chart"), trendOptions).render();

    // Breakdown Chart
    var breakdownLabels = Object.keys(@json($analytics['decision_breakdown']));
    var breakdownData = Object.values(@json($analytics['decision_breakdown']));
    
    var breakdownOptions = {
        chart: {
            height: 350,
            type: 'donut',
        },
        series: breakdownData,
        labels: breakdownLabels,
        colors: ['#556ee6', '#f1b44c', '#34c38f', '#f46a6a', '#50a5f1'],
        legend: {
            position: 'bottom'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%'
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#decision_breakdown_chart"), breakdownOptions).render();
});
</script>
@endsection
