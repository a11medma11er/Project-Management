
<?php $__env->startSection('title'); ?> Activity Analytics <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Activity Logs <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Analytics <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Activity Trends (Last 30 Days)</h4>
                </div>
                <div class="card-body">
                    <canvas id="activityTrendsChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-height-100">
                <div class="card-header">
                    <h4 class="card-title mb-0">AI Suggestions</h4>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="mb-3 text-primary"><?php echo e($aiStats['acceptance_rate']); ?>%</h2>
                        <p class="text-muted mb-4">Acceptance Rate</p>
                        
                        <div class="row text-center mt-4">
                            <div class="col-6">
                                <div class="mt-3">
                                    <p class="text-muted mb-1">Accepted</p>
                                    <h5 class="text-success"><?php echo e($aiStats['accepted']); ?></h5>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mt-3">
                                    <p class="text-muted mb-1">Rejected</p>
                                    <h5 class="text-danger"><?php echo e($aiStats['rejected']); ?></h5>
                                </div>
                            </div>
                        </div>

                        <?php if($aiStats['accepted'] + $aiStats['rejected'] > 0): ?>
                            <div class="progress mt-4" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: <?php echo e($aiStats['acceptance_rate']); ?>%"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Most Active Users</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Activities</th>
                                    <th>Chart</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $maxCount = $topUsers->max('activity_count'); ?>
                                <?php $__currentLoopData = $topUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $userStat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                        <?php echo e($userStat->causer ? substr($userStat->causer->name, 0, 1) : '?'); ?>

                                                    </div>
                                                </div>
                                                <span><?php echo e($userStat->causer->name ?? 'Unknown'); ?></span>
                                            </div>
                                        </td>
                                        <td><strong><?php echo e(number_format($userStat->activity_count)); ?></strong></td>
                                        <td>
                                            <div class="progress" style="height: 6px; width: 100px;">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: <?php echo e(($userStat->activity_count / $maxCount) * 100); ?>%">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Event Distribution</h4>
                </div>
                <div class="card-body">
                    <canvas id="eventDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Event Types Breakdown</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Event Type</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                    <th>Visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = $eventDistribution->sum('count'); ?>
                                <?php $__currentLoopData = $eventDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?php echo e($event->description); ?></span></td>
                                        <td><strong><?php echo e(number_format($event->count)); ?></strong></td>
                                        <td><?php echo e($total > 0 ? round(($event->count / $total) * 100, 1) : 0); ?>%</td>
                                        <td>
                                            <div class="progress" style="height: 8px; min-width: 200px;">
                                                <div class="progress-bar" 
                                                     style="width: <?php echo e($total > 0 ? ($event->count / $total) * 100 : 0); ?>%">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Activity Trends Chart
    const trendsCtx = document.getElementById('activityTrendsChart');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dailyActivities->pluck('date'), 15, 512) ?>,
            datasets: [{
                label: 'Activities',
                data: <?php echo json_encode($dailyActivities->pluck('count'), 15, 512) ?>,
                borderColor: 'rgb(64, 81, 137)',
                backgroundColor: 'rgba(64, 81, 137, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(64, 81, 137)',
                pointBorderColor: '#fff',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Event Distribution Chart
    const eventsCtx = document.getElementById('eventDistributionChart');
    new Chart(eventsCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($eventDistribution->pluck('description'), 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($eventDistribution->pluck('count'), 15, 512) ?>,
                backgroundColor: [
                    '#405189', '#0ab39c', '#f06548', '#f7b84b', '#299cdb',
                    '#564ab1', '#e83e8c', '#20c997', '#6c757d', '#fd7e14'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/management/activity-logs/analytics.blade.php ENDPATH**/ ?>