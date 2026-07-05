<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // MySQL: alter enum column to include new roles
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','treasurer','coach','member') NOT NULL DEFAULT 'member'");
        }

        // Ensure email_verified_at exists
        if (!Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Revert any treasurer/coach back to member before narrowing enum
            DB::statement("UPDATE users SET role = 'member' WHERE role IN ('treasurer','coach')");
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','member') NOT NULL DEFAULT 'member'");
        }
    }
};
