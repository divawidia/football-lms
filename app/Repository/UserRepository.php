<?php

namespace App\Repository;

use App\Models\Coach;
use App\Models\CoachCertification;
use App\Models\CoachSpecialization;
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

    public function getAllUserWithoutLoggedUserData($authUserId){
        return $this->user->where('id', '!=', $authUserId)->get();
    }

    public function getAllPlayers()
    {
        return $this->user->role('player')->get();
    }

    public function find($id)
    {
        return $this->user->findOrFail($id);
    }

    public function createCoachUser(array $data)
    {
        $user = $this->user->create($data);
        $user->assignRole('coach');
        return $user;
    }

    public function update(array $data)
    {
        return $this->user->update($data);
    }

    public function delete()
    {
        return $this->user->delete();
    }
}
