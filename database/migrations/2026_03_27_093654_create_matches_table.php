<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('type', ['league', 'friendly'])->default('friendly');
            $table->date('match_date');
            $table->time('match_time');
            $table->string('venue')->default('Kasarani Astroturf');
            $table->dateTime('deadline')->comment('Availability confirmation deadline');
            $table->enum('status', ['upcoming', 'open', 'locked', 'completed'])->default('upcoming');
            $table->decimal('match_fee', 10, 2)->default(350.00);
            $table->string('home_team')->nullable();
            $table->string('away_team')->nullable();
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->text('notes')->nullable();

            // 🔥 FIX: match users.id type safely
            $table->unsignedBigInteger('created_by');

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};