<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademyRequest;
use App\Models\Academy;
use App\Notifications\AcademyProfileUpdated;
use App\Repository\UserRepository;
use App\Services\AcademyService;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class AcademyController extends Controller
{
    private AcademyService $academyService;
    private UserRepository $userRepository;
    public function __construct(AcademyService $academyService, UserRepository $userRepository)
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

        $adminName = $this->getLoggedUser()->firstName.' '. $this->getLoggedUser()->lastName;

        Notification::send($this->userRepository->getAllAdminUsers(),new AcademyProfileUpdated($adminName));

        $text = 'Academy successfully updated!';
        Alert::success($text);
        return redirect()->route('admin.dashboard');
    }
}
