<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Gate;
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
        return view('public/login');
    }

    public function login(Request $request)
    {
        $data = $request->only(['username', 'password']);

        $result = $this->_authService->loginUser($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_authService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        if (Auth::user()->hasRole(UserType::SuperAdmin()->key)) {
            return Redirect::route('dashboard')->with('success', "Login successfully.");
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            return Redirect::route('dashboard')->with('success', "Login successfully.");
        }

        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            return Redirect::route('dashboard')->with('success', "Login successfully.");
        }

        return back()->with('error', 'Unauthorized access.');
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
}
