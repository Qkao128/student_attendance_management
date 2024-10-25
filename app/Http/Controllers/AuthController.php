<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Gate;

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
            $user = Auth::user();

            if (Gate::allows('teacher')) {
                return Redirect::route('dashboard')->with('error', 'You are already logged.');
            } elseif (Gate::allows('monitor')) {
                return Redirect::route('dashboard.monitor')->with('error', 'You are already logged.');
            }
        }

        return view('public/login');
    }

    public function login(Request $request)
    {
        $data = $request->only(['name', 'password']);

        if (Auth::check()) {
            $user = Auth::user();
            if (Gate::allows('teacher')) {
                return Redirect::route('dashboard')->with('error', 'You are already logged.');
            } elseif (Gate::allows('monitor')) {
                return Redirect::route('dashboard.monitor')->with('error', 'You are already logged.');
            }
        }

        $result = $this->_authService->loginUser($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_authService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        $user = Auth::user();

        if (Gate::allows('teacher')) {
            return Redirect::route('dashboard')->with('success', "Login successfully.");
        } elseif (Gate::allows('monitor')) {
            return Redirect::route('dashboard.monitor')->with('success', "Login successfully.");
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
