<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'slug' => $this->faker->unique()->slug,
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
            'details' => $this->faker->sentence,
            'logo' => $this->faker->imageUrl(),
            'legal_name' => $this->faker->company,
            'address_line' => $this->faker->address,
            'barangay_town_city_province' => $this->faker->city . ', ' . $this->faker->state . ', ' . $this->faker->country,
            'contact_name' => $this->faker->name,
            'email_address' => $this->faker->unique()->safeEmail,
            'mobile_number' => $this->faker->phoneNumber,
            'landline_number' => $this->faker->phoneNumber,
            'bank_name' => $this->faker->randomElement(['Bank of America', 'Chase Bank', 'Wells Fargo']),
            'bank_account_name' => $this->faker->name,
            'bank_account_number' => $this->faker->bankAccountNumber,
            'tin' => $this->faker->numerify('#########'),
            'sss_number' => $this->faker->numerify('#########'),
            'philhealth_number' => $this->faker->numerify('#########'),
            'pagibig_number' => $this->faker->numerify('#########'),
        ];
    }
}
