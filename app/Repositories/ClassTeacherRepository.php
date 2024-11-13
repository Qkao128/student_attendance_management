<?php

namespace App\Repositories;

use App\Models\ClassTeacher;

class ClassTeacherRepository extends Repository
{
    protected $_db;

    public function __construct(ClassTeacher $courseTeacher)
    {
        $this->_db = $courseTeacher;
    }

    public function save($data)
    {
        $model = new ClassTeacher;
        $model->class_id = $data['class_id'];
        $model->user_id = $data['user_id'];

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->class_id = $data['class_id'] ?? $model->class_id;
        $model->user_id = $data['user_id'] ?? $model->user_id;

        $model->update();
        return $model;
    }

    public function getByClassIdAndTeacherId($id, $userId)
    {
        return $this->_db->where('class_id', $id)->where('user_id', $userId)->get();
    }
}
