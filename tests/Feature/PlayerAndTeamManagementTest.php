<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeagueTeam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlayerAndTeamManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user to execute CRUD commands
        $this->adminUser = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_create_a_league_team()
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.teams.store'), [
                'name'       => 'Mathare United',
                'short_name' => 'MUFC',
                'color'      => '#FF0000',
                'kit_color'  => '#FFFFFF',
                'is_active'  => true,
            ]);

        $response->assertRedirect(route('admin.teams.index'));
        $this->assertDatabaseHas('league_teams', [
            'name'       => 'Mathare United',
            'short_name' => 'MUFC',
        ]);
    }

    /** @test */
    public function cannot_create_team_with_duplicate_name_or_code()
    {
        // First team
        LeagueTeam::create([
            'name'       => 'Kibera Stars',
            'short_name' => 'KBS',
            'color'      => '#000000',
        ]);

        // Duplicate Name
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.teams.store'), [
                'name'       => 'Kibera Stars',
                'short_name' => 'NEW',
                'color'      => '#111111',
            ]);

        $response->assertSessionHasErrors('name');

        // Duplicate Short Name
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.teams.store'), [
                'name'       => 'Kibera Stars Two',
                'short_name' => 'KBS',
                'color'      => '#222222',
            ]);

        $response->assertSessionHasErrors('short_name');
    }

    /** @test */
    public function admin_can_create_a_member_with_nationality_and_team()
    {
        Storage::fake('public');

        $team = LeagueTeam::create([
            'name'       => 'Beijing FC',
            'short_name' => 'BFC',
            'color'      => '#00FF00',
        ]);

        $avatarFile = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'username'              => 'johnny',
                'name'                  => 'Johnny Player',
                'email'                 => 'johnny@bfc.com',
                'phone'                 => '0711222333',
                'position'              => 'FW',
                'role'                  => 'member',
                'billing_type'          => 'monthly',
                'nationality'           => 'Ugandan',
                'league_team_id'        => $team->id,
                'jersey_number'         => 9,
                'date_joined'           => '2026-07-21',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
                'avatar'                => $avatarFile,
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'username'       => 'johnny',
            'nationality'    => 'Ugandan',
            'league_team_id' => $team->id,
            'jersey_number'  => 9,
        ]);

        $user = User::where('username', 'johnny')->first();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    /** @test */
    public function cannot_create_player_with_duplicate_jersey_number()
    {
        // First member
        User::factory()->create([
            'username'      => 'first',
            'phone'         => '0711111111',
            'jersey_number' => 7,
            'role'          => 'member',
        ]);

        // Duplicate Jersey Number
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'username'              => 'second',
                'name'                  => 'Second Player',
                'email'                 => 'second@bfc.com',
                'phone'                 => '0722222222',
                'position'              => 'MF',
                'role'                  => 'member',
                'billing_type'          => 'monthly',
                'nationality'           => 'Kenyan',
                'jersey_number'         => 7, // Duplicate
                'password'              => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors('jersey_number');
    }
}
