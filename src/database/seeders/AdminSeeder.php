<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@nongsanttc.com'],
            [
                'name' => 'Nông Sản TTC',
                'password' => 'Aa123456!',
                'type' => User::TYPE_ADMIN,
                'status' => 1,
                'email_verified_at' => now(),
            ],
        );
    }
}
