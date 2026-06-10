<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->where('image', 'like', 'images/store/%')
            ->update(['image' => null, 'gallery' => null]);

        DB::table('posts')
            ->where('image', 'like', 'images/store/%')
            ->update(['image' => null]);

        DB::table('settings')
            ->whereIn('key', ['hero_desktop', 'hero_mobile', 'about_main', 'about_small'])
            ->where('value', 'like', 'images/store/%')
            ->update(['value' => '']);
    }

    public function down(): void
    {
        // Không khôi phục path ảnh tĩnh cũ.
    }
};
