<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure there is at least one booking and a user
        $booking = Booking::inRandomOrder()->first() ?? Booking::factory()->create();
        $user = $booking->renter; // Payment is usually made by the renter for their booking

        $paymentAmount = $booking->total_cost + $booking->deposit_amount_paid;

        return [
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'amount' => $this->faker->randomFloat(2, 50, $paymentAmount > 100 ? $paymentAmount * 1.2 : 2000),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'transaction_id' => Str::uuid(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'payment_type' => $this->faker->randomElement(['initial_payment', 'deposit', 'final_payment', 'refund']),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween($booking->created_at, 'now'),
        ];
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'transaction_id' => Str::uuid(),
        ]);
    }

    /**
     * Indicate that the payment failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'notes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the payment is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'notes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate an initial payment.
     */
    public function initial(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'initial_payment',
        ]);
    }

    /**
     * Indicate a deposit payment.
     */
    public function deposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'deposit',
        ]);
    }

    /**
     * Indicate a final payment.
     */
    public function final(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'final_payment',
        ]);
    }

    /**
     * Indicate a refund payment.
     */
    public function refund(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'refund',
            'amount' => $this->faker->randomFloat(2, -500, -50),
        ]);
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Payment $payment) {
            if ($payment->booking) {
                if ($payment->isSuccessful() && $payment->payment_type !== 'refund') {
                    $payment->booking->update(['payment_status' => 'paid']);
                } elseif ($payment->isFailed()) {
                    $payment->booking->update(['payment_status' => 'failed']);
                }
            }
        });
    }
}