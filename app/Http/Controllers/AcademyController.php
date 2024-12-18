<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademyRequest;
use App\Models\Academy;
use App\Models\Admin;
use App\Notifications\AcademyProfileUpdated;
use App\Repository\Interface\UserRepositoryInterface;
use App\Repository\UserRepository;
use App\Services\AcademyService;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;

class AcademyController extends Controller
{
    private AcademyService $academyService;
    public function __construct(AcademyService $academyService)
    {
        $this->academyService = $academyService;
        $this->academy = Academy::first();
    }

    public function edit()
    {
        return view('pages.academy-profile.edit', [
            'data' => $this->academy,
            'countries' => $this->academyService->getCountryData()
        ]);
    }

    public function update(AcademyRequest $request)
    {
        $data = $request->validated();

        $this->academyService->update($data, $this->academy, $this->getLoggedUserFullName());

        Alert::success('Academy successfully updated!');
        return redirect()->route('admin.dashboard');
    }
}
