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
        // Pengaturan antrian default
        QueueSetting::firstOrCreate([], [
            'prefix' => env('QUEUE_PREFIX', 'A'),
            'avg_service_minutes' => 5,
            'reset_daily' => true,
            'current_counter' => 0,
            'last_reset_date' => today(),
        ]);

        // Admin default
        User::updateOrCreate(['email' => 'admin@antri.test'], [
            'name' => 'Administrator',
            'email' => 'admin@antri.test',
            'password' => 'admin123',
            'loket_name' => 'Admin Panel',
            'is_active' => true,
            'is_operator' => false, // false = admin
        ]);

        // Operator / Loket default
        $operators = [
            [
                'name' => 'Operator 1',
                'email' => 'loket1@antrian.test',
                'password' => 'password',
                'loket_name' => 'Loket 1',
                'is_active' => true,
                'is_operator' => true,
            ],
            [
                'name' => 'Operator 2',
                'email' => 'loket2@antrian.test',
                'password' => 'password',
                'loket_name' => 'Loket 2',
                'is_active' => true,
                'is_operator' => true,
            ],
            [
                'name' => 'Operator 3',
                'email' => 'loket3@antrian.test',
                'password' => 'password',
                'loket_name' => 'Loket 3',
                'is_active' => true,
                'is_operator' => true,
            ],
        ];

        foreach ($operators as $operator) {
            User::updateOrCreate(['email' => $operator['email']], $operator);
        }

        // Dummy data antrian
        $this->call(AntrianSeeder::class);

        $this->command->info('✅ Seed selesai!');
        $this->command->info('   Login: loket1@antrian.test / password');
        $this->command->info('   Login: loket2@antrian.test / password');
        $this->command->info('   Login: loket3@antrian.test / password');
    }
}
