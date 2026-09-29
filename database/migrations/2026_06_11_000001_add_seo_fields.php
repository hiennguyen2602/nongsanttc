<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration bổ sung — cột đã có trong 2026_06_08_000001_create_store_tables.php.
 * Giữ file + check tồn tại cho DB đã migrate trước khi gộp vào bảng gốc.
 * Trước production: squash migrations (xem docs/development.md).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'meta_title')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('meta_title')->nullable()->after('description');
            });
        }

        if (! Schema::hasColumn('products', 'meta_description')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('meta_description', 320)->nullable()->after('meta_title');
            });
        }

        if (! Schema::hasColumn('posts', 'meta_title')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('meta_title')->nullable()->after('title');
            });
        }

        if (! Schema::hasColumn('posts', 'meta_description')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('meta_description', 320)->nullable()->after('meta_title');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('products', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('posts', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });
    }
};
