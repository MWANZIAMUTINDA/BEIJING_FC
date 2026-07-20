<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FootballMatch;
use App\Models\Stadium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $coachUser;

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
    }

    /** @test */
    public function coach_can_schedule_a_match()
    {
        $response = $this->actingAs($this->coachUser)
            ->post(route('matches.store'), [
                'title'      => 'Friendly derby',
                'away_team'  => 'AFC Leopards',
                'type'       => 'friendly',
                'match_date' => now()->addDays(5)->format('Y-m-d'),
                'match_time' => '15:00',
                'venue'      => 'Camp Toyoyo',
                'deadline'   => now()->addDays(3)->format('Y-m-d H:i'),
                'match_fee'  => 250.00,
            ]);

        $this->assertDatabaseHas('matches', [
            'away_team' => 'AFC Leopards',
            'type'      => 'friendly',
        ]);
    }

    /** @test */
    public function scheduling_duplicate_fixture_triggers_session_warning()
    {
        // First match
        FootballMatch::create([
            'title'      => 'Match 1',
            'away_team'  => 'Opponent A',
            'type'       => 'friendly',
            'match_date' => '2026-10-15',
            'match_time' => '16:00:00',
            'venue'      => 'Venue A',
            'deadline'   => '2026-10-12 12:00:00',
            'match_fee'  => 200,
            'created_by' => $this->adminUser->id,
        ]);

        // Duplicate scheduling at same date & time
        $response = $this->actingAs($this->adminUser)
            ->post(route('matches.store'), [
                'title'      => 'Match 2',
                'away_team'  => 'Opponent B',
                'type'       => 'league',
                'match_date' => '2026-10-15',
                'match_time' => '16:00',
                'venue'      => 'Venue B',
                'deadline'   => '2026-10-12 12:00',
                'match_fee'  => 300,
            ]);

        // The store method redirects to matches.show with a 'success' flash message
        $response->assertSessionHas('success');
        // Follow the redirect and ensure warning is carried in session
        $successMsg = $response->getSession()->get('success', '');
        $this->assertStringContainsString('Warning', $successMsg);
    }

    /** @test */
    public function admin_can_edit_and_update_match()
    {
        $match = FootballMatch::create([
            'title'      => 'Old Title',
            'away_team'  => 'Old Opponent',
            'type'       => 'friendly',
            'match_date' => '2026-10-15',
            'match_time' => '16:00:00',
            'venue'      => 'Old Venue',
            'deadline'   => '2026-10-12 12:00:00',
            'match_fee'  => 200,
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('matches.update', $match), [
                'title'      => 'New Title',
                'away_team'  => 'New Opponent',
                'type'       => 'league',
                'match_date' => '2026-10-16',
                'match_time' => '17:00:00',
                'venue'      => 'New Venue',
                'deadline'   => '2026-10-13 12:00:00',
                'match_fee'  => 250,
                'status'     => 'open',
            ]);

        $response->assertRedirect(route('matches.show', $match));
        $this->assertDatabaseHas('matches', [
            'id'        => $match->id,
            'title'     => 'New Title',
            'away_team' => 'New Opponent',
            'status'    => 'open',
        ]);
    }

    /** @test */
    public function search_and_filter_fixtures()
    {
        // Friendly match
        FootballMatch::create([
            'title'      => 'Derby match',
            'away_team'  => 'Nairobi City Stars',
            'type'       => 'friendly',
            'match_date' => now()->addDays(5)->toDateString(),
            'match_time' => '15:00:00',
            'venue'      => 'Kasarani',
            'deadline'   => now()->addDays(3),
            'created_by' => $this->adminUser->id,
        ]);

        // League match
        FootballMatch::create([
            'title'      => 'Cup match',
            'away_team'  => 'Bandari FC',
            'type'       => 'league',
            'match_date' => now()->addDays(6)->toDateString(),
            'match_time' => '15:00:00',
            'venue'      => 'Nyayo',
            'deadline'   => now()->addDays(4),
            'created_by' => $this->adminUser->id,
        ]);

        // Filter by type friendly
        $response = $this->actingAs($this->adminUser)
            ->get(route('matches.index', ['type' => 'friendly']));

        $response->assertSee('Nairobi City Stars');

        // Filter by search query (venue)
        $response = $this->actingAs($this->adminUser)
            ->get(route('matches.index', ['search' => 'Nyayo']));

        $response->assertSee('Bandari FC');
    }
}
