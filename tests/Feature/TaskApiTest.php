<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create();

        $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_get_tasks_filtered_by_status(): void
    {
        $user = User::factory()->create();
        Task::factory()->create(['status' => TaskStatus::New]);
        Task::factory()->create(['status' => TaskStatus::Done]);
        $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/tasks?status=done')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'done');
    }

    public function test_get_tasks_invalid_status_filter(): void
    {
        $user = User::factory()->create();
        $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/tasks?status=invalid')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_create_task(): void
    {
        $user = User::factory()->create();
        $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/tasks', [
                'title' => 'Test task',
                'description' => 'Test description',
                'status' => 'new'
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Test task')
            ->assertJsonPath('data.status', 'new');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test task',
            'status' => 'new'
        ]);
    }

    public function test_create_task_invalid_data(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/tasks', [
                'title' => '',
                'status' => 'invalid'
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_show_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['title' => 'Task to view',]);

        $this
            ->actingAs($user, 'sanctum')
            ->getJson("/api/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.title', 'Task to view');
    }

    public function test_task_not_found(): void
    {
        $user = User::factory()->create();
        $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/tasks/1')
            ->assertNotFound();
    }

    public function test_update_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'title' => 'Old title',
            'status' => TaskStatus::New
        ]);

        $this
            ->actingAs($user, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated title',
                'status' => 'done'
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated title')
            ->assertJsonPath('data.status', 'done');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated title',
            'status' => 'done'
        ]);
    }

    public function test_delete_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $this
            ->actingAs($user, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id,]);
    }

    public function test_get_tasks_unauthenticated_user(): void
    {
        $this->getJson('/api/tasks')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_get_tasks_pagination(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(20)->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 15);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/tasks?page=2')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2);
    }
}
