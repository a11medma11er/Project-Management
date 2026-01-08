<?php

use App\Http\Controllers\Admin\AI\AIControlController;
use App\Http\Controllers\Admin\AI\AIPromptController;
use App\Http\Controllers\Admin\AI\AIDecisionController;
use App\Http\Controllers\Admin\AI\AIGuardrailController;
use App\Http\Controllers\Admin\AI\AIDecisionReviewController;
use App\Http\Controllers\Admin\AI\AIInsightsController;
use App\Http\Controllers\Admin\AI\AIAnalyticsController;
use App\Http\Controllers\Admin\AI\AISafetyController;
use App\Http\Controllers\Admin\AI\AIFeaturesController;
use App\Http\Controllers\Admin\AI\AILearningController;
use App\Http\Controllers\Admin\AI\AIReportingController;
use App\Http\Controllers\Admin\AI\AIWorkflowController;
use App\Http\Controllers\Admin\AI\AIIntegrationController;
use App\Http\Controllers\Admin\AI\AIPerformanceController;
use App\Http\Controllers\Admin\AI\AISecurityController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAIPermission;

/*
|--------------------------------------------------------------------------
| AI Routes
|--------------------------------------------------------------------------
|
| AI-specific routes with granular permission control
| All routes require authentication and specific AI permissions
|
*/

Route::prefix('admin/ai')
    ->middleware(['auth', 'verified'])
    ->name('ai.')
    ->group(function () {

        // ============================================
        // AI Control Panel
        // ============================================
        Route::middleware(['can:access-ai-control'])->group(function () {
            Route::get('/control', [AIControlController::class, 'index'])->name('control.index');
            Route::post('/control/toggle', [AIControlController::class, 'toggle'])->name('control.toggle');
            Route::get('/control/health', [AIControlController::class, 'health'])->name('control.health');
        });

        // ============================================
        // AI Settings Management
        // ============================================
        Route::middleware(['can:manage-ai-settings'])->group(function () {
            Route::get('/settings', [AIControlController::class, 'settings'])->name('settings.index');
            Route::post('/settings', [AIControlController::class, 'updateSettings'])->name('settings.update');
        });

        // ============================================
        // AI Prompts Management
        // ============================================
        Route::middleware(['can:manage-ai-prompts'])->group(function () {
            Route::resource('prompts', AIPromptController::class)->except(['show']);
            Route::post('/prompts/{prompt}/restore', [AIPromptController::class, 'restore'])->name('prompts.restore');
        });

        Route::middleware(['can:test-ai-prompts'])->group(function () {
            Route::post('/prompts/{prompt}/test', [AIPromptController::class, 'test'])->name('prompts.test');
        });

        // ============================================
        // AI Decisions (View Only)
        // ============================================
        Route::middleware(['can:view-ai-decisions'])->group(function () {
            Route::get('/decisions', [AIDecisionController::class, 'index'])->name('decisions.index');
            Route::get('/decisions/{decision}', [AIDecisionController::class, 'show'])->name('decisions.show');
        });

        // ============================================
        // AI Decision Review (Approve/Reject)
        // ============================================
        Route::middleware(['can:approve-ai-actions'])->group(function () {
            Route::get('/decisions/{decision}/review', [AIDecisionReviewController::class, 'show'])->name('decisions.review');
            Route::post('/decisions/{decision}/accept', [AIDecisionReviewController::class, 'accept'])->name('decisions.accept');
            Route::post('/decisions/{decision}/reject', [AIDecisionReviewController::class, 'reject'])->name('decisions.reject');
            Route::post('/decisions/{decision}/modify', [AIDecisionReviewController::class, 'modify'])->name('decisions.modify');
        });

        // ============================================
        // AI Learning
        // ============================================
        Route::middleware(['can:view-ai-analytics'])->group(function () {
            Route::get('/learning', [AILearningController::class, 'index'])->name('learning.index');
            Route::get('/learning/data', [AILearningController::class, 'getData'])->name('learning.data');
        });

        // ============================================
        // AI Reporting
        // ============================================
        Route::middleware(['can:view-ai-analytics'])->group(function () {
            Route::get('/reports', [AIReportingController::class, 'index'])->name('reports.index');
            Route::post('/reports/generate', [AIReportingController::class, 'generate'])->name('reports.generate');
            Route::post('/reports/export/pdf', [AIReportingController::class, 'exportPDF'])->name('reports.export.pdf');
            Route::post('/reports/export/excel', [AIReportingController::class, 'exportExcel'])->name('reports.export.excel');
            Route::get('/reports/comparison', [AIReportingController::class, 'comparison'])->name('reports.comparison');
        });

        // ============================================
        // AI Analytics & Insights
        // ============================================
        Route::middleware(['can:view-ai-analytics'])->group(function () {
            Route::get('/insights', [AIInsightsController::class, 'index'])->name('insights.index');
            Route::get('/analytics', [AIAnalyticsController::class, 'index'])->name('analytics.index');
            Route::get('/analytics/export', [AIAnalyticsController::class, 'export'])->name('analytics.export');
        });

        // ============================================
        // AI Workflows & Automation
        // ============================================
        Route::middleware(['can:manage-ai-settings'])->group(function () {
            Route::get('/workflows', [AIWorkflowController::class, 'index'])->name('workflows.index');
            Route::post('/workflows/run', [AIWorkflowController::class, 'runAutomation'])->name('workflows.run');
            Route::post('/workflows/create-rule', [AIWorkflowController::class, 'createRule'])->name('workflows.create-rule');
            Route::post('/workflows/schedule', [AIWorkflowController::class, 'scheduleAnalysis'])->name('workflows.schedule');
            Route::get('/workflows/workload', [AIWorkflowController::class, 'workloadBalance'])->name('workflows.workload');
        });

        // ============================================
        // AI Integrations
        // ============================================
        Route::middleware(['can:manage-ai-settings'])->group(function () {
            Route::get('/integrations', [AIIntegrationController::class, 'index'])->name('integrations.index');
            Route::post('/integrations/test-provider', [AIIntegrationController::class, 'testProvider'])->name('integrations.test-provider');
            Route::post('/integrations/test-webhook', [AIIntegrationController::class, 'testWebhook'])->name('integrations.test-webhook');
            Route::post('/integrations/test-slack', [AIIntegrationController::class, 'testSlack'])->name('integrations.test-slack');
            Route::get('/integrations/health', [AIIntegrationController::class, 'health'])->name('integrations.health');
        });

        // ============================================
        // AI Performance & Optimization
        // ============================================
        Route::middleware(['can:manage-ai-settings'])->group(function () {
            Route::get('/performance', [AIPerformanceController::class, 'index'])->name('performance.index');
            Route::post('/performance/clear-cache', [AIPerformanceController::class, 'clearCache'])->name('performance.clear-cache');
            Route::post('/performance/warm-cache', [AIPerformanceController::class, 'warmUpCache'])->name('performance.warm-cache');
            Route::get('/performance/suggested-indexes', [AIPerformanceController::class, 'getSuggestedIndexes'])->name('performance.suggested-indexes');
            Route::get('/performance/system-metrics', [AIPerformanceController::class, 'getSystemMetrics'])->name('performance.system-metrics');
        });

        // ============================================
        // AI Security
        // ============================================
        Route::middleware(['can:manage-ai-settings'])->group(function () {
            Route::get('/security', [AISecurityController::class, 'index'])->name('security.index');
            Route::get('/security/check-rate-limit', [AISecurityController::class, 'checkRateLimit'])->name('security.check-rate-limit');
            Route::post('/security/validate-input', [AISecurityController::class, 'validateInput'])->name('security.validate-input');
            Route::get('/security/metrics', [AISecurityController::class, 'getMetrics'])->name('security.metrics');
        });

        // ============================================
        // AI Safety & Guardrails (Legacy)
        // ============================================
        Route::middleware(['can:manage-ai-safety'])->group(function () {
            Route::get('/safety', [AISafetyController::class, 'index'])->name('safety.index');
            Route::post('/safety/guardrails', [AISafetyController::class, 'updateGuardrails'])->name('safety.guardrails');
            Route::post('/safety/fallback', [AISafetyController::class, 'updateFallback'])->name('safety.fallback');
        });

        // AI Features
        // ============================================
        Route::middleware(['can:access-ai-control'])->group(function () {
            Route::get('/features', [AIFeaturesController::class, 'index'])->name('features.index');
            Route::post('/analyze-codebase', [AIFeaturesController::class, 'analyzeCodebase'])->name('features.analyze');
            Route::post('/development-plan', [AIFeaturesController::class, 'createDevelopmentPlan'])->name('features.development_plan');
            Route::post('/breakdown-project', [AIFeaturesController::class, 'breakdownProject'])->name('features.breakdown');
            Route::post('/create-study', [AIFeaturesController::class, 'createStudy'])->name('features.study');
            Route::post('/analyze-task', [AIFeaturesController::class, 'analyzeTask'])->name('features.task');
        });

        // Guardrails Settings
        Route::prefix('guardrails')->name('guardrails.')->middleware('can:manage-ai-settings')->group(function () {
            Route::get('/', [AIGuardrailController::class, 'index'])->name('index');
            Route::post('/rule/update', [AIGuardrailController::class, 'updateRule'])->name('rule.update');
            Route::post('/threshold/update', [AIGuardrailController::class, 'updateThreshold'])->name('threshold.update');
            Route::post('/cache/clear', [AIGuardrailController::class, 'clearCache'])->name('cache.clear');
        });
    });
