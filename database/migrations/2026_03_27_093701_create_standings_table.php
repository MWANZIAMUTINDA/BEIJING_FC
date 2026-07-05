<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_team_id')->constrained('league_teams')->cascadeOnDelete();
            $table->string('season')->default('2025/2026');
            $table->unsignedSmallInteger('played')->default(0);
            $table->unsignedSmallInteger('wins')->default(0);
            $table->unsignedSmallInteger('draws')->default(0);
            $table->unsignedSmallInteger('losses')->default(0);
            $table->unsignedSmallInteger('goals_for')->default(0);
            $table->unsignedSmallInteger('goals_against')->default(0);
            $table->smallInteger('goal_difference')->default(0);
            $table->unsignedSmallInteger('points')->default(0);
            $table->timestamps();

            $table->unique(['league_team_id', 'season']);
        });

        Schema::create('league_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('home_team_id')->constrained('league_teams');
            $table->foreignId('away_team_id')->constrained('league_teams');
            $table->unsignedTinyInteger('home_score');
            $table->unsignedTinyInteger('away_score');
            $table->enum('result', ['home_win', 'away_win', 'draw']);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('league_results');
        Schema::dropIfExists('standings');
    }
};
