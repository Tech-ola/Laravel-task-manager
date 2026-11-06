<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Task Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .project-group { margin-bottom: 2rem; }
        .project-group h3 { margin: 0 0 .6rem; color: #374151; }
        .task-row { display: flex; align-items: center; gap: .75rem; padding: .6rem .8rem;
                    background: #f9fafb; border: 1px solid #e5e7eb; border-radius: .5rem;
                    margin-bottom: .5rem; }
        .drag-handle { cursor: grab; color: #9ca3af; user-select: none; font-size: 1.1rem; }
        .drag-handle:active { cursor: grabbing; }
        .priority-badge { background: #2563eb; color: #fff; border-radius: 999px;
                          padding: .15rem .55rem; font-size: .75rem; min-width: 2rem;
                          text-align: center; }
        .actions { margin-left: auto; display: flex; gap: .5rem; }
        .btn { text-decoration: none; padding: .3rem .7rem; border-radius: .375rem;
               font-size: .85rem; border: 1px solid #d1d5db; background: #fff;
               cursor: pointer; color: #111827; }
        .btn-danger { border-color: #fca5a5; color: #b91c1c; }
        .btn-primary { background: #2563eb; color: #fff; border-color: #2563eb; }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; padding: .75rem;
                         border-radius: .5rem; margin-bottom: 1rem; color: #065f46; }
        .project-filter { margin-bottom: 1.25rem; display: flex; gap: .75rem; align-items: center;
                          flex-wrap: wrap; }
        select { padding: .45rem .6rem; border-radius: .375rem; border: 1px solid #d1d5db; }
        .sortable-ghost { opacity: .4; }
        .empty { color: #6b7280; font-style: italic; }
    </style>
</head>
<body>

<h1>Task Manager</h1>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<form method="GET"
      action="{{ route(\App\Http\Controllers\TaskController::ROUTE_INDEX) }}"
      class="project-filter">
    <label for="project_id"><strong>Filter by project:</strong></label>
    <select name="project_id" id="project_id" onchange="this.form.submit()">
        <option value="">All projects</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}" @selected($selectedProjectId == $project->id)>
                {{ $project->name }}
            </option>
        @endforeach
    </select>
    <button type="button" class="btn"
            onclick="openProjectModal('project_id')">+ New Project</button>
    <a class="btn btn-primary"
       href="{{ route(\App\Http\Controllers\TaskController::ROUTE_CREATE) }}">+ New Task</a>
</form>

@forelse($taskGroups as $group)
    <div class="project-group">
        <h3>{{ $group['project']->name }}</h3>

        <div class="task-list" data-project-id="{{ $group['project']->id }}">
            @foreach($group['tasks'] as $task)
                <div class="task-row" data-id="{{ $task->id }}">
                    <span class="drag-handle" title="Drag to reorder">☰</span>
                    <span class="priority-badge">#{{ $task->priority }}</span>
                    <span>{{ $task->name }}</span>

                    <div class="actions">
                        <a class="btn"
                           href="{{ route(\App\Http\Controllers\TaskController::ROUTE_EDIT, $task) }}">
                            Edit
                        </a>
                        <form method="POST"
                              action="{{ route(\App\Http\Controllers\TaskController::ROUTE_DESTROY, $task) }}"
                              onsubmit="return confirm('Delete this task?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <p class="empty">No tasks yet. Click “+ New Task” to create one.</p>
@endforelse

<script>
    const csrfToken  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const reorderUrl = '{{ route(\App\Http\Controllers\TaskController::ROUTE_REORDER) }}';

    document.querySelectorAll('.task-list').forEach(function (listEl) {
        const projectId = listEl.dataset.projectId;
        if (!projectId) return;

        new Sortable(listEl, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: async function () {
                const orderedIds = Array.from(listEl.querySelectorAll('.task-row'))
                    .map(row => parseInt(row.dataset.id, 10));

                try {
                    const response = await fetch(reorderUrl, {
                        method: 'PATCH',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            ordered_ids: orderedIds,
                            project_id: parseInt(projectId, 10),
                        }),
                    });

                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        alert(err.message || 'Could not save the new order.');
                        return;
                    }

                    const payload = await response.json();
                    payload.data.forEach(function (task) {
                        const row = listEl.querySelector(`.task-row[data-id="${task.id}"]`);
                        if (row) {
                            row.querySelector('.priority-badge').textContent = `#${task.priority}`;
                        }
                    });
                } catch (e) {
                    console.error(e);
                    alert('Network error while saving the new order.');
                }
            },
        });
    });
</script>

@include('partials.project-modal')

</body>
</html>