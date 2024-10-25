<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Services\ClassAdminService;


class ClassAdminController extends Controller
{
    private $_classAdminService;

    public function __construct(ClassAdminService $classAdminService)
    {
        $this->_classAdminService = $classAdminService;
    }

    public function index()
    {
        return view('class/index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'class',
            'course_id',
            'user_id'
        ]);

        $result = $this->_classAdminService->createClass($data);


        if ($result == null) {
            $errorMessage = implode("<br>", $this->_classAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('class.index')->with('success', "Class successfully added.");
    }


    public function update(Request $request, $id)
    {

        $data = $request->only([
            'name',
            'course_id',
            'user_id'
        ]);

        $result = $this->_classAdminService->update($data, $id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_classAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('class.index')->with('success', "Class details successfully updated.");
    }

    public function destroy($id)
    {
        $result = $this->_classAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_classAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('class.index')->with('success', "Class successfully deleted.");
    }
}
