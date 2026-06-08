<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@nongsanttc.local'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
