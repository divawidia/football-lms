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
            'Goalkeeper',
            'Defender',
            'Midfielder',
            'Forward',
            'Technical',
            'Tactical',
            'Mental',
            'Physical Conditioning/Fitness',
            'Youth Development',
            'Analytics',
            'Head Coach'
        ];

        foreach ($specializations as $specialization){
            CoachSpecialization::create([
                'name' => $specialization,
            ]);
        }
    }
}
