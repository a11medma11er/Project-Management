
<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('translation.projects'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Dashboards <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Projects <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    <div class="row project-wrapper">
        <div class="col-xxl-8">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span
                                        class="avatar-title bg-primary-subtle rounded-2 fs-2">
                                        <i data-feather="briefcase" class="text-primary"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3">
                                        Active Projects</p>
                                    <div class="d-flex align-items-center mb-3">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="<?php echo e($statistics['active_projects']); ?>">0</span></h4>
                                        <span class="badge bg-<?php echo e($statistics['projects_trend'] >= 0 ? 'success' : 'danger'); ?>-subtle text-<?php echo e($statistics['projects_trend'] >= 0 ? 'success' : 'danger'); ?> fs-12"><i
                                                class="ri-arrow-<?php echo e($statistics['projects_trend'] >= 0 ? 'up' : 'down'); ?>-s-line fs-13 align-middle me-1"></i><?php echo e(abs ($statistics['projects_trend'])); ?>

                                            %</span>
                                    </div>
                                    <p class="text-muted text-truncate mb-0">Projects this month</p>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div>
                </div><!-- end col -->

                <div class="col-xl-4">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span
                                        class="avatar-title bg-primary-subtle rounded-2 fs-2">
                                        <i data-feather="award" class="text-primary"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted mb-3">New Tasks</p>
                                    <div class="d-flex align-items-center mb-3">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="<?php echo e($statistics['new_tasks']); ?>">0</span></h4>
                                        <span class="badge bg-<?php echo e($statistics['tasks_trend'] >= 0 ? 'success' : 'danger'); ?>-subtle text-<?php echo e($statistics['tasks_trend'] >= 0 ? 'success' : 'danger'); ?> fs-12"><i
                                                class="ri-arrow-<?php echo e($statistics['tasks_trend'] >= 0 ? 'up' : 'down'); ?>-s-line fs-13 align-middle me-1"></i><?php echo e(abs($statistics['tasks_trend'])); ?>

                                            %</span>
                                    </div>
                                    <p class="text-muted mb-0">Tasks this month</p>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div>
                </div><!-- end col -->

                <div class="col-xl-4">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle rounded-2 fs-2">
                                        <i data-feather="clock" class="text-primary"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3">
                                        Total Hours</p>
                                    <div class="d-flex align-items-center mb-3">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="<?php echo e($statistics['total_hours']); ?>">0</span>h <span class="counter-value"
                                                data-target="<?php echo e($statistics['total_minutes']); ?>">0</span>m</h4>
                                        <span class="badge bg-<?php echo e($statistics['hours_trend'] >= 0 ? 'success' : 'danger'); ?>-subtle text-<?php echo e($statistics['hours_trend'] >= 0 ? 'success' : 'danger'); ?> fs-12"><i
                                                class="ri-arrow-<?php echo e($statistics['hours_trend'] >= 0 ? 'up' : 'down'); ?>-s-line fs-13 align-middle me-1"></i><?php echo e(abs($statistics['hours_trend'])); ?>

                                            %</span>
                                    </div>
                                    <p class="text-muted text-truncate mb-0">Work this month</p>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div>
                </div><!-- end col -->
            </div><!-- end row -->

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Projects Overview</h4>
                        </div><!-- end card header -->

                        <div class="card-header p-0 border-0 bg-light-subtle">
                            <div class="row g-0 text-center">
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span class="counter-value"
                                                data-target="<?php echo e($chartData['total_projects']); ?>">0</span></h5>
                                        <p class="text-muted mb-0">Number of Projects</p>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-6 col-sm-3">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span class="counter-value"
                                                data-target="<?php echo e($chartData['active_projects']); ?>">0</span></h5>
                                        <p class="text-muted mb-0">Active Projects</p>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-6 col-sm-6">
                                    <div class="p-3 border border-dashed border-start-0 border-end-0">
                                        <h5 class="mb-1 text-success"><span class="counter-value"
                                                data-target="<?php echo e($chartData['working_hours']); ?>">0</span>h</h5>
                                        <p class="text-muted mb-0">Working Hours</p>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                        </div><!-- end card header -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div><!-- end row -->
        </div><!-- end col -->

        <div class="col-xxl-4">
            <div class="card">
                <div class="card-header border-0">
                    <h4 class="card-title mb-0">Upcoming Schedules</h4>
                </div><!-- end cardheader -->
                <div class="card-body pt-0">


                    <h6 class="text-uppercase fw-semibold mt-4 mb-3 text-muted">Events:</h6>
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="mini-stats-wid d-flex align-items-center mt-3">
                        <div class="flex-shrink-0 avatar-sm">
                            <span
                                class="mini-stat-icon avatar-title rounded-circle text-success bg-success-subtle fs-4">
                                <?php echo e($task->due_date ? $task->due_date->format('d') : '--'); ?>

                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1"><?php echo e($task->title); ?></h6>
                            <p class="text-muted mb-0"><?php echo e($task->project ? $task->project->title : 'N/A'); ?></p>
                        </div>
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0"><?php echo e($task->due_date ? $task->due_date->format('M d') : 'N/A'); ?></p>
                        </div>
                    </div><!-- end -->
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-4">
                        <i class="ri-calendar-line fs-1 mb-2 d-block"></i>
                        <p>No upcoming tasks</p>
                    </div>
                    <?php endif; ?>


                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

    <div class="row">
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title flex-grow-1 mb-0">Active Projects</h4>
                    <div class="flex-shrink-0">
                        <a href="javascript:void(0);" class="btn btn-soft-info btn-sm">Export Report</a>
                    </div>
                </div><!-- end cardheader -->
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-nowrap table-centered align-middle">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th scope="col">Project Name</th>
                                    <th scope="col">Project Lead</th>
                                    <th scope="col">Progress</th>
                                    <th scope="col">Assignee</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" style="width: 10%;">Due Date</th>
                                </tr><!-- end tr -->
                            </thead><!-- thead -->


                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $activeProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="fw-medium"><?php echo e($project->title); ?></td>
                                    <td>
                                        <?php if($project->teamLead): ?>
                                            <img src="<?php echo e($project->teamLead->avatar ? asset('storage/'.$project->teamLead->avatar) : URL::asset('build/images/users/avatar-1.jpg')); ?>"
                                                class="avatar-xxs rounded-circle me-1" alt="">
                                            <a href="javascript:void(0);" class="text-reset"><?php echo e($project->teamLead->name); ?></a>
                                        <?php else: ?>
                                            <span class="text-muted">No Lead</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-1 text-muted fs-13"><?php echo e($project->progress); ?>%</div>
                                            <div class="progress progress-sm flex-grow-1" style="width: 68%;">
                                                <div class="progress-bar bg-primary rounded"
                                                    role="progressbar" style="width: <?php echo e($project->progress); ?>%"
                                                    aria-valuenow="<?php echo e($project->progress); ?>" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="avatar-group flex-nowrap">
                                            <?php $__currentLoopData = $project->members->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="avatar-group-item">
                                                <a href="javascript:void(0);" class="d-inline-block" data-bs-toggle="tooltip" title="<?php echo e($member->name); ?>">
                                                    <img src="<?php echo e($member->avatar ? asset('storage/'.$member->avatar) : URL::asset('build/images/users/avatar-1.jpg')); ?>" alt=""
                                                        class="rounded-circle avatar-xxs">
                                                </a>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($project->members->count() > 3): ?>
                                            <div class="avatar-group-item">
                                                <a href="javascript:void(0);" class="d-inline-block">
                                                    <div class="avatar-xxs">
                                                        <span class="avatar-title rounded-circle bg-light text-primary">
                                                            +<?php echo e($project->members->count() - 3); ?>

                                                        </span>
                                                    </div>
                                                </a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($project->status == 'Completed' ? 'success' : ($project->status == 'Inprogress' ? 'warning' : 'danger')); ?>-subtle text-<?php echo e($project->status == 'Completed' ? 'success' : ($project->status == 'Inprogress' ? 'warning' : 'danger')); ?>">
                                            <?php echo e($project->status); ?>

                                        </span>
                                    </td>
                                    <td class="text-muted"><?php echo e($project->deadline ? $project->deadline->format('d M Y') : 'N/A'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="ri-folder-open-line fs-1 mb-2 d-block"></i>
                                        No active projects found
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div>

                    <div class="align-items-center mt-xl-3 mt-4 justify-content-between d-flex">
                        <div class="flex-shrink-0">
                            <div class="text-muted">Showing <span class="fw-semibold"><?php echo e($activeProjects->count()); ?></span> of <span
                                    class="fw-semibold"><?php echo e($activeProjects->total()); ?></span> Results
                            </div>
                        </div>
                        <?php echo e($activeProjects->links('pagination::bootstrap-4')); ?>

                    </div>


                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->

        <div class="col-xl-5">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1 py-1">My Tasks</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted">All Tasks <i
                                        class="mdi mdi-chevron-down ms-1"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">All Tasks</a>
                                <a class="dropdown-item" href="#">Completed </a>
                                <a class="dropdown-item" href="#">Inprogress</a>
                                <a class="dropdown-item" href="#">Pending</a>
                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table
                            class="table table-borderless table-nowrap table-centered align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Dedline</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Assignee</th>
                                </tr>
                            </thead><!-- end thead -->
                            <tbody id="my-tasks-tbody">
                                <?php $__empty_1 = true; $__currentLoopData = $userTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox"
                                                value="<?php echo e($task->id); ?>" id="checkTask<?php echo e($task->id); ?>">
                                            <label class="form-check-label ms-1" for="checkTask<?php echo e($task->id); ?>">
                                                <?php echo e($task->title); ?>

                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?php echo e($task->due_date ? $task->due_date->format('d M Y') : 'N/A'); ?></td>
                                    <td>
                                        <?php
                                            $statusBadge = match($task->status->value) {
                                                'completed' => 'success',
                                                'in_progress' => 'warning',
                                                'pending' => 'info',
                                                default => 'danger'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($statusBadge); ?>-subtle text-<?php echo e($statusBadge); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $task->status->value))); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($task->assignedUsers->first()): ?>
                                        <a href="javascript:void(0);" class="d-inline-block"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e($task->assignedUsers->first()->name); ?>">
                                            <img src="<?php echo e($task->assignedUsers->first()->avatar ? asset('storage/'.$task->assignedUsers->first()->avatar) : URL::asset('build/images/users/avatar-2.jpg')); ?>" alt=""
                                                class="rounded-circle avatar-xxs">
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr><!-- end -->
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="ri-task-line fs-1 mb-2 d-block"></i>
                                        No tasks assigned
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div>
                    <div class="mt-3 text-center">
                        
                        <a href="<?php echo e(route('management.tasks.index')); ?>" class="text-muted text-decoration-underline">Load
                            More</a>
                    </div>
                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->

    <div class="row">
        <div class="col-xxl-4">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Team Members</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="fw-semibold text-uppercase fs-12">Sort by: </span><span
                                    class="text-muted">Last 30 Days<i
                                        class="mdi mdi-chevron-down ms-1"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">Today</a>
                                <a class="dropdown-item" href="#">Yesterday</a>
                                <a class="dropdown-item" href="#">Last 7 Days</a>
                                <a class="dropdown-item" href="#">Last 30 Days</a>
                                <a class="dropdown-item" href="#">This Month</a>
                                <a class="dropdown-item" href="#">Last Month</a>
                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->

                <div class="card-body">

                    <div class="table-responsive table-card">
                        <table class="table table-borderless table-nowrap align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">Member</th>
                                    <th scope="col">Hours</th>
                                    <th scope="col">Tasks</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody id="team-members-tbody">
                                <?php $__empty_1 = true; $__currentLoopData = $teamMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="d-flex">
                                        <img src="<?php echo e($member->avatar ? asset('storage/'.$member->avatar) : URL::asset('build/images/users/avatar-1.jpg')); ?>" alt=""
                                            class="avatar-xs rounded-3 me-2">
                                        <div>
                                            <h5 class="fs-13 mb-0"><?php echo e($member->name); ?></h5>
                                            <p class="fs-12 mb-0 text-muted"><?php echo e($member->role_name); ?></p>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0"><?php echo e($member->hours); ?>h : <span class="text-muted">150h</span>
                                        </h6>
                                    </td>
                                    <td>
                                        <?php echo e($member->tasks_count); ?>

                                    </td>
                                    <td style="width:5%;">
                                        <div id="radialBar_chart_<?php echo e($loop->iteration); ?>" data-colors='["--vz-<?php echo e($member->progress < 30 ? 'warning' : 'primary'); ?>"]'
                                            data-chart-series="<?php echo e($member->progress); ?>" class="apex-charts" dir="ltr"></div>
                                    </td>
                                </tr><!-- end tr -->
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="ri-team-line fs-1 mb-2 d-block"></i>
                                        No team members found
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>

                                    <td>
                                        <h6 class="mb-0">123h : <span class="text-muted">150h</span>
                                        </h6>
                                    </td>
                                    <td>
                                        658
                                    </td>
                                    <td>
                                        <div id="radialBar_chart_6" data-colors='["--vz-success"]'
                                            data-chart-series="85" class="apex-charts" dir="ltr"></div>
                                    </td>
                                </tr><!-- end tr -->
                                <tr>
                                    <td class="d-flex">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-3.jpg')); ?>" alt=""
                                            class="avatar-xs rounded-3 me-2">
                                        <div>
                                            <h5 class="fs-13 mb-0">Joseph Jackson</h5>
                                            <p class="fs-12 mb-0 text-muted">React Developer</p>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0">117h : <span class="text-muted">150h</span>
                                        </h6>
                                    </td>
                                    <td>
                                        125
                                    </td>
                                    <td>
                                        <div id="radialBar_chart_7" data-colors='["--vz-primary"]'
                                            data-chart-series="70" class="apex-charts" dir="ltr"></div>
                                    </td>
                                </tr><!-- end tr -->
                            </tbody><!-- end tbody -->
                        </table><!-- end table -->
                    </div>
                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->


        <div class="col-xxl-4 col-lg-6">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Projects Status</h4>
                    <div class="flex-shrink-0">
                        <div class="dropdown card-header-dropdown">
                            <a class="dropdown-btn text-muted" href="#" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                All Time <i class="mdi mdi-chevron-down ms-1"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">All Time</a>
                                <a class="dropdown-item" href="#">Last 7 Days</a>
                                <a class="dropdown-item" href="#">Last 30 Days</a>
                                <a class="dropdown-item" href="#">Last 90 Days</a>
                            </div>
                        </div>
                    </div>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="prjects-status"
                        data-colors='["--vz-success", "--vz-primary", "--vz-warning", "--vz-danger"]'
                        class="apex-charts" dir="ltr"></div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <h2 class="me-3 ff-secondary mb-0">258</h2>
                            <div>
                                <p class="text-muted mb-0">Total Projects</p>
                                <p class="text-success fw-medium mb-0">
                                    <span class="badge bg-success-subtle text-success p-1 rounded-circle"><i
                                            class="ri-arrow-right-up-line"></i></span> +3 New
                                </p>
                            </div>
                        </div>

                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-success align-middle me-2"></i>
                                Completed</p>
                            <div>
                                <span class="text-muted pe-5">125 Projects</span>
                                <span class="text-success fw-medium fs-12">15870hrs</span>
                            </div>
                        </div><!-- end -->
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-primary align-middle me-2"></i>
                                In Progress</p>
                            <div>
                                <span class="text-muted pe-5">42 Projects</span>
                                <span class="text-success fw-medium fs-12">243hrs</span>
                            </div>
                        </div><!-- end -->
                        <div
                            class="d-flex justify-content-between border-bottom border-bottom-dashed py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-warning align-middle me-2"></i>
                                Yet to Start</p>
                            <div>
                                <span class="text-muted pe-5">58 Projects</span>
                                <span class="text-success fw-medium fs-12">~2050hrs</span>
                            </div>
                        </div><!-- end -->
                        <div class="d-flex justify-content-between py-2">
                            <p class="fw-medium mb-0"><i
                                    class="ri-checkbox-blank-circle-fill text-danger align-middle me-2"></i>
                                Cancelled</p>
                            <div>
                                <span class="text-muted pe-5">89 Projects</span>
                                <span class="text-success fw-medium fs-12">~900hrs</span>
                            </div>
                        </div><!-- end -->
                    </div>
                </div><!-- end cardbody -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <!-- apexcharts -->
    <script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/pages/dashboard-projects.init.js')); ?>"></script>
    
    <!-- Custom Dashboard JavaScript with Real Backend Data & Interactive Features -->
    <script>
    // Override Projects Overview Chart with Real Data
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var chartEl = document.querySelector("#projects-overview-chart");
            if (chartEl) {
                chartEl.innerHTML = '';
                
                var linechartcustomerColors = getChartColorsArray("projects-overview-chart");
                if (linechartcustomerColors) {
                    var monthlyData = <?php echo json_encode($chartData['monthly_data'], 15, 512) ?>;
                    var months = <?php echo json_encode($chartData['months'], 15, 512) ?>;
                    
                    var options = {
                        series: [{
                            name: 'Projects Created',
                            type: 'bar',
                            data: monthlyData
                        }],
                        chart: {
                            height: 374,
                            type: 'bar',
                            toolbar: {
                                show: false,
                            }
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '30%',
                                borderRadius: 4
                            }
                        },
                        xaxis: {
                            categories: months,
                            axisTicks: {
                                show: false
                            },
                            axisBorder: {
                                show: false
                            }
                        },
                        grid: {
                            show: true,
                            padding: {
                                top: 0,
                                right: -2,
                                bottom: 15,
                                left: 10
                            },
                        },
                        colors: linechartcustomerColors,
                        tooltip: {
                            y: {
                                formatter: function (y) {
                                    return y + " Projects"
                                }
                            }
                        }
                    };
                    
                    var chart = new ApexCharts(chartEl, options);
                    chart.render();
                }
            }
        }, 500);

        // ==================== MY TASKS FILTER ====================
        const myTasksFilter = document.querySelectorAll('.dropdown-menu a[href="#"]');
        myTasksFilter.forEach(link => {
            if (link.closest('.card-header') && link.textContent.trim().match(/(All Tasks|Completed|Inprogress|Pending)/)) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const status = this.textContent.trim().toLowerCase().replace(' ', '_');
                    const actualStatus = status === 'all_tasks' ? 'all' : status;
                    
                    // Update dropdown text
                    const dropdownBtn = this.closest('.dropdown').querySelector('.dropdown-btn .text-muted');
                    if (dropdownBtn) {
                        dropdownBtn.innerHTML = this.textContent + ' <i class="mdi mdi-chevron-down ms-1"></i>';
                    }
                    
                    // Fetch filtered tasks
                    fetch('/dashboard/tasks/filter?status=' + actualStatus)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateMyTasksTable(data.tasks);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            }
        });

        function updateMyTasksTable(tasks) {
            const tbody = document.getElementById('my-tasks-tbody');
            if (!tbody) return;
            
            if (tasks.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="ri-task-line fs-1 mb-2 d-block"></i>
                            No tasks found
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = tasks.map(task => {
                const statusColors = {
                    'completed': 'success',
                    'in_progress': 'warning',
                    'pending': 'info',
                    'new': 'primary'
                };
                const badgeColor = statusColors[task.status] || 'secondary';
                const statusText = task.status.replace('_', ' ');
                
                return `
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input fs-15" type="checkbox" value="${task.id}" id="checkTask${task.id}">
                                <label class="form-check-label ms-1" for="checkTask${task.id}">
                                    ${task.title}
                                </label>
                            </div>
                        </td>
                        <td class="text-muted">${task.due_date}</td>
                        <td>
                            <span class="badge bg-${badgeColor}-subtle text-${badgeColor}">
                                ${statusText.charAt(0).toUpperCase() + statusText.slice(1)}
                            </span>
                        </td>
                        <td>
                            ${task.assignee ? `
                                <a href="javascript:void(0);" class="d-inline-block" data-bs-toggle="tooltip" title="${task.assignee.name}">
                                    <img src="${task.assignee.avatar || '<?php echo e(URL::asset("build/images/users/avatar-2.jpg")); ?>'}" 
                                         alt="" class="rounded-circle avatar-xxs">
                                </a>
                            ` : ''}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // ==================== UPCOMING SCHEDULES CALENDAR ====================
        const calendarInput = document.querySelector('[data-provider="flatpickr"]');
        if (calendarInput && typeof flatpickr !== 'undefined') {
            flatpickr(calendarInput, {
                inline: true,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr) {
                    if (dateStr) {
                        fetch('/dashboard/tasks/upcoming?date=' + dateStr)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    updateUpcomingSchedules(data.tasks);
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                }
            });
        }

        function updateUpcomingSchedules(tasks) {
            const container = document.querySelector('.upcoming-scheduled');
            if (!container) return;
            
            // Find events container (after calendar and h6)
            let eventsContainer = container.querySelector('h6').nextElementSibling;
            
            if (tasks.length === 0) {
                const existingEvents = container.querySelectorAll('.mini-stats-wid');
                existingEvents.forEach(el => el.remove());
                
                const noData = document.createElement('div');
                noData.className = 'text-center text-muted py-4';
                noData.innerHTML = '<i class="ri-calendar-line fs-1 mb-2 d-block"></i><p>No tasks for selected date</p>';
                container.querySelector('h6').after(noData);
                return;
            }
            
            // Remove existing events
            const existingEvents = container.querySelectorAll('.mini-stats-wid');
            existingEvents.forEach(el => el.remove());
            
            // Add new events
            const h6 = container.querySelector('h6');
            tasks.forEach(task => {
                const eventHtml = `
                    <div class="mini-stats-wid d-flex align-items-center mt-3">
                        <div class="flex-shrink-0 avatar-sm">
                            <span class="mini-stat-icon avatar-title rounded-circle text-success bg-success-subtle fs-4">
                                ${task.day}
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">${task.title}</h6>
                            <p class="text-muted mb-0">${task.project}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">${task.due_date}</p>
                        </div>
                    </div>
                `;
                h6.insertAdjacentHTML('afterend', eventHtml);
            });
        }

        // ==================== TEAM MEMBERS SORT ====================
        const teamSortLinks = document.querySelectorAll('.dropdown-menu a[href="#"]');
        teamSortLinks.forEach(link => {
            const text = link.textContent.trim();
            if (text.match(/(Today|Yesterday|Last 7 Days|Last 30 Days|This Month|Last Month)/)) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const periodMap = {
                        'Today': 'today',
                        'Yesterday': 'today',
                        'Last 7 Days': 'week',
                        'Last 30 Days': 'month', 
                        'This Month': 'month',
                        'Last Month': 'month'
                    };
                    const period = periodMap[text] || 'month';
                    
                    // Update dropdown text
                    const dropdownBtn = this.closest('.dropdown').querySelector('.dropdown-btn span:last-child');
                    if (dropdownBtn) {
                        dropdownBtn.innerHTML = text + '<i class="mdi mdi-chevron-down ms-1"></i>';
                    }
                    
                    // Fetch sorted team members
                    fetch('/dashboard/team/sort?period=' + period)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateTeamMembersTable(data.members);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            }
        });

        function updateTeamMembersTable(members) {
            const tbody = document.getElementById('team-members-tbody');
            if (!tbody || members.length === 0) return;
            
            tbody.innerHTML = members.map((member, index) => {
                const chartId = 'radialBar_chart_dynamic_' + member.id;
                return `
                    <tr>
                        <td class="d-flex">
                            <img src="${member.avatar || '<?php echo e(URL::asset("build/images/users/avatar-1.jpg")); ?>'}" 
                                 alt="" class="avatar-xs rounded-3 me-2">
                            <div>
                                <h5 class="fs-13 mb-0">${member.name}</h5>
                                <p class="fs-12 mb-0 text-muted">${member.role}</p>
                            </div>
                        </td>
                        <td>
                            <h6 class="mb-0">${member.hours}h : <span class="text-muted">150h</span></h6>
                        </td>
                        <td>${member.tasks_count}</td>
                        <td style="width:5%;">
                            <div id="${chartId}" data-colors='["--vz-${member.progress < 30 ? 'warning' : 'primary'}"]' 
                                 data-chart-series="${member.progress}" class="apex-charts" dir="ltr"></div>
                        </td>
                    </tr>
                `;
            }).join('');
            
            // Re-initialize radial charts
            setTimeout(() => {
                members.forEach(member => {
                    const chartId = 'radialBar_chart_dynamic_' + member.id;
                    const chartEl = document.getElementById(chartId);
                    if (chartEl) {
                        const colors = getChartColorsArray(chartId);
                        const options = {
                            series: [member.progress],
                            chart: {
                                type: 'radialBar',
                                width: 36,
                                height: 36,
                                sparkline: { enabled: true }
                            },
                            dataLabels: { enabled: false },
                            plotOptions: {
                                radialBar: {
                                    hollow: { margin: 0, size: '50%' },
                                    track: { margin: 1 },
                                    dataLabels: { show: false }
                                }
                            },
                            colors: colors
                        };
                        new ApexCharts(chartEl, options).render();
                    }
                });
            }, 100);
        }

        // ==================== VIEW ALL EVENTS LINK ====================
        const viewAllLink = document.querySelector('a[href="javascript:void(0);"].text-decoration-underline');
        if (viewAllLink && viewAllLink.textContent.includes('View all Events')) {
            viewAllLink.addEventListener('click', function(e) {
                e.preventDefault();
                // Redirect to tasks page
                window.location.href = '/tasks';
            });
        }
    });
    </script>
    
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/dashboard-projects.blade.php ENDPATH**/ ?>