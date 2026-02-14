<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::factory()
            ->admin()
            ->create([
                'username' => 'admin',
                'email' => 'admin@example.com',
                'description' => 'Default administrator',
            ]);

        $this->command->info('Admin password: ' . env('ADMIN_PASSWORD', '[random generated]'));

        User::factory(30)->create();

        $this->call(SongsTableSeeder::class);
    }
}
