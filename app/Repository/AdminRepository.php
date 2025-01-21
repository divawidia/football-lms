<?php

namespace App\Repository;

use App\Models\Admin;
use App\Models\User;
use App\Repository\Interface\AdminRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AdminRepository implements AdminRepositoryInterface
{
    protected Admin $admin;
    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    public function getAll($withRelation = ['user'], $thisMonth = false, $retrievalMethod = 'all', $columns = ['*'])
    {
        $query = $this->admin->with($withRelation);
        if ($thisMonth) {
            $query->whereBetween('created_at',[Carbon::now()->startOfMonth(), Carbon::now()]);
        }

        if ($retrievalMethod == 'count') {
            return $query->count();
        } elseif ($retrievalMethod == 'single') {
            return $query->first($columns);
        } else {
            return $query->get($columns);
        }
    }

    public function find($id)
    {
        return $this->admin->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->admin->create($data);
    }

    public function update(array $data, Admin $admin): int
    {
        $admin->update($data);
        return $admin->user->update($data);
    }
}
