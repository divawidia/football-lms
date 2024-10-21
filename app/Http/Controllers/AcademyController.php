<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademyRequest;
use App\Models\Academy;
use App\Services\AcademyService;
use RealRashid\SweetAlert\Facades\Alert;

class AcademyController extends Controller
{
    private AcademyService $academyService;
    public function __construct(AcademyService $academyService)
    {
        $this->academyService = $academyService;
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

        $text = 'Academy successfully updated!';
        Alert::success($text);
        return redirect()->route('admin.dashboard');
    }
}
