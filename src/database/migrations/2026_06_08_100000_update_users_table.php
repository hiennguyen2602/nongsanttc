<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('type')->default(3)->after('password')->comment('1: Admin, 2: Staff, 3: User');
            $table->tinyInteger('status')->default(1)->after('type')->comment('1: Active, 0: Inactive');
        });

        if (Schema::hasColumn('users', 'is_admin')) {
            DB::table('users')->where('is_admin', true)->update(['type' => 1]);

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        DB::table('users')->where('type', 1)->update(['is_admin' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['type', 'status']);
        });
    }
};
