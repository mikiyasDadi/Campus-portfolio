<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // <--- ADD THIS LINE HERE!

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // This is YOU. Role ID 1 = Administrator
        User::create([
            'username'   => 'admin',
            'first_name' => 'System', 
            'last_name'  => 'Administrator',
            'email'      => 'admin@test.com',
            'password'   => Hash::make('password'), // Or whatever password you use
            'role_id'    => 1,
            'status'     => 'active',
        ]);
        $this->call([
        FacultySeeder::class,
        // ... your other seeders like UserSeeder
    ]);
    }
}