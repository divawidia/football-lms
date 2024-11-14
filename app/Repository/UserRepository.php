<?php

namespace App\Repository;

use App\Models\Team;
use App\Models\User;

class UserRepository
{
    protected User $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getAll()
    {
        return $this->user->all();
    }

    public function getInArray($relation,$ids)
    {
        return $this->user->whereHas($relation, function ($q) use ($ids){
            $q->whereIn('id', $ids);
        })->get();
    }

    public function getAllUserWithoutLoggedUserData($authUserId){
        return $this->user->where('id', '!=', $authUserId)->get();
    }

    public function getAllByRole($role)
    {
        return $this->user->role($role)->get();
    }

    public function getAllAdminUsers()
    {
        return $this->getAllByRole(['admin', 'Super-Admin']);
    }

    public function allTeamsParticipant(Team $team, $admins = true, $coaches = true, $players = true)
    {
        $allUsers = collect();
        if ($admins){
            $admins = $this->getAllAdminUsers();
            $allUsers = $allUsers->merge($admins);
        }

        if ($players){
            $playersIds = collect($team->players)->pluck('id')->all();
            $players = $this->getInArray('player', $playersIds);
            $allUsers = $allUsers->merge($players);
        }

        if ($coaches){
            $coachesIds = collect($team->coaches)->pluck('id')->all();
            $coaches = $this->getInArray('coach', $coachesIds);
            $allUsers = $allUsers->merge($coaches);
        }


        return $allUsers;
    }

    public function find($id)
    {
        return $this->user->findOrFail($id);
    }

    public function createUserWithRole(array $data, $role)
    {
        $user = $this->user->create($data);
        $user->assignRole($role);
        return $user;
    }

    public function updateUserStatus($userModel, $status = ['1', '0'])
    {
        return $userModel->user()->update(['status' => $status]);
    }

    public function changePassword($data, $userModel)
    {
        return $userModel->user()->update([
            'password' => bcrypt($data['password'])
        ]);
    }

    public function update(array $data)
    {
        return $this->user->update($data);
    }

    public function delete($userModel)
    {
        $userModel->delete();
        $userModel->user->roles()->detach();
        return $userModel->user()->delete();
    }
}
