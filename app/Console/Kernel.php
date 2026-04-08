<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Booking;
use App\Services\NotificationService;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('firebase:fetch:gps')->everyFiveMinutes();
        $schedule->command('firebase:fetch:imu')->everyFiveMinutes();

        // إشعار اقتراب موعد الحجز (قبل يوم)
        $schedule->call(function () {

            $bookings = Booking::with('equipment')
                ->where('booking_status', 'confirmed')
                ->whereDate('start_date', now()->addDay())
                ->whereNull('starting_soon_notified_at')
                ->get();

            foreach ($bookings as $booking) {
                NotificationService::bookingStartingSoon($booking, $booking->equipment);

                $booking->update([
                    'starting_soon_notified_at' => now()
                ]);
            }
        })->daily();
        // php artisan schedule:work
        //  لازم اشغل هذا الامر لما بدي الاشعار هذا يشتغل

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}