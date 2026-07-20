<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationHardeningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function inactive_user_cannot_login_web()
    {
        $inactiveUser = User::factory()->create([
            'email'     => 'inactive@bfc.com',
            'password'  => Hash::make('password123'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'inactive@bfc.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function active_user_logs_out_mid_session_if_deactivated()
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Make requests to authenticated page, works fine
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // Deactivate user in the database
        $user->update(['is_active' => false]);

        // Next request gets blocked and logged out
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function invalid_credentials_do_not_create_session()
    {
        $user = User::factory()->create([
            'email'    => 'user@bfc.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'user@bfc.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function user_can_log_out_destroying_session()
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /** @test */
    public function user_passwords_are_securely_hashed_and_salted()
    {
        $user = User::factory()->create([
            'password' => Hash::make('mysecurepassword'),
        ]);

        $this->assertNotEquals('mysecurepassword', $user->password);
        $this->assertTrue(Hash::check('mysecurepassword', $user->password));

        // Laravel Bcrypt uses unique salts automatically by structure
        $info = password_get_info($user->password);
        $this->assertEquals('bcrypt', $info['algoName']);
    }
}
