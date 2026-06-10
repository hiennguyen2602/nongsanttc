<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::query()->updateOrCreate(
            ['key' => 'company_name'],
            [
                'group' => 'contact',
                'value' => 'Công ty TNHH Nông Sản TTC',
                'type' => 'text',
                'label' => 'Tên công ty',
            ],
        );
    }

    public function down(): void
    {
        Setting::query()->where('key', 'company_name')->delete();
    }
};
