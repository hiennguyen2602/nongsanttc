<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndex('users', 'users_type_index', fn (Blueprint $table) => $table->index('type', 'users_type_index'));

        $this->addIndex('products', 'products_active_featured_index', fn (Blueprint $table) => $table->index(['is_active', 'is_featured'], 'products_active_featured_index'));
        $this->addIndex('products', 'products_active_category_index', fn (Blueprint $table) => $table->index(['is_active', 'category_id'], 'products_active_category_index'));
        $this->addIndex('products', 'products_updated_at_index', fn (Blueprint $table) => $table->index('updated_at', 'products_updated_at_index'));

        $this->addIndex('posts', 'posts_published_at_index', fn (Blueprint $table) => $table->index(['is_published', 'published_at'], 'posts_published_at_index'));

        $this->addIndex('banners', 'banners_active_position_sort_index', fn (Blueprint $table) => $table->index(['is_active', 'position', 'sort_order'], 'banners_active_position_sort_index'));

        $this->addIndex('orders', 'orders_status_index', fn (Blueprint $table) => $table->index('status', 'orders_status_index'));
        $this->addIndex('orders', 'orders_created_at_index', fn (Blueprint $table) => $table->index('created_at', 'orders_created_at_index'));
        $this->addIndex('orders', 'orders_status_created_at_index', fn (Blueprint $table) => $table->index(['status', 'created_at'], 'orders_status_created_at_index'));
        $this->addIndex('orders', 'orders_viewed_at_index', fn (Blueprint $table) => $table->index('viewed_at', 'orders_viewed_at_index'));

        $this->addIndex('promotions', 'promotions_is_active_index', fn (Blueprint $table) => $table->index('is_active', 'promotions_is_active_index'));

        $this->addIndex('contact_messages', 'contact_messages_viewed_at_index', fn (Blueprint $table) => $table->index('viewed_at', 'contact_messages_viewed_at_index'));
        $this->addIndex('contact_messages', 'contact_messages_created_at_index', fn (Blueprint $table) => $table->index('created_at', 'contact_messages_created_at_index'));
    }

    public function down(): void
    {
        $this->dropIndex('users', 'users_type_index');

        $this->dropIndex('products', 'products_active_featured_index');
        $this->dropIndex('products', 'products_active_category_index');
        $this->dropIndex('products', 'products_updated_at_index');

        $this->dropIndex('posts', 'posts_published_at_index');

        $this->dropIndex('banners', 'banners_active_position_sort_index');

        $this->dropIndex('orders', 'orders_status_index');
        $this->dropIndex('orders', 'orders_created_at_index');
        $this->dropIndex('orders', 'orders_status_created_at_index');
        $this->dropIndex('orders', 'orders_viewed_at_index');

        $this->dropIndex('promotions', 'promotions_is_active_index');

        $this->dropIndex('contact_messages', 'contact_messages_viewed_at_index');
        $this->dropIndex('contact_messages', 'contact_messages_created_at_index');
    }

    private function addIndex(string $table, string $indexName, callable $callback): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, $callback);
    }

    private function dropIndex(string $table, string $indexName): void
    {
        if (! $this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, fn (Blueprint $table) => $table->dropIndex($indexName));
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
