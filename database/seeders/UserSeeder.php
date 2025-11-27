<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// تأكد من استيراد Hash

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::factory()->admin()->create([
            'first_name'   => 'Admin',
            'last_name'    => 'User',
            'email'        => 'admin@example.com',
            'password'     => Hash::make('password'), // كلمة مرور قوية للمسؤول
            'phone_number' => '0599123456',
        ]);

        // Create some owner users
        User::factory(5)->user()->create();

        // Create some general users with random roles
        User::factory(15)->create();

        $this->command->info('Users seeded!');
    }
}
