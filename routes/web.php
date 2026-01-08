<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes with Rate Limiting (5 attempts per minute)
Auth::routes(['verify' => true]);

//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('root');

// Redirect legacy route to new controller route
Route::get('apps-tasks-kanban', function() {
    return redirect()->route('management.tasks.kanban');
});

// Dashboard AJAX Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/tasks/filter', [App\Http\Controllers\DashboardController::class, 'filterTasks'])->name('dashboard.tasks.filter');
    Route::get('/dashboard/tasks/upcoming', [App\Http\Controllers\DashboardController::class, 'getUpcomingTasksByDate'])->name('dashboard.tasks.upcoming');
    Route::get('/dashboard/team/sort', [App\Http\Controllers\DashboardController::class, 'sortTeamMembers'])->name('dashboard.team.sort');
});

//Update User Details - Rate Limited to prevent abuse
Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');
});

// Management Routes (Users, Roles, Permissions)
Route::middleware(['auth'])->prefix('management')->name('management.')->group(function () {
    
    // Users Management
    Route::middleware('can:view-users')->group(function () {
        Route::resource('users', App\Http\Controllers\Management\UserManagementController::class);
    });
    
    // Roles Management
    Route::middleware('can:view-roles')->group(function () {
        Route::resource('roles', App\Http\Controllers\Management\RoleController::class);
    });
    
    // Permissions Management
    Route::middleware('can:view-permissions')->group(function () {
        Route::resource('permissions', App\Http\Controllers\Management\PermissionController::class);
    });
    
    // Projects Management
    Route::middleware('can:view-projects')->group(function () {
        // Toggle favorite BEFORE resource to avoid route conflict
        Route::post('projects/{project}/toggle-favorite', [App\Http\Controllers\Management\ProjectController::class, 'toggleFavorite'])
            ->name('projects.toggleFavorite');
        
        Route::resource('projects', App\Http\Controllers\Management\ProjectController::class);
    });
    
    // Activity Logs Management
    Route::middleware('can:view-activity-logs')->group(function () {
        Route::get('activity-logs', [App\Http\Controllers\Management\ActivityLogController::class, 'index'])
            ->name('activity-logs.index');
        Route::get('activity-logs/analytics', [App\Http\Controllers\Management\ActivityLogController::class, 'analytics'])
            ->name('activity-logs.analytics');
        Route::get('activity-logs/{activity}', [App\Http\Controllers\Management\ActivityLogController::class, 'show'])
            ->name('activity-logs.show');
        Route::post('activity-logs/cleanup', [App\Http\Controllers\Management\ActivityLogController::class, 'cleanup'])
            ->name('activity-logs.cleanup')
            ->middleware('can:manage-activity-logs');
    });
    
    // Context Readiness Testing (Development/Demo)
    Route::middleware('can:view-tasks')->prefix('context-test')->name('context-test.')->group(function () {
        Route::get('demo', [App\Http\Controllers\Management\ContextTestController::class, 'demo'])
            ->name('demo');
        Route::get('builder/{task}', [App\Http\Controllers\Management\ContextTestController::class, 'testContextBuilder'])
            ->name('builder');
        Route::get('activities/{task}', [App\Http\Controllers\Management\ContextTestController::class, 'testActivityService'])
            ->name('activities');
        Route::get('ai-status', [App\Http\Controllers\Management\ContextTestController::class, 'testAIStatus'])
            ->name('ai-status');
        Route::get('user-patterns/{userId}', [App\Http\Controllers\Management\ContextTestController::class, 'testUserPatterns'])
            ->name('user-patterns');
    });
    
    // Tasks Management
    Route::middleware('can:view-tasks')->group(function () {
        // Kanban board (must be before resource route)
        Route::get('tasks/kanban', [App\Http\Controllers\KanbanController::class, 'index'])
            ->name('tasks.kanban');
        Route::post('tasks/kanban/update-status', [App\Http\Controllers\KanbanController::class, 'updateStatus'])
            ->name('tasks.kanban.update');
            
        Route::resource('tasks', App\Http\Controllers\Management\TaskController::class);
        
        // Additional endpoints
        Route::post('tasks/{task}/comments', [App\Http\Controllers\Management\TaskController::class, 'storeComment'])
            ->name('tasks.comments.store');
        Route::post('tasks/{task}/attachments', [App\Http\Controllers\Management\TaskController::class, 'storeAttachment'])
            ->name('tasks.attachments.store');
        Route::post('tasks/{task}/sub-tasks', [App\Http\Controllers\Management\TaskController::class, 'storeSubTask'])
            ->name('tasks.sub-tasks.store');
        Route::put('tasks/{task}/sub-tasks/{subTask}/toggle', [App\Http\Controllers\Management\TaskController::class, 'toggleSubTask'])
            ->name('tasks.sub-tasks.toggle');
        Route::post('tasks/{task}/time-entries', [App\Http\Controllers\Management\TaskController::class, 'storeTimeEntry'])
            ->name('tasks.time-entries.store');
    });

});

// ============================================
// AI Routes (Centralized Management)
// ============================================
require __DIR__ . '/ai.php';

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
