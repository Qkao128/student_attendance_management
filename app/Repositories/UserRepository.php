<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\UserType;
use Illuminate\Support\Facades\DB;
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
        $model->profile_image = $data['profile_image'] ?? null;
        $model->username = $data['username'];
        $model->email = $data['email'];
        $model->password = Hash::make($data['password']);
        $model->teacher_user_id = $data['teacher_user_id'] ?? null;
        $model->student_id = $data['student_id'] ?? null;

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->username = $data['username'] ?? $model->username;
        $model->email = $data['email'] ?? $model->email;
        $model->password = ($data['password'] ?? false) ? Hash::make($data['password']) : $model->password;
        $model->profile_image = (array_key_exists('profile_image', $data)) ? $data['profile_image'] : $model->profile_image;
        $model->student_id = $data['student_id'] ?? $model->student_id;

        $model->update();
        return $model;
    }

    public function getAllBySearchTerm($data)
    {

        $user = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'username')
            ->where('username', 'LIKE', "%$user%")
            ->whereNull('teacher_user_id')
            ->where('users.deleted_at', '=', null)
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
            ->where('users.deleted_at', '=', null)
            ->count();

        return $totalCount;
    }

    public function getMonitorByStudentId($teacherId, $id)
    {
        $data = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('students', 'users.student_id', '=', 'students.id')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->select([
                'users.id',
                'users.username',
                'users.student_id',
                'users.teacher_user_id',
                'users.profile_image',
                'students.name as monitor_name',
                'users.email as monitor_email',
                'classes.id as class_id',
                'classes.name as class_name',
                'courses.id as course_id',
                'courses.name as course_name',
            ])
            ->where('roles.name', '=', UserType::Monitor()->key)
            ->where('users.teacher_user_id', $teacherId)
            ->where('users.id', $id)
            ->where('users.deleted_at', '=', null)
            ->first();

        return $data;
    }
}
