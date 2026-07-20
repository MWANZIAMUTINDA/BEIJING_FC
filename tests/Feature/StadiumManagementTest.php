<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Stadium;
use App\Models\FootballMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StadiumManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $memberUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->memberUser = User::factory()->create([
            'role'      => 'member',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_create_a_stadium()
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.stadiums.store'), [
                'name'      => 'Nyayo Stadium',
                'location'  => 'Nairobi City',
                'capacity'  => 30000,
                'surface'   => 'grass',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.stadiums.index'));
        $this->assertDatabaseHas('stadiums', [
            'name'     => 'Nyayo Stadium',
            'location' => 'Nairobi City',
        ]);
    }

    /** @test */
    public function cannot_create_stadium_with_duplicate_name()
    {
        Stadium::create([
            'name'    => 'Kasarani',
            'surface' => 'grass',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.stadiums.store'), [
                'name'    => 'Kasarani',
                'surface' => 'artificial',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function member_cannot_manage_stadiums()
    {
        $response = $this->actingAs($this->memberUser)
            ->post(route('admin.stadiums.store'), [
                'name'    => 'Member Stadium',
                'surface' => 'grass',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_edit_stadium()
    {
        $stadium = Stadium::create([
            'name'    => 'Kasarani Arena',
            'surface' => 'artificial',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.stadiums.update', $stadium), [
                'name'      => 'Kasarani Arena Updated',
                'surface'   => 'grass',
                'is_active' => false,
            ]);

        $response->assertRedirect(route('admin.stadiums.index'));
        $this->assertDatabaseHas('stadiums', [
            'id'        => $stadium->id,
            'name'      => 'Kasarani Arena Updated',
            'surface'   => 'grass',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function cannot_delete_stadium_if_referenced_in_a_match()
    {
        $stadium = Stadium::create([
            'name'    => 'Camp Toyoyo',
            'surface' => 'artificial',
        ]);

        // Create match referencing this venue name
        FootballMatch::create([
            'title'      => 'Match at Toyoyo',
            'away_team'  => 'Gor Mahia',
            'type'       => 'friendly',
            'match_date' => now()->addDays(5)->toDateString(),
            'match_time' => '15:00',
            'venue'      => 'Camp Toyoyo Stadium, Nairobi', // references name
            'deadline'   => now()->addDays(3),
            'match_fee'  => 300,
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.stadiums.destroy', $stadium));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('stadiums', ['id' => $stadium->id]);
    }
}
