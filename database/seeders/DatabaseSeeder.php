<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // قم بتشغيل الـ Seeders بالترتيب الذي يضمن توفر البيانات الأساسية للعلاقات الخارجية.
        $this->call([
            UserSeeder::class,               // Users (owners, renters, admins) must exist first
            EquipmentCategorySeeder::class,  // Categories must exist before equipment
            EquipmentSeeder::class,          // Equipment must exist before images, bookings, tracking, favorites
            EquipmentImageSeeder::class,     // Images depend on Equipment
            BookingSeeder::class,            // Bookings depend on Users and Equipment
            PaymentSeeder::class,            // Payments depend on Bookings and Users
            InvoiceSeeder::class,            // Invoices depend on Bookings
            ReviewSeeder::class,             // Reviews depend on Bookings, Users, and Equipment
            MessageSeeder::class,            // Messages depend on Users (and optionally Bookings)
            NotificationSeeder::class,       // Notifications depend on Users (and optionally Bookings, Equipment)
            EquipmentTrackingSeeder::class,  // Equipment Tracking depends on Equipment
            UserFavoriteSeeder::class,       // User Favorites depend on Users and Equipment
        ]);
    }
}