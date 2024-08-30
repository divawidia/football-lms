<?php

namespace Database\Seeders;

use App\Models\CoachCertification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoachCertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $certifications = [
            'National D' => 'PSSI',
            'AFC C' => 'AFC',
            'AFC B' => 'AFC',
            'AFC A' => 'AFC',
            'AFC PRO' => 'AFC',
            'UEFA B' => 'UEFA',
            'UEFA A' => 'UEFA',
            'UEFA PRO' => 'UEFA',
        ];

        foreach ($certifications as $name => $federation){
            CoachCertification::create([
                'name' => $name,
                'federation' => $federation,
            ]);
        }
    }
}
