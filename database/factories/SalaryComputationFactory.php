<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalaryComputation>
 */
class SalaryComputationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'employee_id' => function () {
                return \App\Models\Employee::factory()->create()->id;
            },
            'basic_salary' => $this->faker->numberBetween(10000, 50000),
            'hourly_rate' => $this->faker->numberBetween(50, 90),
            'daily_rate' => $this->faker->numberBetween(500, 700),
            'working_hours_per_day' => $this->faker->numberBetween(6, 10),
            'break_hours_per_day' => $this->faker->numberBetween(0, 1),
            'working_days_per_week' => $this->faker->numberBetween(5, 6),
            'overtime_rate' => $this->faker->numberBetween(20, 50),
            'night_diff_rate' => $this->faker->numberBetween(10, 30),
            'regular_holiday_rate' => $this->faker->numberBetween(100, 200),
            'special_holiday_rate' => $this->faker->numberBetween(50, 100),
            'total_sick_leave_hours' => $this->faker->numberBetween(0, 40),
            'total_vacation_leave_hours' => $this->faker->numberBetween(0, 80),
            'available_sick_leave_hours' => $this->faker->numberBetween(0, 40),
            'available_vacation_leave_hours' => $this->faker->numberBetween(0, 80),
        ];
    }
}
