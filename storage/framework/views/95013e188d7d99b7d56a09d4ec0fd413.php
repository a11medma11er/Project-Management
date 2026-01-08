
<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('translation.kanbanboard'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/dragula/dragula.min.css')); ?>" rel="stylesheet">
    <style>
        .tasks-wrapper {
            max-height: calc(100vh - 280px);
            overflow-y: auto;
        }
        .tasks-board .tasks-list {
            min-width: 300px !important;
            margin-right: 30px !important;
        }

        #kanbanboard::-webkit-scrollbar {
            height: 12px;
        }
        #kanbanboard::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 10px;
        }
        #kanbanboard::-webkit-scrollbar-thumb {
            background: #888; 
            border-radius: 10px;
        }
        #kanbanboard::-webkit-scrollbar-thumb:hover {
            background: #555; 
        }
    </style>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Tasks <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>Kanban Board <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    <div class="card">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-lg-auto">
                    <div class="hstack gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createboardModal"><i
                                class="ri-add-line align-bottom me-1"></i> Create Board</button>
                    </div>
                </div>
                <!--end col-->
                <div class="col-lg-3 col-auto">
                    <div class="search-box">
                        <input type="text" class="form-control search" id="search-task-options" placeholder="Search for project, tasks...">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>
                <div class="col-auto ms-sm-auto">
                    <div class="avatar-group" id="newMembar">
                        <a href="javascript: void(0);" class="avatar-group-item"
                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                            title="Nancy">
                            <img src="<?php echo e(URL::asset('build/images/users/avatar-5.jpg')); ?>" alt=""
                                class="rounded-circle avatar-xs">
                        </a>
                        <a href="javascript: void(0);" class="avatar-group-item"
                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                            title="Frank">
                            <img src="<?php echo e(URL::asset('build/images/users/avatar-3.jpg')); ?>" alt=""
                                class="rounded-circle avatar-xs">
                        </a>
                        <a href="javascript: void(0);" class="avatar-group-item"
                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                            title="Tonya">
                            <img src="<?php echo e(URL::asset('build/images/users/avatar-10.jpg')); ?>" alt=""
                                class="rounded-circle avatar-xs">
                        </a>
                        <a href="javascript: void(0);" class="avatar-group-item"
                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                            title="Thomas">
                            <img src="<?php echo e(URL::asset('build/images/users/avatar-8.jpg')); ?>" alt=""
                                class="rounded-circle avatar-xs">
                        </a>
                        <a href="javascript: void(0);" class="avatar-group-item"
                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                            title="Herbert">
                            <img src="<?php echo e(URL::asset('build/images/users/avatar-2.jpg')); ?>" alt=""
                                class="rounded-circle avatar-xs">
                        </a>
                        <a href="#addmemberModal" data-bs-toggle="modal" class="avatar-group-item">
                            <div class="avatar-xs">
                                <div class="avatar-title rounded-circle">
                                    +
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end card-body-->
    </div>
    <!--end card-->

    <div class="tasks-board mb-3" id="kanbanboard">
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">Unassigned <small
                            class="badge bg-success align-bottom ms-1 totaltask-badge">2</small></h6>
                </div>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="fw-medium text-muted fs-12">Priority<i
                                    class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Priority</a>
                            <a class="dropdown-item" href="#">Date Added</a>
                        </div>
                    </div>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="unassigned-task" class="tasks">
                    <?php $__currentLoopData = $boardTasks['unassigned']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.kanban-task-card', ['task' => $task], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="my-3">
                <button class="btn btn-soft-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#creatertaskModal">Add More</button>
            </div>
        </div>
        <!--end tasks-list-->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">To Do <small
                        class="badge bg-secondary align-bottom ms-1 totaltask-badge"><?php echo e($boardTasks['todo']->count()); ?></small></h6>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="todo-task" class="tasks">
                    <?php $__currentLoopData = $boardTasks['todo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         <?php echo $__env->make('partials.kanban-task-card', ['task' => $task], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="my-3">
                <button class="btn btn-soft-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#creatertaskModal">Add More</button>
            </div>
        </div>
        <!--end tasks-list-->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">Inprogress <small
                        class="badge bg-warning align-bottom ms-1 totaltask-badge">2</small></h6>
                </div>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="fw-medium text-muted fs-12">Priority<i
                                    class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Priority</a>
                            <a class="dropdown-item" href="#">Date Added</a>
                        </div>
                    </div>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="inprogress-task" class="tasks">
                    <?php $__currentLoopData = $boardTasks['inprogress']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.kanban-task-card', ['task' => $task], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="my-3">
                <button class="btn btn-soft-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#creatertaskModal">Add More</button>
            </div>
        </div>
        <!--end tasks-list-->
        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">In Reviews <small
                            class="badge bg-info align-bottom ms-1 totaltask-badge"><?php echo e($boardTasks['reviews']->count()); ?></small></h6>
                </div>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="fw-medium text-muted fs-12">Priority<i
                                    class="mdi mdi-chevron-down ms-1"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Priority</a>
                            <a class="dropdown-item" href="#">Date Added</a>
                        </div>
                    </div>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="reviews-task" class="tasks">
                    <?php $__currentLoopData = $boardTasks['reviews']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.kanban-task-card', ['task' => $task], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="my-3">
                <button class="btn btn-soft-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#creatertaskModal">Add More</button>
            </div>
        </div>

        <div class="tasks-list">
            <div class="d-flex mb-3">
                <div class="flex-grow-1">
                    <h6 class="fs-14 text-uppercase fw-semibold mb-0">Completed <small
                            class="badge bg-success align-bottom ms-1 totaltask-badge"><?php echo e($boardTasks['completed']->count()); ?></small></h6>
                </div>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <span class="fw-semibold text-uppercase fs-12">Priority</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Priority</a>
                            <a class="dropdown-item" href="#">Date Added</a>
                        </div>
                    </div>
                </div>
            </div>
            <div data-simplebar class="tasks-wrapper px-3 mx-n3">
                <div id="completed-task" class="tasks">
                    <?php $__currentLoopData = $boardTasks['completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.kanban-task-card', ['task' => $task], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="my-3">
                <button class="btn btn-soft-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#creatertaskModal">Add More</button>
            </div>
        </div>
        <!--end tasks-list-->
    </div>
    <!--end task-board-->

    <div class="modal fade" id="addmemberModal" tabindex="-1" aria-labelledby="addmemberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-primary-subtle">
                    <h5 class="modal-title" id="addmemberModalLabel">Add Member</h5>
                    <button type="button" class="btn-close" id="btn-close-member"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="submissionidInput" class="form-label">Submission ID</label>
                                <input type="number" class="form-control" id="submissionidInput"
                                    placeholder="Submission ID">
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="profileimgInput" class="form-label">Profile Images</label>
                                <input class="form-control" type="file" id="profileimgInput">
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <label for="firstnameInput" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstnameInput"
                                    placeholder="Enter firstname">
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <label for="lastnameInput" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastnameInput"
                                    placeholder="Enter lastname">
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="designationInput" class="form-label">Designation</label>
                                <input type="text" class="form-control" id="designationInput"
                                    placeholder="Designation">
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="titleInput" class="form-label">Title</label>
                                <input type="text" class="form-control" id="titleInput"
                                    placeholder="Title">
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <label for="numberInput" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="numberInput"
                                    placeholder="Phone number">
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <label for="joiningdateInput" class="form-label">Joining Date</label>
                                <input type="text" class="form-control" id="joiningdateInput"
                                    data-provider="flatpickr" placeholder="Select date">
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="emailInput" class="form-label">Email ID</label>
                                <input type="email" class="form-control" id="emailInput"
                                    placeholder="Email">
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i
                            class="ri-close-line align-bottom me-1"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="addMember">Add Member</button>
                </div>
            </div>
        </div>
    </div>
    <!--end add member modal-->

    <div class="modal fade" id="createboardModal" tabindex="-1" aria-labelledby="createboardModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-primary-subtle">
                    <h5 class="modal-title" id="createboardModalLabel">Add Board</h5>
                    <button type="button" class="btn-close" id="addBoardBtn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="boardName" class="form-label">Board Name</label>
                                <input type="text" class="form-control" id="boardName"
                                    placeholder="Enter board name">
                            </div>
                            <div class="mt-4">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="addNewBoard">Add
                                        Board</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end add board modal-->

    <div class="modal fade" id="creatertaskModal" tabindex="-1" aria-labelledby="creatertaskModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header p-3 bg-info-subtle">
                    <h5 class="modal-title" id="creatertaskModalLabel">Create New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo e(route('management.tasks.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="clientName" class="form-label">Client / Project Name</label>
                                <input type="text" class="form-control" id="clientName" name="client_name"
                                    placeholder="Enter client or project name">
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="task-title" class="form-label">Task Title</label>
                                <input type="text" class="form-control" id="task-title" name="title" required
                                    placeholder="Task title">
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="task-description" class="form-label">Task
                                    Description</label>
                                <textarea class="form-control" id="task-description" name="description" rows="3" required
                                    placeholder="Task description"></textarea>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="formFile" class="form-label">Tasks Images</label>
                                <input class="form-control" type="file" id="formFile" name="attachments[]" multiple>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <label for="tasks-progress" class="form-label">Add Team Member</label>
                                <div data-simplebar style="height: 95px;">
                                    <ul class="list-unstyled vstack gap-2 mb-0">
                                        <?php $__currentLoopData = \App\Models\User::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox"
                                                    name="assigned_users[]" value="<?php echo e($user->id); ?>" id="user-<?php echo e($user->id); ?>">
                                                <label class="form-check-label d-flex align-items-center"
                                                    for="user-<?php echo e($user->id); ?>">
                                                    <span class="flex-shrink-0">
                                                        <?php if($user->avatar): ?>
                                                            <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="" class="avatar-xxs rounded-circle">
                                                        <?php else: ?>
                                                            <div class="avatar-xxs">
                                                                <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                                                    <?php echo e(substr($user->name, 0, 1)); ?>

                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </span>
                                                    <span class="flex-grow-1 ms-2">
                                                        <?php echo e($user->name); ?>

                                                    </span>
                                                </label>
                                            </div>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-4">
                                <label for="due-date" class="form-label">Due Date</label>
                                <input type="text" class="form-control" id="due-date" name="due_date"
                                    data-provider="flatpickr" placeholder="Select date">
                            </div>
                            <!--end col-->
                            <div class="col-lg-4">
                                <label for="categories" class="form-label">Tags</label>
                                <input type="text" class="form-control" id="categories" name="tags[]"
                                    placeholder="Enter tag">
                            </div>
                            <!--end col-->
                            <div class="col-lg-4">
                                <label for="priority-field" class="form-label">Priority</label>
                                <select class="form-control" name="priority" id="priority-field">
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                            <input type="hidden" name="status" value="pending">
                            <!--end col-->
                            <div class="mt-4">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="button" class="btn btn-light"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add Task</button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end add board modal-->

    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="delete-btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px">
                        </lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you sure ?</h4>
                            <p class="text-muted mx-4 mb-0">Are you sure you want to remove this tasks ?
                            </p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn w-sm btn-danger" id="delete-record">Yes, Delete
                            It!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end modal -->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/dragula/dragula.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/dom-autoscroller/dom-autoscroller.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tasks_list = [
                document.getElementById("unassigned-task"),
                document.getElementById("todo-task"),
                document.getElementById("inprogress-task"),
                document.getElementById("reviews-task"),
                document.getElementById("completed-task")
            ];

            // 1. Initialize Dragula
            if (typeof dragula !== 'undefined') {
                var drake = dragula(tasks_list)
                    .on('drag', function (el) {
                        el.className = el.className.replace('ex-moved', '');
                    })
                    .on('drop', function (el, target, source, sibling) {
                        el.className += ' ex-moved';
                        
                        // Update UI Counts & Empty States
                        noTaskImage();
                        taskCounter();

                        // AJAX Update
                        var taskId = el.getAttribute('data-task-id');
                        var newStatusId = target.getAttribute('id');

                        axios.post('<?php echo e(route("management.tasks.kanban.update")); ?>', {
                            taskId: taskId,
                            status: newStatusId
                        })
                        .then(function (response) {
                            // console.log('Status updated');
                        })
                        .catch(function (error) {
                            console.error('Error updating status:', error);
                            // Optional: Toast error
                        });
                    })
                    .on('over', function (el, container) {
                        container.className += ' ex-over';
                    })
                    .on('out', function (el, container) {
                        container.className = container.className.replace('ex-over', '');
                        noTaskImage();
                        taskCounter();
                    });

                // 2. Initialize AutoScroll
                if (typeof autoScroll !== 'undefined') {
                    autoScroll([
                        document.querySelector("#kanbanboard"),
                        document.querySelector(".tasks-board"),
                        ...tasks_list
                    ], {
                        margin: 20,
                        maxSpeed: 100,
                        scrollWhenOutside: true,
                        autoScroll: function () {
                            return this.down && drake.dragging;
                        }
                    });
                }
            }

            // Helper: Toggle "No Task" image if list is empty
            function noTaskImage() {
                tasks_list.forEach(function (item) {
                    var taskBox = item.querySelectorAll(".tasks-box");
                    if(taskBox.length > 0){
                        item.classList.remove("noTask");
                    } else {
                        item.classList.add("noTask");
                    }
                });
            }

            // Helper: Update Badges
            function taskCounter() {
                tasks_list.forEach(function (element) {
                    var task_box = element.querySelectorAll(".tasks-box");
                    var task_counted = task_box.length;
                    
                    // Find duplicate badges in header if any, or find the closest specific badge
                    // Structure: .tasks-list -> .d-flex -> .flex-grow-1 -> h6 -> .totaltask-badge
                    var parent = element.closest('.tasks-list');
                    if(parent) {
                        var badge = parent.querySelector(".totaltask-badge");
                        if(badge) badge.innerText = task_counted;
                    }
                });
            }
            
            // Initial Run
            noTaskImage();
            taskCounter();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/apps-tasks-kanban.blade.php ENDPATH**/ ?>