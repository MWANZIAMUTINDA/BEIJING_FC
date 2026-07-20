<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeagueTeam;
use App\Models\Stadium;
use App\Models\FootballMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiValidationTest extends TestCase
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
    public function player_api_crud_operations()
    {
        // 1. Create Player via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/players', [
                'username'      => 'ronaldo7',
                'name'          => 'Cristiano Ronaldo',
                'email'         => 'cr7@alnasr.com',
                'phone'         => '0799999999',
                'position'      => 'FW',
                'billing_type'  => 'match',
                'nationality'   => 'Other',
                'jersey_number' => 7,
                'password'      => 'siuuuuuu7',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status'  => 'success',
                'message' => 'Player created successfully.',
            ]);

        $player = User::where('username', 'ronaldo7')->firstOrFail();

        // 2. Read Player via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/players/' . $player->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.username', 'ronaldo7');

        // 3. Update Player via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson('/api/players/' . $player->id, [
                'name'          => 'Cristiano Ronaldo Dos Santos',
                'jersey_number' => 77,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.jersey_number', 77);

        // 4. Delete Player via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson('/api/players/' . $player->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $player->id]);
    }

    /** @test */
    public function team_api_crud_operations()
    {
        // 1. Create Team via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/teams', [
                'name'       => 'Gor Mahia FC',
                'short_name' => 'GMFC',
                'color'      => '#00FF00',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status'  => 'success',
                'message' => 'Team created successfully.',
            ]);

        $team = LeagueTeam::where('short_name', 'GMFC')->firstOrFail();

        // 2. Update Team via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson('/api/teams/' . $team->id, [
                'name'       => 'Kogalo FC',
                'short_name' => 'KGL',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.short_name', 'KGL');

        // 3. Delete Team via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson('/api/teams/' . $team->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('league_teams', ['id' => $team->id]);
    }

    /** @test */
    public function stadium_api_crud_operations()
    {
        // 1. Create Stadium via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/stadiums', [
                'name'    => 'Mombasa Municipal',
                'surface' => 'grass',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Mombasa Municipal');

        $stadium = Stadium::where('name', 'Mombasa Municipal')->firstOrFail();

        // 2. Update Stadium via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson('/api/stadiums/' . $stadium->id, [
                'capacity' => 12000,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.capacity', 12000);

        // 3. Delete Stadium via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson('/api/stadiums/' . $stadium->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('stadiums', ['id' => $stadium->id]);
    }

    /** @test */
    public function match_api_crud_operations()
    {
        // 1. Create Match via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/matches', [
                'title'      => 'Big Derby',
                'away_team'  => 'Tusker FC',
                'type'       => 'league',
                'match_date' => now()->addDays(10)->toDateString(),
                'match_time' => '15:00',
                'venue'      => 'Ruaraka Stadium',
                'deadline'   => now()->addDays(8)->toDateTimeString(),
                'match_fee'  => 100,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.away_team', 'Tusker FC');

        $match = FootballMatch::where('away_team', 'Tusker FC')->firstOrFail();

        // 2. Update Match via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson('/api/matches/' . $match->id, [
                'status' => 'open',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'open');

        // 3. Delete Match via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson('/api/matches/' . $match->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('matches', ['id' => $match->id]);
    }

    /** @test */
    public function user_api_crud_operations()
    {
        // 1. Create User via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', [
                'username' => 'treasurerjim',
                'name'     => 'Jim Gold',
                'email'    => 'jim@bfc.com',
                'phone'    => '0711112222',
                'role'     => 'treasurer',
                'password' => 'password123',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'treasurer');

        $user = User::where('username', 'treasurerjim')->firstOrFail();

        // 2. Update User via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson('/api/users/' . $user->id, [
                'name' => 'Jim Gold II',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Jim Gold II');

        // 3. Delete User via API
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
