<?php

namespace App\Repository;

use App\Models\Team;
use App\Models\User;
use App\Repository\Interface\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    protected User $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getAll(string|int $withoutUserid = null, $role = ['Super-Admin', 'admin', 'coach', 'player'], string|array $column = ['*']): Collection
    {
        $query = $this->user->role($role);
        if ($withoutUserid) {
            $query->where('id', '!=', $withoutUserid);
        }
        return $query->get($column);
    }

    public function getInArray($relation,$ids)
    {
        return $this->user->whereHas($relation, function ($q) use ($ids){
            $q->whereIn('id', $ids);
        })->get();
    }

    public function getAllAdminUsers(): Collection
    {
        return $this->getAll(role: ['admin', 'Super-Admin']);
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

    public function update($userModel, array $data)
    {
        $userModel->update($data);
        $userModel->user->update($data);
        return $userModel;
    }

    public function delete($userModel)
    {
        $userModel->delete();
        $userModel->user->roles()->detach();
        return $userModel->user()->delete();
    }
}
