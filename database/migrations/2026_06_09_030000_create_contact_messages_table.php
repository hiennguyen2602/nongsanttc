<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->index('viewed_at', 'contact_messages_viewed_at_index');
            $table->index('created_at', 'contact_messages_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
