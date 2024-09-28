<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingVideoRequest;
use App\Models\TrainingVideo;
use App\Services\TrainingVideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TrainingVideoController extends Controller
{
    private TrainingVideoService $trainingVideoService;

    public function __construct(TrainingVideoService $trainingVideoService){
        $this->trainingVideoService = $trainingVideoService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.admins.academies.training-videos.index',[
            'data' => $this->trainingVideoService->index()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admins.academies.training-videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrainingVideoRequest $request)
    {
        $data = $request->validated();

        $trainingVideos = $this->trainingVideoService->store($data, Auth::user()->id());

        Alert::success('Training Videos'. $data['trainingTitle'] .' successfully created!');
        return route('training-videos.show', $trainingVideos->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingVideo $trainingVideo)
    {
        return view('pages.admins.academies.training-videos.detail',[
            'data' => $trainingVideo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrainingVideo $trainingVideo)
    {
        return view('pages.admins.academies.training-videos.edit',[
            'data' => $trainingVideo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
