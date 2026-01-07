

<?php $__env->startSection('title'); ?> Permissions Management <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
<?php $__env->slot('li_1'); ?> Management <?php $__env->endSlot(); ?>
<?php $__env->slot('title'); ?> Permissions <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="row">
    <div class="col-12">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Permissions List</h5>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-permissions')): ?>
                        <a href="<?php echo e(route('management.permissions.create')); ?>" class="btn btn-primary">
                            <i class="ri-add-line align-middle"></i> Add Permission
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="accordion" id="permissionsAccordion">
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module => $modulePermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-<?php echo e($module); ?>">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-<?php echo e($module); ?>">
                                    <strong class="text-uppercase"><?php echo e(ucfirst($module)); ?> Management</strong>
                                    <span class="badge bg-info ms-2"><?php echo e(count($modulePermissions)); ?> permissions</span>
                                </button>
                            </h2>
                            <div id="collapse-<?php echo e($module); ?>" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Permission Name</th>
                                                    <th>Assigned to Roles</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $modulePermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td>
                                                            <code><?php echo e($permission->name); ?></code>
                                                        </td>
                                                        <td>
                                                            <?php $__currentLoopData = $permission->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <span class="badge bg-primary me-1"><?php echo e($role->name); ?></span>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-permissions')): ?>
                                                                    <a href="<?php echo e(route('management.permissions.edit', $permission)); ?>" 
                                                                       class="btn btn-soft-info">
                                                                        <i class="ri-pencil-fill"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-permissions')): ?>
                                                                    <form action="<?php echo e(route('management.permissions.destroy', $permission)); ?>" 
                                                                          method="POST" class="d-inline">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('DELETE'); ?>
                                                                        <button type="submit" class="btn btn-soft-danger"
                                                                                onclick="return confirm('Delete this permission?')">
                                                                            <i class="ri-delete-bin-fill"></i>
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/management/permissions/index.blade.php ENDPATH**/ ?>