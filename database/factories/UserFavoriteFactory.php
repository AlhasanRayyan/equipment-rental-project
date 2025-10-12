<?php

namespace Database\Factories;

use App\Models\UserFavorite;
use App\Models\User;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserFavorite>
 */
class UserFavoriteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserFavorite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure users and equipment exist
        // Now, we just need a general user, not specifically 'renter' or 'owner'
        do {
            $user = User::where('role', 'user')->inRandomOrder()->first() ?? User::factory()->regularUser()->create();
            $equipment = Equipment::inRandomOrder()->first() ?? Equipment::factory()->create();
        } while (UserFavorite::where('user_id', $user->id)->where('equipment_id', $equipment->id)->exists());


        return [
            'user_id' => $user->id,
            'equipment_id' => $equipment->id,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Attach favorite to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Attach favorite to a specific equipment.
     */
    public function forEquipment(Equipment $equipment): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_id' => $equipment->id,
        ]);
    }
}