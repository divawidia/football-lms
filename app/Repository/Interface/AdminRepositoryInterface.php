<?php

namespace App\Repository\Interface;

use App\Models\Admin;
use App\Models\User;

interface AdminRepositoryInterface
{

    public function getAll();

    public function find($id);

    public function create(array $data);
}
