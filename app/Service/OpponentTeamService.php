<?php

namespace App\Service;

use App\Http\Requests\OpponentTeamRequest;
use App\Models\Competition;
use App\Models\OpponentTeam;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OpponentTeamService
{
    private function deleteLogo($logo): void
    {
        if (Storage::disk('public')->exists($logo) && $logo != 'images/undefined-user.png'){
            Storage::disk('public')->delete($logo);
        }
    }
    public  function store(array $opponentTeamData){

        if (array_key_exists('logo', $opponentTeamData)){
            $opponentTeamData['logo'] =$opponentTeamData['logo']->store('assets/team-logo', 'public');
        }else{
            $opponentTeamData['logo'] = 'images/undefined-user.png';
        }
        $opponentTeamData['status'] = '1';
        return OpponentTeam::create($opponentTeamData);
    }

    public function update(array $opponentTeamData, OpponentTeam $opponentTeam): OpponentTeam
    {
        if (array_key_exists('logo', $opponentTeamData)){
            $this->deleteLogo($opponentTeam->logo);
            $opponentTeamData['logo'] = $opponentTeamData['logo']->store('assets/team-logo', 'public');
        }else{
            $opponentTeamData['logo'] = $opponentTeam->logo;
        }

        $opponentTeam->update($opponentTeamData);

        return $opponentTeam;
    }

    public function activate(OpponentTeam $opponentTeam): OpponentTeam
    {
        $opponentTeam->update(['status' => '1']);
        return $opponentTeam;
    }

    public function deactivate(OpponentTeam $opponentTeam): OpponentTeam
    {
        $opponentTeam->update(['status' => '0']);
        return $opponentTeam;
    }

    public function destroy(OpponentTeam $opponentTeam): OpponentTeam
    {
        $this->deleteLogo($opponentTeam->logo);
        $opponentTeam->delete();
        return $opponentTeam;
    }
}
