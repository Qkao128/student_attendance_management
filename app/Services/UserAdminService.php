<?php

namespace App\Services;

use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;

class UserAdminService extends Service
{
    protected $_userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->_userRepository = $userRepository;
    }

    public function createUser($data)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($data, [
                'profile_image' => 'nullable|file|mimes:jpeg,png,jpg|max:512000',
                'username' => 'required|string|max:255|unique:users,username',
                'password' => 'required|confirmed|min:8',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }

                return null;
            }


            $user = $this->_userRepository->save($data);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add user.");

            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $user = $this->_userRepository->getById($id);

            if ($user == null) {
                return false;
            }

            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get user details.");

            return null;
        }
    }



    public function update($data, $id)
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


            if (!Gate::allows('admin', Auth::user())) {
                throw new Exception();
            }

            $user = $this->_userRepository->getById($id);


            if ($user == null) {
                throw new Exception();
            }


            $existingClass = $this->_userRepository->getByCourseAndName($data['course_id'], $data['name']);

            if ($existingClass && $existingClass->id !== $id) {
                array_push($this->_errorMessage, "This name already exists for this course.");

                DB::rollBack();
                return null;
            }

            $user = $this->_userRepository->update($data, $id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update user details.");
            DB::rollBack();
            return null;
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $user = $this->_userRepository->getById($id);

            if (!Gate::allows('admin', Auth::user()) || $user == null) {
                throw new Exception();
            }

            $user = $this->_userRepository->deleteById($id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete user details.");

            DB::rollBack();
            return null;
        }
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 5;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];

            $user = $this->_userRepository->getAllBySearchTerm($data);

            $totalCount = $this->_userRepository->getTotalCountBySearchTerm($data);

            $results = array(
                "results" => $user->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            return $results;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get user select option.");
            DB::rollBack();
            return null;
        }
    }
}
