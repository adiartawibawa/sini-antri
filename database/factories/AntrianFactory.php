<?php

namespace Database\Factories;

use App\Models\Antrian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Antrian>
 */
class AntrianFactory extends Factory
{
    protected $model = Antrian::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['waiting', 'called', 'serving', 'completed', 'skipped']);
        $hasOperator = in_array($status, ['called', 'serving', 'completed', 'skipped']);

        // Randomize dates within the last 7 days
        $createdAt = $this->faker->dateTimeBetween('-7 days', 'now');
        $calledAt = $hasOperator ? (clone $createdAt)->modify('+'.rand(5, 30).' minutes') : null;
        $servedAt = in_array($status, ['serving', 'completed']) ? (clone $calledAt)->modify('+'.rand(1, 5).' minutes') : null;
        $completedAt = $status === 'completed' ? (clone $servedAt)->modify('+'.rand(5, 20).' minutes') : null;

        return [
            'uuid' => (string) Str::uuid(),
            'queue_number' => $this->faker->bothify('A###'),
            'queue_order' => $this->faker->unique()->numberBetween(1, 10000),
            'visitor_name' => $this->faker->name(),
            'purpose' => $this->faker->randomElement(['Pendaftaran', 'Konsultasi', 'Pengambilan Obat', 'Administrasi', 'Lainnya']),
            'status' => $status,
            'operator_id' => $hasOperator ? User::where('is_operator', true)->inRandomOrder()->first()?->id : null,
            'called_at' => $calledAt,
            'served_at' => $servedAt,
            'completed_at' => $completedAt,
            'created_at' => $createdAt,
            'updated_at' => $completedAt ?: ($servedAt ?: ($calledAt ?: $createdAt)),
        ];
    }
}
