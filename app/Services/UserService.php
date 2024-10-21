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

class UserService extends Service
{
    public function update(array $data, User $user)
    {
        $data['foto'] = $this->updateImage($data, 'foto', 'assets/user-profile', $user->foto);
        return $user->update($data);
    }

    public function changePassword($data, User $user)
    {
        return $user->update([
            'password' => bcrypt($data['password'])
        ]);
    }
}
