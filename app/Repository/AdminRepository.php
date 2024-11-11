<?php

namespace App\Repository;

use App\Models\Admin;
use App\Models\User;

class AdminRepository
{
    protected Admin $admin;
    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    public function getAll()
    {
        return $this->admin->with('user')->get();
    }

    public function find($id)
    {
        return $this->admin->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->admin->create($data);
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
