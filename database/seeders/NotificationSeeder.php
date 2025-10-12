<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use App\Models\Equipment;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure dependencies exist: users, bookings, equipment
        if (User::count() === 0) {
            $this->call(UserSeeder::class);
        }
        if (Booking::count() === 0) {
            $this->call(BookingSeeder::class);
        }
        if (Equipment::count() === 0) {
            $this->call(EquipmentSeeder::class);
        }

        $users = User::all();
        $bookings = Booking::all();
        $equipments = Equipment::all();

        // Create general notifications for various users
        foreach ($users as $user) {
            Notification::factory(rand(3, 8))->forUser($user)->create();
        }

        // Create specific booking-related notifications
        foreach ($bookings as $booking) {
            // Renter gets confirmation/cancellation notifications
            Notification::factory()->forUser($booking->renter)->forBooking($booking)->type('booking_confirmed')->create();
            if (rand(0,1)) {
                Notification::factory()->forUser($booking->renter)->forBooking($booking)->type('new_message')->unread()->create();
            }

            // Owner gets request/payment notifications
            Notification::factory()->forUser($booking->owner)->forBooking($booking)->type('booking_request')->unread()->create();
            Notification::factory()->forUser($booking->owner)->forBooking($booking)->type('payment_received')->create();
        }

        // Create some system alerts and expired notifications
        Notification::factory(5)->type('system_alert')->create();
        Notification::factory(3)->expired()->type('system_alert')->create();
        Notification::factory(2)->type('equipment_approved')->forUser(User::where('role', 'user')->inRandomOrder()->first())->create([
            'reference_id' => $equipments->random()->id,
        ]);


        $this->command->info('Notifications seeded!');
    }
}