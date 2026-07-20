<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementEnhancedTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_create_a_coach_without_member_billing_fields()
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'username'              => 'coachjoe',
                'name'                  => 'Coach Joe',
                'email'                 => 'joe@bfc.com',
                'phone'                 => '0712123123',
                'role'                  => 'coach',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'coachjoe',
            'role'     => 'coach',
        ]);
    }

    /** @test */
    public function admin_can_assign_new_role_privilege()
    {
        $user = User::factory()->create([
            'role' => 'member',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.role', $user), [
                'role' => 'treasurer',
            ]);

        $response->assertRedirect();
        $this->assertEquals('treasurer', $user->fresh()->role);
    }

    /** @test */
    public function admin_can_disable_or_activate_user_account()
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        // Toggle to inactive
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.toggle', $user));

        $this->assertFalse($user->fresh()->is_active);

        // Toggle back to active
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.toggle', $user));

        $this->assertTrue($user->fresh()->is_active);
    }
}
