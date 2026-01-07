<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="dashboard-projects" class="logo logo-dark">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('build/images/logo-dark.png')); ?>" alt="" height="17">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="dashboard-projects" class="logo logo-light">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>" alt="" height="17">
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
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['view-users', 'view-roles', 'view-permissions'])): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarManagement" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarManagement">
                        <i class="ri-settings-4-line"></i> <span>Management</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarManagement">
                        <ul class="nav nav-sm flex-column">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-users')): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route('management.users.index')); ?>" class="nav-link">
                                    <i class="ri-user-line me-1"></i> Users
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-permissions')): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route('management.permissions.index')); ?>" class="nav-link">
                                    <i class="ri-shield-keyhole-line me-1"></i> Permissions
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view-roles')): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route('management.roles.index')); ?>" class="nav-link">
                                    <i class="ri-admin-line me-1"></i> Roles
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

                <!-- Projects Section -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProjects" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarProjects">
                        <i class="ri-folder-line"></i> <span>Projects</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProjects">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="apps-projects-list" class="nav-link">
                                    <i class="ri-list-check me-1"></i> List
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-projects-overview" class="nav-link">
                                    <i class="ri-eye-line me-1"></i> Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-projects-create" class="nav-link">
                                    <i class="ri-add-circle-line me-1"></i> Create Project
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Tasks Section -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarTasks" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarTasks">
                        <i class="ri-task-line"></i> <span>Tasks</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarTasks">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="apps-tasks-kanban" class="nav-link">
                                    <i class="ri-layout-masonry-line me-1"></i> Kanban Board
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-tasks-list-view" class="nav-link">
                                    <i class="ri-list-unordered me-1"></i> List View
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-tasks-details" class="nav-link">
                                    <i class="ri-file-text-line me-1"></i> Task Details
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- AI Section -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAI" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAI">
                        <i class="ri-robot-line"></i> <span>Artificial Intelligence</span>
                        <span class="badge bg-info ms-1">Coming Soon</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAI">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link text-muted">
                                    <i class="ri-chat-smile-2-line me-1"></i> AI Assistant
                                    <span class="badge bg-secondary ms-1">Coming Soon</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link text-muted">
                                    <i class="ri-bar-chart-box-line me-1"></i> AI Analytics
                                    <span class="badge bg-secondary ms-1">Coming Soon</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
<?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>