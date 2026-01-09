@extends('layouts.master')
@section('title')
    {{ $project->title }}
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-n4 mx-n4 card-border-effect-none">
                <div class="bg-primary-subtle">
                    <div class="card-body pb-0 px-4">
                        <div class="row mb-3">
                            <div class="col-md">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-auto">
                                        <div class="avatar-md">
                                            <div class="avatar-title bg-white rounded-circle">
                                                @if($project->thumbnail)
                                                    <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="" class="avatar-xs rounded-circle">
                                                @else
                                                    <span class="avatar-xs">{{ strtoupper(substr($project->title, 0, 2)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div>
                                            <h4 class="fw-bold">{{ $project->title }}</h4>
                                            <div class="hstack gap-3 flex-wrap">
                                                @if($project->category)
                                                <div><i class="ri-building-line align-bottom me-1"></i> {{ $project->category }}</div>
                                                <div class="vr"></div>
                                                @endif
                                                <div>Created: <span class="fw-medium">{{ $project->created_at->format('d M, Y') }}</span></div>
                                                <div class="vr"></div>
                                                <div>Deadline: <span class="fw-medium">{{ $project->deadline->format('d M, Y') }}</span></div>
                                                <div class="vr"></div>
                                                <div class="badge rounded-pill bg-{{ $project->status == 'Completed' ? 'success' : ($project->status == 'On Hold' ? 'warning' : 'info') }} fs-12">{{ $project->status }}</div>
                                                <div class="badge rounded-pill bg-{{ $project->priority == 'High' ? 'danger' : ($project->priority == 'Low' ? 'success' : 'warning') }} fs-12">{{ $project->priority }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="hstack gap-1 flex-wrap">
                                    <button type="button" class="btn py-0 fs-16 favourite-btn {{ $project->is_favorite ? 'active' : '' }}" data-project-id="{{ $project->id }}">
                                        <i class="ri-star-fill"></i>
                                    </button>
                                    @can('edit-projects')
                                    <a href="{{ route('management.projects.edit', $project->id) }}" class="btn py-0 fs-16 text-body">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    @endcan
                                    @can('delete-projects')
                                    <button type="button" class="btn py-0 fs-16 text-body" onclick="event.preventDefault(); 
                                        if(confirm('Are you sure?')) document.getElementById('delete-form-{{ $project->id }}').submit();">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                    <form id="delete-form-{{ $project->id }}" 
                                        action="{{ route('management.projects.destroy', $project->id) }}" 
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview"
                                    role="tab">
                                    Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-documents" role="tab">
                                    Documents
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-tasks" role="tab">
                                    Tasks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-activities" role="tab">
                                    Activities
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-team" role="tab">
                                    Team
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- end card body -->
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="tab-content text-muted">
                <div class="tab-pane fade show active" id="project-overview" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-9 col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-muted">
                                        <h6 class="mb-3 fw-semibold text-uppercase">Summary</h6>
                                        <div>{!! $project->description !!}</div>

                                        @if($project->skills && count($project->skills) > 0)
                                        <div class="pt-3 border-top border-top-dashed mt-4">
                                            <h6 class="mb-3 fw-semibold text-uppercase">Skills</h6>
                                            <div class="d-flex flex-wrap gap-2 fs-15">
                                                @foreach($project->skills as $skill)
                                                    <span class="badge bg-primary-subtle text-primary">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif



                                        <div class="pt-3 border-top border-top-dashed mt-4">
                                            <div class="row gy-3">

                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Create Date :</p>
                                                        <h5 class="fs-15 mb-0">{{ $project->created_at->format('d M, Y') }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Deadline :</p>
                                                        <h5 class="fs-15 mb-0">{{ $project->deadline->format('d M, Y') }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Priority :</p>
                                                        <div class="badge bg-{{ $project->priority == 'High' ? 'danger' : ($project->priority == 'Low' ? 'success' : 'warning') }} fs-12">{{ $project->priority }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Status :</p>
                                                        <div class="badge bg-{{ $project->status == 'Completed' ? 'success' : ($project->status == 'On Hold' ? 'warning' : 'info') }} fs-12">{{ $project->status }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->

                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Comments</h4>
                                    <div class="flex-shrink-0">
                                        <div class="dropdown card-header-dropdown">
                                            <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted">Recent<i
                                                        class="mdi mdi-chevron-down ms-1"></i></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Recent</a>
                                                <a class="dropdown-item" href="#">Top Rated</a>
                                                <a class="dropdown-item" href="#">Previous</a>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- end card header -->

                                <div class="card-body">

                                    <div data-simplebar style="height: 300px;" class="px-3 mx-n3 mb-2">
                                        @forelse($project->comments->where('parent_id', null) as $comment)
                                        <div class="d-flex mb-4">
                                            <div class="flex-shrink-0">
                                                @if($comment->user->avatar)
                                                    <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}"
                                                        class="avatar-xs rounded-circle" />
                                                @else
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title rounded-circle bg-primary">
                                                            {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="fs-13">{{ $comment->user->name }} 
                                                    <small class="text-muted ms-2">{{ $comment->created_at->format('d M Y - h:iA') }}</small>
                                                </h5>
                                                <p class="text-muted">{{ $comment->comment }}</p>
                                                
                                                @if($comment->replies->count() > 0)
                                                    @foreach($comment->replies as $reply)
                                                    <div class="d-flex mt-3">
                                                        <div class="flex-shrink-0">
                                                            @if($reply->user->avatar)
                                                                <img src="{{ asset('storage/' . $reply->user->avatar) }}" alt="{{ $reply->user->name }}"
                                                                    class="avatar-xs rounded-circle" />
                                                            @else
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title rounded-circle bg-secondary">
                                                                        {{ strtoupper(substr($reply->user->name, 0, 2)) }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="fs-13">{{ $reply->user->name }} 
                                                                <small class="text-muted ms-2">{{ $reply->created_at->format('d M Y - h:iA') }}</small>
                                                            </h5>
                                                            <p class="text-muted">{{ $reply->comment }}</p>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-4">
                                            <i class="ri-message-3-line fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">No comments yet. Be the first to comment!</p>
                                        </div>
                                        @endforelse
                                    </div>
                                    <form action="{{ route('management.projects.comments.store', $project) }}" method="POST" class="mt-4">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="comment-textarea" class="form-label text-body">Leave a Comment</label>
                                                <textarea class="form-control bg-light border-light" name="comment" id="comment-textarea" rows="3"
                                                    placeholder="Enter your comment..." required></textarea>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ri-send-plane-2-fill align-bottom me-1"></i> Post Comment
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!--end col-->
                        <div class="col-xl-3 col-lg-4">
                            <div class="card">
                                <div class="card-header align-items-center d-flex border-bottom-dashed">
                                    <h4 class="card-title mb-0 flex-grow-1">Members</h4>
                                    @can('edit-projects')
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('management.projects.edit', $project->id) }}" class="btn btn-soft-primary btn-sm">
                                            Edit
                                        </a>
                                    </div>
                                    @endcan
                                </div>

                                <div class="card-body">
                                    <div data-simplebar style="max-height: 235px;" class="mx-n3 px-3">
                                        <div class="vstack gap-3">
                                            @if($project->teamLead)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs flex-shrink-0 me-3">
                                                    @if($project->teamLead->avatar)
                                                        <img src="{{ asset('storage/' . $project->teamLead->avatar) }}" alt="" class="img-fluid rounded-circle">
                                                    @else
                                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                            {{ strtoupper(substr($project->teamLead->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fs-13 mb-0">{{ $project->teamLead->name }}</h5>
                                                    <small class="text-muted">Team Lead</small>
                                                </div>
                                            </div>
                                            @endif

                                            @foreach($project->members as $member)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs flex-shrink-0 me-3">
                                                    @if($member->avatar)
                                                        <img src="{{ asset('storage/' . $member->avatar) }}" alt="" class="img-fluid rounded-circle">
                                                    @else
                                                        <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fs-13 mb-0">{{ $member->name }}</h5>
                                                    <small class="text-muted">{{ $member->pivot->role ?? 'Member' }}</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end card-->

                            <div class="card">
                                <div class="card-header align-items-center d-flex border-bottom-dashed">
                                    <h4 class="card-title mb-0 flex-grow-1">Project Details</h4>
                                </div>
                                <div class="card-body">
                                    <div class="pb-3 border-bottom border-bottom-dashed mb-3">
                                        <div class="mb-2 d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted mb-0">Progress</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="text-muted">{{ $project->progress }}%</span>
                                            </div>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                style="width: {{ $project->progress }}%" 
                                                aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-muted text-uppercase fw-semibold mb-2">Privacy</p>
                                        <span class="badge bg-{{ $project->privacy == 'Public' ? 'success' : ($project->privacy == 'Private' ? 'danger' : 'warning') }}-subtle text-{{ $project->privacy == 'Public' ? 'success' : ($project->privacy == 'Private' ? 'danger' : 'warning') }}">
                                            {{ $project->privacy }}
                                        </span>
                                    </div>
                                    @if($project->start_date)
                                    <div class="mb-3">
                                        <p class="text-muted text-uppercase fw-semibold mb-2">Start Date</p>
                                        <h6 class="fs-14 mb-0">{{ $project->start_date->format('d M, Y') }}</h6>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-muted text-uppercase fw-semibold mb-2">Total Members</p>
                                        <h6 class="fs-14 mb-0">{{ $project->members->count() + ($project->teamLead ? 1 : 0) }}</h6>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header align-items-center d-flex border-bottom-dashed">
                                    <h4 class="card-title mb-0 flex-grow-1">Attachments</h4>
                                </div>

                                <div class="card-body">
                                    @if($project->attachments->count() > 0)
                                    <div class="vstack gap-2">
                                        @foreach($project->attachments as $attachment)
                                        <div class="border rounded border-dashed p-2">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar-sm">
                                                        <div class="avatar-title bg-light text-primary rounded fs-24">
                                                            <i class="{{ 
                                                                str_contains($attachment->file_path, '.pdf') ? 'ri-file-pdf-line' :
                                                                (str_contains($attachment->file_path, '.zip') ? 'ri-folder-zip-line' :
                                                                (str_contains($attachment->file_path, '.doc') ? 'ri-file-word-line' :
                                                                'ri-file-line'))
                                                            }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h5 class="fs-13 mb-1">
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                                           class="text-body text-truncate d-block" 
                                                           download>{{ basename($attachment->file_path) }}</a>
                                                    </h5>
                                                    <div>{{ $attachment->file_size }}</div>
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                                           class="btn btn-icon text-muted btn-sm fs-18" 
                                                           download>
                                                            <i class="ri-download-2-line"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-center text-muted py-4">
                                        <i class="ri-file-line fs-1 mb-2"></i>
                                        <p class="mb-0">No attachments yet</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-documents" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <h5 class="card-title flex-grow-1">Documents</h5>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">File Name</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Size</th>
                                                    <th scope="col">Upload Date</th>
                                                    <th scope="col" style="width: 120px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                $iconMap = [
                                                    'pdf' => ['icon' => 'ri-file-pdf-fill', 'color' => 'danger'],
                                                    'doc' => ['icon' => 'ri-file-word-fill', 'color' => 'primary'],
                                                    'docx' => ['icon' => 'ri-file-word-fill', 'color' => 'primary'],
                                                    'xls' => ['icon' => 'ri-file-excel-fill', 'color' => 'success'],
                                                    'xlsx' => ['icon' => 'ri-file-excel-fill', 'color' => 'success'],
                                                    'ppt' => ['icon' => 'ri-file-ppt-fill', 'color' => 'warning'],
                                                    'pptx' => ['icon' => 'ri-file-ppt-fill', 'color' => 'warning'],
                                                    'zip' => ['icon' => 'ri-folder-zip-line', 'color' => 'primary'],
                                                    'rar' => ['icon' => 'ri-folder-zip-line', 'color' => 'primary'],
                                                    '7z' => ['icon' => 'ri-folder-zip-line', 'color' => 'primary'],
                                                    'jpg' => ['icon' => 'ri-image-2-fill', 'color' => 'danger'],
                                                    'jpeg' => ['icon' => 'ri-image-2-fill', 'color' => 'danger'],
                                                    'png' => ['icon' => 'ri-image-2-fill', 'color' => 'danger'],
                                                    'gif' => ['icon' => 'ri-image-2-fill', 'color' => 'danger'],
                                                    'mp4' => ['icon' => 'ri-video-line', 'color' => 'primary'],
                                                    'avi' => ['icon' => 'ri-video-line', 'color' => 'primary'],
                                                    'mov' => ['icon' => 'ri-video-line', 'color' => 'primary'],
                                                    'txt' => ['icon' => 'ri-file-text-fill', 'color' => 'secondary'],
                                                ];
                                                @endphp
                                                @forelse($project->attachments as $attachment)
                                                @php
                                                $extension = strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION));
                                                $iconData = $iconMap[$extension] ?? ['icon' => 'ri-file-fill', 'color' => 'secondary'];
                                                $fileSize = $attachment->file_size ? number_format($attachment->file_size / 1024, 2) . ' KB' : 'N/A';
                                                if($attachment->file_size > 1024 * 1024) {
                                                    $fileSize = number_format($attachment->file_size / (1024 * 1024), 2) . ' MB';
                                                }
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm">
                                                                <div class="avatar-title bg-light text-{{ $iconData['color'] }} rounded fs-24">
                                                                    <i class="{{ $iconData['icon'] }}"></i>
                                                                </div>
                                                            </div>
                                                            <div class="ms-3 flex-grow-1">
                                                                <h5 class="fs-14 mb-0">
                                                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                                                       download="{{ $attachment->file_name }}" 
                                                                       class="text-body">
                                                                        {{ $attachment->file_name ?? basename($attachment->file_path) }}
                                                                    </a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ strtoupper($extension) }} File</td>
                                                    <td>{{ $fileSize }}</td>
                                                    <td>{{ $attachment->created_at->format('d M Y') }}</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0);" class="btn btn-soft-secondary btn-sm btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                                                                        <i class="ri-eye-fill me-2 align-bottom text-muted"></i>View
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ asset('storage/' . $attachment->file_path) }}" download="{{ $attachment->file_name }}">
                                                                        <i class="ri-download-2-fill me-2 align-bottom text-muted"></i>Download
                                                                    </a>
                                                                </li>
                                                                @can('delete-project-attachments')
                                                                <li class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('management.projects.attachments.destroy', [$project, $attachment]) }}" method="POST" style="display: inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this file?')">
                                                                            <i class="ri-delete-bin-5-fill me-2 align-bottom text-muted"></i>Delete
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5">
                                                        <lord-icon src="https://cdn.lordicon.com/nocovwne.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                                                        <h5 class="mt-2">No documents uploaded yet</h5>
                                                        <p class="text-muted">Upload project documents to share with team members.</p>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-tasks" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <h5 class="card-title flex-grow-1 mb-0">Tasks</h5>
                                @can('create-tasks')
                                <div class="flex-shrink-0">
                                    <a href="{{ route('management.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line align-bottom me-1"></i> Add Task
                                    </a>
                                </div>
                                @endcan
                            </div>
                            
                            @if($project->tasks && $project->tasks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-borderless table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Task</th>
                                            <th scope="col">Assigned To</th>
                                            <th scope="col">Priority</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Due Date</th>
                                            <th scope="col">Progress</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->tasks as $task)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar-sm">
                                                            <div class="avatar-title rounded-circle bg-{{ $task->priority == 'High' ? 'danger' : ($task->priority == 'Low' ? 'success' : 'warning') }}-subtle text-{{ $task->priority == 'High' ? 'danger' : ($task->priority == 'Low' ? 'success' : 'warning') }}">
                                                                <i class="ri-task-line"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="fs-14 mb-1">
                                                            <a href="{{ route('management.tasks.show', $task->id) }}" class="text-body">{{ $task->title }}</a>
                                                        </h5>
                                                        @if($task->description)
                                                        <p class="text-muted mb-0">{{ Str::limit(strip_tags($task->description), 50) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($task->assignedUsers && $task->assignedUsers->count() > 0)
                                                <div class="avatar-group">
                                                    @foreach($task->assignedUsers->take(3) as $user)
                                                    <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $user->name }}">
                                                        @if($user->avatar)
                                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="rounded-circle avatar-xs">
                                                        @else
                                                        <div class="avatar-xs">
                                                            <div class="avatar-title rounded-circle bg-primary">
                                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </a>
                                                    @endforeach
                                                    @if($task->assignedUsers->count() > 3)
                                                    <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $task->assignedUsers->count() - 3 }} more">
                                                        <div class="avatar-xs">
                                                            <div class="avatar-title rounded-circle">
                                                                +{{ $task->assignedUsers->count() - 3 }}
                                                            </div>
                                                        </div>
                                                    </a>
                                                    @endif
                                                </div>
                                                @else
                                                <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority == 'High' ? 'danger' : ($task->priority == 'Low' ? 'success' : 'warning') }}">
                                                    {{ $task->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->status == 'Completed' ? 'success' : ($task->status == 'In Progress' ? 'primary' : 'warning') }}">
                                                    {{ $task->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                <span class="{{ $task->due_date->isPast() && $task->status != 'Completed' ? 'text-danger' : '' }}">
                                                    {{ $task->due_date->format('d M Y') }}
                                                </span>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 me-2">
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-{{ $task->progress == 100 ? 'success' : 'primary' }}" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $task->progress }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <span class="text-muted">{{ $task->progress }}%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('management.tasks.show', $task->id) }}">
                                                                <i class="ri-eye-fill me-2 align-bottom text-muted"></i>View
                                                            </a>
                                                        </li>
                                                        @can('edit-tasks')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('management.tasks.edit', $task->id) }}">
                                                                <i class="ri-pencil-fill me-2 align-bottom text-muted"></i>Edit
                                                            </a>
                                                        </li>
                                                        @endcan
                                                        @can('delete-tasks')
                                                        <li class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('management.tasks.destroy', $task->id) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure?')">
                                                                    <i class="ri-delete-bin-fill me-2 align-bottom text-muted"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                                <h5 class="mt-2">No tasks yet</h5>
                                <p class="text-muted">Create tasks to manage project work and track progress.</p>
                                @can('create-tasks')
                                <a href="{{ route('management.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary mt-2">
                                    <i class="ri-add-line align-bottom me-1"></i> Create Task
                                </a>
                                @endcan
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-activities" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Activities</h5>
                            @if($activities->count() > 0)
                            <div class="acitivity-timeline py-3">
                                @foreach($activities as $activity)
                                <div class="acitivity-item d-flex mb-4">
                                    <div class="flex-shrink-0">
                                        @if($activity->causer && $activity->causer->avatar)
                                            <img src="{{ asset('storage/' . $activity->causer->avatar) }}" alt=""
                                                class="avatar-xs rounded-circle acitivity-avatar" />
                                        @elseif($activity->causer)
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-primary">
                                                    {{ strtoupper(substr($activity->causer->name, 0, 2)) }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-secondary">
                                                    <i class="ri-user-line"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">
                                            {{ $activity->causer ? $activity->causer->name : 'System' }}
                                            @if($activity->created_at->diffInHours() < 24)
                                                <span class="badge bg-primary-subtle text-primary align-middle">New</span>
                                            @endif
                                        </h6>
                                        <p class="text-muted mb-2">
                                            @php
                                            $subjectType = class_basename($activity->subject_type);
                                            $description = $activity->description;
                                            @endphp
                                            
                                            @if($subjectType == 'Project')
                                                @if($description == 'created')
                                                    Created the project
                                                @elseif($description == 'updated')
                                                    Updated project details
                                                    @if($activity->properties && $activity->properties->has('attributes'))
                                                        @php
                                                        $attributes = $activity->properties->get('attributes');
                                                        $old = $activity->properties->get('old', []);
                                                        $changes = [];
                                                        if(isset($attributes['status']) && isset($old['status'])) 
                                                            $changes[] = "status from {$old['status']} to {$attributes['status']}";
                                                        if(isset($attributes['priority']) && isset($old['priority'])) 
                                                            $changes[] = "priority from {$old['priority']} to {$attributes['priority']}";
                                                        if(isset($attributes['progress']) && isset($old['progress'])) 
                                                            $changes[] = "progress from {$old['progress']}% to {$attributes['progress']}%";
                                                        @endphp
                                                        @if(count($changes) > 0)
                                                            ({{ implode(', ', $changes) }})
                                                        @endif
                                                    @endif
                                                @elseif($description == 'deleted')
                                                    Deleted the project
                                                @else
                                                    {{ ucfirst($description) }} the project
                                                @endif
                                            
                                            @elseif($subjectType == 'Task')
                                                @if($description == 'created')
                                                    Created a new task
                                                    @if($activity->subject)
                                                        <strong>"{{ $activity->subject->title }}"</strong>
                                                    @endif
                                                @elseif($description == 'updated')
                                                    Updated task
                                                    @if($activity->subject)
                                                        <strong>"{{ $activity->subject->title }}"</strong>
                                                    @endif
                                                @elseif($description == 'deleted')
                                                    Deleted a task
                                                @else
                                                    {{ ucfirst($description) }} a task
                                                @endif
                                            
                                            @elseif($subjectType == 'ProjectComment')
                                                @if($description == 'created')
                                                    Added a comment
                                                @elseif($description == 'updated')
                                                    Updated a comment
                                                @elseif($description == 'deleted')
                                                    Deleted a comment
                                                @else
                                                    {{ ucfirst($description) }} a comment
                                                @endif
                                            
                                            @elseif($subjectType == 'ProjectAttachment')
                                                @if($description == 'created')
                                                    Uploaded a file
                                                    @if($activity->subject)
                                                        <strong>"{{ $activity->subject->file_name }}"</strong>
                                                    @endif
                                                @elseif($description == 'deleted')
                                                    Deleted a file
                                                @else
                                                    {{ ucfirst($description) }} a file
                                                @endif
                                            
                                            @else
                                                {{ ucfirst($description) }} {{ strtolower($subjectType) }}
                                            @endif
                                        </p>
                                        <small class="mb-0 text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="ri-history-line fs-1 text-muted mb-3"></i>
                                <p class="text-muted mb-0">No activities recorded yet</p>
                            </div>
                            @endif
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-team" role="tabpanel">
                    <div class="row g-4 mb-3">
                        <div class="col-sm">
                            <div class="d-flex">
                                <div class="search-box me-2">
                                    <input type="text" class="form-control" placeholder="Search member...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <div>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#inviteMembersModal"><i class="ri-share-line me-1 align-bottom"></i>
                                    Invite Member</button>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                    <div class="team-list list-view-filter">
                        {{-- Team Lead Card --}}
                        @if($project->teamLead)
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <span class="badge badge-soft-warning">Team Lead</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                @if($project->teamLead->avatar)
                                                    <img src="{{ asset('storage/' . $project->teamLead->avatar) }}" alt="" class="img-fluid d-block rounded-circle" />
                                                @else
                                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                        {{ strtoupper(substr($project->teamLead->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="team-content">
                                                <a href="{{ route('management.users.show', $project->teamLead) }}" class="d-block">
                                                    <h5 class="fs-16 mb-1">{{ $project->teamLead->name }}</h5>
                                                </a>
                                                <p class="text-muted mb-0">{{ $project->teamLead->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">{{ $project->teamLead->ledProjects()->count() }}</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">{{ $project->teamLead->assignedTasks()->count() }}</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="{{ route('management.users.show', $project->teamLead) }}" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!--end card-->

                        {{-- Team Members Cards --}}
                        @forelse($project->members as $member)
                        <div class="card team-box">
                            <div class="card-body px-4">
                                <div class="row align-items-center team-row">
                                    <div class="col team-settings">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                @if($member->pivot->role ?? null)
                                                <span class="badge badge-soft-info">{{ ucfirst($member->pivot->role) }}</span>
                                                @endif
                                            </div>
                                            @can('manage-project-members')
                                            <div class="col text-end dropdown">
                                                <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill fs-17"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('management.users.show', $member) }}">
                                                            <i class="ri-eye-fill text-muted me-2 align-bottom"></i>View Profile
                                                        </a>
                                                    </li>
                                                    <li class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('management.projects.members.destroy', [$project, $member]) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to remove this member?')">
                                                                <i class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Remove
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="team-profile-img">
                                            <div class="avatar-lg img-thumbnail rounded-circle">
                                                @if($member->avatar)
                                                    <img src="{{ asset('storage/' . $member->avatar) }}" alt="" class="img-fluid d-block rounded-circle" />
                                                @else
                                                    @php
                                                    $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                                    $colorIndex = ord(strtoupper($member->name[0])) % count($colors);
                                                    @endphp
                                                    <div class="avatar-title bg-{{ $colors[$colorIndex] }}-subtle text-{{ $colors[$colorIndex] }} rounded-circle">
                                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="team-content">
                                                <a href="{{ route('management.users.show', $member) }}" class="d-block">
                                                    <h5 class="fs-16 mb-1">{{ $member->name }}</h5>
                                                </a>
                                                <p class="text-muted mb-0">{{ $member->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col">
                                        <div class="row text-muted text-center">
                                            <div class="col-6 border-end border-end-dashed">
                                                <h5 class="mb-1">{{ $member->projects()->count() }}</h5>
                                                <p class="text-muted mb-0">Projects</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-1">{{ $member->assignedTasks()->count() }}</h5>
                                                <p class="text-muted mb-0">Tasks</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col">
                                        <div class="text-end">
                                            <a href="{{ route('management.users.show', $member) }}" class="btn btn-light view-btn">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        @if(!$project->teamLead)
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <lord-icon src="https://cdn.lordicon.com/eszyyflr.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                                <h5 class="mt-2">No team members yet</h5>
                                <p class="text-muted">Add team members to start collaboration on this project.</p>
                            </div>
                        </div>
                        @endif
                        @endforelse
                        <!--end card-->
                    </div>
                    <!-- end team list -->

                    <div class="row g-0 text-center text-sm-start align-items-center mb-3">
                    </div><!-- end row -->
                </div>
                <!-- end tab pane -->
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
    <!-- Modal -->
    <div class="modal fade" id="inviteMembersModal" tabindex="-1" aria-labelledby="inviteMembersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header p-3 ps-4 bg-success-subtle">
                    <h5 class="modal-title" id="inviteMembersModalLabel">Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="search-box mb-3">
                        <input type="text" class="form-control bg-light border-light" placeholder="Search here...">
                        <i class="ri-search-line search-icon"></i>
                    </div>

                    <div class="mb-4 d-flex align-items-center">
                        <div class="me-2">
                            <h5 class="mb-0 fs-13">Members :</h5>
                        </div>
                        <div class="avatar-group justify-content-center">
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-placement="top" title="Brent Gonzalez">
                                <div class="avatar-xs">
                                    <img src="{{ URL::asset('build/images/users/avatar-3.jpg') }}" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-placement="top" title="Sylvia Wright">
                                <div class="avatar-xs">
                                    <div class="avatar-title rounded-circle bg-secondary">
                                        S
                                    </div>
                                </div>
                            </a>
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-placement="top" title="Ellen Smith">
                                <div class="avatar-xs">
                                    <img src="{{ URL::asset('build/images/users/avatar-4.jpg') }}" alt="" class="rounded-circle img-fluid">
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="mx-n4 px-4" data-simplebar style="max-height: 225px;">
                        <div class="vstack gap-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}" alt="" class="img-fluid rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Nancy Martino</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <div class="avatar-title bg-danger-subtle text-danger rounded-circle">
                                        HB
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Henry Baird</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <img src="{{ URL::asset('build/images/users/avatar-3.jpg') }}" alt="" class="img-fluid rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Frank Hook</a></h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <img src="{{ URL::asset('build/images/users/avatar-4.jpg') }}" alt="" class="img-fluid rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Jennifer Carter</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                        AC
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Alexis Clarke</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0 me-3">
                                    <img src="{{ URL::asset('build/images/users/avatar-7.jpg') }}" alt="" class="img-fluid rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">Joseph Parker</a>
                                    </h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-light btn-sm">Add</button>
                                </div>
                            </div>
                            <!-- end member item -->
                        </div>
                        <!-- end list -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light w-xs" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success w-xs">Invite</button>
                </div>
            </div>
            <!-- end modal-content -->
        </div>
        <!-- modal-dialog -->
    </div>
    <!-- end modal -->
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/pages/project-overview.init.js') }}"></script>
    <script>
        // Favorite toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const favoriteBtn = document.querySelector('.favourite-btn');
            
            if (favoriteBtn) {
                favoriteBtn.addEventListener('click', function() {
                    const projectId = this.dataset.projectId;
                    
                    fetch(`/management/projects/${projectId}/toggle-favorite`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.toggle('active');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            }
        });
    </script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
