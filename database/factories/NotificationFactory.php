<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure we have users
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $notificationTypes = [
            'booking_request', 'booking_confirmed', 'booking_cancelled',
            'payment_received', 'payment_failed', 'refund_issued',
            'new_message', 'equipment_approved', 'equipment_rejected',
            'review_received', 'system_alert'
        ];

        $type = $this->faker->randomElement($notificationTypes);
        $referenceId = null;

        // Try to link reference_id to relevant models based on type
        switch ($type) {
            case 'booking_request':
            case 'booking_confirmed':
            case 'booking_cancelled':
            case 'payment_received':
            case 'payment_failed':
            case 'refund_issued':
            case 'new_message':
            case 'review_received': // Reviews are often tied to bookings
                $booking = Booking::inRandomOrder()->first();
                if ($booking) {
                    $referenceId = $booking->id;
                }
                break;
            case 'equipment_approved':
            case 'equipment_rejected':
                $equipment = Equipment::inRandomOrder()->first();
                if ($equipment) {
                    $referenceId = $equipment->id;
                }
                break;
            default:
                // No specific reference for system_alert
                break;
        }


        return [
            'user_id' => $user->id,
            'type' => $type,
            'message' => $this->faker->sentence(8),
            'reference_id' => $referenceId,
            'is_read' => $this->faker->boolean(70),
            'expires_at' => $this->faker->optional(0.3)->dateTimeBetween('now', '+1 month'),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Indicate that the notification is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }

    /**
     * Indicate that the notification is not expired.
     */
    public function notExpired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->optional(0.5)->dateTimeBetween('now', '+1 year'),
        ]);
    }

    /**
     * Indicate a specific notification type.
     */
    public function type(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Attach notification to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Attach notification to a specific booking.
     */
    public function forBooking(Booking $booking): static
    {
        return $this->state(fn (array $attributes) => [
            'reference_id' => $booking->id,
            'type' => $this->faker->randomElement(['booking_request', 'booking_confirmed', 'booking_cancelled']),
        ]);
    }
}