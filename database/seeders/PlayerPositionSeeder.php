<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\PlayerPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayerPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Goalkeeper (GK)' => 'Defender',
            'Center Back (CB)' => 'Defender',
            'Left Full Back (LB)' => 'Defender',
            'Right Full Back (RB)' => 'Defender',
            'Left Wing Back (LWB)' => 'Defender',
            'Right Wing Back (RWB)' => 'Defender',
            'Central Defensive Midfielder (CDM)' => 'Midfielder',
            'Left Defensive Midfielder (LDM)' => 'Midfielder',
            'Right Defensive Midfielder (RDM)' => 'Midfielder',
            'Central Midfielder (CM)' => 'Midfielder',
            'Left Centre Midfielder (LCM)' => 'Midfielder',
            'Right Centre Midfielder (RCM)' => 'Midfielder',
            'Left Midfielder (RM)' => 'Midfielder',
            'Right Midfielder (LM)' => 'Midfielder',
            'Central Atacking Midfielder (CAM)' => 'Midfielder',
            'Left Atacking Midfielder (LAM)' => 'Midfielder',
            'Right Atacking Midfielder (RAM)' => 'Midfielder',
            'Left Wing Forward (LWF)' => 'Forward',
            'Right Wing Forward (RWF)' => 'Forward',
            'Centre Forward (CF)' => 'Forward',
            'Left Centre Forward (LCF)' => 'Forward',
            'Right Centre Forward (RCF)' => 'Forward',
            'Second Striker (SS)' => 'Forward',
        ];

        foreach ($positions as $name => $category){
            PlayerPosition::create([
                'name' => $name,
                'category' => $category,
            ]);
        }
    }
}
