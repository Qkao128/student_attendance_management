<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends Repository
{
    protected $_db;

    public function __construct(User $user)
    {
        $this->_db = $user;
    }

    public function save($data)
    {
        $model = new User;
        $model->name = $data['name'];
        $model->password = $data['password'];
        $model->profile_image = $data['profile_image'] ?? null;

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;
        $model->password = ($data['password'] ?? false) ? $data['password'] : $model->password;
        $model->profile_image = (array_key_exists('profile_image', $data)) ? $data['profile_image'] : $model->profile_image;

        $model->update();
        return $model;
    }

    public function getAllBySearchTerm($data)
    {

        $user = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name')
            ->where('name', 'LIKE', "%$user%")
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTerm($data)
    {

        $user = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->where('name', 'LIKE', "%$user%")
            ->count();

        return $totalCount;
    }
}
