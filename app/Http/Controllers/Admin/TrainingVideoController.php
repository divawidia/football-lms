<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingVideo;
use App\Services\TrainingVideoService;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
