<?php

namespace Database\Seeders;

use App\Models\CoachSpecialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoachSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            'Goalkeeper Coach',
            'Defender Coach',
            'Midfielder Coach',
            'Forward Coach',
            'Technical Coach',
            'Tactical Analyst',
            'Mental Coach',
            'Conditioning/Fitness Coach',
            'Youth Development Coach',
            'Match Analyst',
            'Head Coach',
            'Assistant Head Coach'
        ];

        foreach ($specializations as $specialization){
            CoachSpecialization::create([
                'name' => $specialization,
            ]);
        }
    }
}
