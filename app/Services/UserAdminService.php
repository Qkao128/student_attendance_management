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
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserAdminService extends Service
{
    protected $_userRepository;
    protected $_studentRepository;

    public function __construct(
        UserRepository $userRepository,
        StudentRepository $studentRepository,
    ) {
        $this->_userRepository = $userRepository;
        $this->_studentRepository = $studentRepository;
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
                'permission' => 'required|string|in:' . UserType::SuperAdmin()->key . ',' . UserType::Admin()->key,
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (!Auth::check() || !Auth::user()->hasAnyRole(UserType::SuperAdmin()->key)) {
                throw new Exception("You do not have the required permissions.");
            }

            if (isset($data['profile_image']) && !empty($data['profile_image'])) {
                $fileName = $this->generateFileName();
                $fileExtension = $data['profile_image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['profile_image']->storeAs('public/profile_image', $fileName);

                $data['profile_image'] = $fileName;
            }

            $user = $this->_userRepository->save($data);

            if ($data['permission'] === UserType::SuperAdmin()->key) {
                $user->assignRole(UserType::SuperAdmin()->key);
            } else if ($data['permission'] === UserType::Admin()->key) {
                $user->assignRole(UserType::Admin()->key);
            }

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

    public function getByTeacherId($id)
    {
        try {
            $user = $this->_userRepository->getByTeacherId($id);

            if ($user == null) {
                return false;
            }

            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get user details.");

            return null;
        }
    }


    public function getMonitorByStudentId($teacherId, $id)
    {
        try {
            $user = $this->_userRepository->getMonitorByStudentId($teacherId, $id);

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
                'username' => 'required|string|max:255|unique:users,username,' . $id,
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'required|confirmed|min:8',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $user = $this->_userRepository->getById($id);

            if ($user == null) {
                throw new Exception();
            }

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($user->id !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage this user.');
                }
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

            $errorMessage = $e->getMessage() ?: "Fail to update user details.";
            array_push($this->_errorMessage, $errorMessage);

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

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $user = $this->_userRepository->getById($id);

            if ($user == null) {
                throw new Exception();
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($user->id !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage this user.');
                }
            }

            $user = $this->_userRepository->update($data, $id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() ?: "Fail to update password.";
            array_push($this->_errorMessage, $errorMessage);

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

            if (!Auth::check() || !Auth::user()->hasAnyRole(UserType::SuperAdmin()->key)) {
                throw new Exception('You do not have permission to perform this action.');
            }

            if ($user == null) {
                throw new Exception();
            }

            $user = $this->_userRepository->deleteById($id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to delete user.");

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


    public function createMonitor($data, $teacherId)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($data, [
                'profile_image' => 'nullable|mimes:jpeg,png,jpg|max:512000',
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:8',
                'permission' => 'required|string|in:' . UserType::Monitor()->key,
                'student_id' => 'required|exists:students,id',
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

            $teacher = $this->_userRepository->getById($teacherId);

            if ($teacher == null) {
                throw new Exception();
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($teacher->id !== Auth::user()->id || $teacherId !== Auth::user()->id) {
                    throw new Exception('You are not authorized to manage this user.');
                }
            }

            if (isset($data['profile_image']) && !empty($data['profile_image'])) {
                $fileName = $this->generateFileName();
                $fileExtension = $data['profile_image']->extension();
                $fileName = $fileName . '.' . $fileExtension;

                $data['profile_image']->storeAs('public/profile_image', $fileName);

                $data['profile_image'] = $fileName;
            }

            $data['teacher_user_id'] = $teacherId;

            $user = $this->_userRepository->save($data);

            if ($data['permission'] === UserType::Monitor()->key) {
                $user->assignRole(UserType::Monitor()->key);
            } else {
                throw new Exception('Please select the permission for this monitor');
            }

            DB::commit();
            return $user;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() ?: "Fail to add user.";
            array_push($this->_errorMessage, $errorMessage);

            DB::rollBack();
            return null;
        }
    }

    public function updateMonitor($data, $teacherId, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'profile_image' => 'nullable|mimes:jpeg,png,jpg|max:512000',
                'username' => 'required|string|max:255|unique:users,username,' . $id,
                'email' => 'required|email|unique:users,email,' . $id,
                'student_id' => 'required|exists:students,id',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            $user = $this->_userRepository->getById($id);

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            if ($user == null || $user->teacher_user_id != $teacherId) {
                throw new Exception();
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($user->teacher_user_id !== Auth::user()->id || $teacherId !== Auth::user()->id || $user->teacher_user_id != $teacherId) {
                    throw new Exception('You are not authorized to manage this user.');
                }
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
            $errorMessage = $e->getMessage() ?: "Fail to update user details.";
            array_push($this->_errorMessage, $errorMessage);

            DB::rollBack();
            return null;
        }
    }

    public function updateMonitorPassword($data, $teacherId, $id)
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

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key, UserType::Monitor()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $user = $this->_userRepository->getById($id);

            if ($user == null || $user->teacher_user_id != $teacherId) {
                throw new Exception();
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($user->teacher_user_id !== Auth::user()->id || $teacherId !== Auth::user()->id || $user->teacher_user_id != $teacherId) {
                    throw new Exception('You are not authorized to manage this user.');
                }
            }

            if (Auth::user()->hasRole(UserType::Monitor()->key)) {
                $student = $this->_studentRepository->getById(Auth::user()->student_id);
                if (!$student || $user->student_id !== $student->id || Auth::user()->teacher_user_id !== $teacherId) {
                    throw new Exception('You are not authorized to manage attendance for this user');
                }
            }


            $user = $this->_userRepository->update($data, $id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() ?: "Fail to update password.";
            array_push($this->_errorMessage, $errorMessage);

            DB::rollBack();
            return null;
        }
    }


    public function deleteMonitorById($teacherId, $id)
    {
        DB::beginTransaction();

        try {
            if ($id == Auth::id()) {
                array_push($this->_errorMessage, "You are not allowed to delete own account");

                DB::rollBack();
                return null;
            }

            if (!Auth::check() || !Auth::user()->hasAnyRole([UserType::SuperAdmin()->key, UserType::Admin()->key])) {
                throw new Exception('You do not have permission to perform this action.');
            }

            $user = $this->_userRepository->getById($id);

            if ($user == null  || $user->teacher_user_id != $teacherId) {
                throw new Exception();
            }

            if (Auth::user()->hasRole(UserType::Admin()->key)) {
                if ($user->teacher_user_id !== Auth::user()->id || $teacherId !== Auth::user()->id || $user->teacher_user_id != $teacherId) {
                    throw new Exception('You are not authorized to manage this user.');
                }
            }

            $user = $this->_userRepository->deleteById($id);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            $errorMessage = $e->getMessage() ?: "Fail to delete user.";
            array_push($this->_errorMessage, $errorMessage);

            DB::rollBack();
            return null;
        }
    }
}
