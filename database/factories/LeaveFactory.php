<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leave>
 */
class LeaveFactory extends Factory
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
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'created_by' => $employee->user->id,
            'title' => $this->faker->sentence,
            'type' => $this->faker->randomElement(['sick_leave','vacation_leave','emergency_leave','leave_without_pay']),
            'description' => $this->faker->sentence,
            'hours' => $this->faker->randomFloat(2, 1, 8),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'submitted_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'remarks' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['submitted','approved','declined','discarded']),
        ];
    }
}
