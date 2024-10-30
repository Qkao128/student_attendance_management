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
        $model->class = $data['class'];
        $model->course_id = $data['course_id'];
        $model->user_id = $data['user_id'];

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->class = $data['class'] ?? $model->class;
        $model->course_id = $data['course_id'] ?? $model->course_id;
        $model->user_id = $data['user_id'] ?? $model->user_id;

        $model->update();
        return $model;
    }
}
