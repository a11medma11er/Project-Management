@extends('layouts.master')
@section('title')
    {{ isset($project) ? 'Edit Project' : 'Create Project' }}
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Project
        @endslot
        @slot('title')
            {{ isset($project) ? 'Edit Project' : 'Create Project' }}
        @endslot
    @endcomponent
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ isset($project) ? route('management.projects.update', $project->id) : route('management.projects.store') }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($project))
            @method('PUT')
        @endif
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="project-title-input">Project Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="project-title-input" name="title" 
                               value="{{ old('title', $project->title ?? '') }}" 
                               placeholder="Enter project title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <label class="form-label" for="project-thumbnail-img">Thumbnail Image</label>
                        @if(isset($project) && $project->thumbnail)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="Current thumbnail" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted mt-1">Current thumbnail (upload new to replace)</p>
                            </div>
                        @endif
                        <input class="form-control @error('thumbnail') is-invalid @enderror" 
                               id="project-thumbnail-img" name="thumbnail" type="file"
                               accept="image/png, image/gif, image/jpeg, image/jpg, image/webp">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Project Description</label>
                        <textarea id="ckeditor-classic" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3 mb-lg-0">
                                <label for="choices-priority-input" class="form-label">Priority</label>
                                <select class="form-select @error('priority') is-invalid @enderror" name="priority" data-choices data-choices-search-false
                                    id="choices-priority-input">
                                    <option value="High" {{ old('priority', $project->priority ?? 'Medium') == 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Medium" {{ old('priority', $project->priority ?? 'Medium') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Low" {{ old('priority', $project->priority ?? 'Medium') == 'Low' ? 'selected' : '' }}>Low</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3 mb-lg-0">
                                <label for="choices-status-input" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" data-choices data-choices-search-false
                                    id="choices-status-input">
                                    <option value="Inprogress" {{ old('status', $project->status ?? 'Inprogress') == 'Inprogress' ? 'selected' : '' }}>Inprogress</option>
                                    <option value="Completed" {{ old('status', $project->status ?? 'Inprogress') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="On Hold" {{ old('status', $project->status ?? 'Inprogress') == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div>
                                <label for="datepicker-deadline-input" class="form-label">Deadline</label>
                                <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                       id="datepicker-deadline-input" name="deadline"
                                       value="{{ old('deadline', isset($project) ? $project->deadline->format('Y-m-d') : '') }}" 
                                       required>
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attached files</h5>
                </div>
                <div class="card-body">
                    <div>
                        <p class="text-muted">Add Attached files here.</p>

                        <div class="mb-3">
                            <input type="file" 
                                   name="attachments[]" 
                                   class="form-control" 
                                   id="attachments-input"
                                   multiple 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar">
                            <div class="form-text">You can select multiple files (Max 10MB per file)</div>
                        </div>

                        <!-- Preview selected files -->
                        <div id="files-preview" class="mt-3"></div>

                        @if(isset($project) && $project->attachments->count() > 0)
                        <h6 class="mt-4 mb-3">Existing Attachments:</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($project->attachments as $attachment)
                            <li class="mt-2">
                                <div class="border rounded">
                                    <div class="d-flex p-2">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm bg-light rounded">
                                                <div class="avatar-title bg-primary-subtle text-primary rounded fs-24">
                                                    <i class="ri-file-text-fill"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="pt-1">
                                                <h5 class="fs-14 mb-1">{{ $attachment->file_name }}</h5>
                                                <p class="fs-13 text-muted mb-0">{{ $attachment->file_size_human }}</p>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-3">
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                               download="{{ $attachment->file_name }}" 
                                               class="btn btn-sm btn-success me-1">
                                                <i class="ri-download-2-line"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            <!-- end card -->
            <div class="text-end mb-4">
                <a href="{{ route('management.projects.index') }}" class="btn btn-secondary w-sm me-1">Cancel</a>
                <button type="submit" class="btn btn-primary w-sm">
                    {{ isset($project) ? 'Update' : 'Create' }} Project
                </button>
            </div>
        </div>
        <!-- end col -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Privacy</h5>
                </div>
                <div class="card-body">
                    <div>
                        <label for="choices-privacy-status-input" class="form-label">Status</label>
                        <select class="form-select @error('privacy') is-invalid @enderror" name="privacy" data-choices data-choices-search-false
                            id="choices-privacy-status-input">
                            <option value="Private" {{ old('privacy', $project->privacy ?? 'Team') == 'Private' ? 'selected' : '' }}>Private</option>
                            <option value="Team" {{ old('privacy', $project->privacy ?? 'Team') == 'Team' ? 'selected' : '' }}>Team</option>
                            <option value="Public" {{ old('privacy', $project->privacy ?? 'Team') == 'Public' ? 'selected' : '' }}>Public</option>
                        </select>
                        @error('privacy')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="choices-categories-input" class="form-label">Category</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" 
                               id="choices-categories-input" name="category"
                               value="{{ old('category', $project->category ?? '') }}" 
                               placeholder="e.g., Designing, Development">
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="choices-text-input" class="form-label">Skills</label>
                        <input class  ="form-control @error('skills') is-invalid @enderror" id="choices-text-input" 
                               name="skills" placeholder="Enter Skills (comma separated)" type="text"
                               value="{{ old('skills', isset($project) && is_array($project->skills) ? implode(', ', $project->skills) : '') }}" />
                        <small class="text-muted">Separate skills with commas</small>
                        @error('skills')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Members</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="choices-lead-input" class="form-label">Team Lead</label>
                        <select class="form-select @error('team_lead_id') is-invalid @enderror" name="team_lead_id" data-choices data-choices-search-false id="choices-lead-input">
                            <option value="">No Team Lead</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('team_lead_id', $project->team_lead_id ?? '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('team_lead_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div>
                        <label class="form-label">Team Members</label>
                        <div class="avatar-group" id="selected-members-display">
                            @if(isset($project) && $project->members->count() > 0)
                                @foreach($project->members as $member)
                                    <a href="javascript: void(0);" class="avatar-group-item member-avatar" 
                                       data-member-id="{{ $member->id }}" data-bs-toggle="tooltip"
                                       data-bs-trigger="hover" data-bs-placement="top" title="{{ $member->name }}">
                                        <div class="avatar-xs">
                                            @if($member->avatar)
                                                <img src="{{ asset('storage/' . $member->avatar) }}" alt="" class="rounded-circle img-fluid">
                                            @else
                                                <div class="avatar-title rounded-circle bg-primary">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                            <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip"
                                data-bs-trigger="hover" data-bs-placement="top" title="Add Members">
                                <div class="avatar-xs" data-bs-toggle="modal" data-bs-target="#inviteMembersModal">
                                    <div class="avatar-title fs-16 rounded-circle bg-light border-dashed border text-primary">
                                        +
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div id="selected-members-inputs">
                            @if(isset($project) && $project->members->count() > 0)
                                @foreach($project->members as $member)
                                    <input type="checkbox" name="members[]" value="{{ $member->id }}" checked style="display: none;">
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
    </form>
    <!-- end form -->



    <!-- Modal -->
    <div class="modal fade" id="inviteMembersModal" tabindex="-1" aria-labelledby="inviteMembersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-3 ps-4 bg-success-subtle">
                    <h5 class="modal-title" id="inviteMembersModalLabel">Add Team Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="search-box mb-3">
                        <input type="text" class="form-control bg-light border-light" id="member-search" placeholder="Search members...">
                        <i class="ri-search-line search-icon"></i>
                    </div>

                    <div class="mb-4 d-flex align-items-center">
                        <div class="me-2">
                            <h5 class="mb-0 fs-13">Selected Members:</h5>
                        </div>
                        <div class="avatar-group justify-content-center" id="modal-selected-members">
                            <span class="text-muted" id="no-members-text">No members selected</span>
                        </div>
                    </div>
                    <div class="mx-n4 px-4" data-simplebar style="max-height: 225px;">
                        <div class="vstack gap-3">
                            @foreach($users as $user)
                                <div class="d-flex align-items-center member-item" data-user-name="{{ $user->name }}">
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
                                        <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">{{ $user->name }}</a></h5>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button type="button" class="btn btn-light btn-sm add-member-btn" 
                                            data-user-id="{{ $user->id }}" 
                                            data-user-name="{{ $user->name }}"
                                            data-user-avatar="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}">Add</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
    <script src="{{ URL::asset('build/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <script>
        // CKEditor initialization
        ClassicEditor
            .create(document.querySelector('#ckeditor-classic'))
            .catch(error => {
                console.error(error);
            });

        // File attachments preview
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('attachments-input');
            const filesPreview = document.getElementById('files-preview');

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    filesPreview.innerHTML = '';
                    const files = e.target.files;

                    if (files.length > 0) {
                        filesPreview.innerHTML = '<h6 class="mb-3">Selected Files:</h6>';
                        
                        Array.from(files).forEach((file, index) => {
                            const fileSize = (file.size / 1024).toFixed(2); // KB
                            const fileSizeDisplay = fileSize > 1024 ? 
                                (fileSize / 1024).toFixed(2) + ' MB' : 
                                fileSize + ' KB';

                            const fileItem = document.createElement('div');
                            fileItem.className = 'border rounded p-2 mb-2';
                            fileItem.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm bg-light rounded">
                                            <div class="avatar-title bg-secondary-subtle text-secondary rounded">
                                                <i class="ri-file-text-line fs-20"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${file.name}</h6>
                                        <p class="text-muted mb-0 fs-12">${fileSizeDisplay}</p>
                                    </div>
                                </div>
                            `;
                            filesPreview.appendChild(fileItem);
                        });
                    }
                });
            }
        });

        // Team Members Management
        document.addEventListener('DOMContentLoaded', function() {
            const selectedMembers = new Set();
            const membersDisplay = document.getElementById('selected-members-display');
            const membersInputs = document.getElementById('selected-members-inputs');
            const modalSelectedMembers = document.getElementById('modal-selected-members');
            const noMembersText = document.getElementById('no-members-text');
            const memberSearch = document.getElementById('member-search');

            // Load existing members
            @if(isset($project) && $project->members->count() > 0)
                @foreach($project->members as $member)
                    selectedMembers.add({{ $member->id }});
                @endforeach
                updateModalDisplay();
            @endif

            // Add member button click
            document.querySelectorAll('.add-member-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = parseInt(this.dataset.userId);
                    const userName = this.dataset.userName;
                    const userAvatar = this.dataset.userAvatar;

                    if (selectedMembers.has(userId)) {
                        selectedMembers.delete(userId);
                        this.textContent = 'Add';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-light');
                    } else {
                        selectedMembers.add(userId);
                        this.textContent = 'Remove';
                        this.classList.remove('btn-light');
                        this.classList.add('btn-success');
                    }

                    updateMembersDisplay();
                    updateModalDisplay();
                });
            });

            // Search members
            if (memberSearch) {
                memberSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    document.querySelectorAll('.member-item').forEach(item => {
                        const name = item.dataset.userName.toLowerCase();
                        item.style.display = name.includes(searchTerm) ? '' : 'none';
                    });
                });
            }

            function updateMembersDisplay() {
                // Update main display
                const addButton = membersDisplay.querySelector('[data-bs-target="#inviteMembersModal"]').parentElement;
                membersDisplay.innerHTML = '';
                membersDisplay.appendChild(addButton);

                // Update hidden inputs
                membersInputs.innerHTML = '';

                selectedMembers.forEach(userId => {
                    const memberItem = document.querySelector(`.add-member-btn[data-user-id="${userId}"]`);
                    if (memberItem) {
                        const userName = memberItem.dataset.userName;
                        const userAvatar = memberItem.dataset.userAvatar;
                        
                        // Add avatar to display
                        const avatar = document.createElement('a');
                        avatar.href = 'javascript: void(0);';
                        avatar.className = 'avatar-group-item member-avatar';
                        avatar.dataset.memberId = userId;
                        avatar.dataset.bsToggle = 'tooltip';
                        avatar.title = userName;
                        avatar.innerHTML = `
                            <div class="avatar-xs">
                                ${userAvatar ? 
                                    `<img src="${userAvatar}" alt="" class="rounded-circle img-fluid">` :
                                    `<div class="avatar-title rounded-circle bg-primary">${userName.charAt(0).toUpperCase()}</div>`
                                }
                            </div>
                        `;
                        membersDisplay.insertBefore(avatar, addButton);

                        // Add hidden input
                        const input = document.createElement('input');
                        input.type = 'checkbox';
                        input.name = 'members[]';
                        input.value = userId;
                        input.checked = true;
                        input.style.display = 'none';
                        membersInputs.appendChild(input);
                    }
                });
            }

            function updateModalDisplay() {
                modalSelectedMembers.innerHTML = '';
                
                if (selectedMembers.size === 0) {
                    modalSelectedMembers.appendChild(noMembersText);
                } else {
                    selectedMembers.forEach(userId => {
                        const memberItem = document.querySelector(`.add-member-btn[data-user-id="${userId}"]`);
                        if (memberItem) {
                            const userName = memberItem.dataset.userName;
                            const userAvatar = memberItem.dataset.userAvatar;
                            
                            const avatar = document.createElement('a');
                            avatar.href = 'javascript: void(0);';
                            avatar.className = 'avatar-group-item';
                            avatar.innerHTML = `
                                <div class="avatar-xs">
                                    ${userAvatar ? 
                                        `<img src="${userAvatar}" class="rounded-circle img-fluid">` :
                                        `<div class="avatar-title rounded-circle bg-secondary">${userName.charAt(0).toUpperCase()}</div>`
                                    }
                                </div>
                            `;
                            modalSelectedMembers.appendChild(avatar);
                        }
                    });
                }
            }

            // Update button states on modal open
            document.getElementById('inviteMembersModal').addEventListener('shown.bs.modal', function() {
                document.querySelectorAll('.add-member-btn').forEach(btn => {
                    const userId = parseInt(btn.dataset.userId);
                    if (selectedMembers.has(userId)) {
                        btn.textContent = 'Remove';
                        btn.classList.remove('btn-light');
                        btn.classList.add('btn-success');
                    } else {
                        btn.textContent = 'Add';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-light');
                    }
                });
            });
        });
    </script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
