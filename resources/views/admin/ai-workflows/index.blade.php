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

    <!-- Run Manual Automation (Dual Mode: Local vs Cloud) -->
    @php
        $aiProvider = \App\Models\AI\AISetting::where('key', 'ai_provider')->value('value') ?? 'local';
        $isCloudMode = $aiProvider !== 'local';
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-play-circle-line text-success"></i> Run Automation
                        @if($isCloudMode)
                            <span class="badge bg-primary ms-2">Cloud Mode</span>
                        @else
                            <span class="badge bg-secondary ms-2">Local Mode</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if(!$isCloudMode)
                        <!-- Legacy UI for Local Mode -->
                        <button type="button" class="btn btn-primary" id="runAutomation">
                            <i class="ri-robot-line"></i> Run AI Automation Now
                        </button>
                        <div id="automationResults" class="mt-3" style="display: none;"></div>
                    @else
                        <!-- New Batch UI for Cloud Mode -->
                        <p class="text-muted mb-3">
                            <i class="ri-information-line"></i> 
                            Cloud mode allows you to process features independently with batch AI analysis.
                        </p>
                        
                        <div class="row g-3">
                            <!-- Feature: Priority Adjustments -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary d-flex justify-content-between align-items-center">
                                            <span><i class="ri-price-tag-3-line"></i> Priority Adjustments</span>
                                            <span class="badge bg-soft-primary text-primary">{{ $availableCounts['priority'] ?? 0 }}</span>
                                        </h6>
                                        <p class="text-muted small mb-2">Analyze tasks needing priority changes</p>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Limit</span>
                                            <input type="number" class="form-control" id="priority-limit" value="3" min="1" max="50">
                                        </div>
                                        <button class="btn btn-primary btn-sm w-100" onclick="runFeature('priority')">
                                            <i class="ri-play-fill"></i> Execute
                                        </button>
                                        <div id="priority-result" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature: Assignment Suggestions -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary d-flex justify-content-between align-items-center">
                                            <span><i class="ri-user-add-line"></i> Assignment Suggestions</span>
                                            <span class="badge bg-soft-primary text-primary">{{ $availableCounts['assignment'] ?? 0 }}</span>
                                        </h6>
                                        <p class="text-muted small mb-2">Suggest assignees for unassigned tasks</p>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Limit</span>
                                            <input type="number" class="form-control" id="assignment-limit" value="5" min="1" max="50">
                                        </div>
                                        <button class="btn btn-primary btn-sm w-100" onclick="runFeature('assignment')">
                                            <i class="ri-play-fill"></i> Execute
                                        </button>
                                        <div id="assignment-result" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature: Deadline Extensions -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary d-flex justify-content-between align-items-center">
                                            <span><i class="ri-calendar-check-line"></i> Deadline Extensions</span>
                                            <span class="badge bg-soft-primary text-primary">{{ $availableCounts['deadline'] ?? 0 }}</span>
                                        </h6>
                                        <p class="text-muted small mb-2">Analyze overdue tasks for deadline adjustments</p>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Limit</span>
                                            <input type="number" class="form-control" id="deadline-limit" value="10" min="1" max="50">
                                        </div>
                                        <button class="btn btn-primary btn-sm w-100" onclick="runFeature('deadline')">
                                            <i class="ri-play-fill"></i> Execute
                                        </button>
                                        <div id="deadline-result" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature: Project Health -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary d-flex justify-content-between align-items-center">
                                            <span><i class="ri-heart-pulse-line"></i> Project Health</span>
                                            <span class="badge bg-soft-primary text-primary">{{ $availableCounts['projects'] ?? 0 }}</span>
                                        </h6>
                                        <p class="text-muted small mb-2">Assess risks for active projects</p>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Limit</span>
                                            <input type="number" class="form-control" id="projects-limit" value="5" min="1" max="50">
                                        </div>
                                        <button class="btn btn-primary btn-sm w-100" onclick="runFeature('projects')">
                                            <i class="ri-play-fill"></i> Execute
                                        </button>
                                        <div id="projects-result" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="ri-information-line"></i> 
                                <strong>Batch Processing:</strong> Each feature processes multiple items in a single AI request, reducing wait time from minutes to seconds.
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Workload Balance -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span><i class="ri-user-settings-line text-info"></i> Workload Balance</span>
                        <span class="badge bg-soft-info text-info">{{ $availableCounts['overloaded_users'] ?? 0 }} overloaded</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <button type="button" class="btn btn-info btn-sm" id="checkWorkload">
                            <i class="ri-refresh-line"></i> Check Workload
                        </button>
                        
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text">Max Tasks per User</span>
                            <input type="number" class="form-control" id="workloadThreshold" value="{{ $threshold ?? 5 }}" min="1" max="50">
                            <button class="btn btn-outline-secondary" type="button" id="saveThreshold">
                                <i class="ri-save-line"></i>
                            </button>
                        </div>
                    </div>
                    <div id="workloadResults"></div>
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

                        <!-- Rule Builder Mode Switch -->
                        <div class="mb-3">
                            <label class="form-label d-block">Rule Mode</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="mode" id="modeVisual" value="visual" checked autocomplete="off">
                                <label class="btn btn-outline-primary" for="modeVisual">Visual Builder (Easy)</label>

                                <input type="radio" class="btn-check" name="mode" id="modeAdvanced" value="advanced" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="modeAdvanced">Advanced JSON (Super User)</label>
                            </div>
                        </div>

                        <!-- Visual Builder UI -->
                        <div id="visualBuilder" class="p-3 border rounded mb-3 bg-light">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small">Field</label>
                                    <select class="form-select" id="builderField">
                                        <option value="" disabled selected>Select a field...</option>
                                        
                                        <optgroup label="Task Fields (Basic)">
                                            <option value="priority">Task Priority</option>
                                            <option value="status">Task Status</option>
                                            <option value="due_date">Due Date</option>
                                        </optgroup>

                                        <optgroup label="Calculated Metrics (Smart)">
                                            <option value="assigned_users_count">Team Size (Assigned Users)</option>
                                            <option value="days_until_due">Days Until Due</option>
                                            <option value="days_overdue">Days Overdue (Late)</option>
                                        </optgroup>

                                        <optgroup label="Project Fields (Parent)">
                                            <option value="project.status">Project Status</option>
                                            <option value="project.priority">Project Priority</option>
                                            <option value="project.category">Project Category</option>
                                            <option value="project.progress">Project Progress (%)</option>
                                            <option value="project.deadline">Project Deadline</option>
                                        </optgroup>

                                        <optgroup label="Advanced Relations">
                                            <option value="assignedUsers.*.avatar">Any Assignee Missing Avatar</option>
                                            <option value="creator.name">Creator Name</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label small">Condition</label>
                                    <select class="form-select" id="builderOperator">
                                        <option value="=">Equals (=)</option>
                                        <option value="!=">Not Equals (!=)</option>
                                        <option value=">">Greater Than (>)</option>
                                        <option value="<">Less Than (<)</option>
                                    </select>
                                </div>
                                <div class="col-md-5 mb-2">
                                    <label class="form-label small">Value</label>
                                    <input type="text" class="form-control" id="builderValue" placeholder="e.g. high, 2, NULL">
                                </div>
                            </div>
                            <small class="text-muted">Currently building: <span id="builderPreview" class="fw-bold text-primary"></span></small>
                        </div>

                        <!-- Advanced JSON Input (Hidden by default) -->
                        <div class="mb-3" id="jsonInputGroup" style="display:none;">
                            <label class="form-label">Conditions (JSON) <span class="badge bg-danger">Advanced</span></label>
                            <textarea class="form-control" name="conditions" id="conditionsJson" rows="3" placeholder='{"field": "priority", "operator": "=", "value": "high"}'></textarea>
                            <small class="text-muted">Directly edit the rule engine payload.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line"></i> Create Rule
                        </button>
                    </form>
                    
                    <div class="alert alert-warning mt-3 mb-0 py-2">
                        <small>
                            <i class="ri-alert-line me-1"></i> 
                            <strong>Note:</strong> All active rules are currently evaluated when "Run Automation" is executed (Scheduled Analysis), based on the current state of tasks. Real-time event triggers will be enabled in future updates.
                        </small>
                    </div>
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
                    <!-- Scheduled Jobs List -->
                    <div class="mt-4">
                        <h6 class="border-bottom pb-2 mb-3">Scheduled Jobs History</h6>
                        @if(isset($scheduledJobs) && count($scheduledJobs) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Run At</th>
                                        <th>Status</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scheduledJobs as $job)
                                    <tr>
                                        <td>#{{ $job->id }}</td>
                                        <td>{{ $job->type }}</td>
                                        <td>{{ $job->run_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            @if($job->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($job->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($job->status == 'processing')
                                                <span class="badge bg-info">Processing</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ Str::limit(json_encode($job->output ?? $job->error_message), 30) }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <p class="text-muted small">No scheduled jobs found.</p>
                        @endif
                    </div>
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
                                    <th>Name</th>
                                    <th>Trigger</th>
                                    <th>Conditions</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeRules as $rule)
                                <tr>
                                    <td>{{ $rule['id'] }}</td>
                                    <td><span class="fw-medium">{{ $rule['name'] ?? 'Untitled' }}</span></td>
                                    <td><span class="badge bg-soft-info">{{ $rule['trigger'] }}</span></td>
                                    <td><code>{{ json_encode($rule['conditions']) }}</code></td>
                                    <td>{{ is_array($rule['action']) ? implode(', ', $rule['action']) : $rule['action'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $rule['is_active'] ? 'success' : 'secondary' }}">
                                            {{ $rule['is_active'] ? 'Active' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($rule['created_at'])->format('M d, Y') }}</td>
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
document.addEventListener('DOMContentLoaded', function() {
    // Run Automation
    const runBtn = document.getElementById('runAutomation');
    if (runBtn) {
        runBtn.addEventListener('click', function() {
            const btn = this;
            const originalContent = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line spinner-border-sm"></i> Running...';
            
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
                        
                        const resultDiv = document.getElementById('automationResults');
                        resultDiv.innerHTML = html;
                        resultDiv.style.display = 'block';
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.data.message);
                        } else {
                            alert(response.data.message);
                        }
                    }
                })
                .catch(error => {
                    const msg = 'Failed to run automation';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else {
                        alert(msg);
                    }
                    console.error(error);
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                });
        });
    }

    // Rule Builder Logic
    const initRuleBuilder = () => {
        const modeVisual = document.getElementById('modeVisual');
        const modeAdvanced = document.getElementById('modeAdvanced');
        const visualBuilder = document.getElementById('visualBuilder');
        const jsonInputGroup = document.getElementById('jsonInputGroup');
        
        const builderField = document.getElementById('builderField');
        const builderOperator = document.getElementById('builderOperator');
        const builderValue = document.getElementById('builderValue');
        const conditionsJson = document.getElementById('conditionsJson');
        const builderPreview = document.getElementById('builderPreview');
        
        if (!modeVisual) return; // Guard clause

        // Toggle Modes
        const toggleMode = () => {
            if (modeVisual.checked) {
                visualBuilder.style.display = 'block';
                jsonInputGroup.style.display = 'none';
                updateJsonFromBuilder(); // Sync on switch
            } else {
                visualBuilder.style.display = 'none';
                jsonInputGroup.style.display = 'block';
            }
        };

        modeVisual.addEventListener('change', toggleMode);
        modeAdvanced.addEventListener('change', toggleMode);

        // Sync Builder to JSON
        const updateJsonFromBuilder = () => {
            let val = builderValue.value;
            
            // Smart Type Casting
            if (!isNaN(val) && val !== '' && val !== null) {
                val = Number(val);
            }
            
            const ruleObj = {
                field: builderField.value,
                operator: builderOperator.value,
                value: val
            };
            
            const jsonStr = JSON.stringify(ruleObj, null, 2);
            conditionsJson.value = jsonStr;
            builderPreview.innerText = `${builderField.options[builderField.selectedIndex].text} ${builderOperator.value} ${val}`;
        };

        // Event Listeners for Live Sync
        builderField.addEventListener('change', updateJsonFromBuilder);
        builderOperator.addEventListener('change', updateJsonFromBuilder);
        builderValue.addEventListener('input', updateJsonFromBuilder);
        
        // Init
        toggleMode();
        updateJsonFromBuilder();
    };

    initRuleBuilder();

    // Run Feature (Batch AI Processing for Cloud Mode)
    window.runFeature = function(feature) {
        const limitInput = document.getElementById(`${feature}-limit`);
        const resultDiv = document.getElementById(`${feature}-result`);
        const limit = limitInput ? parseInt(limitInput.value) : 10;
        
        // Show loading
        resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Processing...';
        
        axios.post('{{ route("ai.workflows.run-feature") }}', {
            feature: feature,
            limit: limit
        })
        .then(response => {
            if (response.data.success) {
                const result = response.data.result;
                
                // Format result
                let html = '';
                
                if (result.decisions_created !== undefined) {
                    html = `<div class="alert alert-success alert-sm py-2 mb-0">
                        <i class="ri-check-line"></i> 
                        <strong>${result.decisions_created}</strong> decisions created
                    </div>`;
                } else if (result.message) {
                    html = `<div class="alert alert-info alert-sm py-2 mb-0">
                        <i class="ri-information-line"></i> ${result.message}
                    </div>`;
                } else {
                    html = '<div class="alert alert-success alert-sm py-2 mb-0">Completed successfully</div>';
                }
                
                resultDiv.innerHTML = html;
                
                if (typeof toastr !== 'undefined') {
                    toastr.success(`${feature} feature executed successfully`);
                }
            }
        })
        .catch(error => {
            console.error(error);
            resultDiv.innerHTML = `<div class="alert alert-danger alert-sm py-2 mb-0">
                <i class="ri-error-warning-line"></i> Failed: ${error.response?.data?.message || error.message}
            </div>`;
            
            if (typeof toastr !== 'undefined') {
                toastr.error('Feature execution failed');
            }
        });
    };

    // Check Workload
    const checkWorkloadBtn = document.getElementById('checkWorkload');
    if (checkWorkloadBtn) {
        checkWorkloadBtn.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            
            axios.get('{{ route("ai.workflows.workload") }}')
                .then(response => {
                    if (response.data.success) {
                        const recs = response.data.recommendations;
                        const resultDiv = document.getElementById('workloadResults');
                        
                        if (recs.length === 0) {
                            resultDiv.innerHTML = '<div class="alert alert-info py-2 mb-0">Workload is balanced! No user exceeds the threshold.</div>';
                        } else {
                            let html = '<div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>User</th><th>Active Tasks</th><th>Recommendation</th></tr></thead><tbody>';
                            
                            recs.forEach(rec => {
                                html += `<tr>
                                    <td>${rec.user_name}</td>
                                    <td><span class="badge bg-danger">${rec.active_tasks}</span></td>
                                    <td>${rec.recommendation}</td>
                                </tr>`;
                            });
                            
                            html += '</tbody></table></div>';
                            resultDiv.innerHTML = html;
                        }
                        
                        if (typeof toastr !== 'undefined') toastr.success('Workload analyzed');
                    }
                })
                .catch(error => {
                    if (typeof toastr !== 'undefined') toastr.error('Failed to check workload');
                    console.error(error);
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    // Save Threshold
    const saveThresholdBtn = document.getElementById('saveThreshold');
    if (saveThresholdBtn) {
        saveThresholdBtn.addEventListener('click', function() {
            const btn = this;
            const threshold = document.getElementById('workloadThreshold').value;
            btn.disabled = true;

            axios.post('/admin/ai/workflows/update-threshold', { threshold: threshold })
                .then(response => {
                    if (response.data.success) {
                        if (typeof toastr !== 'undefined') toastr.success('Threshold updated');
                        else alert('Threshold updated');
                    }
                })
                .catch(error => {
                    console.error(error);
                    if (typeof toastr !== 'undefined') toastr.error('Failed to update threshold');
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

    // Create Rule
    const createRuleForm = document.getElementById('createRuleForm');
    if (createRuleForm) {
        createRuleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let conditions = {};
            try {
                const conditionsText = this.querySelector('[name="conditions"]').value;
                conditions = conditionsText ? JSON.parse(conditionsText) : {};
            } catch (e) {
                if (typeof toastr !== 'undefined') toastr.error('Invalid JSON in conditions');
                else alert('Invalid JSON in conditions');
                return;
            }
            
            const formData = {
                trigger: this.querySelector('[name="trigger"]').value,
                action: this.querySelector('[name="action"]').value,
                conditions: conditions,
                enabled: this.querySelector('#ruleEnabled').checked
            };
            
            axios.post('{{ route("ai.workflows.create-rule") }}', formData)
                .then(response => {
                    if (response.data.success) {
                        if (typeof toastr !== 'undefined') toastr.success(response.data.message);
                        else alert(response.data.message);
                        createRuleForm.reset();
                    }
                })
                .catch(error => {
                    if (typeof toastr !== 'undefined') toastr.error('Failed to create rule');
                    console.error(error);
                });
        });
    }

    // Schedule Analysis
    const scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                type: this.querySelector('[name="type"]').value,
                run_at: this.querySelector('[name="run_at"]').value,
                params: {}
            };
            
            axios.post('{{ route("ai.workflows.schedule") }}', formData)
                .then(response => {
                    if (response.data.success) {
                        if (typeof toastr !== 'undefined') toastr.success(response.data.message);
                        else alert(response.data.message);
                        scheduleForm.reset();
                        // Reload to show new schedule in table
                        setTimeout(() => location.reload(), 1000);
                    }
                })
                .catch(error => {
                    if (typeof toastr !== 'undefined') toastr.error('Failed to schedule analysis');
                    console.error(error);
                });
        });
    }
});
</script>
@endsection
