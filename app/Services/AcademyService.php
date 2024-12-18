<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
use App\Models\User;
use App\Notifications\AcademyProfileUpdated;
use App\Repository\Interface\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Nnjeim\World\World;
use Yajra\DataTables\Facades\DataTables;

class AcademyService extends Service
{
    private UserRepositoryInterface $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function update(array $data, $academy, $loggedAdminName)
    {
        $data['logo'] = $this->updateImage($data, 'logo', 'assets/academy-profile', $academy->logo);

        $this->sendNotification($loggedAdminName);

        return $academy->update($data);
    }

    private function sendNotification($loggedAdminName): void
    {
        $admins = $this->userRepository->getAllAdminUsers();
        Notification::send($admins, new AcademyProfileUpdated($loggedAdminName));
    }
}
