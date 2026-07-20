<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nationality')->nullable()->default('Kenyan')->after('emergency_phone');
            $table->foreignId('league_team_id')->nullable()->after('jersey_number')
                  ->constrained('league_teams')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['league_team_id']);
            $table->dropColumn(['nationality', 'league_team_id']);
        });
    }
};
