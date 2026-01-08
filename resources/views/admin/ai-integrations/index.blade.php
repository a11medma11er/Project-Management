@extends('layouts.master')

@section('title') AI Integrations @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 font-size-18">
                    <i class="ri-links-line"></i> AI Integrations
                </h4>
                <p class="text-muted mt-2">Manage external AI providers and integrations</p>
            </div>
        </div>
    </div>

    <!-- Integration Health Status -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-heart-pulse-line text-success"></i> Integration Health
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Status</th>
                                    <th>Configured</th>
                                    <th>Rate Limited</th>
                                    <th>Last Call</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($health as $provider => $status)
                                <tr>
                                    <td>
                                        <strong>{{ ucfirst($provider) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $status['configured'] && !$status['rate_limited'] ? 'success' : 'warning' }}">
                                            {{ $status['configured'] && !$status['rate_limited'] ? 'Healthy' : 'Limited' }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="ri-{{ $status['configured'] ? 'check' : 'close' }}-circle-line text-{{ $status['configured'] ? 'success' : 'danger' }}"></i>
                                        {{ $status['configured'] ? 'Yes' : 'No' }}
                                    </td>
                                    <td>
                                        @if($status['rate_limited'])
                                            <span class="badge bg-danger">Yes</span>
                                        @else
                                            <span class="badge bg-success">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $status['last_call'] ?? 'Never' }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test AI Providers -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-test-tube-line text-primary"></i> Test AI Provider
                    </h5>
                </div>
                <div class="card-body">
                    <form id="testProviderForm">
                        <div class="mb-3">
                            <label class="form-label">Provider</label>
                            <select class="form-select" name="provider" required>
                                <option value="local">Local (Rule-based)</option>
                                <option value="openai">OpenAI</option>
                                <option value="claude">Claude (Anthropic)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Test Prompt</label>
                            <textarea class="form-control" name="prompt" rows="3" required placeholder="Enter a test prompt..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="ri-send-plane-line"></i> Test Provider
                        </button>
                    </form>

                    <div id="providerResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-webhook-line text-info"></i> Test Webhook
                    </h5>
                </div>
                <div class="card-body">
                    <form id="testWebhookForm">
                        <div class="mb-3">
                            <label class="form-label">Event Name</label>
                            <input type="text" class="form-control" name="event" required placeholder="test.event">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Data (JSON)</label>
                            <textarea class="form-control" name="data" rows="3" placeholder='{"key": "value"}'></textarea>
                        </div>

                        <button type="submit" class="btn btn-info">
                            <i class="ri-send-plane-line"></i> Send Webhook
                        </button>
                    </form>

                    <div id="webhookResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Slack -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-slack-line text-warning"></i> Test Slack Notification
                    </h5>
                </div>
                <div class="card-body">
                    <form id="testSlackForm" class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="2" required placeholder="Test message from AI system"></textarea>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Channel (Optional)</label>
                            <input type="text" class="form-control" name="channel" placeholder="#ai-notifications">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Username (Optional)</label>
                            <input type="text" class="form-control" name="username" placeholder="AI Bot">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                                <i class="ri-send-plane-line"></i> Send to Slack
                            </button>
                        </div>
                    </form>

                    <div id="slackResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Guide -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-information-line text-secondary"></i> Configuration Guide
                    </h5>
                </div>
                <div class="card-body">
                    <h6>Environment Variables:</h6>
                    <pre class="bg-light p-3 rounded"><code># OpenAI
OPENAI_API_KEY=your_openai_api_key
OPENAI_MODEL=gpt-4

# Claude (Anthropic)
CLAUDE_API_KEY=your_claude_api_key
CLAUDE_MODEL=claude-3-sonnet-20240229

# Webhook
AI_WEBHOOK_URL=https://your-webhook-endpoint.com/ai

# Slack
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
SLACK_CHANNEL=#ai-notifications</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Test Provider
$('#testProviderForm').submit(function(e) {
    e.preventDefault();
    
    const btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true).html('<i class="ri-loader-4-line spinner-border-sm"></i> Testing...');
    
    const formData = {
        provider: $('[name="provider"]').val(),
        prompt: $('[name="prompt"]').val()
    };
    
    axios.post('{{ route("ai.integrations.test-provider") }}', formData)
        .then(response => {
            if (response.data.success) {
                const result = response.data.result;
                
                let html = `
                    <div class="alert alert-success">
                        <h6 class="alert-heading">Provider: ${result.provider}</h6>
                        <p class="mb-1"><strong>Response:</strong></p>
                        <p>${result.response}</p>
                        <small class="text-muted">Model: ${result.model || 'N/A'}</small>
                    </div>
                `;
                
                $('#providerResult').html(html).show();
                toastr.success('Provider test successful');
            }
        })
        .catch(error => {
            const msg = error.response?.data?.message || 'Test failed';
            $('#providerResult').html(`<div class="alert alert-danger">${msg}</div>`).show();
            toastr.error(msg);
        })
        .finally(() => {
            btn.prop('disabled', false).html('<i class="ri-send-plane-line"></i> Test Provider');
        });
});

// Test Webhook
$('#testWebhookForm').submit(function(e) {
    e.preventDefault();
    
    let data = {};
    try {
        const dataText = $('[name="data"]').val();
        data = dataText ? JSON.parse(dataText) : {};
    } catch (e) {
        toastr.error('Invalid JSON in data field');
        return;
    }
    
    const formData = {
        event: $('[name="event"]').val(),
        data: data
    };
    
    axios.post('{{ route("ai.integrations.test-webhook") }}', formData)
        .then(response => {
            const msg = response.data.message;
            const alertClass = response.data.success ? 'success' : 'warning';
            $('#webhookResult').html(`<div class="alert alert-${alertClass}">${msg}</div>`).show();
            toastr[alertClass](msg);
        })
        .catch(error => {
            const msg = error.response?.data?.message || 'Webhook test failed';
            $('#webhookResult').html(`<div class="alert alert-danger">${msg}</div>`).show();
            toastr.error(msg);
        });
});

// Test Slack
$('#testSlackForm').submit(function(e) {
    e.preventDefault();
    
    const formData = {
        message: $('[name="message"]').val(),
        channel: $('[name="channel"]').val(),
        username: $('[name="username"]').val()
    };
    
    axios.post('{{ route("ai.integrations.test-slack") }}', formData)
        .then(response => {
            const msg = response.data.message;
            const alertClass = response.data.success ? 'success' : 'warning';
            $('#slackResult').html(`<div class="alert alert-${alertClass}">${msg}</div>`).show();
            toastr[alertClass](msg);
        })
        .catch(error => {
            const msg = error.response?.data?.message || 'Slack test failed';
            $('#slackResult').html(`<div class="alert alert-danger">${msg}</div>`).show();
            toastr.error(msg);
        });
});
</script>
@endsection
