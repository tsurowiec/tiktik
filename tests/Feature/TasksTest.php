<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/tasks')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_tasks_page(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/tasks')->assertOk();
    }

    public function test_dashboard_redirects_to_tasks(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/dashboard')->assertRedirect('/tasks');
    }
}
