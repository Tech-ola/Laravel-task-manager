<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'index'])->name(TaskController::ROUTE_INDEX);

Route::post('/projects', [ProjectController::class, 'store'])->name(ProjectController::ROUTE_STORE);

Route::patch('/tasks/reorder', [TaskController::class, 'reorder'])->name(TaskController::ROUTE_REORDER);

Route::get('/tasks/create', [TaskController::class, 'create'])->name(TaskController::ROUTE_CREATE);
Route::post('/tasks', [TaskController::class, 'store'])->name(TaskController::ROUTE_STORE);
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name(TaskController::ROUTE_EDIT);
Route::put('/tasks/{task}', [TaskController::class, 'update'])->name(TaskController::ROUTE_UPDATE);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name(TaskController::ROUTE_DESTROY);