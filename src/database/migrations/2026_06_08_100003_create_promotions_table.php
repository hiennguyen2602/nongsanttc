<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedInteger('min_order')->default(0);
            $table->unsignedInteger('discount_amount')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->string('image_mobile')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('image_mobile');
        });

        Schema::dropIfExists('promotions');
    }
};
