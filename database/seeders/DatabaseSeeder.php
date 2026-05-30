<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::query()->firstOrCreate(
            ['email' => (string) env('ADMIN_PANEL_EMAIL', 'admin@santaeduviges.local')],
            [
                'name' => (string) env('ADMIN_PANEL_NAME', 'Administrador Parroquial'),
                'password' => (string) env('ADMIN_PANEL_PASSWORD', 'admin12345'),
                'is_admin' => true,
            ]
        );

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
