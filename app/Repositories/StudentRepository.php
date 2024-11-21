<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentRepository extends Repository
{
    protected $_db;

    public function __construct(Student $student)
    {
        $this->_db = $student;
    }

    public function bulkSave($dataList)
    {
        $students = [];
        foreach ($dataList as $data) {
            $students[] = [
                "class_id" => $data['class_id'],
                "profile_image" => $data['profile_image'] ?? null,
                "name" => $data['name'],
                "gender" => $data['gender'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ];
        }

        $this->_db->insert($students);

        return $data;
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;
        $model->gender = $data['gender'] ?? $model->gender;
        $model->profile_image = (array_key_exists('profile_image', $data)) ? $data['profile_image'] : $model->profile_image;

        $model->update();
        return $model;
    }

    public function getStudentByClassIdAndId($classId, $id)
    {
        return $this->_db->where('class_id', $classId)->where('id', $id)->get();
    }
}
