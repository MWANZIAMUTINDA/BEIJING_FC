<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('league_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name', 5);
            $table->string('color', 7)->default('#000000');
            $table->string('kit_color', 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('match_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('team_label');
            $table->json('players_list')->nullable()->comment('Array of user_ids');
            $table->unsignedTinyInteger('position_balance_score')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_teams');
        Schema::dropIfExists('league_teams');
    }
};