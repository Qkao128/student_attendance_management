<?php

namespace App\Services;

use Exception;
use App\Enums\UserType;
use App\Models\student;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class AuthService extends Service
{
    protected $_userRepository;
    public $_errorMessage = [];

    public function __construct(UserRepository $userRepository)
    {
        $this->_userRepository = $userRepository;
    }
    public function loginUser($data)
    {
        try {
            $rateLimiterKey = 'login';
            $rateLimiter = $this->checkTooManyFailedAttempts($rateLimiterKey);

            if ($rateLimiter) {
                array_push($this->_errorMessage, "Too many attempts, please try again later.");
                return null;
            }

            $validator = Validator::make($data, [
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                RateLimiter::hit($this->throttleKey($rateLimiterKey), 600);
                return null;
            }

            if (Auth::attempt(['username' => $data['username'], 'password' => $data['password']])) {
                RateLimiter::clear($this->throttleKey($rateLimiterKey));

                // 檢查是否是 Monitor
                $user = Auth::user();
                if ($user->hasRole(UserType::Monitor()->key)) {
                    $student = DB::table('students')
                        ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
                        ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
                        ->where('students.id', $user->student_id)
                        ->select([
                            'classes.course_id',
                            'classes.is_disabled as class_is_disabled',
                            'classes.deleted_at as class_deleted_at',
                            'courses.deleted_at as course_deleted_at'
                        ])
                        ->groupBy('classes.course_id', 'classes.is_disabled', 'classes.deleted_at', 'courses.deleted_at') // 包含非聚合字段
                        ->first();

                    if (!$student || $student->class_deleted_at || $student->course_deleted_at) {
                        Auth::logout(); // 退出登录
                        array_push($this->_errorMessage, 'The class or course associated with the monitor has been deleted.');
                        return null;
                    }

                    if ($student->class_is_disabled) {
                        Auth::logout(); // 退出登录
                        array_push($this->_errorMessage, 'The class associated with the monitor is currently disabled.');
                        return null;
                    }
                }

                return true;
            } else {
                array_push($this->_errorMessage, 'Invalid username or password.');
                RateLimiter::hit($this->throttleKey($rateLimiterKey), 600);
                return null;
            }
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to login.");
            RateLimiter::hit($this->throttleKey($rateLimiterKey), 600);
            return null;
        }
    }

    public function logoutUser()
    {
        try {
            Auth::logout();
            Session::flush();
            return true;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to logout.");
            return null;
        }
    }

    public function throttleKey($key)
    {
        return $key . request()->ip();
    }

    public function checkTooManyFailedAttempts($key)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($key), 10)) {
            return false;
        }
        return true;
    }
}
