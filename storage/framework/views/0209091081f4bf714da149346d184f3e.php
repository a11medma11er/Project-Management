<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="<?php echo e(URL::asset('build/images/logo-dark.png')); ?>" alt="" height="17">
                        </span>
                    </a>

                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="<?php echo e(URL::asset('build/images/logo-sm.png')); ?>" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search..." autocomplete="off" id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar style="max-height: 320px;">
                            <!-- item-->
                            <div class="dropdown-header">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Recent Searches</h6>
                            </div>

                            <div class="dropdown-item bg-transparent text-wrap">
                                <a href="index" class="btn btn-soft-primary btn-sm rounded-pill">how to setup <i class="mdi mdi-magnify ms-1"></i></a>
                                <a href="index" class="btn btn-soft-primary btn-sm rounded-pill">buttons <i class="mdi mdi-magnify ms-1"></i></a>
                            </div>
                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                            </div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-bubble-chart-line align-middle fs-18 text-muted me-2"></i>
                                <span>Analytics Dashboard</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-lifebuoy-line align-middle fs-18 text-muted me-2"></i>
                                <span>Help Center</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-user-settings-line align-middle fs-18 text-muted me-2"></i>
                                <span>My account settings</span>
                            </a>

                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-2 text-uppercase">Members</h6>
                            </div>

                            <div class="notification-list">
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-2.jpg')); ?>" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Angela Bernier</h6>
                                            <span class="fs-11 mb-0 text-muted">Manager</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-3.jpg')); ?>" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">David Grasso</h6>
                                            <span class="fs-11 mb-0 text-muted">Web Designer</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="<?php echo e(URL::asset('build/images/users/avatar-5.jpg')); ?>" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Mike Bunch</h6>
                                            <span class="fs-11 mb-0 text-muted">React Developer</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="text-center pt-3 pb-1">
                            <a href="pages-search-results" class="btn btn-primary btn-sm">View All Results <i class="ri-arrow-right-line ms-1"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php switch(Session::get('lang')):
                        case ('ru'): ?>
                        <img src="<?php echo e(URL::asset('build/images/flags/russia.svg')); ?>" class="rounded" alt="Header Language" height="20">
                        <?php break; ?>
                        <?php case ('it'): ?>
                        <img src="<?php echo e(URL::asset('build/images/flags/italy.svg')); ?>" class="rounded" alt="Header Language" height="20">
                        <?php break; ?>
                        <?php case ('sp'): ?>
                        <img src="<?php echo e(URL::asset('build/images/flags/spain.svg')); ?>" class="rounded" alt="Header Language" height="20">
                        <?php break; ?>
                        <?php case ('ch'): ?>
                        <img src="<?php echo e(URL::asset('build/images/flags/china.svg')); ?>" class="rounded" alt="Header Language" height="20">
                        <?php break; ?>
                        <?php case ('fr'): ?>
                        <img src="<?php echo e(URL::asset('build/images/flags/french.svg')); ?>" class="rounded" alt="Header Language" height="20">
                        <?php break; ?>
                        <?php case ('gr'): ?>
                        <img src="<?php echo e(URL::asset('build/images/flags/germany.svg')); ?>" class="rounded" alt="Header Language" height="20">
                        <?php break; ?>
                        <?php case ('ae'): ?>
                        <img src="<?php echo e(URL::asset('build/images/flags/ae.svg')); ?>" class="rounded" alt="Header Language" height="20">
                        <?php break; ?>
                        <?php default: ?>
                        <img src="<?php echo e(URL::asset('build/images/flags/us.svg')); ?>" class="rounded" alt="Header Language" height="20">
                        <?php endswitch; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">

                        <!-- item-->
                        <a href="<?php echo e(url('index/en')); ?>" class="dropdown-item notify-item language py-2" data-lang="en" title="English">
                            <img src="<?php echo e(URL::asset('build/images/flags/us.svg')); ?>" alt="user-image" class="me-2 rounded" height="20">
                            <span class="align-middle">English</span>
                        </a>

                        <!-- item-->
                        <a href="<?php echo e(url('index/sp')); ?>" class="dropdown-item notify-item language" data-lang="sp" title="Spanish">
                            <img src="<?php echo e(URL::asset('build/images/flags/spain.svg')); ?>" alt="user-image" class="me-2 rounded" height="20">
                            <span class="align-middle">Española</span>
                        </a>

                        <!-- item-->
                        <a href="<?php echo e(url('index/gr')); ?>" class="dropdown-item notify-item language" data-lang="gr" title="German">
                            <img src="<?php echo e(URL::asset('build/images/flags/germany.svg')); ?>" alt="user-image" class="me-2 rounded" height="20"> <span class="align-middle">Deutsche</span>
                        </a>

                        <!-- item-->
                        <a href="<?php echo e(url('index/it')); ?>" class="dropdown-item notify-item language" data-lang="it" title="Italian">
                            <img src="<?php echo e(URL::asset('build/images/flags/italy.svg')); ?>" alt="user-image" class="me-2 rounded" height="20">
                            <span class="align-middle">Italiana</span>
                        </a>

                        <!-- item-->
                        <a href="<?php echo e(url('index/ru')); ?>" class="dropdown-item notify-item language" data-lang="ru" title="Russian">
                            <img src="<?php echo e(URL::asset('build/images/flags/russia.svg')); ?>" alt="user-image" class="me-2 rounded" height="20">
                            <span class="align-middle">русский</span>
                        </a>

                        <!-- item-->
                        <a href="<?php echo e(url('index/ch')); ?>" class="dropdown-item notify-item language" data-lang="ch" title="Chinese">
                            <img src="<?php echo e(URL::asset('build/images/flags/china.svg')); ?>" alt="user-image" class="me-2 rounded" height="20">
                            <span class="align-middle">中国人</span>
                        </a>

                        <!-- item-->
                        <a href="<?php echo e(url('index/fr')); ?>" class="dropdown-item notify-item language" data-lang="fr" title="French">
                            <img src="<?php echo e(URL::asset('build/images/flags/french.svg')); ?>" alt="user-image" class="me-2 rounded" height="20">
                            <span class="align-middle">français</span>
                        </a>

                        <!-- item-->
                        <a href="<?php echo e(url('index/ae')); ?>" class="dropdown-item notify-item language" data-lang="ar" title="Arabic">
                            <img src="<?php echo e(URL::asset('build/images/flags/ae.svg')); ?>" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">Arabic</span>
                        </a>
                    </div>
                </div>





                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>

                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        <?php if(auth()->user()->unreadNotifications->count() > 0): ?>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                            <?php echo e(min(auth()->user()->unreadNotifications->count(), 9)); ?><?php echo e(auth()->user()->unreadNotifications->count() > 9 ? '+' : ''); ?>

                            <span class="visually-hidden">unread messages</span>
                        </span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> AI Notifications </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge bg-light-subtle text-body fs-13"> <?php echo e(auth()->user()->unreadNotifications->count()); ?> New</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content position-relative" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                <div data-simplebar style="max-height: 300px;" class="pe-2">
                                    <?php $__empty_1 = true; $__currentLoopData = auth()->user()->unreadNotifications->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                    <i class="ri-lightbulb-line"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="<?php echo e(route('ai.decisions.show', $notification->data['decision_id'])); ?>" class="stretched-link">
                                                    <h6 class="mt-0 mb-2 lh-base">
                                                        AI Decision: <b><?php echo e(Str::limit($notification->data['recommendation'], 50)); ?></b>
                                                    </h6>
                                                </a>
                                                <p class="mb-1 fs-12 text-muted">
                                                    Confidence: <span class="badge bg-<?php echo e($notification->data['confidence_score'] >= 0.8 ? 'success' : ($notification->data['confidence_score'] >= 0.6 ? 'warning' : 'danger')); ?>">
                                                        <?php echo e(round($notification->data['confidence_score'] * 100)); ?>%
                                                    </span>
                                                </p>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> <?php echo e($notification->created_at->diffForHumans()); ?></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="text-center py-4">
                                        <i class="ri-notification-off-line fs-48 text-muted"></i>
                                        <p class="text-muted mt-2">No new notifications</p>
                                    </div>
                                    <?php endif; ?>

                                    <?php if(auth()->user()->unreadNotifications->count() > 0): ?>
                                    <div class="my-3 text-center view-all">
                                        <a href="<?php echo e(route('ai.decisions.index', ['status' => 'pending'])); ?>" class="btn btn-soft-success waves-effect waves-light">
                                            View All Decisions <i class="ri-arrow-right-line align-middle"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="<?php if(Auth::user()->avatar != ''): ?><?php echo e(asset('storage/' . Auth::user()->avatar)); ?><?php else: ?><?php echo e(URL::asset('build/images/users/avatar-1.jpg')); ?><?php endif; ?>" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-semibold user-name-text"><?php echo e(Auth::user()->name); ?></span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted text-muted user-name-sub-text">Founder</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome Anna!</h6>
                        <a class="dropdown-item" href="pages-profile"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                        <a class="dropdown-item" href="<?php echo e(route('management.tasks.kanban')); ?>"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="pages-profile-settings"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                        <a class="dropdown-item " href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off font-size-16 align-middle me-1"></i> <span key="t-logout"><?php echo app('translator')->get('translation.logout'); ?></span></a>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php /**PATH D:\work\my projects\Git\Project-Management\resources\views/layouts/topbar.blade.php ENDPATH**/ ?>