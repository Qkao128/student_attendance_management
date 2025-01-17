<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\UserType;
use App\Models\Classes;
use App\Models\student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $model->date_from = $data['date_from'];
        $model->date_to = $data['date_to'];

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;
        $model->course_id = $data['course_id'] ?? $model->course_id;
        $model->is_disabled = $data['is_disabled'] ?? $model->is_disabled;
        $model->date_from = $data['date_from'] ?? $model->date_from;
        $model->date_to = $data['date_to'] ?? $model->date_to;

        $model->update();
        return $model;
    }

    public function getByIdWithDetails($id)
    {
        return DB::table('classes')
            ->select([
                'classes.id',
                'classes.name',
                'classes.is_disabled',
                'classes.date_from',
                'classes.date_to',
                'class_teachers.user_id',
                'courses.name as course_name',
                'users.username as user_name',
                DB::raw('COUNT(students.id) as member_count'),
            ])
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('students', 'classes.id', '=', 'students.class_id')
            ->groupBy(
                'classes.id',
                'classes.name',
                'classes.is_disabled',
                'classes.date_from',
                'classes.date_to',
                'class_teachers.user_id',
                'courses.name',
                'users.username'
            )
            ->where('classes.id', $id)
            ->whereNull('courses.deleted_at')
            ->whereNull('classes.deleted_at')
            ->whereNull('users.deleted_at')
            ->where('classes.is_disabled', false)
            ->first();
    }

    public function getByIdWithClassDetails($id)
    {
        return DB::table('classes')
            ->select([
                'classes.id',
                'classes.name',
                'classes.is_disabled',
                'classes.date_from',
                'classes.date_to',
                'class_teachers.user_id',
                'courses.name as course_name',
                'users.username as user_name',
                DB::raw('COUNT(students.id) as member_count'),
            ])
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('students', 'classes.id', '=', 'students.class_id')
            ->groupBy(
                'classes.id',
                'classes.name',
                'classes.is_disabled',
                'classes.date_from',
                'classes.date_to',
                'class_teachers.user_id',
                'courses.name',
                'users.username'
            )
            ->where('classes.id', $id)
            ->whereNull('courses.deleted_at')
            ->whereNull('classes.deleted_at')
            ->whereNull('users.deleted_at')
            ->first();
    }

    public function getClassCount()
    {
        // Start with the base query
        $query = DB::table('classes')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false);

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

        $classCount = $query->count();

        // 移除 dd()，返回之前保存的結果
        return $classCount;
    }

    public function getByTeacherId($classId)
    {
        return DB::table('class_teachers')
            ->where('class_id', $classId)
            ->value('user_id');
    }

    public function getAllBySearchTermAndCourse_id($data)
    {
        $query = $this->_db->with(['classTeacher.user'])
            ->select('classes.id', 'classes.name')
            ->where('classes.name', 'LIKE', "%{$data['search_term']}%")
            ->where('classes.course_id', $data['course_id'])
            ->whereNull('classes.deleted_at')
            ->where('classes.is_disabled', false);

        // 如果有 teacher_id，驗證該 teacher_id 是否為 Admin
        if (!empty($data['teacher_id'])) {
            $user = User::find($data['teacher_id']); // 查詢指定的使用者

            if ($user && $user->hasRole(UserType::Admin()->key)) {
                // 如果是 Admin，則根據 teacher_id 過濾
                $query->whereHas('classTeacher', function ($q) use ($data) {
                    $q->where('user_id', $data['teacher_id']);
                });
            }
        }

        return $query->skip($data['offset'])->take($data['result_count'])->get();
    }

    public function getTotalCountBySearchTermAndCourse_id($data)
    {
        $query = $this->_db->with(['classTeacher.user'])
            ->where('classes.name', 'LIKE', "%{$data['search_term']}%")
            ->where('classes.course_id', $data['course_id'])
            ->whereNull('classes.deleted_at')
            ->where('classes.is_disabled', false);

        // 如果有 teacher_id，驗證該 teacher_id 是否為 Admin
        if (!empty($data['teacher_id'])) {
            $user = User::find($data['teacher_id']); // 查詢指定的使用者

            if ($user && $user->hasRole(UserType::Admin()->key)) {
                // 如果是 Admin，則根據 teacher_id 過濾
                $query->whereHas('classTeacher', function ($q) use ($data) {
                    $q->where('user_id', $data['teacher_id']);
                });
            }
        }

        return $query->count();
    }
}
