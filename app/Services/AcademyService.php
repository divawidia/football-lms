<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class AcademyService extends Service
{
    public function update(array $data, $academy)
    {
        $data['logo'] = $this->updateImage($data, 'logo', 'assets/academy-profile', $academy->logo);
        return $academy->update($data);
    }
}
