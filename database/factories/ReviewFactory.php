<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Booking;
use App\Models\User;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     * This will be overridden by states or explicit attributes from the seeder.
     * By default, it creates a new completed booking to ensure it's self-contained
     * if called without a specific booking.
     */
    public function definition(): array
    {
        // ننشئ Booking جديداً كاملاً كحل افتراضي. هذا سيتم تجاوزه بواسطة Seeder
        // عندما يحدد booking_id صراحةً. هذا يضمن أن Factory يمكنه العمل بشكل مستقل.
        // نستخدم createQuietly لتجنب تشغيل afterCreating callbacks الخاصة بالـ Booking
        // والتي قد تتسبب في مشاكل إذا كانت Bookings Factory معقدة.
        $booking = Booking::factory()->completed()->createQuietly();

        $reviewer = $booking->renter;
        $reviewedUser = $booking->owner;
        $equipment = $booking->equipment;

        $ratingOverall = $this->faker->numberBetween(1, 5);

        // ضمان أن تاريخ المراجعة والرد يكون بعد تاريخ انتهاء الحجز
        $effectiveEndDate = $booking->end_date->isPast() ? $booking->end_date : Carbon::now()->subDay();
        $reviewDate = Carbon::parse($this->faker->dateTimeBetween($effectiveEndDate, 'now'));
        $ownerResponseDate = $this->faker->optional(0.3)->dateTimeBetween($reviewDate, 'now');

        return [
            'booking_id' => $booking->id, // Default, will be overridden by forBooking state
            'reviewer_id' => $reviewer->id,
            'reviewed_user_id' => $reviewedUser->id,
            'equipment_id' => $equipment->id,
            'rating_overall' => $ratingOverall,
            'comment' => $this->faker->paragraph(),
            'owner_communication_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'equipment_condition_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'renter_punctuality_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'is_verified' => $this->faker->boolean(70),
            'owner_response' => $this->faker->optional(0.3)->paragraph(1),
            'owner_response_date' => $ownerResponseDate ? Carbon::parse($ownerResponseDate) : null,
            'created_at' => $reviewDate,
        ];
    }

    // دوال States (verified, withOwnerResponse, positive, negative)
    // تأكد من أن هذه الدوال موجودة في ملفك.

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => ['is_verified' => true]);
    }

    public function withOwnerResponse(): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_response' => $this->faker->paragraph(1),
            'owner_response_date' => now(),
        ]);
    }

    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating_overall' => $this->faker->numberBetween(4, 5),
            'owner_communication_rating' => $this->faker->numberBetween(4, 5),
            'equipment_condition_rating' => $this->faker->numberBetween(4, 5),
            'renter_punctuality_rating' => $this->faker->numberBetween(4, 5),
        ]);
    }

    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating_overall' => $this->faker->numberBetween(1, 2),
            'owner_communication_rating' => $this->faker->numberBetween(1, 2),
            'equipment_condition_rating' => $this->faker->numberBetween(1, 2),
            'renter_punctuality_rating' => $this->faker->numberBetween(1, 2),
        ]);
    }

    /**
     * Configure the review attributes for a given booking.
     * Use this state when you explicitly know which booking the review is for.
     */
    public function forBooking(Booking $booking): static
    {
        // ضمان أن تاريخ المراجعة والرد يكون بعد تاريخ انتهاء الحجز
        $effectiveEndDate = $booking->end_date->isPast() ? $booking->end_date : Carbon::now()->subDay();
        $reviewDate = Carbon::parse($this->faker->dateTimeBetween($effectiveEndDate, 'now'));

        return $this->state(fn (array $attributes) => [
            'booking_id' => $booking->id,
            'reviewer_id' => $booking->renter_id,
            'reviewed_user_id' => $booking->owner_id,
            'equipment_id' => $booking->equipment_id,
            'created_at' => $reviewDate, // override created_at if forBooking is used
        ]);
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Review $review) {
            // Update average ratings for equipment
            if ($review->equipment) {
                $review->equipment->update([
                    'average_rating' => $review->equipment->reviews()->avg('rating_overall') ?? 0.00,
                    'total_reviews' => $review->equipment->reviews()->count(),
                ]);
            }

            // Update average ratings for reviewedUser (owner)
            if ($review->reviewedUser && $review->reviewedUser->role === 'user') {
                $avgOwnerRating = $review->reviewedUser->receivedReviews()
                                                       ->where('reviewed_user_id', $review->reviewed_user_id)
                                                       ->avg('owner_communication_rating') ?? 0.00;
                $review->reviewedUser->update([
                    'average_owner_rating' => $avgOwnerRating,
                ]);
            }

            // Update average ratings for reviewer (renter)
            if ($review->reviewer && $review->reviewer->role === 'user') {
                $avgRenterRating = $review->reviewer->writtenReviews()
                                                    ->where('reviewer_id', $review->reviewer_id)
                                                    ->avg('renter_punctuality_rating') ?? 0.00;
                $review->reviewer->update([
                    'average_renter_rating' => $avgRenterRating,
                ]);
            }
        });
    }
}