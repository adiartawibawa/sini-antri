<?php

namespace Database\Seeders;

use App\Models\QueueSetting;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Pengaturan antrian default
        QueueSetting::create([
            'prefix' => env('QUEUE_PREFIX', 'A'),
            'avg_service_minutes' => 5,
            'reset_daily' => true,
            'current_counter' => 0,
            'last_reset_date' => today(),
        ]);

        // Operator / Loket default
        User::create([
            'name' => 'Operator 1',
            'email' => 'loket1@antrian.test',
            'password' => 'password',
            'loket_name' => 'Loket 1',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Operator 2',
            'email' => 'loket2@antrian.test',
            'password' => 'password',
            'loket_name' => 'Loket 2',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Operator 3',
            'email' => 'loket3@antrian.test',
            'password' => 'password',
            'loket_name' => 'Loket 3',
            'is_active' => true,
        ]);

        $this->command->info('✅ Seed selesai!');
        $this->command->info('   Login: loket1@antrian.test / password');
        $this->command->info('   Login: loket2@antrian.test / password');
        $this->command->info('   Login: loket3@antrian.test / password');
    }
}
