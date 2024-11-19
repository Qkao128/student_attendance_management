<?php

namespace App\Services;

use Exception;
use App\Enums\UserType;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ClassRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\ClassTeacherRepository;

class ClassAdminService extends Service
{
    protected $_classRepository;
    protected $_classTeacherRepository;

    public function __construct(
        ClassRepository $classRepository,
        ClassTeacherRepository $classTeacherRepository,
    ) {
        $this->_classRepository = $classRepository;
        $this->_classTeacherRepository = $classTeacherRepository;
    }

    public function createClass($data)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255|unique:classes,name',
                'course_id' => 'required|exists:courses,id',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }

                return null;
            }

            if (!Auth::check() || !Auth::user()->hasRole(UserType::SuperAdmin()->key)) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $existingClass = $this->_classRepository->getByCourseAndName($data['course_id'], $data['name']);
            if ($existingClass) {
                array_push($this->_errorMessage, "This name already exists for this course.");

                DB::rollBack();
                return null;
            }


            $class = $this->_classRepository->save($data);

            $data['class_id'] = $class->id;
            $classTeacher = $this->_classTeacherRepository->save($data);


            DB::commit();
            return $class;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add class.");

            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $class = $this->_classRepository->getById($id);

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
                'name' => 'required|string|max:255|unique:classes,name,' . $id,
                'course_id' => 'required|exists:courses,id',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (!Auth::check() || !Auth::user()->hasRole(UserType::SuperAdmin()->key)) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $class = $this->_classRepository->getById($id);

            $classTeacher = $this->_classTeacherRepository->getByClassIdAndTeacherId($id, $data['user_id']);

            if ($class == null || $classTeacher == null) {
                throw new Exception();
            }


            $existingClass = $this->_classRepository->getByCourseAndName($data['course_id'], $data['name']);

            if ($existingClass && $existingClass->id !== $id) {
                array_push($this->_errorMessage, "This name already exists for this course.");

                DB::rollBack();
                return null;
            }

            $class = $this->_classRepository->update($data, $id);

            $classTeacher = $this->_classTeacherRepository->update($data, $id);

            DB::commit();
            return $class;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update class details.");
            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $class = $this->_classRepository->getById($id);

            if ($class == null) {
                throw new Exception();
            }

            if (!Auth::check() || !Auth::user()->hasRole(UserType::SuperAdmin()->key)) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $class = $this->_classRepository->deleteById($id);

            DB::commit();
            return $class;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete class details.");

            DB::rollBack();
            return null;
        }
    }
}
