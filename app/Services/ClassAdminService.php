<?php

namespace App\Services;

use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Repositories\ClassRepository;
use Illuminate\Support\Facades\Validator;

class ClassAdminService extends Service
{
    protected $_classRepository;

    public function __construct(
        ClassRepository $classRepository,
    ) {
        $this->_classRepository = $classRepository;
    }

    public function createClass($data)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }

                return null;
            }

            if (!Gate::allows('teacher', Auth::user())) {
                throw new Exception();
            }

            $class = $this->_classRepository->save($data);

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
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $class = $this->_classRepository->getById($id);


            $class = $this->_classRepository->update($data, $id);

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
