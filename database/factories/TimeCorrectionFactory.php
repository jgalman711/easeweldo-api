<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeCorrection>
 */
class TimeCorrectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = Employee::inRandomOrder()->first();
        return [
            'employee_id' => $employee->id,
            'company_id' => $employee->company_id,
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'clock_in' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'clock_out' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'remarks' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
