<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash; // تأكد من استيراد Hash

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone_number' => $this->faker->unique()->phoneNumber(),
            'password' => Hash::make('password'), // كلمة مرور افتراضية
            'role' => $this->faker->randomElement(['user', 'admin']), 
            'profile_picture_url' => $this->faker->imageUrl(640, 480, 'people', true),
            'description' => $this->faker->paragraph(),
            'location_text' => $this->faker->address(),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'last_login' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'), // 80% chance of last login
            'average_owner_rating' => $this->faker->randomFloat(2, 1, 5),
            'average_renter_rating' => $this->faker->randomFloat(2, 1, 5),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is an owner.
     */
    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
        ]);
    }

}