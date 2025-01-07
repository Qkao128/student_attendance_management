<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Enums\UserType;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        return DB::table('students')
            ->leftjoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->where('class_id', $classId)
            ->where('courses.deleted_at', '=', null)
            ->where('classes.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->where('users.deleted_at', '=', null)
            ->whereDate('students.enrollment_date', '<=', $date)
            ->count();
    }

    public function getStudentCount($date)
    {
        $query = DB::table('students')
            ->leftjoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->where('courses.deleted_at', '=', null)
            ->where('classes.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->where('users.deleted_at', '=', null)
            ->whereDate('students.enrollment_date', '<=', $date);


        // Apply additional filters based on user role
        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $query->where('classes.id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                $query->where('class_teachers.user_id', Auth::user()->id);
            }
        }

        $result = $query->count();
        // Finally, get the count of distinct class IDs
        return $result;
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
