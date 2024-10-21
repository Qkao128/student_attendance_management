<?php

namespace App\Services;

use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CourseRepository;

class CourseAdminService extends Service
{
    protected $_courseRepository;

    public function __construct(
        CourseRepository $courseRepository,
    ) {
        $this->_courseRepository = $courseRepository;
    }

    public function createCourse($data)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }

                return null;
            }

            $course = $this->_courseRepository->save($data);

            DB::commit();
            return $course;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add course.");

            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $course = $this->_courseRepository->getById($id);

            if ($course == null) {
                return false;
            }

            return $course;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get course details.");

            return null;
        }
    }


    public function update($data, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $course = $this->_courseRepository->getById($id);


            $course = $this->_courseRepository->update($data, $id);

            DB::commit();
            return $course;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update course details.");
            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {

            $course = $this->_courseRepository->deleteById($id);

            DB::commit();
            return $course;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete course details.");

            DB::rollBack();
            return null;
        }
    }
}
