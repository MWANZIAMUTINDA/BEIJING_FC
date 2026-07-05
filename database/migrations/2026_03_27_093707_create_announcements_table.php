<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->enum('type', ['general', 'match_reminder', 'payment_alert', 'league_update', 'urgent'])->default('general');
            $table->boolean('send_sms')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->unique(['announcement_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
        Schema::dropIfExists('announcements');
    }
};
