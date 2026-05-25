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
            'first_name' => 'FULafia',
            'last_name' => 'SuperAdmin',
            'other_names' => 'Portal',
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
            'first_name' => 'FULafia',
            'last_name' => 'Admin',
            'other_names' => null,
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
            'first_name' => 'John',
            'last_name' => 'Doe',
            'other_names' => null,
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
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'other_names' => null,
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

        // 5. Default Ongoing Conference
        $conference = \App\Models\Conference::create([
            'title' => '1st FULafia International Conference on Advanced Scientific Research',
            'description' => 'Join academic scholars, researchers, and tech industry innovators at the Federal University of Lafia to explore artificial intelligence, renewable energy, and future computing breakthroughs.',
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(33),
            'venue' => 'ETF Lecture Theatre, FULafia Permanent Campus',
            'status' => 'ongoing',
            'accommodation_fee' => 15000.00,
            'conference_material_fee' => 5000.00,
        ]);

        // Seed attendee types for this conference
        $categories = [
            'Researchers' => 35000.00,
            'Postgraduate Students' => 25000.00,
            'Undergraduate Students' => 10000.00,
            'Corporate Bodies' => 100000.00,
            'International attendee' => 150000.00,
            'Virtual Attendee' => 15000.00,
        ];

        foreach ($categories as $name => $fee) {
            \App\Models\AttendeeType::create([
                'conference_id' => $conference->id,
                'name' => $name,
                'fee' => $fee,
            ]);
        }
    }
}
