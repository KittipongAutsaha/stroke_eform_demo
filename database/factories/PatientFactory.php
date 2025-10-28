<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'hn'            => strtoupper('HN-' . fake()->unique()->numerify('########')),
            'cid'           => fake()->optional()->numerify('#############'),
            'first_name'    => fake()->firstName(),
            'last_name'     => fake()->lastName(),
            'dob'           => fake()->date('Y-m-d', '-10 years'),
            'sex'           => fake()->randomElement(['male', 'female', 'other', 'unknown']),
            'address_short' => fake()->optional()->city(),
            'note_general'  => fake()->optional()->sentence(),
            // ค่านี้จะถูก override ที่ Seeder อยู่แล้ว แต่ใส่กันพังไว้ก่อน:
            'created_by'    => 1,
            'updated_by'    => 1,
        ];
    }
}
