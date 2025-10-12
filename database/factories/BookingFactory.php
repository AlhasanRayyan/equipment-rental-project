<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $equipment = Equipment::inRandomOrder()->first() ?? Equipment::factory()->create();
        $renter = User::where('role', 'user')->inRandomOrder()->first() ?? User::factory()->regularUser()->create();
        $startDate = Carbon::parse($this->faker->dateTimeBetween('-3 months', '+3 months'));
        $endDate = (clone $startDate)->addDays($this->faker->numberBetween(1, 30));
        $rentalDurationDays = $startDate->diffInDays($endDate);

        $rateType = $this->faker->randomElement(['daily', 'weekly', 'monthly']);
        $rate = $equipment->getRateByType($rateType) ?? $equipment->daily_rate;

        $totalCost = $rate * $rentalDurationDays;
        if ($rateType === 'weekly' && $rentalDurationDays >= 7 && $equipment->weekly_rate) {
            $totalCost = floor($rentalDurationDays / 7) * $equipment->weekly_rate + ($rentalDurationDays % 7) * $equipment->daily_rate;
        } elseif ($rateType === 'monthly' && $rentalDurationDays >= 30 && $equipment->monthly_rate) {
            $totalCost = floor($rentalDurationDays / 30) * $equipment->monthly_rate + ($rentalDurationDays % 30) * $equipment->daily_rate;
        }


        return [
            'equipment_id' => $equipment->id,
            'renter_id' => $renter->id,
            'owner_id' => $equipment->owner_id, // Get owner from the selected equipment
            'start_date' => $startDate,
            'end_date' => $endDate,
            'rental_duration_days' => $rentalDurationDays,
            'rental_rate_type' => $rateType,
            'total_cost' => $totalCost,
            'deposit_amount_paid' => $equipment->deposit_amount,
            'payment_status' => $this->faker->randomElement(['pending', 'paid']),
            'booking_status' => $this->faker->randomElement(['pending', 'confirmed', 'active', 'completed', 'cancelled']),
            'pickup_location' => $this->faker->address(),
            'return_location' => $this->faker->address(),
            'contract_url' => $this->faker->optional()->url(),
            'special_requirements' => $this->faker->optional()->sentence(),
            'confirmed_at' => $this->faker->optional(0.7)->dateTimeBetween('-2 months', 'now'),
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'booking_status' => 'pending',
            'confirmed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn(array $attributes) => [
            'booking_status' => 'confirmed',
            'confirmed_at' => now(),
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ]);
    }

    /**
     * Indicate that the booking is active.
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = Carbon::now()->subDays($this->faker->numberBetween(1, 10));
            $endDate = (clone $startDate)->addDays($this->faker->numberBetween(5, 20));
            return [
                'booking_status' => 'active',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'rental_duration_days' => $startDate->diffInDays($endDate),
                'confirmed_at' => now()->subDays($this->faker->numberBetween(11, 20)),
                'cancelled_at' => null,
                'cancellation_reason' => null,
            ];
        });
    }

    /**
     * Indicate that the booking is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $endDate = Carbon::now()->subDays($this->faker->numberBetween(1, 30));
            $startDate = (clone $endDate)->subDays($this->faker->numberBetween(1, 30));
            return [
                'booking_status' => 'completed',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'rental_duration_days' => $startDate->diffInDays($endDate),
                'confirmed_at' => $startDate->subDays(rand(1, 5)),
                'cancelled_at' => null,
                'cancellation_reason' => null,
            ];
        });
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'booking_status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the payment is paid.
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Booking $booking) {
            // Update equipment status if booking is active
            if ($booking->booking_status === 'active' || $booking->booking_status === 'confirmed') {
                $booking->equipment->update(['status' => 'rented']);
            } elseif ($booking->booking_status === 'completed' || $booking->booking_status === 'cancelled') {
                $booking->equipment->update(['status' => 'available']);
            }
        });
    }
}
