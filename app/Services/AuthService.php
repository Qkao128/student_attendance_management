<?php

namespace App\Services;

use Exception;
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
                'name' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                RateLimiter::hit($this->throttleKey($rateLimiterKey), 600);
                return null;
            }

            if (Auth::attempt(['name' => $data['name'], 'password' => $data['password']])) {
                RateLimiter::clear($this->throttleKey($rateLimiterKey));
                return true;
            } else {
                array_push($this->_errorMessage, 'Invalid name or password.');
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
