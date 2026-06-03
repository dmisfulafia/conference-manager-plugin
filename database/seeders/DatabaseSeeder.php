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
            'password' => bcrypt('password@2026'),
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
            'password' => bcrypt('passwor@2026'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 3. Regular Professional Attendee User
        // User::create([
        //     'title' => 'Mr.',
        //     'first_name' => 'John',
        //     'last_name' => 'Doe',
        //     'other_names' => null,
        //     'email' => 'user@example.com',
        //     'phone' => '08123456789',
        //     'gender' => 'Male',
        //     'occupation' => 'Software Engineer',
        //     'institution' => 'Google LLC',
        //     'country' => 'United States',
        //     'password' => bcrypt('password'),
        //     'role' => 'user',
        //     'email_verified_at' => now(),
        // ]);

        // 4. Student Attendee User
        // User::create([
        //     'title' => 'Ms.',
        //     'first_name' => 'Jane',
        //     'last_name' => 'Smith',
        //     'other_names' => null,
        //     'email' => 'student@example.com',
        //     'phone' => '09012345678',
        //     'gender' => 'Female',
        //     'occupation' => 'Undergraduate Student',
        //     'institution' => 'Federal University of Lafia',
        //     'country' => 'Nigeria',
        //     'password' => bcrypt('password'),
        //     'role' => 'user',
        //     'email_verified_at' => now(),
        // ]);

        // 5. 5th FULafia Annual International Conference (Ongoing)
        $ongoingConference = \App\Models\Conference::create([
            'title' => '5th FULafia Annual International Conference',
            'description' => 'Multidisciplinary International Conference on Insecurity and Sustainable Development: Multidisciplinary Pathways to Peace, Stability and National Transformation.',
            'start_date' => '2026-08-10',
            'end_date' => '2026-08-13',
            'venue' => 'Federal University of Lafia, Nasarawa State, Nigeria',
            'status' => 'ongoing',
            'accommodation_fee' => 0.00,
            'conference_material_fee' => 0.00,
        ]);

        $ongoingCategories = [
            'Researchers' => 35000.00,
            'Postgraduate Students' => 25000.00,
            'Undergraduate Students' => 0.00,
            'Corporate Bodies' => 100000.00,
            'International attendee' => 0.00,
            'Virtual Attendee' => 0.00,
        ];

        foreach ($ongoingCategories as $name => $fee) {
            \App\Models\AttendeeType::create([
                'conference_id' => $ongoingConference->id,
                'name' => $name,
                'fee' => $fee,
            ]);
        }

        // 6. 4th FULafia Annual International Conference (Past)
        $pastConference = \App\Models\Conference::create([
            'title' => '4th FULafia Annual International Conference',
            'description' => 'International Conference on Artificial Intelligence: Blessing or Curse to National Development in Nigeria. Features a special panel discussion on "GMO: A Problem or Solution".',
            'start_date' => '2025-03-04',
            'end_date' => '2025-03-06',
            'venue' => 'Malam Adamu Adamu Hall, Permanent Site, Federal University of Lafia',
            'status' => 'past',
            'accommodation_fee' => 0.00,
            'conference_material_fee' => 0.00,
        ]);

        $pastCategories = [
            'Researchers' => 35000.00,
            'Postgraduate Students' => 25000.00,
            'Undergraduate Students' => 0.00,
            'Corporate Bodies' => 100000.00,
            'International attendee' => 0.00,
            'Virtual Attendee' => 0.00,
        ];

        foreach ($pastCategories as $name => $fee) {
            \App\Models\AttendeeType::create([
                'conference_id' => $pastConference->id,
                'name' => $name,
                'fee' => $fee,
            ]);
        }
    }
}
