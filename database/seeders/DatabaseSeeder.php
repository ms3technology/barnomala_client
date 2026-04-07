<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OptionsSeeder::class,
        ]);

        \App\Models\User::updateOrCreate(
            ['email' => env('CLIENT_ADMIN_EMAIL', 'admin@barnomala.com')],
            [
                'name' => 'System Admin',
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
            ]
        );
    }
}
