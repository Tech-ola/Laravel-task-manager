<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public const ROUTE_STORE = 'projects.store';

    public const MESSAGE_CREATED = 'Project created successfully.';

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json([
            'message' => self::MESSAGE_CREATED,
            'data'    => (new ProjectResource($project))->resolve(),
        ], 201);
    }
}