<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_another_admin_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this
            ->withoutMiddleware(PreventRequestForgery::class)
            ->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Secretaria',
                'email' => 'secretaria@parroquia.com',
                'password' => 'Contrasena123!',
                'password_confirmation' => 'Contrasena123!',
                'is_admin' => 1,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'secretaria@parroquia.com',
            'is_admin' => true,
        ]);
    }

    public function test_admin_cannot_remove_own_admin_access(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this
            ->withoutMiddleware(PreventRequestForgery::class)
            ->actingAs($admin)
            ->from(route('admin.users.edit', $admin->id))
            ->put(route('admin.users.update', $admin->id), [
                'name' => $admin->name,
                'email' => $admin->email,
            ]);

        $response->assertRedirect(route('admin.users.edit', $admin->id));
        $response->assertSessionHasErrors('is_admin');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_admin' => true,
        ]);
    }
}
