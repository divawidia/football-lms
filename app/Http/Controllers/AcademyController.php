<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademyRequest;
use App\Models\Academy;
use App\Notifications\AcademyProfileUpdated;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\UserRepository;
use App\Services\AcademyService;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class AcademyController extends Controller
{
    private AcademyService $academyService;
    private UserRepositoryInterface $userRepository;
    public function __construct(AcademyService $academyService, UserRepositoryInterface $userRepository)
    {
        $this->academyService = $academyService;
        $this->userRepository = $userRepository;
    }

    public function edit()
    {
        $data = Academy::first();
        return view('pages.academy-profile.edit', [
            'data' => $data,
            'countries' => $this->academyService->getCountryData()
        ]);
    }

    public function update(AcademyRequest $request)
    {
        $data = $request->validated();
        $academy = Academy::first();

        $this->academyService->update($data, $academy);

        $this->sendNotification();

        Alert::success('Academy successfully updated!');
        return redirect()->route('admin.dashboard');
    }

    /**
     * Send notifications to all admin users about the academy profile update.
     */
    private function sendNotification(): void
    {
        $adminName = $this->getLoggedUserFullName();
        $admins = $this->userRepository->getAllAdminUsers();

        Notification::send($admins, new AcademyProfileUpdated($adminName));
    }
}
