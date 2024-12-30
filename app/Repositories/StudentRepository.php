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
                "enrollment_date" => $data['enrollment_date'],
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
        $model->enrollment_date = $data['enrollment_date'] ?? $model->enrollment_date;

        $model->update();
        return $model;
    }

    public function getStudentByClassIdAndId($classId, $id)
    {
        return $this->_db->where('class_id', $classId)->where('id', $id)->get();
    }

    public function getByClassId($id)
    {

        return $this->_db->where('class_id', $id)->get();
    }

    public function getStudentCountByClassId($classId, $date)
    {
        return $this->_db->where('class_id', $classId)->whereDate('students.enrollment_date', '<=', $date)->count();
    }

    public function getStudentCount($date)
    {
        return DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->where('classes.is_disabled', false)
            ->whereDate('students.enrollment_date', '<=', $date)
            ->count();
    }

    public function getAllBySearchTermAndClass_id($data)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name')
            ->where('name', 'LIKE', "%$name%")
            ->where('class_id', '=', $data['class_id'])
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTermAndClass_id($data)
    {

        $name = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->where('name', 'LIKE', "%$name%")
            ->where('class_id', '=', $data['class_id'])
            ->count();

        return $totalCount;
    }
}
