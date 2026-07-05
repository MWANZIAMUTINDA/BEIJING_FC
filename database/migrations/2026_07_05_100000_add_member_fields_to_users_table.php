<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('emergency_contact')->nullable()->after('avatar');
            $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
            $table->unsignedTinyInteger('jersey_number')->nullable()->after('emergency_phone');
            $table->date('date_joined')->nullable()->after('jersey_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['emergency_contact', 'emergency_phone', 'jersey_number', 'date_joined']);
        });
    }
};
