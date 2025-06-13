<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class TaskController extends Controller implements HasMiddleware
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public static function middleware()
    {
        return [
            'auth',
            new Middleware('can:viewAny,' . Task::class, only: ['index']),
            new Middleware('can:create,' . Task::class, only: ['create', 'store']),
            new Middleware('can:update,task', only: ['edit', 'update']),
            new Middleware('can:delete,task', only: ['destroy']),
            new Middleware('can:changeStatus,task', only: ['changeStatus']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $this->taskService->getFilteredTasks($request);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'users' => $this->taskService->getAllUsers(),
            'statuses' => $this->taskService->getStatuses(),
            'priorities' => $this->taskService->getPriorities(),
            'filters' => $request->only(['status', 'priority', 'user_id', 'overdue', 'created_by_me', 'order_by', 'order_direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'users' => $this->taskService->getAllUsers(),
            'statuses' => $this->taskService->getStatuses(),
            'priorities' => $this->taskService->getPriorities(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $this->taskService->createTask($request->validated());

        return redirect()->route('tasks.index')
            ->with('title', 'Task creato con successo!')
            ->with('icon', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task = $this->taskService->getTaskWithRelations($task);

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'users' => $this->taskService->getAllUsers(),
            'statuses' => $this->taskService->getStatuses(),
            'priorities' => $this->taskService->getPriorities(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->taskService->updateTask($task, $request->validated());

        return redirect()->back()
            ->with('title', 'Task aggiornato con successo!')
            ->with('icon', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->taskService->deleteTask($task);

        return redirect()->route('tasks.index')
            ->with('title', 'Task eliminato con successo!')
            ->with('icon', 'success');
    }

    /**
     * Change the task status.
     */
    public function changeStatus(Request $request, Task $task)
    {
        $validated = $this->taskService->validateStatusChange($request);
        $this->taskService->changeTaskStatus($task, $validated['status']);

        return redirect()->back()
            ->with('title', 'Stato del task aggiornato con successo!')
            ->with('icon', 'success');
    }
}
