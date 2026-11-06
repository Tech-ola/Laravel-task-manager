<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Task</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input, select { width: 100%; padding: .5rem; margin-top: .25rem; border: 1px solid #d1d5db;
                        border-radius: .375rem; box-sizing: border-box; }
        .error { color: #b91c1c; font-size: .85rem; margin-top: .25rem; }
        .btn { margin-top: 1.25rem; padding: .55rem 1.1rem; border-radius: .375rem;
               border: none; background: #2563eb; color: #fff; cursor: pointer; }
        .btn-secondary { background: #fff; color: #111827; border: 1px solid #d1d5db;
                         margin-top: 0; padding: .5rem .8rem; }
        .input-row { display: flex; gap: .5rem; align-items: flex-start; }
        .input-row select { flex: 1; }
    </style>
</head>
<body>

<h1>Create Task</h1>

<form method="POST" action="{{ route(\App\Http\Controllers\TaskController::ROUTE_STORE) }}">
    @csrf

    <label for="name">Task name</label>
    <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255">
    @error('name') <div class="error">{{ $message }}</div> @enderror

    <label for="project_id">Project</label>
    <div class="input-row">
        <select name="project_id" id="project_id" required>
            <option value="">— Select a project —</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
        <button type="button" class="btn btn-secondary"
                onclick="openProjectModal('project_id')">+ New Project</button>
    </div>
    @error('project_id') <div class="error">{{ $message }}</div> @enderror

    <label for="priority">Priority (optional)</label>
    <input type="number" name="priority" id="priority"
           value="{{ old('priority', \App\Models\Task::DEFAULT_PRIORITY) }}"
           min="{{ \App\Models\Task::PRIORITY_MIN }}" max="{{ \App\Models\Task::PRIORITY_MAX }}">
    @error('priority') <div class="error">{{ $message }}</div> @enderror

    <button type="submit" class="btn">Save Task</button>
</form>

<p><a href="{{ route(\App\Http\Controllers\TaskController::ROUTE_INDEX) }}">← Back to list</a></p>

@include('partials.project-modal')

</body>
</html>