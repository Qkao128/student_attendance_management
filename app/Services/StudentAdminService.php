<?php

namespace App\Services;

use Exception;
use App\Models\Student;
use App\Services\Service;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Repositories\ClassRepository;
use App\Repositories\studentRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentAdminService extends Service
{
    protected $_studentRepository;
    protected $_classRepository;

    public function __construct(
        StudentRepository $studentRepository,
        ClassRepository $classRepository,
    ) {
        $this->_studentRepository = $studentRepository;
        $this->_classRepository = $classRepository;
    }

    public function createStudent($data, $classId)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($data, [
                'student' => 'required|array',
                'student.*.profile_image' => 'nullable|file|mimes:jpeg,png,jpg|max:512000',
                'student.*.name' => 'required|string|max:255',
                'student.*.gender' => 'required|string|in:male,female',
            ]);

            if ($validator->fails()) {
                $this->_errorMessage = $validator->errors()->all();
                return null;
            }

            $class = $this->_classRepository->getById($classId);
            if (!$class || !Gate::allows('admin', Auth::user())) {
                throw new Exception("Unauthorized or invalid class.");
            }

            if (!empty($data['student'])) {
                foreach ($data['student'] as $index => $column) {
                    $data['student'][$index]['class_id'] = $classId;

                    if (isset($column['profile_image'])) {
                        $fileName = $this->generateFileName();
                        $fileExtension = $column['profile_image']->extension();
                        $fileName = $fileName . '.' . $fileExtension;
                        $column['profile_image']->storeAs('public/profile_image', $fileName);
                        $data['student'][$index]['profile_image'] = $fileName;
                    } else {
                        $data['student'][$index]['profile_image'] = null;
                    }
                }

                $student = $this->_studentRepository->bulkSave($data['student']);
            }
            DB::commit();
            return $student;
        } catch (Exception $e) {
            DB::rollBack();
            $this->_errorMessage[] = "Failed to add student: " . $e->getMessage();
            return null;
        }
    }


    public function getById($id)
    {
        try {
            $class = $this->_studentRepository->getById($id);

            if ($class == null) {
                return false;
            }

            return $class;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get class details.");

            return null;
        }
    }

    public function update($data, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'class' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (!Gate::allows('admin', Auth::user())) {
                throw new Exception();
            }

            $class = $this->_studentRepository->getById($id);

            if ($class == null) {
                throw new Exception();
            }


            $class = $this->_studentRepository->update($data, $id);

            DB::commit();
            return $class;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update class details.");
            DB::rollBack();
            return null;
        }
    }

    public function getDatatable($classId)
    {
        $students = Student::where('class_id', $classId)->get();

        $result =  DataTables::of($students)->make();

        return $result;
    }

    public function deleteById($classId, $id)
    {
        DB::beginTransaction();

        try {
            $class = $this->_classRepository->getById($classId);
            $student = $this->_studentRepository->getById($id);

            if (!Gate::allows('admin', Auth::user()) || $class == null || $student  == null) {
                throw new Exception();
            }

            $student = $this->_studentRepository->deleteById($id);

            DB::commit();
            return $class;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete student details.");

            DB::rollBack();
            return null;
        }
    }

    private function generateFileName()
    {
        return Str::random(5) . Str::uuid() . Str::random(5);
    }
}
