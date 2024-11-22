<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserAdminService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class UserAdminController extends Controller
{
    private $_userAdminService;

    public function __construct(UserAdminService $userAdminService)
    {
        $this->_userAdminService = $userAdminService;
    }

    public function index()
    {
        return view('account/index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'profile_image',
            'username',
            'email',
            'password',
            'password_confirmation',
        ]);
        $result = $this->_userAdminService->createUser($data);


        if ($result == null) {
            $errorMessage = implode("<br>", $this->_userAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('user.index')->with('success', "Account successfully added.");
    }

    public function show($id)
    {
        $user = $this->_userAdminService->getById($id);

        if ($user === false) {
            abort(404);
        }

        if ($user == null) {
            $errorMessage = implode("<br>", $this->_userAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return view('account/show', compact('user'));
    }


    public function edit($id)
    {
        $user = $this->_userAdminService->getById($id);

        if ($user === false) {
            abort(404);
        }

        if ($user == null) {
            $errorMessage = implode("<br>", $this->_userAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return view('account/edit', compact('user'));
    }


    public function update(Request $request, $id)
    {

        $data = $request->only([
            'profile_image',
            'username',
            'email',
        ]);

        $result = $this->_userAdminService->update($data, $id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_userAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('user.index')->with('success', "Account details successfully updated.");
    }

    public function updatePassword(Request $request, $id)
    {
        $data = $request->only([
            'password',
            'password_confirmation',
        ]);

        $result = $this->_userAdminService->updatePassword($data, $id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_userAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return back()->with('success', "Password successfully updated.");
    }

    public function destroy($id)
    {

        $result = $this->_userAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_userAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('user.index')->with('success', "Account successfully deleted.");
    }


    public function selectOption(Request $request)
    {
        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
        ];

        $results = $this->_userAdminService->getSelectOption($data);
        return $results;
    }
}
