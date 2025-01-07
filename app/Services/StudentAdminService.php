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
                'student.*.gender' => 'required|string|in:Male,Female',
                'student.*.enrollment_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                $this->_errorMessage = $validator->errors()->all();
                return null;
            }

            foreach ($data['student'] as $student) {
                if (strtotime($student['enrollment_date']) > strtotime(date('Y-m-d'))) {
                    throw new Exception('Enrollment date must not be a future date.');
                }
            }

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $class = $this->_classRepository->getByIdWithClassDetails($classId);

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
            $errorMessage = $e->getMessage() ?: "Fail to add student.";
            array_push($this->_errorMessage, $errorMessage);

            DB::rollBack();
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
            array_push($this->_errorMessage, "Fail to get student details.");

            return null;
        }
    }

    public function getByClassId($id)
    {
        try {
            $student = $this->_studentRepository->getByClassId($id);

            if ($student == null) {
                return false;
            }

            return $student;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get student details.");

            return null;
        }
    }

    public function getStudentCountByClassId($classId, $date)
    {
        try {
            $student = $this->_studentRepository->getStudentCountByClassId($classId, $date);

            if ($student == null) {
                return false;
            }

            return $student;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get student details.");

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
                'gender' => 'required|string|in:Male,Female',
                'enrollment_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (strtotime($data['enrollment_date']) > strtotime(date('Y-m-d'))) {
                throw new Exception('Enrollment date must not be a future date.');
            }

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $class = $this->_classRepository->getByIdWithClassDetails($classId);

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

            $errorMessage = $e->getMessage() ?: "Fail to update student details.";
            array_push($this->_errorMessage, $errorMessage);
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

            $class = $this->_classRepository->getByTeacherId($classId);

            $student = $this->_studentRepository->getStudentByClassIdAndId($classId, $id);


            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            if ($class == null || $student == null) {
                throw new Exception();
            }


            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($class !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage this class.');
                }
            }

            $student = $this->_studentRepository->deleteById($id);

            DB::commit();
            return $class;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() ?: "Fail to delete student details.";
            array_push($this->_errorMessage, $errorMessage);

            DB::rollBack();
            return null;
        }
    }

    private function generateFileName()
    {
        return Str::random(5) . Str::uuid() . Str::random(5);
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 5;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];

            $student = $this->_studentRepository->getAllBySearchTermAndClass_id($data);

            $totalCount = $this->_studentRepository->getTotalCountBySearchTermAndClass_id($data);

            $results = array(
                "results" => $student->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            return $results;
        } catch (Exception $e) {

            array_push($this->_errorMessage, "Fail to get student select option.");
            DB::rollBack();
            return null;
        }
    }
}
