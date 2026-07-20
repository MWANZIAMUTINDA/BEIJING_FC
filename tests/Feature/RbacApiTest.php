<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Stadium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $coachUser;
    protected User $memberUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->coachUser = User::factory()->create([
            'role'      => 'coach',
            'is_active' => true,
        ]);

        $this->memberUser = User::factory()->create([
            'role'      => 'member',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function unauthenticated_api_requests_are_denied()
    {
        $response = $this->postJson('/api/stadiums', [
            'name'    => 'Unauth Stadium',
            'surface' => 'grass',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function member_role_cannot_create_stadiums_api()
    {
        $response = $this->actingAs($this->memberUser, 'sanctum')
            ->postJson('/api/stadiums', [
                'name'    => 'New Arena',
                'surface' => 'grass',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function coach_role_can_schedule_matches_api()
    {
        $response = $this->actingAs($this->coachUser, 'sanctum')
            ->postJson('/api/matches', [
                'away_team'  => 'AFC Leopards',
                'type'       => 'friendly',
                'match_date' => now()->addDays(5)->toDateString(),
                'match_time' => '16:00',
                'venue'      => 'Kasarani',
                'deadline'   => now()->addDays(3)->toDateTimeString(),
                'match_fee'  => 200,
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function coach_role_cannot_manage_stadiums_api()
    {
        $response = $this->actingAs($this->coachUser, 'sanctum')
            ->postJson('/api/stadiums', [
                'name'    => 'Coach Stadium',
                'surface' => 'grass',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_role_can_create_stadiums_api()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/stadiums', [
                'name'    => 'Admin Stadium',
                'surface' => 'indoor',
            ]);

        $response->assertStatus(201);
    }
}
