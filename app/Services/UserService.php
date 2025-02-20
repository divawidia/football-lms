<?php

namespace App\Services;

use App\Repository\UserRepository;

class UserService extends Service
{
    public UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(string|int $withoutUserid = null, $role = ['Super-Admin', 'admin', 'coach', 'player'], string|array $column = ['*'])
    {
        return $this->userRepository->getAll($withoutUserid, $role, $column);
    }

    public function update(array $data, $user)
    {
        $data['foto'] = $this->updateImage($data, 'foto', 'assets/user-profile', $user->foto);
        return $user->update($data);
    }

    public function changePassword($data, $user)
    {
        return $user->update([
            'password' => bcrypt($data['password'])
        ]);
    }
}
