<?php

namespace App\Services;

use Exception;
use App\Enums\UserType;
use App\Services\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
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
                'profile_image' => 'nullable|mimes:jpeg,png,jpg|max:512000',
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:8',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (Auth::user()->hasAnyRole(UserType::SuperAdmin()->key)) {
                throw new Exception();
            }

            if (isset($data['profile_image']) && !empty($data['profile_image'])) {
                $fileName = $this->generateFileName();
                $fileExtension = $data['profile_image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['profile_image']->storeAs('public/profile_image', $fileName);

                $data['profile_image'] = $fileName;
            }

            $user = $this->_userRepository->save($data);
            $user->assignRole(UserType::Admin()->key);

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
                'profile_image' => 'nullable|file|mimes:jpeg,png,jpg|max:512000',
                'username' => 'required|string|max:255|unique:users,username' . $id,
                'email' => 'required|email|unique:users,email' . $id,
                'password' => 'required|confirmed|min:8',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $user = $this->_userRepository->getById($id);

            if ($user == null || $user->hasAnyRole(UserType::Admin()->key) != true || $user->hasAnyRole(UserType::SuperAdmin()->key) != true) {
                throw new Exception();
            }

            if (!empty($data['profile_image'])) {
                if ($user['profile_image'] != null && Storage::exists('public/profile_image/' . $user['profile_image'])) {
                    Storage::delete('public/profile_image/' . $user['profile_image']);
                }

                $fileName = $this->generateFileName();
                $fileExtension = $data['profile_image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['profile_image']->storeAs('public/profile_image', $fileName);
                $data['profile_image'] = $fileName;
            }

            $user = $this->_userRepository->update($data, $id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update staff details.");

            DB::rollBack();
            return null;
        }
    }

    public function updatePassword($data, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'password' => 'required|confirmed|min:8',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }

                return null;
            }

            $user = $this->_userRepository->getById($id);

            if ($user == null || $user->hasAnyRole(UserType::Admin()->key) != true || $user->hasAnyRole(UserType::SuperAdmin()->key) != true) {
                throw new Exception();
            }

            $user = $this->_userRepository->update($data, $id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update password.");

            DB::rollBack();
            return null;
        }
    }


    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            if ($id == Auth::id()) {
                array_push($this->_errorMessage, "You are not allowed to delete own account");

                DB::rollBack();
                return null;
            }

            $user = $this->_userRepository->getById($id);

            if ($user == null || $user->hasAnyRole(UserType::Admin()->key) != true) {
                throw new Exception();
            }

            $user = $this->_userRepository->deleteById($id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete account.");

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


    public function generateFileName()
    {
        return Str::random(5) . Str::uuid() . Str::random(5);
    }
}
