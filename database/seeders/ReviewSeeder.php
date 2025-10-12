<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Booking;
use App\Models\User;
use App\Models\Equipment;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure dependencies exist: users, equipment, completed bookings
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }
        if (Equipment::count() === 0) {
            $this->call(EquipmentSeeder::class);
        }
        if (Booking::completed()->count() < 10) {
            // If not enough completed bookings, create more.
            // Factory for Booking will handle creating users/equipment if needed.
            Booking::factory(10)->completed()->create();
        }

        // Get all bookings that are completed and do not yet have a review.
        $eligibleBookings = Booking::completed()->doesntHave('review')->get();

        // Create reviews for a portion of eligible bookings
        // Limit the number to prevent too many, and allow some for specific states later
        $reviewsToCreate = min($eligibleBookings->count(), 20); // Create up to 20 general reviews

        foreach ($eligibleBookings->take($reviewsToCreate) as $booking) {
            Review::factory()->forBooking($booking)->create();
        }

        // --- Create reviews with specific states for *additional* eligible bookings ---
        // Fetch more eligible bookings if available, or create new ones if needed for variety
        $remainingEligibleBookings = Booking::completed()->doesntHave('review')->inRandomOrder()->take(10)->get();

        // Create positive reviews with owner responses
        if ($remainingEligibleBookings->count() >= 5) {
            foreach ($remainingEligibleBookings->take(5) as $booking) {
                Review::factory()->forBooking($booking)->withOwnerResponse()->verified()->positive()->create();
            }
            $remainingEligibleBookings = $remainingEligibleBookings->splice(5); // Remove used bookings
        } else {
            // If not enough existing bookings, create new ones to ensure specific review types exist
            Review::factory(5)->positive()->withOwnerResponse()->verified()->create();
        }

        // Create negative reviews
        if ($remainingEligibleBookings->count() >= 3) {
            foreach ($remainingEligibleBookings->take(3) as $booking) {
                Review::factory()->forBooking($booking)->negative()->create();
            }
        } else {
            Review::factory(3)->negative()->create();
        }

        $this->command->info('Reviews seeded!');
    }
}