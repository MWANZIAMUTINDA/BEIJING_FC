<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MySQL Stored Procedures for Beijing FC Management System.
 *
 * Creates the following stored procedures:
 *  - sp_get_player_stats         : Aggregate stats for a given player
 *  - sp_get_team_squad           : Full squad list for a team
 *  - sp_get_upcoming_matches     : Next N fixtures (stadium + teams)
 *  - sp_record_match_result      : Update score & resolve winner
 *  - sp_get_user_activity_report : Login + role summary per user
 */
return new class extends Migration
{
    /**
     * Run the migrations — create stored procedures.
     * Only created for MySQL/MariaDB; silently skipped on other drivers.
     */
    public function up(): void
    {
        if (! $this->isMysql()) {
            return;
        }

        // Allow multiple statements in one call
        DB::unprepared($this->dropAll());
        DB::unprepared($this->spGetPlayerStats());
        DB::unprepared($this->spGetTeamSquad());
        DB::unprepared($this->spGetUpcomingMatches());
        DB::unprepared($this->spRecordMatchResult());
        DB::unprepared($this->spGetUserActivityReport());
    }

    /**
     * Reverse the migrations — drop stored procedures.
     */
    public function down(): void
    {
        if (! $this->isMysql()) {
            return;
        }

        DB::unprepared($this->dropAll());
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function isMysql(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function dropAll(): string
    {
        return "
            DROP PROCEDURE IF EXISTS sp_get_player_stats;
            DROP PROCEDURE IF EXISTS sp_get_team_squad;
            DROP PROCEDURE IF EXISTS sp_get_upcoming_matches;
            DROP PROCEDURE IF EXISTS sp_record_match_result;
            DROP PROCEDURE IF EXISTS sp_get_user_activity_report;
        ";
    }

    // -------------------------------------------------------------------------
    // Stored Procedures
    // -------------------------------------------------------------------------

    /**
     * sp_get_player_stats
     * Returns aggregate information about a single player.
     *
     * IN  p_player_id  BIGINT  — ID of the player to retrieve stats for.
     *
     * Result set columns:
     *   player_id, full_name, position, nationality, age, team_name,
     *   jersey_number, salary
     */
    private function spGetPlayerStats(): string
    {
        return "
CREATE PROCEDURE sp_get_player_stats(IN p_player_id BIGINT)
BEGIN
    SELECT
        p.id                                   AS player_id,
        p.name                                 AS full_name,
        p.position,
        p.nationality,
        TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) AS age,
        t.name                                 AS team_name,
        p.jersey_number,
        p.salary
    FROM players p
    LEFT JOIN teams t ON t.id = p.team_id
    WHERE p.id = p_player_id;
END
";
    }

    /**
     * sp_get_team_squad
     * Returns the full squad (all players) for a given team.
     *
     * IN  p_team_id  BIGINT  — ID of the team.
     *
     * Result set columns:
     *   player_id, name, position, nationality, jersey_number, age, salary
     */
    private function spGetTeamSquad(): string
    {
        return "
CREATE PROCEDURE sp_get_team_squad(IN p_team_id BIGINT)
BEGIN
    SELECT
        p.id                                           AS player_id,
        p.name,
        p.position,
        p.nationality,
        p.jersey_number,
        TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) AS age,
        p.salary
    FROM players p
    WHERE p.team_id = p_team_id
    ORDER BY p.jersey_number;
END
";
    }

    /**
     * sp_get_upcoming_matches
     * Returns the next N fixtures sorted by date/time ascending.
     *
     * IN  p_limit  INT  — Maximum number of matches to return (default 10).
     *
     * Result set columns:
     *   match_id, home_team, away_team, stadium_name, match_date, match_time, status
     */
    private function spGetUpcomingMatches(): string
    {
        return "
CREATE PROCEDURE sp_get_upcoming_matches(IN p_limit INT)
BEGIN
    DECLARE v_limit INT;
    SET v_limit = IFNULL(p_limit, 10);

    SELECT
        m.id                 AS match_id,
        ht.name              AS home_team,
        at.name              AS away_team,
        s.name               AS stadium_name,
        m.match_date,
        m.match_time,
        m.status
    FROM matches m
    LEFT JOIN teams   ht ON ht.id = m.home_team_id
    LEFT JOIN teams   at ON at.id = m.away_team_id
    LEFT JOIN stadiums s  ON s.id  = m.stadium_id
    WHERE m.match_date >= CURDATE()
      AND m.status = 'scheduled'
    ORDER BY m.match_date ASC, m.match_time ASC
    LIMIT v_limit;
END
";
    }

    /**
     * sp_record_match_result
     * Updates the score and status of a match and determines the winning team.
     *
     * IN  p_match_id      BIGINT  — ID of the match to update.
     * IN  p_home_score    INT     — Goals scored by the home team.
     * IN  p_away_score    INT     — Goals scored by the away team.
     * OUT p_winner_name   VARCHAR — Name of the winning team ('Draw' if tied).
     */
    private function spRecordMatchResult(): string
    {
        return "
CREATE PROCEDURE sp_record_match_result(
    IN  p_match_id    BIGINT,
    IN  p_home_score  INT,
    IN  p_away_score  INT,
    OUT p_winner_name VARCHAR(255)
)
BEGIN
    DECLARE v_home_team_id BIGINT;
    DECLARE v_away_team_id BIGINT;
    DECLARE v_home_name    VARCHAR(255);
    DECLARE v_away_name    VARCHAR(255);

    -- Fetch team IDs
    SELECT home_team_id, away_team_id
    INTO   v_home_team_id, v_away_team_id
    FROM   matches
    WHERE  id = p_match_id;

    -- Fetch team names
    SELECT name INTO v_home_name FROM teams WHERE id = v_home_team_id;
    SELECT name INTO v_away_name FROM teams WHERE id = v_away_team_id;

    -- Persist result
    UPDATE matches
    SET home_score = p_home_score,
        away_score = p_away_score,
        status     = 'completed',
        updated_at = NOW()
    WHERE id = p_match_id;

    -- Determine winner
    IF p_home_score > p_away_score THEN
        SET p_winner_name = v_home_name;
    ELSEIF p_away_score > p_home_score THEN
        SET p_winner_name = v_away_name;
    ELSE
        SET p_winner_name = 'Draw';
    END IF;
END
";
    }

    /**
     * sp_get_user_activity_report
     * Summarises user account details alongside their assigned role.
     *
     * No parameters.
     *
     * Result set columns:
     *   user_id, name, email, role, account_status, created_at
     */
    private function spGetUserActivityReport(): string
    {
        return "
CREATE PROCEDURE sp_get_user_activity_report()
BEGIN
    SELECT
        u.id                AS user_id,
        u.name,
        u.email,
        u.role,
        u.status            AS account_status,
        u.created_at
    FROM users u
    ORDER BY u.created_at DESC;
END
";
    }
};
