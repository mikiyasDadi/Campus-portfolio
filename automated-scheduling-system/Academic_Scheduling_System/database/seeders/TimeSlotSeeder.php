<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
{
    $slots = [
        ['start_time' => '08:00:00', 'end_time' => '09:00:00'],
        ['start_time' => '09:00:00', 'end_time' => '10:00:00'],
        ['start_time' => '10:00:00', 'end_time' => '11:00:00'],
        ['start_time' => '11:00:00', 'end_time' => '12:00:00'],
    ];

    foreach ($slots as $slot) {
        \App\Models\TimeSlot::create($slot);
    }
}
}
