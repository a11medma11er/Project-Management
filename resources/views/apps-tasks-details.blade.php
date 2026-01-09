@extends('layouts.master')
@section('title') @lang('translation.task-details') @endsection
@section('content')

<div class="row">
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="card-title mb-3 flex-grow-1 text-start">Time Tracking</h6>
                <div class="mb-2">
                    <lord-icon src="https://cdn.lordicon.com/kbtmbyzy.json" trigger="loop"
                        colors="primary:#8c68cd,secondary:#4788ff" style="width:90px;height:90px">
                    </lord-icon>
                </div>
                <h3 class="mb-1">
                    <span id="totalTime">{{ $task->timeEntries->sum('duration_minutes') }}</span> min
                </h3>
                <h5 class="fs-14 mb-2">Total Logged Time</h5>
                
                <!-- Live Timer Display -->
                <div id="liveTimerDisplay" class="mb-3" style="display: none;">
                    <h4 class="text-primary mb-0">
                        <i class="ri-time-line"></i> 
                        <span id="currentTimer">00:00:00</span>
                    </h4>
                    <small class="text-muted">Current Session</small>
                </div>
                
                <div class="hstack gap-2 justify-content-center">
                    <button id="stopBtn" class="btn btn-danger btn-sm" style="display: none;">
                        <i class="ri-stop-circle-line align-bottom me-1"></i> Stop
                    </button>
                    <button id="startBtn" class="btn btn-primary btn-sm">
                        <i class="ri-play-circle-line align-bottom me-1"></i> Start
                    </button>
                </div>
                
                <!-- Quick Add Time Form (Hidden by default) -->
                <div id="quickAddForm" class="mt-3 border-top pt-3" style="display: none;">
                    <form action="{{ route('management.tasks.time-entries.store', $task) }}" method="POST" class="text-start">
                        @csrf
                        <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="duration_minutes" id="sessionDuration">
                        <input type="hidden" name="task_title" value="{{ $task->title }}">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="ri-save-line"></i> Save Session
                        </button>
                    </form>
                </div>
            </div>
        </div><!--end card-->
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-4">
                    <select class="form-control" name="choices-single-default" data-choices data-choices-search-false>
                        <option value="">Select Task board</option>
                        <option value="Unassigned">Unassigned</option>
                        <option value="To Do">To Do</option>
                        <option value="Inprogress">Inprogress</option>
                        <option value="In Reviews" selected>In Reviews</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="table-card">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-medium">Tasks No</td>
                                <td>{{ $task->task_number }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Tasks Title</td>
                                <td>{{ $task->title }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Project Name</td>
                                <td>{{ $task->project ? $task->project->title : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Priority</td>
                                <td><span class="badge @if($task->priority === 'High') bg-danger-subtle text-danger @elseif($task->priority === 'Medium') bg-warning-subtle text-warning @else bg-success-subtle text-success @endif">{{ $task->priority }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Status</td>
                                <td><span class="badge @if($task->status === 'New') bg-info-subtle text-info @elseif($task->status === 'Pending') bg-warning-subtle text-warning @elseif($task->status === 'Inprogress') bg-secondary-subtle text-secondary @else bg-success-subtle text-success @endif">{{ $task->status }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Due Date</td>
                                <td>{{ $task->due_date->format('d M, Y') }}</td>
                            </tr>
                        </tbody>
                    </table><!--end table-->
                </div>
            </div>
        </div><!--end card-->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex mb-3">
                    <h6 class="card-title mb-0 flex-grow-1">Assigned To</h6>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteMembersModal"><i class="ri-share-line me-1 align-bottom"></i> Assigned Member</button>
                    </div>
                </div>
                <ul class="list-unstyled vstack gap-3 mb-0">
                    @forelse($task->assignedUsers as $user)
                    <li>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="avatar-xs rounded-circle">
                                @else
                                    <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle avatar-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-1"><a href="javascript:void(0);">{{ $user->name }}</a></h6>
                                <p class="text-muted mb-0">{{ $user->email }}</p>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li>
                        <div class="text-muted text-center">No members assigned</div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div><!--end card-->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Attachments</h5>
                <div class="vstack gap-2">
                    @forelse($task->attachments as $attachment)
                        @php
                            $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                            $icon = match($ext) {
                                'zip', 'rar', '7z' => 'ri-folder-zip-line',
                                'ppt', 'pptx' => 'ri-file-ppt-2-line',
                                'doc', 'docx' => 'ri-file-word-line',
                                'xls', 'xlsx', 'csv' => 'ri-file-excel-line',
                                'pdf' => 'ri-file-pdf-line',
                                'jpg', 'jpeg', 'png', 'gif', 'svg' => 'ri-image-2-line',
                                'txt' => 'ri-file-text-line',
                                default => 'ri-file-line'
                            };
                            $size = $attachment->file_size;
                            $formattedSize = $size > 1048576 ? round($size / 1048576, 2) . ' MB' : round($size / 1024, 2) . ' KB';
                        @endphp
                    <div class="border rounded border-dashed p-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-light text-primary rounded fs-24">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="fs-13 mb-1"><a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-body text-truncate d-block">{{ $attachment->file_name }}</a></h5>
                                <div>{{ $formattedSize }}</div>
                            </div>
                            <div class="flex-shrink-0 ms-2">
                                <div class="d-flex gap-1">
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" download class="btn btn-icon text-muted btn-sm fs-18"><i class="ri-download-2-line"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-muted text-center py-2">No attachments</div>
                    @endforelse
                </div>
            </div>
        </div><!--end card-->
    </div><!---end col-->
    <div class="col-xxl-9">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">
                    <h6 class="mb-3 fw-semibold text-uppercase">Sub-tasks</h6>
                    <ul class=" ps-3 list-unstyled vstack gap-2">
                        @forelse($task->subTasks as $subTask)
                        <li>
                            <div class="form-check">
                                <input class="form-check-input subtask-checkbox" type="checkbox" value="{{ $subTask->id }}" id="subTask{{ $subTask->id }}" 
                                    data-url="{{ route('management.tasks.sub-tasks.toggle', [$task, $subTask]) }}"
                                    {{ $subTask->is_completed ? 'checked' : '' }}>
                                <label class="form-check-label {{ $subTask->is_completed ? 'text-decoration-line-through text-muted' : '' }}" for="subTask{{ $subTask->id }}">
                                    {{ $subTask->title }}
                                </label>
                            </div>
                        </li>
                        @empty
                        <li><div class="text-muted">No sub-tasks</div></li>
                        @endforelse
                    </ul>

                    <div class="pt-3 border-top border-top-dashed mt-4">
                        <h6 class="mb-3 fw-semibold text-uppercase">Tasks Tags</h6>
                        <div class="hstack flex-wrap gap-2 fs-15">
                             @forelse($task->tags as $tag)
                                <div class="badge fw-medium bg-primary-subtle text-primary">{{ $tag->tag }}</div>
                             @empty
                                <div class="text-muted fs-12">No tags</div>
                             @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end card-->
        <div class="card">
            <div class="card-header">
                <div>
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home-1" role="tab">
                                Comments ({{ $task->comments->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#messages-1" role="tab">
                                Attachments File ({{ $task->attachments->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#profile-1" role="tab">
                                @php
                                   $totalMin = $task->timeEntries->sum('duration_minutes');
                                   $hrs = floor($totalMin / 60);
                                   $min = $totalMin % 60;
                                @endphp
                                Time Entries ({{ $hrs }} hrs {{ $min }} min)
                            </a>
                        </li>
                    </ul><!--end nav-->
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="home-1" role="tabpanel">
                        <h5 class="card-title mb-4">Comments</h5>
                        <div data-simplebar style="height: 508px;" class="px-3 mx-n3 mb-2">
                             @forelse($task->comments as $comment)
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0">
                                         @if($comment->user->avatar)
                                            <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="" class="avatar-xs rounded-circle" />
                                         @else
                                            <div class="avatar-xs rounded-circle bg-secondary-subtle">
                                                <span class="d-flex align-items-center justify-content-center w-100 h-100 text-secondary fs-10">
                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                         @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="fs-13"><a href="javascript:void(0)" class="text-body">{{ $comment->user->name }}</a> <small class="text-muted">{{ $comment->created_at->format('d M Y - h:i A') }}</small></h5>
                                        <p class="text-muted">{{ $comment->comment }}</p>
                                        <a href="javascript: void(0);" class="badge text-muted bg-light" onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('d-none')"><i class="mdi mdi-reply"></i> Reply</a>
                                        
                                        <!-- Replies -->
                                        @foreach($comment->replies as $reply)
                                            <div class="d-flex mt-4">
                                                <div class="flex-shrink-0">
                                                    @if($reply->user->avatar)
                                                        <img src="{{ asset('storage/' . $reply->user->avatar) }}" alt="" class="avatar-xs rounded-circle" />
                                                    @else
                                                        <div class="avatar-xs rounded-circle bg-secondary-subtle">
                                                            <span class="d-flex align-items-center justify-content-center w-100 h-100 text-secondary fs-10">
                                                                {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="fs-13"><a href="javascript:void(0)" class="text-body">{{ $reply->user->name }}</a> <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small></h5>
                                                    <p class="text-muted">{{ $reply->comment }}</p>
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Reply Form -->
                                        <div class="mt-3 d-none" id="reply-form-{{ $comment->id }}">
                                            <form action="{{ route('management.tasks.comments.store', $task) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div class="input-group">
                                                    <input type="text" name="comment" class="form-control" placeholder="Reply..." required>
                                                    <button class="btn btn-primary" type="submit">Send</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                             @empty
                                <div class="text-center py-4">
                                    <p class="text-muted">No comments yet.</p>
                                </div>
                             @endforelse
                        </div>
                        <form action="{{ route('management.tasks.comments.store', $task) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label for="comment-textarea" class="form-label">Leave a Comment</label>
                                    <textarea class="form-control bg-light border-light" name="comment" id="comment-textarea" rows="3" placeholder="Enter your comment..." required></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary"><i class="ri-send-plane-2-fill align-bottom me-1"></i> Post Comment</button>
                                </div>
                            </div>
                        </form>
                    </div><!--end tab-pane-->
                    <div class="tab-pane" id="messages-1" role="tabpanel">
                        <div class="table-responsive table-card">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">File Name</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Size</th>
                                        <th scope="col">Upload Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($task->attachments as $attachment)
                                        @php
                                            $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                                            $icon = match($ext) {
                                                'zip', 'rar', '7z' => 'ri-file-zip-fill',
                                                'ppt', 'pptx' => 'ri-file-ppt-fill',
                                                'doc', 'docx' => 'ri-file-word-fill',
                                                'xls', 'xlsx', 'csv' => 'ri-file-excel-fill',
                                                'pdf' => 'ri-file-pdf-fill',
                                                'jpg', 'jpeg', 'png', 'gif', 'svg' => 'ri-image-2-fill',
                                                'txt' => 'ri-file-text-fill',
                                                default => 'ri-file-fill'
                                            };
                                            $color = match($ext) {
                                                'zip', 'rar', '7z' => 'primary',
                                                'ppt', 'pptx' => 'danger',
                                                'doc', 'docx' => 'info',
                                                'xls', 'xlsx', 'csv' => 'success',
                                                'pdf' => 'danger',
                                                default => 'secondary'
                                            };
                                            $size = $attachment->file_size;
                                            $formattedSize = $size > 1048576 ? round($size / 1048576, 2) . ' MB' : round($size / 1024, 2) . ' KB';
                                        @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-{{ $color }}-subtle text-{{ $color }} rounded fs-20">
                                                        <i class="{{ $icon }}"></i>
                                                    </div>
                                                </div>
                                                <div class="ms-3 flex-grow-1">
                                                    <h6 class="fs-15 mb-0"><a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">{{ $attachment->file_name }}</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ strtoupper($ext) }} File</td>
                                        <td>{{ $formattedSize }}</td>
                                        <td>{{ $attachment->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="btn btn-light btn-icon" id="dropdownMenuLink{{ $attachment->id }}" data-bs-toggle="dropdown" aria-expanded="true">
                                                    <i class="ri-equalizer-fill"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink{{ $attachment->id }}">
                                                    <li><a class="dropdown-item" href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"><i class="ri-eye-fill me-2 align-middle text-muted"></i>View</a></li>
                                                    <li><a class="dropdown-item" href="{{ asset('storage/' . $attachment->file_path) }}" download><i class="ri-download-2-fill me-2 align-middle text-muted"></i>Download</a></li>
                                                    <li class="dropdown-divider"></li>
                                                    <!-- Delete not implemented yet properly -->
                                                    <li><a class="dropdown-item" href="javascript:void(0);"><i class="ri-delete-bin-5-line me-2 align-middle text-muted"></i>Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No attachments found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table><!--end table-->
                        </div>
                    </div><!--end tab-pane-->
                    <div class="tab-pane" id="profile-1" role="tabpanel">
                        <h6 class="card-title mb-4 pb-2">Time Entries</h6>
                        
                        <!-- Add Time Entry Form -->
                        <div class="card border mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Add Time Entry</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('management.tasks.time-entries.store', $task) }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Date</label>
                                            <input type="date" class="form-control" name="date" max="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Duration (minutes)</label>
                                            <input type="number" class="form-control" name="duration_minutes" min="1" max="1440" placeholder="e.g., 120" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Idle Time (minutes)</label>
                                            <input type="number" class="form-control" name="idle_minutes" min="0" placeholder="e.g., 5">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="ri-add-line"></i> Add Entry
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive table-card">
                            <table class="table align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">Member</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Duration</th>
                                        <th scope="col">Timer Idle</th>
                                        <th scope="col">Tasks Title</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($task->timeEntries as $entry)
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center">
                                                @if($entry->user->avatar)
                                                    <img src="{{ asset('storage/' . $entry->user->avatar) }}" alt="" class="rounded-circle avatar-xxs">
                                                @else
                                                    <div class="avatar-xxs rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center">
                                                        <span class="text-secondary fs-10">{{ strtoupper(substr($entry->user->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1 ms-2">
                                                    <a href="javascript:void(0);" class="fw-medium">{{ $entry->user->name }}</a>
                                                </div>
                                            </div>
                                        </th>
                                        <td>{{ $entry->date->format('d M, Y') }}</td>
                                        <td>
                                            @php
                                                $hrs = floor($entry->duration_minutes / 60);
                                                $min = $entry->duration_minutes % 60;
                                            @endphp
                                            {{ $hrs }} hrs {{ $min }} min
                                        </td>
                                        <td>
                                            @if($entry->idle_minutes > 0)
                                                @php
                                                    $idleHrs = floor($entry->idle_minutes / 60);
                                                    $idleMin = $entry->idle_minutes % 60;
                                                @endphp
                                                @if($idleHrs > 0){{ $idleHrs }} hrs @endif{{ $idleMin }} min
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $entry->task_title ?? $task->title }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="ri-time-line fs-20 d-block mb-2"></i>
                                            No time entries recorded yet
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table><!--end table-->
                        </div>
                    </div><!--edn tab-pane-->

                </div><!--end tab-content-->
            </div>
        </div><!--end card-->
    </div><!--end col-->
</div><!--end row-->

<div class="modal fade" id="inviteMembersModal" tabindex="-1" aria-labelledby="inviteMembersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <form action="{{ route('management.tasks.update', $task) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header p-3 ps-4 bg-primary-subtle">
                    <h5 class="modal-title" id="inviteMembersModalLabel">Team Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="search-box mb-3">
                        <input type="text" class="form-control bg-light border-light" placeholder="Search here...">
                        <i class="ri-search-line search-icon"></i>
                    </div>

                    <div class="mx-n4 px-4" data-simplebar style="max-height: 225px;">
                        <div class="vstack gap-3">
                            @foreach($users as $user)
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="img-fluid rounded-circle">
                                    @else
                                        <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="javascript:void(0);" class="text-body d-block">{{ $user->name }}</a></h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="assigned_users[]" value="{{ $user->id }}" 
                                            id="user_{{ $user->id }}" {{ $task->assignedUsers->contains($user->id) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light w-xs" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary w-xs">Update Assignments</button>
                </div>
            </form>
        </div>
        <!-- end modal-content -->
    </div>
    <!-- modal-dialog -->
</div>
<!-- end modal -->
@endsection
@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ============================================
        // Sub-tasks Toggle Functionality
        // ============================================
        const checkboxes = document.querySelectorAll('.subtask-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const url = this.dataset.url;
                const isChecked = this.checked;
                const label = this.nextElementSibling;

                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ is_completed: isChecked })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        if (data.is_completed) {
                            label.classList.add('text-decoration-line-through', 'text-muted');
                        } else {
                            label.classList.remove('text-decoration-line-through', 'text-muted');
                        }
                    } else {
                        alert('Failed to update subtask');
                        this.checked = !isChecked; // Revert
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating subtask: ' + error.message);
                    this.checked = !isChecked; // Revert
                });
            });
        });

        // ============================================
        // Time Tracking Functionality
        // ============================================
        const startBtn = document.getElementById('startBtn');
        const stopBtn = document.getElementById('stopBtn');
        const currentTimerDisplay = document.getElementById('currentTimer');
        const liveTimerDisplay = document.getElementById('liveTimerDisplay');
        const quickAddForm = document.getElementById('quickAddForm');
        const sessionDurationInput = document.getElementById('sessionDuration');
        
        const STORAGE_KEY = 'task_{{ $task->id }}_timer';
        let timerInterval = null;
        let startTime = null;
        let elapsedSeconds = 0;

        // Load saved timer state
        function loadTimerState() {
            const savedState = localStorage.getItem(STORAGE_KEY);
            if (savedState) {
                const state = JSON.parse(savedState);
                startTime = new Date(state.startTime);
                elapsedSeconds = Math.floor((Date.now() - startTime.getTime()) / 1000);
                startTimer(false); // Resume without resetting
            }
        }

        // Save timer state
        function saveTimerState() {
            if (startTime) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({
                    startTime: startTime.toISOString()
                }));
            }
        }

        // Clear timer state
        function clearTimerState() {
            localStorage.removeItem(STORAGE_KEY);
        }

        // Format seconds to HH:MM:SS
        function formatTime(seconds) {
            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        // Update timer display
        function updateDisplay() {
            currentTimerDisplay.textContent = formatTime(elapsedSeconds);
            sessionDurationInput.value = Math.ceil(elapsedSeconds / 60); // Convert to minutes
        }

        // Start timer
        function startTimer(reset = true) {
            if (reset) {
                startTime = new Date();
                elapsedSeconds = 0;
            }
            
            liveTimerDisplay.style.display = 'block';
            startBtn.style.display = 'none';
            stopBtn.style.display = 'inline-block';
            quickAddForm.style.display = 'none';
            
            updateDisplay();
            saveTimerState();
            
            timerInterval = setInterval(() => {
                elapsedSeconds++;
                updateDisplay();
            }, 1000);
        }

        // Stop timer
        function stopTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            
            startBtn.style.display = 'inline-block';
            stopBtn.style.display = 'none';
            
            if (elapsedSeconds > 0) {
                quickAddForm.style.display = 'block';
            }
            
            clearTimerState();
        }

        // Event listeners
        startBtn.addEventListener('click', () => startTimer(true));
        stopBtn.addEventListener('click', stopTimer);

        // Auto-save form submission handler
        quickAddForm.querySelector('form').addEventListener('submit', function() {
            // Reset timer after saving
            setTimeout(() => {
                elapsedSeconds = 0;
                liveTimerDisplay.style.display = 'none';
                quickAddForm.style.display = 'none';
                updateDisplay();
            }, 100);
        });

        // Load timer on page load
        loadTimerState();
    });
</script>
@endsection
