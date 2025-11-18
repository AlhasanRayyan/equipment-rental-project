<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User;
use App\Models\Booking;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Message::truncate();
        // ما في بيانات افتراضية لجدول الرسائل
        // راح تنضاف فقط الرسائل الحقيقية من الموقع
        // $this->command->info('Messages seeder skipped (no fake data).');
    }

    // public function run(): void
    // {
    //     // Ensure dependencies exist: users, bookings
    //     if (User::count() < 2) {
    //         $this->call(UserSeeder::class);
    //     }
    //     if (Booking::count() === 0) {
    //         $this->call(BookingSeeder::class);
    //     }

    //     $users = User::all();
    //     $bookings = Booking::all();

    //     // Create general messages between random users
    //     for ($i = 0; $i < 20; $i++) {
    //         $sender = $users->random();
    //         $receiver = $users->except($sender->id)->random();
    //         Message::factory()->from($sender)->to($receiver)->create();
    //     }

    //     // Create messages related to bookings
    //     foreach ($bookings as $booking) {
    //         $renter = $booking->renter;
    //         $owner = $booking->owner;

    //         // Conversation between renter and owner about the booking
    //         $messagesPerBooking = rand(2, 5);
    //         for ($i = 0; $i < $messagesPerBooking; $i++) {
    //             $sender = ($i % 2 === 0) ? $renter : $owner;
    //             $receiver = ($i % 2 === 0) ? $owner : $renter;
    //             Message::factory()->from($sender)->to($receiver)->forBooking($booking)->create();
    //         }

    //         // Create some unread messages
    //         if (rand(0, 1)) {
    //              Message::factory()->from($owner)->to($renter)->forBooking($booking)->unread()->create();
    //         }
    //     }

    //     $this->command->info('Messages seeded!');
    // }
}

