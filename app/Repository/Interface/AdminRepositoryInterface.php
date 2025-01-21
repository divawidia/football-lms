<?php

namespace App\Repository\Interface;

use App\Models\Admin;
use App\Models\User;

interface AdminRepositoryInterface
{

    public function getAll($withRelation = ['user'], $thisMonth = false, $retrievalMethod = 'all', $columns = ['*']);

    public function find($id);

    public function create(array $data);

    public function update(array $data, Admin $admin);
}
