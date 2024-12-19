<?php

namespace App\Repository\Interface;

use App\Models\Team;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getAll();

    public function getInArray($relation,$ids);

    public function getAllUserWithoutLoggedUserData($authUserId);

    public function getAllByRole($role);

    public function getAllAdminUsers();

    public function allTeamsParticipant(Team $team, $admins = true, $coaches = true, $players = true);

    public function find($id);

    public function createUserWithRole(array $data, $role);

    public function updateUserStatus($userModel, $status = ['1', '0']);

    public function changePassword($data, $userModel);

    public function update($userModel, array $data);

    public function delete($userModel);
}
