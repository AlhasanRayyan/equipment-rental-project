<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Booking;
use Faker\Factory as Faker;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Ensure bookings exist.
        if (Booking::count() === 0) {
            $this->call(BookingSeeder::class);
        }

        // Get all bookings that don't have an invoice yet.
        // هذه الـ query حاسمة لمنع التكرار.
        $bookingsWithoutInvoice = Booking::doesntHave('invoice')->get();

        foreach ($bookingsWithoutInvoice as $booking) {
            $status = $faker->randomElement(['issued', 'paid', 'overdue']);
            
            // إنشاء الفاتورة باستخدام state 'forBooking' لضمان الربط الصحيح
            Invoice::factory()
                ->forBooking($booking) // هذا يضمن ربط الفاتورة بالـ Booking الصحيح
                ->{$status}()          // تطبيق حالة الفاتورة (issued, paid, overdue)
                ->create();
        }

        $this->command->info('Invoices seeded!');
    }
}