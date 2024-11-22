<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\UserType;
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
        $model->username = $data['username'];
        $model->email = $data['email'];
        $model->password = Hash::make($data['password']);
        $model->profile_image = $data['profile_image'] ?? null;

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->username = $data['username'] ?? $model->username;
        $model->email = $data['email'] ?? $model->email;
        $model->password = ($data['password'] ?? false) ? $data['password'] : $model->password;
        $model->profile_image = (array_key_exists('profile_image', $data)) ? $data['profile_image'] : $model->profile_image;

        $model->update();
        return $model;
    }

    public function getAllBySearchTerm($data)
    {

        $user = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'username')
            ->where('username', 'LIKE', "%$user%")
            ->whereNull('teacher_user_id')
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
            ->where('username', 'LIKE', "%$user%")
            ->whereNull('teacher_user_id')
            ->count();

        return $totalCount;
    }
}
