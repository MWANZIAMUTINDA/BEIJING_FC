<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('internal_match_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_result_id')->constrained('league_results')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['goal', 'assist']);
            $table->unsignedTinyInteger('minute')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_match_goals');
    }
};
