<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('total_owed', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0)->comment('positive = credit, negative = debt');
            $table->unsignedSmallInteger('matches_paid')->default(0);
            $table->unsignedSmallInteger('months_paid')->default(0);
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_balances');
    }
};
