<?php

namespace Database\Factories;

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminSetting>
 */
class AdminSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure there is at least one admin user to link to updated_by
        $adminUser = User::where('role', 'admin')->inRandomOrder()->first() ?? User::factory()->admin()->create();

        return [
            'setting_key' => $this->faker->unique()->slug(3, false) . '_' . Str::random(5), // Generate a unique key
            'setting_value' => $this->faker->sentence(3),
            'description' => $this->faker->optional(0.7)->paragraph(1),
            'updated_by' => $adminUser->id, // Always link to an admin user
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate a specific setting key and value for common settings.
     */
    public function taxRate(): static
    {
        return $this->state(fn (array $attributes) => [
            'setting_key' => 'tax_rate_percent',
            'setting_value' => $this->faker->randomFloat(2, 5, 20), // e.g., 15.00
            'description' => 'Global tax rate applied to rental costs.',
        ]);
    }

    public function contactEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'setting_key' => 'contact_email',
            'setting_value' => 'support@' . $this->faker->domainName(),
            'description' => 'Main contact email for customer support.',
        ]);
    }

    public function minRentalDays(): static
    {
        return $this->state(fn (array $attributes) => [
            'setting_key' => 'minimum_rental_days',
            'setting_value' => $this->faker->numberBetween(1, 3),
            'description' => 'Minimum number of days for an equipment rental.',
        ]);
    }

    public function maintenanceMode(): static
    {
        return $this->state(fn (array $attributes) => [
            'setting_key' => 'maintenance_mode',
            'setting_value' => $this->faker->boolean() ? 'true' : 'false', // قيمة نصية 'true'/'false'
            'description' => 'Set to true to put the application in maintenance mode.',
        ]);
    }

    public function termsAndConditionsUrl(): static
    {
        return $this->state(fn (array $attributes) => [
            'setting_key' => 'terms_and_conditions_url',
            'setting_value' => $this->faker->url(),
            'description' => 'URL to the terms and conditions page.',
        ]);
    }
}