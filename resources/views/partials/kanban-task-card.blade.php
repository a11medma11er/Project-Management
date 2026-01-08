<div class="card tasks-box" data-task-id="{{ $task->id }}">
    <div class="card-body">
        <div class="d-flex mb-2">
            <div class="flex-grow-1" style="min-width: 0;">
                <h6 class="fs-15 mb-0 text-truncate task-title">
                    <a href="{{ route('management.tasks.show', $task->id) }}" class="d-block text-truncate">{{ $task->title }}</a>
                </h6>
            </div>
            <div class="dropdown">
                <a href="javascript:void(0);" class="text-muted" id="dropdownMenuLink{{ $task->id }}" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill"></i></a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink{{ $task->id }}">
                    <li><a class="dropdown-item" href="{{ route('management.tasks.show', $task->id) }}"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                    <li><a class="dropdown-item" href="{{ route('management.tasks.edit', $task->id) }}"><i class="ri-edit-2-line align-bottom me-2 text-muted"></i> Edit</a></li>
                </ul>
            </div>
        </div>
        <p class="text-muted text-truncate mb-0">{{ Str::limit($task->description, 60) }}</p>
        <div class="mb-3">
            <div class="d-flex mb-1">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-0"><span class="text-secondary">{{ $task->progress ?? 0 }}%</span> of 100%</h6>
                </div>
                <div class="flex-shrink-0">
                    <span class="text-muted">{{ $task->created_at->format('d M, Y') }}</span>
                </div>
            </div>
            <div class="progress rounded-3 progress-sm">
                <div class="progress-bar bg-{{ $task->priority == 'High' ? 'danger' : ($task->priority == 'Medium' ? 'warning' : 'success') }}" role="progressbar" style="width: {{ $task->progress ?? 0 }}%" aria-valuenow="{{ $task->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                <span class="badge bg-primary-subtle text-primary">{{ $task->priority ?? 'Low' }}</span>
            </div>
            <div class="flex-shrink-0">
                <div class="avatar-group">
                    @foreach($task->assignedUsers as $user)
                        <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{ $user->name }}">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="rounded-circle avatar-xxs">
                            @else
                                <div class="avatar-xxs">
                                    <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer border-top-dashed">
        <div class="d-flex">
            <div class="flex-grow-1" style="min-width: 0;">
                <h6 class="text-muted mb-0 text-truncate">#{{ $task->id }}</h6>
            </div>
            <div class="flex-shrink-0">
                <ul class="link-inline mb-0">
                    <li class="list-inline-item">
                        <a href="javascript:void(0)" class="text-muted"><i class="ri-eye-line align-bottom"></i> {{ rand(5, 50) }}</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="javascript:void(0)" class="text-muted"><i class="ri-question-answer-line align-bottom"></i> {{ $task->comments->count() }}</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="javascript:void(0)" class="text-muted"><i class="ri-attachment-2 align-bottom"></i> {{ $task->attachments->count() }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
