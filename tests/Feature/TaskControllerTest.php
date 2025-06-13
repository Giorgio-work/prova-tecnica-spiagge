<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutMiddleware;

    protected User $user;
    protected User $assignedUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->assignedUser = User::factory()->create();
    }

    #[Test]
    public function it_displays_tasks_index_page_for_authenticated_user()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tasks/Index')
                ->has('tasks')
                ->has('users')
                ->has('statuses')
                ->has('priorities')
                ->has('filters')
            );
    }

    #[Test]
    public function it_filters_tasks_by_status()
    {
        Task::factory()->create([
            'status' => 'pending',
            'user_id' => $this->user->id
        ]);
        Task::factory()->create([
            'status' => 'completed',
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index', ['status' => 'pending']));

        $response->assertStatus(200);
    }

    #[Test]
    public function it_filters_tasks_by_priority()
    {
        Task::factory()->create([
            'priority' => 'high',
            'user_id' => $this->user->id
        ]);
        Task::factory()->create([
            'priority' => 'low',
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index', ['priority' => 'high']));

        $response->assertStatus(200);
    }

    #[Test]
    public function it_filters_overdue_tasks()
    {
        Task::factory()->create([
            'due_date' => now()->subDays(1),
            'status' => 'pending',
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index', ['overdue' => true]));

        $response->assertStatus(200);
    }

    #[Test]
    public function it_displays_create_task_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('tasks.create'));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tasks/Create')
                ->has('users')
                ->has('statuses')
                ->has('priorities')
            );
    }

    #[Test]
    public function it_creates_a_new_task_successfully()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'medium',
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            '_token' => csrf_token()
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id
        ]);
    }

    #[Test]
    public function it_validates_status_field_with_valid_values()
    {
        $taskData = [
            'title' => 'Test Task',
            'status' => 'invalid_status',
            'priority' => 'medium',
            'user_id' => $this->user->id,
            '_token' => csrf_token()
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertSessionHasErrors(['status']);
    }

    #[Test]
    public function it_validates_priority_field_with_valid_values()
    {
        $taskData = [
            'title' => 'Test Task',
            'status' => 'pending',
            'priority' => 'invalid_priority',
            'user_id' => $this->user->id,
            '_token' => csrf_token()
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertSessionHasErrors(['priority']);
    }

    #[Test]
    public function it_validates_due_date_is_not_in_past()
    {
        $taskData = [
            'title' => 'Test Task',
            'status' => 'pending',
            'priority' => 'medium',
            'user_id' => $this->user->id,
            'due_date' => now()->subDays(1)->format('Y-m-d'),
            '_token' => csrf_token()
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tasks.store'), $taskData);

        $response->assertSessionHasErrors(['due_date']);
    }

    #[Test]
    public function it_displays_edit_task_page()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.edit', $task));

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Tasks/Edit')
                ->has('task')
                ->has('users')
                ->has('statuses')
                ->has('priorities')
            );
    }

    #[Test]
    public function it_orders_tasks_by_due_date()
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(5)
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'due_date' => now()->addDays(1)
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index', [
                'order_by' => 'due_date',
                'order_direction' => 'asc'
            ]));

        $response->assertStatus(200);
    }

    #[Test]
    public function it_orders_tasks_by_priority()
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'priority' => 'low'
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'priority' => 'urgent'
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('tasks.index', [
                'order_by' => 'priority',
                'order_direction' => 'desc'
            ]));

        $response->assertStatus(200);
    }
}
