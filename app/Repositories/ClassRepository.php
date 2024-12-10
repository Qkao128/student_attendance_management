<?php

namespace App\Repositories;

use App\Models\Classes;
use Illuminate\Support\Facades\DB;

class ClassRepository extends Repository
{
    protected $_db;

    public function __construct(Classes $class)
    {
        $this->_db = $class;
    }

    public function save($data)
    {
        $model = new Classes;
        $model->name = $data['name'];
        $model->course_id = $data['course_id'];
        $model->is_disabled = $data['is_disabled'];

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;
        $model->course_id = $data['course_id'] ?? $model->course_id;
        $model->is_disabled = $data['is_disabled'] ?? $model->is_disabled;

        $model->update();
        return $model;
    }

    public function getByIdWithDetails($id)
    {
        return  DB::table('classes')
            ->select([
                'classes.id',
                'classes.name',
                'classes.is_disabled',
                'classes.created_at',
                'class_teachers.user_id',
                'courses.name as course_name',
                'users.username as user_name',
                DB::raw('COUNT(students.id) as member_count'),
            ])
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('students', 'classes.id', '=', 'students.class_id')
            ->groupBy('classes.id', 'class_teachers.user_id', 'courses.name', 'users.username', 'classes.created_at')
            ->where('classes.id', $id)
            ->first();
    }
}
