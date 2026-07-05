<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['available', 'unavailable', 'maybe'])->default('unavailable');
            $table->boolean('is_locked')->default(false);
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('admin_override')->default(false);
            $table->timestamps();

            $table->unique(['match_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
