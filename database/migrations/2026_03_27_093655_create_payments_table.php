<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['monthly', 'match', 'partial', 'penalty'])->default('monthly');
            $table->enum('status', ['pending', 'confirmed', 'failed', 'reversed'])->default('pending');
            $table->string('mpesa_code', 20)->nullable()->unique();
            $table->string('phone', 20);
            $table->string('mpesa_receipt_number', 30)->nullable();
            $table->string('transaction_date')->nullable();
            $table->decimal('balance_after', 10, 2)->default(0);
            $table->foreignId('match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};