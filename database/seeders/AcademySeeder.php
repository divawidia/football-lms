<?php

namespace Database\Seeders;

use App\Models\Academy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Academy::create([
            'email' => 'academy@example.com',
            'phoneNumber' => '0872123617312',
            'academyName' => 'SSB Dreamfields',
            'address' => 'Jl. Antah Berantah, Jakarta Selatan',
            'state' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'country' => 'Indonesia',
            'zipCode' => '80361',
            'directorName' => 'asjdasndakj',
            'status' => '1',
        ]);
    }
}
