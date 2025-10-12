<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\User;
use Faker\Factory as Faker; // تأكد أن هذا السطر موجود هنا

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تهيئة Faker للاستخدام داخل Seeder
        $faker = Faker::create(); // وتأكد أن هذا السطر موجود هنا

        // Ensure dependencies exist before creating payments
        if (Booking::count() === 0) {
            $this->call(BookingSeeder::class);
        }
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }

        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            // Create a main payment for each booking
            Payment::factory()->completed()->create([
                'booking_id' => $booking->id,
                'user_id' => $booking->renter_id,
                'amount' => $booking->total_cost + $booking->deposit_amount_paid,
                'payment_type' => 'initial_payment',
            ]);

            // Add some partial or failed payments randomly
            if (rand(0, 1)) { // 50% chance of an additional payment event
                // استخدام $faker الذي قمنا بتهيئته
                $type = $faker->randomElement(['pending', 'failed', 'refund']); // وتأكد أنك تستخدم $faker هنا
                Payment::factory()->{$type}()->create([
                    'booking_id' => $booking->id,
                    'user_id' => $booking->renter_id,
                ]);
            }
        }

        $this->command->info('Payments seeded!');
    }
}