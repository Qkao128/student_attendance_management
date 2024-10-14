<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    private $_authService;

    public function __construct(AuthService $authService)
    {
        $this->_authService = $authService;
    }

    public function loginIndex()
    {
        if (Auth::check()) {
            return Redirect::route('dashboard');
        }

        return view('public/login');
    }

    public function login(Request $request)
    {
        $data = $request->only(['name', 'password']);

        if (Auth::check()) {
            return Redirect::route('dashboard')->with('error', 'You are already logged in.');
        }

        $result = $this->_authService->loginUser($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_authService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('dashboard')->with('success', "Login successfully.");
    }

    public function logout()
    {
        $result = $this->_authService->logoutUser();

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_authService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('login.index');
    }

    public function registerIndex()
    {
        if (Auth::check()) {
            return Redirect::route('dashboard');
        }

        return view('public/register');
    }

    public function register(Request $request)
    {
        if (Auth::check()) {
            return Redirect::route('dashboard')->with('error', 'You are already logged in.');
        }

        $data = $request->only(['profile_image', 'name', 'password', 'password_confirmation']);
        $result = $this->_authService->registerUser($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_authService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('login.index')->with('success', "Successfully registered.");
    }
}
