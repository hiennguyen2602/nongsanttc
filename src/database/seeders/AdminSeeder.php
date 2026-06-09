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
                'name' => 'Administrator',
                'password' => 'password',
                'type' => User::TYPE_ADMIN,
                'status' => 1,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'staff@nongsanttc.com'],
            [
                'name' => 'Staff',
                'password' => 'password',
                'type' => User::TYPE_STAFF,
                'status' => 1,
                'email_verified_at' => now(),
            ],
        );
    }
}
