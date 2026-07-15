<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faculties = [
        ['name' => 'Faculty of Engineering', 'code' => 'FOE'],
        ['name' => 'Faculty of Informatics', 'code' => 'FOI'],
        ['name' => 'Faculty of Business and Economics', 'code' => 'FBE'],
        ['name' => 'Faculty of Social Sciences', 'code' => 'FSS'],
        ['name' => 'Faculty of Medicine', 'code' => 'FOM'],
    ];

    foreach ($faculties as $faculty) {
        \App\Models\Faculty::updateOrCreate(['code' => $faculty['code']], $faculty);
    }
    }
}
