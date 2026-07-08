<?php

namespace Tests\Feature;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesWmsTestData;
use Tests\TestCase;

class UserActivoTest extends TestCase
{
    use RefreshDatabase;
    use CreatesWmsTestData;

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = $this->makeUser('bodeguero');
        $user->update(['password' => bcrypt('password123'), 'activo' => false]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_active_user_can_log_in(): void
    {
        $user = $this->makeUser('bodeguero');
        $user->update(['password' => bcrypt('password123'), 'activo' => true]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)
            ->patch("/usuarios/{$admin->id}/toggle-activo")
            ->assertRedirect();

        $this->assertTrue($admin->fresh()->activo);
    }

    public function test_admin_can_deactivate_another_admin_when_not_the_last_active_one(): void
    {
        $adminA = $this->makeUser('admin');
        $adminB = $this->makeUser('admin');

        $this->actingAs($adminA)
            ->patch("/usuarios/{$adminB->id}/toggle-activo")
            ->assertRedirect(route('users.index'));

        $this->assertFalse($adminB->fresh()->activo);
    }

    public function test_user_policy_blocks_deactivating_the_last_active_admin(): void
    {
        $lastActiveAdmin = $this->makeUser('admin');
        $inactiveAdmin = $this->makeUser('admin');
        $inactiveAdmin->update(['activo' => false]);

        $policy = new UserPolicy();

        // Se refrescan ambos modelos: Eloquent::create() no relee columnas con
        // default de BD (como "activo"), así que el objeto en memoria podría
        // no reflejar el valor real todavía.
        $this->assertFalse($policy->toggleActivo($inactiveAdmin->fresh(), $lastActiveAdmin->fresh()));
    }
}
