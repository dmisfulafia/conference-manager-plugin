<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Super Admin
        User::create([
            'title' => 'Prof.',
            'name' => 'FULafia Super Admin',
            'email' => 'superadmin@fulafia.edu.ng',
            'phone' => '08012345678',
            'gender' => 'Male',
            'occupation' => 'Professor of Computer Science',
            'institution' => 'Federal University of Lafia',
            'country' => 'Nigeria',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // 2. Regular Admin
        User::create([
            'title' => 'Dr.',
            'name' => 'FULafia Admin',
            'email' => 'admin@fulafia.edu.ng',
            'phone' => '08087654321',
            'gender' => 'Female',
            'occupation' => 'Senior Lecturer',
            'institution' => 'Federal University of Lafia',
            'country' => 'Nigeria',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 3. Regular Professional Attendee User
        User::create([
            'title' => 'Mr.',
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'phone' => '08123456789',
            'gender' => 'Male',
            'occupation' => 'Software Engineer',
            'institution' => 'Google LLC',
            'country' => 'United States',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // 4. Student Attendee User
        User::create([
            'title' => 'Ms.',
            'name' => 'Jane Smith',
            'email' => 'student@example.com',
            'phone' => '09012345678',
            'gender' => 'Female',
            'occupation' => 'Undergraduate Student',
            'institution' => 'Federal University of Lafia',
            'country' => 'Nigeria',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}
