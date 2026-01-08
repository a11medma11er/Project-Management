<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="dashboard-projects" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="dashboard-projects" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>Menu</span></li>

                <!-- Management Section -->
                @canany(['view-users', 'view-roles', 'view-permissions'])
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarManagement" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarManagement">
                        <i class="ri-settings-4-line"></i> <span>Management</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarManagement">
                        <ul class="nav nav-sm flex-column">
                            @can('view-users')
                            <li class="nav-item">
                                <a href="{{ route('management.users.index') }}" class="nav-link">
                                    <i class="ri-user-line me-1"></i> Users
                                </a>
                            </li>
                            @endcan
                            @can('view-permissions')
                            <li class="nav-item">
                                <a href="{{ route('management.permissions.index') }}" class="nav-link">
                                    <i class="ri-shield-keyhole-line me-1"></i> Permissions
                                </a>
                            </li>
                            @endcan
                            @can('view-roles')
                            <li class="nav-item">
                                <a href="{{ route('management.roles.index') }}" class="nav-link">
                                    <i class="ri-admin-line me-1"></i> Roles
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                <!-- Projects Section -->
                @can('view-projects')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProjects" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarProjects">
                        <i class="ri-folder-line"></i> <span>Projects</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProjects">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('management.projects.index') }}" class="nav-link">
                                    <i class="ri-list-check me-1"></i> All Projects
                                </a>
                            </li>
                            @can('create-projects')
                            <li class="nav-item">
                                <a href="{{ route('management.projects.create') }}" class="nav-link">
                                    <i class="ri-add-line me-1"></i> Create Project
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcan


                <!-- Tasks Section -->
                @can('view-tasks')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarTasks" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarTasks">
                        <i class="ri-task-line"></i> <span>Tasks</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarTasks">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('management.tasks.kanban') }}" class="nav-link">
                                    <i class="ri-layout-masonry-line me-1"></i> Kanban Board
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('management.tasks.index') }}" class="nav-link">
                                    <i class="ri-list-unordered me-1"></i> All Tasks
                                </a>
                            </li>
                            @can('create-tasks')
                            <li class="nav-item">
                                <a href="{{ route('management.tasks.create') }}" class="nav-link">
                                    <i class="ri-add-line me-1"></i> Create Task
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcan

                {{-- Activity Logs --}}
                @can('view-activity-logs')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarActivityLogs" data-bs-toggle="collapse" role="button" 
                       aria-expanded="false" aria-controls="sidebarActivityLogs">
                        <i class="ri-file-list-3-line"></i> <span>Activity Logs</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarActivityLogs">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('management.activity-logs.index') }}" class="nav-link">
                                    <i class="ri-list-check me-1"></i> All Logs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('management.activity-logs.analytics') }}" class="nav-link">
                                    <i class="ri-bar-chart-line me-1"></i> Analytics
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endcan

                <!-- AI Section -->
                @can('access-ai-control')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAI" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAI">
                        <i class="ri-robot-line"></i> <span>AI Assistant</span>
                        <span class="badge bg-success ms-1">Live</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAI">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('ai.control.index') }}" class="nav-link">
                                    <i class="ri-dashboard-line me-1"></i> AI Control Panel
                                </a>
                            </li>
                            @can('view-ai-decisions')
                            <li class="nav-item">
                                <a href="{{ route('ai.decisions.index') }}" class="nav-link">
                                    <i class="ri-lightbulb-line me-1"></i> AI Decisions
                                </a>
                            </li>
                            @endcan
                            @can('manage-ai-prompts')
                            <li class="nav-item">
                                <a href="{{ route('ai.prompts.index') }}" class="nav-link">
                                    <i class="ri-edit-box-line me-1"></i> Prompts
                                </a>
                            </li>
                            @endcan
                            @can('view-ai-analytics')
                            <li class="nav-item">
                                <a href="{{ route('ai.insights.index') }}" class="nav-link">
                                    <i class="ri-lightbulb-flash-line me-1"></i> Insights
                                </a>
                            </li>
                            @endcan
                            @can('view-ai-analytics')
                            <li class="nav-item">
                                <a href="{{ route('ai.learning.index') }}" class="nav-link">
                                    <i class="ri-brain-line me-1"></i> Learning
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('ai.reports.index') }}" class="nav-link">
                                    <i class="ri-file-chart-line me-1"></i> Reports
                                </a>
                            </li>
                            @endcan
                            @can('manage-ai-settings')
                            <li class="nav-item">
                                <a href="{{ route('ai.workflows.index') }}" class="nav-link">
                                    <i class="ri-robot-2-line me-1"></i> Workflows
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('ai.integrations.index') }}" class="nav-link">
                                    <i class="ri-links-line me-1"></i> Integrations
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('ai.performance.index') }}" class="nav-link">
                                    <i class="ri-dashboard-line me-1"></i> Performance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('ai.security.index') }}" class="nav-link">
                                    <i class="ri-shield-check-line me-1"></i> Security
                                </a>
                            </li>
                            @endcan
                            @can('manage-ai-settings')
                            <li class="nav-item">
                                <a href="{{ route('ai.settings.index') }}" class="nav-link">
                                    <i class="ri-settings-3-line me-1"></i> Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('ai.guardrails.index') }}" class="nav-link">
                                    <i class="ri-shield-check-line me-1"></i> Guardrails
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('ai.features.index') }}" class="nav-link">
                                    <i class="ri-magic-line me-1"></i> AI Features
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcan

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
