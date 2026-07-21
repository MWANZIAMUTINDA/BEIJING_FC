<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * StoredProcedureController
 *
 * Exposes dedicated REST endpoints that invoke the MySQL stored procedures
 * defined in the create_stored_procedures migration.
 *
 * All routes require a valid Sanctum API token (auth:sanctum middleware).
 *
 * Routes (registered in routes/api.php):
 *   GET  /api/sp/player/{id}/stats       → sp_get_player_stats
 *   GET  /api/sp/team/{id}/squad         → sp_get_team_squad
 *   GET  /api/sp/matches/upcoming        → sp_get_upcoming_matches
 *   POST /api/sp/match/{id}/result       → sp_record_match_result
 *   GET  /api/sp/users/activity-report   → sp_get_user_activity_report
 */
class StoredProcedureController extends Controller
{
    // -------------------------------------------------------------------------
    // sp_get_player_stats
    // -------------------------------------------------------------------------

    /**
     * GET /api/sp/player/{id}/stats
     *
     * Returns aggregate stats for a single player by calling
     * the sp_get_player_stats stored procedure.
     */
    public function playerStats(int $id): JsonResponse
    {
        $rows = DB::select('CALL sp_get_player_stats(?)', [$id]);

        if (empty($rows)) {
            return response()->json(['message' => 'Player not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $rows[0],
        ]);
    }

    // -------------------------------------------------------------------------
    // sp_get_team_squad
    // -------------------------------------------------------------------------

    /**
     * GET /api/sp/team/{id}/squad
     *
     * Returns the full squad for a team by calling
     * the sp_get_team_squad stored procedure.
     */
    public function teamSquad(int $id): JsonResponse
    {
        $rows = DB::select('CALL sp_get_team_squad(?)', [$id]);

        return response()->json([
            'success' => true,
            'team_id' => $id,
            'count'   => count($rows),
            'data'    => $rows,
        ]);
    }

    // -------------------------------------------------------------------------
    // sp_get_upcoming_matches
    // -------------------------------------------------------------------------

    /**
     * GET /api/sp/matches/upcoming?limit=10
     *
     * Returns the next N upcoming fixtures by calling
     * the sp_get_upcoming_matches stored procedure.
     *
     * Query param: limit (int, default 10, max 50)
     */
    public function upcomingMatches(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $limit = min(max($limit, 1), 50);

        $rows = DB::select('CALL sp_get_upcoming_matches(?)', [$limit]);

        return response()->json([
            'success' => true,
            'count'   => count($rows),
            'data'    => $rows,
        ]);
    }

    // -------------------------------------------------------------------------
    // sp_record_match_result
    // -------------------------------------------------------------------------

    /**
     * POST /api/sp/match/{id}/result
     *
     * Body: { "home_score": int, "away_score": int }
     *
     * Calls sp_record_match_result and returns the winner name.
     */
    public function recordMatchResult(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        // Call the stored procedure; MySQL output param via SELECT
        DB::statement('CALL sp_record_match_result(?, ?, ?, @winner)', [
            $id,
            $data['home_score'],
            $data['away_score'],
        ]);

        $result      = DB::select('SELECT @winner AS winner_name');
        $winnerName  = $result[0]->winner_name ?? 'Unknown';

        return response()->json([
            'success'      => true,
            'match_id'     => $id,
            'home_score'   => $data['home_score'],
            'away_score'   => $data['away_score'],
            'winner'       => $winnerName,
        ]);
    }

    // -------------------------------------------------------------------------
    // sp_get_user_activity_report
    // -------------------------------------------------------------------------

    /**
     * GET /api/sp/users/activity-report
     *
     * Returns all users with role + status details.
     * Restricted to admin role only.
     */
    public function userActivityReport(): JsonResponse
    {
        if (! auth()->user()?->hasRole('admin')) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $rows = DB::select('CALL sp_get_user_activity_report()');

        return response()->json([
            'success' => true,
            'count'   => count($rows),
            'data'    => $rows,
        ]);
    }
}
