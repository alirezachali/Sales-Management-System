<?php

namespace Tests\Feature;

use App\Livewire\Todos\TodoManager;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TodoManagerTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $name = 'مدیر'): User
    {
        return User::create([
            'name' => $name,
            'username' => 'user_' . uniqid(),
            'email' => uniqid() . '@example.com',
            'password' => 'password',
        ]);
    }

    public function test_manager_can_assign_todo_to_another_user(): void
    {
        $manager = $this->createUser('مدیر');
        $alireza = $this->createUser('علیرضا');

        $this->actingAs($manager);

        // ثبت کار برای علیرضا
        Livewire::test(TodoManager::class)
            ->call('openCreateModal')
            ->set('title', 'گزارش فروش ماهانه')
            ->set('assigned_to', (string) $alireza->id)
            ->call('save')
            ->assertHasNoErrors();

        $todo = Todo::where('title', 'گزارش فروش ماهانه')->first();

        $this->assertNotNull($todo);
        // ثبت‌کننده = مدیر، انجام‌دهنده = علیرضا
        $this->assertEquals($manager->id, $todo->user_id);
        $this->assertEquals($alireza->id, $todo->assigned_to);
    }

    public function test_new_todo_defaults_assignee_to_current_user(): void
    {
        $manager = $this->createUser('مدیر');

        $this->actingAs($manager);

        Livewire::test(TodoManager::class)
            ->call('openCreateModal')
            ->assertSet('assigned_to', (string) $manager->id);
    }

    public function test_manager_sees_completion_status_of_assigned_task(): void
    {
        $manager = $this->createUser('مدیر');
        $alireza = $this->createUser('علیرضا');

        $todo = Todo::create([
            'user_id' => $manager->id,
            'assigned_to' => $alireza->id,
            'title' => 'انجام خرید',
            'status' => Todo::STATUS_PENDING,
            'priority' => Todo::PRIORITY_HIGH,
        ]);

        $this->actingAs($manager);

        Livewire::test(TodoManager::class)
            ->assertSee('انجام خرید')
            ->assertSee('علیرضا')
            ->assertViewHas('todos', function ($todos) use ($todo) {
                return $todos->contains($todo);
            });

        // علیرضا آن را تکمیل می‌کند
        $this->actingAs($alireza);
        Livewire::test(TodoManager::class)
            ->call('toggleComplete', $todo->id);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'status' => Todo::STATUS_COMPLETED,
        ]);
    }
}
