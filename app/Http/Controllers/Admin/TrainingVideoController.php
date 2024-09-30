<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingVideoRequest;
use App\Models\TrainingVideo;
use App\Services\TrainingVideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
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

        $trainingVideos = $this->trainingVideoService->store($data, Auth::user()->id);

        Alert::success('Training Videos '. $data['trainingTitle'] .' successfully created!');
        return redirect()->route('training-videos.show', $trainingVideos->id);
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
    public function edit(TrainingVideo $trainingVideo): JsonResponse
    {
        return response()->json([
            'status' => '200',
            'data' => $trainingVideo,
            'message' => 'Success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrainingVideoRequest $request, TrainingVideo $trainingVideo)
    {
        $data = $request->validated();

        $trainingVideos = $this->trainingVideoService->update($data, $trainingVideo);

        return response()->json($trainingVideos);
    }

    public function unpublish(TrainingVideo $trainingVideo){
        $this->trainingVideoService->unpublish($trainingVideo);

        Alert::success('Training '.$trainingVideo->trainingTitle.' status successfully unpublished!');
        return redirect()->route('training-videos.show', $trainingVideo->id);
    }

    public function publish(TrainingVideo $trainingVideo){
        $this->trainingVideoService->publish($trainingVideo);

        Alert::success('Team '.$trainingVideo->trainingTitle.' status successfully published!');
        return redirect()->route('training-videos.show', $trainingVideo->id);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainingVideo $trainingVideo)
    {
        $this->trainingVideoService->destroy($trainingVideo);

        return response()->json(200);
    }
}
