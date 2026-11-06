<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    public const ROUTE_INDEX   = 'tasks.index';
    public const ROUTE_CREATE  = 'tasks.create';
    public const ROUTE_STORE   = 'tasks.store';
    public const ROUTE_EDIT    = 'tasks.edit';
    public const ROUTE_UPDATE  = 'tasks.update';
    public const ROUTE_DESTROY = 'tasks.destroy';
    public const ROUTE_REORDER = 'tasks.reorder';

    public const MESSAGE_CREATED   = 'Task created successfully.';
    public const MESSAGE_UPDATED   = 'Task updated successfully.';
    public const MESSAGE_DELETED   = 'Task deleted successfully.';
    public const MESSAGE_REORDERED = 'Tasks reordered successfully.';

    public const VIEW_INDEX  = 'tasks.index';
    public const VIEW_CREATE = 'tasks.create';
    public const VIEW_EDIT   = 'tasks.edit';

    public function index(Request $request): View
    {
        $projects          = Project::orderBy(Project::COLUMN_NAME)->get();
        $selectedProjectId = $request->query('project_id');

        $taskGroups = []; 

        if ($selectedProjectId) {
            $project = Project::findOrFail($selectedProjectId);

            $taskGroups[] = [
                'project' => $project,
                'tasks'   => Task::where(Task::COLUMN_PROJECT_ID, $project->id)
                    ->orderBy(Task::COLUMN_PRIORITY)
                    ->orderBy(Task::COLUMN_ID)
                    ->get(),
            ];
        } else {
            $tasksByProject = Task::with('project')
                ->orderBy(Task::COLUMN_PRIORITY)
                ->orderBy(Task::COLUMN_ID)
                ->get()
                ->groupBy(Task::COLUMN_PROJECT_ID);

            foreach ($tasksByProject as $projectTasks) {
                $taskGroups[] = [
                    'project' => $projectTasks->first()->project,
                    'tasks'   => $projectTasks,
                ];
            }
        }

        return view(self::VIEW_INDEX, compact('projects', 'taskGroups', 'selectedProjectId'));
    }

    public function create(): View
    {
        $projects = Project::orderBy(Project::COLUMN_NAME)->get();

        return view(self::VIEW_CREATE, compact('projects'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Task::create([
            Task::COLUMN_NAME       => $validated[Task::COLUMN_NAME],
            Task::COLUMN_PROJECT_ID => $validated[Task::COLUMN_PROJECT_ID],
            Task::COLUMN_PRIORITY   => $validated[Task::COLUMN_PRIORITY] ?? Task::DEFAULT_PRIORITY,
        ]);

        return redirect()
            ->route(self::ROUTE_INDEX)
            ->with('success', self::MESSAGE_CREATED);
    }

    public function edit(Task $task): View
    {
        $projects = Project::orderBy(Project::COLUMN_NAME)->get();

        return view(self::VIEW_EDIT, compact('task', 'projects'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()
            ->route(self::ROUTE_INDEX)
            ->with('success', self::MESSAGE_UPDATED);
    }

    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->{Task::COLUMN_PROJECT_ID};
        $task->delete();

        $this->normalizePriorities($projectId);

        return redirect()
            ->route(self::ROUTE_INDEX)
            ->with('success', self::MESSAGE_DELETED);
    }

    public function reorder(ReorderTasksRequest $request): JsonResponse
    {
        $validated  = $request->validated();
        $orderedIds = $validated['ordered_ids'];
        $projectId  = (int) $validated['project_id'];

        DB::transaction(function () use ($orderedIds, $projectId) {
            foreach ($orderedIds as $index => $taskId) {
                Task::where(Task::COLUMN_ID, $taskId)
                    ->where(Task::COLUMN_PROJECT_ID, $projectId)
                    ->update([Task::COLUMN_PRIORITY => $index + 1]);
            }
        });

        $tasks = Task::where(Task::COLUMN_PROJECT_ID, $projectId)
            ->orderBy(Task::COLUMN_PRIORITY)
            ->get();

        return response()->json([
            'message' => self::MESSAGE_REORDERED,
            'data'    => TaskResource::collection($tasks)->resolve(),
        ]);
    }

    private function normalizePriorities(int $projectId): void
    {
        $tasks = Task::where(Task::COLUMN_PROJECT_ID, $projectId)
            ->orderBy(Task::COLUMN_PRIORITY)
            ->orderBy(Task::COLUMN_ID)
            ->get();

        foreach ($tasks as $index => $task) {
            $task->update([Task::COLUMN_PRIORITY => $index + 1]);
        }
    }
}