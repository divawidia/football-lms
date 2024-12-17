<?php

namespace App\Repository;

use App\Models\Admin;
use App\Models\User;
use App\Repository\Interface\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface
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
}
