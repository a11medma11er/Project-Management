@extends('layouts.master')

@section('title') AI Workflows & Automation @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 font-size-18">
                    <i class="ri-robot-2-line"></i> AI Workflows & Automation
                </h4>
                <p class="text-muted mt-2">Automate repetitive tasks with AI-powered workflows</p>
            </div>
        </div>
    </div>

    <!-- Run Manual Automation -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-play-circle-line text-success"></i> Run Automation
                    </h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary" id="runAutomation">
                        <i class="ri-robot-line"></i> Run AI Automation Now
                    </button>
                    <div id="automationResults" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workload Balance -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-settings-line text-info"></i> Workload Balance
                    </h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-info btn-sm" id="checkWorkload">
                        <i class="ri-refresh-line"></i> Check Workload
                    </button>
                    <div id="workloadResults" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Automation Rule -->
    <div class="row">
        <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-add-circle-line text-primary"></i> Create Automation Rule
                    </h5>
                </div>
                <div class="card-body">
                    <form id="createRuleForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Trigger</label>
                                <select class="form-select" name="trigger" required>
                                    <option value="">Select trigger...</option>
                                    <option value="task_created">Task Created</option>
                                    <option value="deadline_approaching">Deadline Approaching</option>
                                    <option value="task_overdue">Task Overdue</option>
                                    <option value="priority_high">High Priority Task</option>
                                    <option value="no_assignee">No Assignee</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Action</label>
                                <select class="form-select" name="action" required>
                                    <option value="">Select action...</option>
                                    <option value="analyze">Analyze Task</option>
                                    <option value="notify">Send Notification</option>
                                    <option value="adjust_priority">Adjust Priority</option <option value="suggest_assignee">Suggest Assignee</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Enabled</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="enabled" id="ruleEnabled" checked>
                                    <label class="form-check-label" for="ruleEnabled">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Conditions (JSON)</label>
                            <textarea class="form-control" name="conditions" rows="3" placeholder='{"priority": "high", "days_until_due": 3}'></textarea>
                            <small class="text-muted">Enter conditions as JSON object</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line"></i> Create Rule
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Analysis -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-calendar-event-line text-warning"></i> Schedule Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <form id="scheduleForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Analysis Type</label>
                                <select class="form-select" name="type" required>
                                    <option value="project_health">Project Health Check</option>
                                    <option value="task_analysis">Task Analysis</option>
                                    <option value="resource_allocation">Resource Allocation</option>
                                    <option value="priority_review">Priority Review</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Run At</label>
                                <input type="datetime-local" class="form-control" name="run_at" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="ri-time-line"></i> Schedule Analysis
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Rules (if any) -->
    @if(count($activeRules) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-list-check text-primary"></i> Active Automation Rules
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Trigger</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeRules as $rule)
                                <tr>
                                    <td>{{ $rule['id'] }}</td>
                                    <td>{{ $rule['trigger'] }}</td>
                                    <td>{{ $rule['action'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $rule['enabled'] ? 'success' : 'secondary' }}">
                                            {{ $rule['enabled'] ? 'Active' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td>{{ $rule['created_at'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Run Automation
$('#runAutomation').click(function() {
    $(this).prop('disabled', true).html('<i class="ri-loader-4-line spinner-border-sm"></i> Running...');
    
    axios.post('{{ route("ai.workflows.run") }}')
        .then(response => {
            if (response.data.success) {
                const results = response.data.results;
                
                let html = `
                    <div class="alert alert-success">
                        <h6 class="alert-heading">Automation Completed!</h6>
                        <ul class="mb-0">
                            <li>Tasks Analyzed: <strong>${results.tasks_analyzed}</strong></li>
                            <li>Decisions Created: <strong>${results.decisions_created}</strong></li>
                            <li>Auto-Executed: <strong>${results.auto_executed}</strong></li>
                            <li>Errors: <strong>${results.errors.length}</strong></li>
                        </ul>
                    </div>
                `;
                
                $('#automationResults').html(html).show();
                toastr.success(response.data.message);
            }
        })
        .catch(error => {
            toastr.error('Failed to run automation');
            console.error(error);
        })
        .finally(() => {
            $('#runAutomation').prop('disabled', false).html('<i class="ri-robot-line"></i> Run AI Automation Now');
        });
});

// Check Workload
$('#checkWorkload').click(function() {
    $(this).prop('disabled', true);
    
    axios.get('{{ route("ai.workflows.workload") }}')
        .then(response => {
            if (response.data.success) {
                const recs = response.data.recommendations;
                
                if (recs.length === 0) {
                    $('#workloadResults').html('<div class="alert alert-info">Workload is balanced! No recommendations at this time.</div>');
                } else {
                    let html = '<div class="table-responsive"><table class="table"><thead><tr><th>User</th><th>Active Tasks</th><th>Recommendation</th></tr></thead><tbody>';
                    
                    recs.forEach(rec => {
                        html += `<tr>
                            <td>${rec.user_name}</td>
                            <td><span class="badge bg-warning">${rec.active_tasks}</span></td>
                            <td>${rec.recommendation}</td>
                        </tr>`;
                    });
                    
                    html += '</tbody></table></div>';
                    $('#workloadResults').html(html);
                }
                
                toastr.success('Workload analyzed');
            }
        })
        .catch(error => {
            toastr.error('Failed to check workload');
            console.error(error);
        })
        .finally(() => {
            $('#checkWorkload').prop('disabled', false);
        });
});

// Create Rule
$('#createRuleForm').submit(function(e) {
    e.preventDefault();
    
    let conditions = {};
    try {
        const conditionsText = $('[name="conditions"]').val();
        conditions = conditionsText ? JSON.parse(conditionsText) : {};
    } catch (e) {
        toastr.error('Invalid JSON in conditions');
        return;
    }
    
    const formData = {
        trigger: $('[name="trigger"]').val(),
        action: $('[name="action"]').val(),
        conditions: conditions,
        enabled: $('#ruleEnabled').is(':checked')
    };
    
    axios.post('{{ route("ai.workflows.create-rule") }}', formData)
        .then(response => {
            if (response.data.success) {
                toastr.success(response.data.message);
                $('#createRuleForm')[0].reset();
            }
        })
        .catch(error => {
            toastr.error('Failed to create rule');
            console.error(error);
        });
});

// Schedule Analysis
$('#scheduleForm').submit(function(e) {
    e.preventDefault();
    
    const formData = {
        type: $('[name="type"]').val(),
        run_at: $('[name="run_at"]').val(),
        params: {}
    };
    
    axios.post('{{ route("ai.workflows.schedule") }}', formData)
        .then(response => {
            if (response.data.success) {
                toastr.success(response.data.message);
                $('#scheduleForm')[0].reset();
            }
        })
        .catch(error => {
            toastr.error('Failed to schedule analysis');
            console.error(error);
        });
});
</script>
@endsection
