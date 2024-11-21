<?php

namespace App\Services;

use Exception;
use App\Enums\UserType;
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

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $class = $this->_classRepository->getById($classId);

            if ($class == null) {
                throw new Exception();
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($class->user_id !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage this class.');
                }
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

    public function update($data, $classId, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'profile_image' => 'nullable|file|mimes:jpeg,png,jpg|max:512000',
                'name' => 'required|string|max:255',
                'gender' => 'required|string|in:male,female',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $class = $this->_classRepository->getById($classId);

            if ($class == null) {
                throw new Exception();
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($class->user_id !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage this class.');
                }
            }

            $student = $this->_studentRepository->getById($id);

            if ($student == null) {
                throw new Exception();
            }

            if (isset($data['profile_image'])) {
                if (!empty($data['profile_image'])) {
                    if ($student['profile_image'] != null && Storage::exists('public/profile_image/' . $student['profile_image'])) {
                        Storage::delete('public/profile_image/' . $student['profile_image']);
                    }

                    $fileName = $this->generateFileName();
                    $fileExtension = $data['profile_image']->extension();
                    $fileName = $fileName . '.' . $fileExtension;

                    $data['profile_image']->storeAs('public/profile_image', $fileName);
                    $data['profile_image'] = $fileName;
                } else {
                    $data['profile_image'] = null;
                }
            }

            $student = $this->_studentRepository->update($data, $id);

            DB::commit();
            return $student;
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

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            if ($class == null || $student == null) {
                throw new Exception();
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($class->user_id !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage this class.');
                }
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
