<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\MatchApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\LeagueApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\PlayerApiController;
use App\Http\Controllers\Api\TeamApiController;
use App\Http\Controllers\Api\StadiumApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\AnnouncementApiController;
use App\Http\Controllers\Api\ExpenseApiController;

/*
|--------------------------------------------------------------------------
| Mobile REST API Endpoints (Laravel Sanctum Protected)
|--------------------------------------------------------------------------
|
| Phase 7: Authentication — login checks is_active, returns Sanctum token
| Phase 8: RBAC — mutation routes guarded by api.role middleware
| Phase 9: Full CRUD for Players, Teams, Matches, Stadiums, Users
|
*/

// ─── Public — No Auth Required ───────────────────────────────────────────────
Route::post('/login',  [AuthApiController::class, 'login']);

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // ─── Matches ─────────────────────────────────────────────────────────────
    // Read — any authenticated user
    Route::get('/matches',        [MatchApiController::class, 'index']);
    Route::get('/matches/filter', [MatchApiController::class, 'filter']);
    Route::get('/matches/{match}', [MatchApiController::class, 'show']);

    // Write — admin or coach only (Phase 8 RBAC)
    Route::middleware('api.role:admin,coach')->group(function () {
        Route::post('/matches',           [MatchApiController::class, 'store']);
        Route::put('/matches/{match}',    [MatchApiController::class, 'update']);
        Route::delete('/matches/{match}', [MatchApiController::class, 'destroy']);
        Route::post('/matches/{match}/lock',       [MatchApiController::class, 'lockAvailability']);
        Route::post('/matches/{match}/teams',      [MatchApiController::class, 'generateTeams']);
        Route::post('/matches/{match}/teams/swap', [MatchApiController::class, 'swapPlayers']);
        Route::post('/matches/{match}/result',     [MatchApiController::class, 'recordResult']);
    });

    // Availability — any member
    Route::post('/matches/{match}/availability', [MatchApiController::class, 'updateAvailability']);

    // ─── Players ─────────────────────────────────────────────────────────────
    Route::get('/players',         [PlayerApiController::class, 'index']);
    Route::get('/players/{user}',  [PlayerApiController::class, 'show']);

    // Admin only — Phase 8
    Route::middleware('api.role:admin')->group(function () {
        Route::post('/players',          [PlayerApiController::class, 'store']);
        Route::put('/players/{user}',    [PlayerApiController::class, 'update']);
        Route::delete('/players/{user}', [PlayerApiController::class, 'destroy']);
    });

    // ─── Teams ───────────────────────────────────────────────────────────────
    Route::get('/teams',         [TeamApiController::class, 'index']);
    Route::get('/teams/{team}',  [TeamApiController::class, 'show']);

    Route::middleware('api.role:admin')->group(function () {
        Route::post('/teams',          [TeamApiController::class, 'store']);
        Route::put('/teams/{team}',    [TeamApiController::class, 'update']);
        Route::delete('/teams/{team}', [TeamApiController::class, 'destroy']);
    });

    // ─── Stadiums ────────────────────────────────────────────────────────────
    Route::get('/stadiums',           [StadiumApiController::class, 'index']);
    Route::get('/stadiums/{stadium}', [StadiumApiController::class, 'show']);

    Route::middleware('api.role:admin')->group(function () {
        Route::post('/stadiums',            [StadiumApiController::class, 'store']);
        Route::put('/stadiums/{stadium}',   [StadiumApiController::class, 'update']);
        Route::delete('/stadiums/{stadium}',[StadiumApiController::class, 'destroy']);
    });

    // ─── Users (all roles - admin panel) ─────────────────────────────────────
    Route::get('/users',        [UserApiController::class, 'index']);
    Route::get('/users/{user}', [UserApiController::class, 'show']);

    Route::middleware('api.role:admin')->group(function () {
        Route::post('/users',          [UserApiController::class, 'store']);
        Route::put('/users/{user}',    [UserApiController::class, 'update']);
        Route::delete('/users/{user}', [UserApiController::class, 'destroy']);
    });

    // Legacy /members aliases (backwards compat)
    Route::get('/members',        [PlayerApiController::class, 'index']);
    Route::get('/members/{user}', [PlayerApiController::class, 'show']);

    // ─── Payments ────────────────────────────────────────────────────────────
    Route::get('/payments', [PaymentApiController::class, 'index']);
    Route::post('/payments/pay',    [PaymentApiController::class, 'initiatePayment']);
    Route::post('/mpesa/stkpush',   [PaymentApiController::class, 'initiatePayment']);

    // ─── League Standings ────────────────────────────────────────────────────
    Route::get('/standings',          [LeagueApiController::class, 'standings']);
    Route::get('/standings/internal', [LeagueApiController::class, 'internalStandings']);

    // ─── Profile ─────────────────────────────────────────────────────────────
    Route::get('/profile',    [ProfileApiController::class, 'show']);
    Route::put('/profile',    [ProfileApiController::class, 'update']);
    Route::post('/profile/avatar', [ProfileApiController::class, 'updateAvatar']);

    // ─── Announcements / Notifications ───────────────────────────────────────
    Route::get('/notifications', [AnnouncementApiController::class, 'index']);
    Route::post('/notifications/{announcement}/read', [AnnouncementApiController::class, 'markRead']);

    // ─── Expenses ────────────────────────────────────────────────────────────
    Route::get('/expenses',  [ExpenseApiController::class, 'index']);
    Route::post('/expenses', [ExpenseApiController::class, 'store']);
});
