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

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;
        $model->course_id = $data['course_id'] ?? $model->course_id;

        $model->update();
        return $model;
    }

    public function getByCourseAndName($courseId, $className)
    {
        $data = $this->_db->where('course_id', $courseId)
            ->where('name', $className)
            ->first();

        return $data;
    }
}
