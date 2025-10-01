<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure we have at least two users (sender and receiver)
        $sender = User::inRandomOrder()->first() ?? User::factory()->create();
        $receiver = User::where('id', '!=', $sender->id)->inRandomOrder()->first() ?? User::factory()->create();

        // Optionally associate with a booking
        $booking = $this->faker->boolean(50) ? (Booking::inRandomOrder()->first() ?? Booking::factory()->create()) : null;

        return [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'booking_id' => $booking?->id, // Nullable booking_id
            'content' => $this->faker->sentence($this->faker->numberBetween(5, 20)),
            // تم التعديل هنا: استخدام قيم enum الجديدة
            'message_type' => $this->faker->randomElement(['complaint', 'inquiry', 'notification']),
            'attachment_url' => $this->faker->optional(0.1)->imageUrl(), // 10% chance of an attachment
            'is_read' => $this->faker->boolean(70), // 70% chance of being read
            'is_resolved' => $this->faker->boolean(30), // تم إضافة حقل is_resolved
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Indicate that the message is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Indicate that the message is related to a booking.
     */
    public function forBooking(Booking $booking): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_id' => $booking->id,
        ]);
    }

    /**
     * Indicate a specific sender.
     */
    public function from(User $sender): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_id' => $sender->id,
        ]);
    }

    /**
     * Indicate a specific receiver.
     */
    public function to(User $receiver): static
    {
        return $this->state(fn (array $attributes) => [
            'receiver_id' => $receiver->id,
        ]);
    }

    /**
     * Indicate that the message is a complaint.
     */
    public function complaint(): static
    {
        return $this->state(fn (array $attributes) => [
            'message_type' => 'complaint',
        ]);
    }

    /**
     * Indicate that the message is an inquiry.
     */
    public function inquiry(): static
    {
        return $this->state(fn (array $attributes) => [
            'message_type' => 'inquiry',
        ]);
    }

    /**
     * Indicate that the message is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_resolved' => true,
            'is_read' => true, // عادة تكون محلولة ومقروءة
        ]);
    }
}