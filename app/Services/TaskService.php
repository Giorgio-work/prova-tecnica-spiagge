<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * Get filtered and paginated tasks for the authenticated user.
     */
    public function getFilteredTasks(Request $request): LengthAwarePaginator
    {
        $query = Task::query();

        // Filter by status if provided
        if ($request->has('status') && isset($request->status)) {
            $query->status($request->status);
        }

        // Filter by priority if provided
        if ($request->has('priority') && isset($request->priority)) {
            $query->priority($request->priority);
        }

        // Filter by overdue
        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        // Show only tasks created by the authenticated user
        $query->where('user_id', Auth::id());

        // Order by due date or created date
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');

        if (in_array($orderBy, ['due_date', 'created_at', 'priority'])) {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $query->with(['user'])->paginate(10);
    }

    /**
     * Get all users.
     */
    public function getAllUsers()
    {
        return User::all();
    }

    /**
     * Get available statuses.
     */
    public function getStatuses(): array
    {
        return ['pending', 'in_progress', 'completed', 'cancelled'];
    }

    /**
     * Get available priorities.
     */
    public function getPriorities(): array
    {
        return ['low', 'medium', 'high', 'urgent'];
    }

    /**
     * Create a new task.
     */
    public function createTask(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Get a task with its relationships.
     */
    public function getTaskWithRelations(Task $task): Task
    {
        return $task->load(['user']);
    }

    /**
     * Update a task.
     */
    public function updateTask(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Change task status.
     */
    public function changeTaskStatus(Task $task, string $status): bool
    {
        Gate::authorize('changeStatus', $task);

        $task->status = $status;

        if ($status === 'completed' && !$task->completed_at) {
            $task->completed_at = now();
        } elseif ($status !== 'completed') {
            $task->completed_at = null;
        }

        return $task->save();
    }

    /**
     * Validate status change request.
     */
    public function validateStatusChange(Request $request): array
    {
        return $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
        ]);
    }
}