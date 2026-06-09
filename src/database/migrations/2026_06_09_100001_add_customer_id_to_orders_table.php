<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('order_code')->constrained()->nullOnDelete();
        });

        $phoneMap = [];

        foreach (DB::table('orders')->orderBy('id')->get() as $order) {
            $phone = preg_replace('/\s+/', '', (string) $order->customer_phone);

            if ($phone === '') {
                continue;
            }

            if (! isset($phoneMap[$phone])) {
                $phoneMap[$phone] = DB::table('customers')->insertGetId([
                    'name' => $order->customer_name,
                    'phone' => $phone,
                    'email' => $order->customer_email,
                    'address' => $order->customer_address,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);
            } else {
                DB::table('customers')->where('id', $phoneMap[$phone])->update([
                    'name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'address' => $order->customer_address,
                    'updated_at' => $order->updated_at,
                ]);
            }

            DB::table('orders')->where('id', $order->id)->update([
                'customer_id' => $phoneMap[$phone],
                'customer_phone' => $phone,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
