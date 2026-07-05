<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['turf_hire', 'equipment', 'refreshments', 'transport', 'miscellaneous'])->default('miscellaneous');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->foreignId('paid_by')->constrained('users')->cascadeOnDelete();
            $table->date('expense_date');
            $table->string('receipt_url')->nullable();
            $table->foreignId('match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};