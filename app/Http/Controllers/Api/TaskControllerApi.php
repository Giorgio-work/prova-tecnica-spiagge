<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskControllerApi extends Controller implements HasMiddleware
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public static function middleware()
    {
        return [
            'auth:sanctum',
            new Middleware('can:viewAny,' . Task::class, only: ['index']),
            new Middleware('can:create,' . Task::class, only: ['store']),
            new Middleware('can:update,task', only: ['update']),
            new Middleware('can:delete,task', only: ['destroy']),
            new Middleware('can:changeStatus,task', only: ['changeStatus']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $tasks = $this->taskService->getFilteredTasks($request);

        return response()->json([
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
            'users' => $this->taskService->getAllUsers(),
            'statuses' => $this->taskService->getStatuses(),
            'priorities' => $this->taskService->getPriorities(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());

        return response()->json([
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        $task = $this->taskService->getTaskWithRelations($task);

        return response()->json([
            'data' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $this->taskService->updateTask($task, $request->validated());

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Change the task status.
     */
    public function changeStatus(Request $request, Task $task): JsonResponse
    {
        $validated = $this->taskService->validateStatusChange($request);
        $this->taskService->changeTaskStatus($task, $validated['status']);

        return response()->json([
            'message' => 'Task status updated successfully',
            'data' => $task->fresh()
        ]);
    }
}