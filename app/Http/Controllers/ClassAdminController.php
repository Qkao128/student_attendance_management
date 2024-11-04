<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserAdminService;
use App\Services\ClassAdminService;
use App\Services\CourseAdminService;
use Illuminate\Support\Facades\Redirect;


class ClassAdminController extends Controller
{
    private $_classAdminService;
    private $_courseAdminService;
    private $_userAdminService;

    public function __construct(ClassAdminService $classAdminService, CourseAdminService $courseAdminService, UserAdminService $userAdminService)
    {
        $this->_classAdminService = $classAdminService;
        $this->_courseAdminService = $courseAdminService;
        $this->_userAdminService = $userAdminService;
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

    public function show($id)
    {
        $class = $this->_classAdminService->getById($id);

        if ($class === false) {
            abort(404);
        }

        if ($class == null) {
            $errorMessage = implode("<br>", $this->_classAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return view('class/show', compact('class'));
    }


    public function edit($id)
    {
        $class = $this->_classAdminService->getById($id);
        $course = $this->_courseAdminService->getById($class->course_id);
        $user = $this->_userAdminService->getById($class->user_id);

        if ($class === false || $course === false || $user == false) {
            abort(404);
        }

        if ($class == null && $course === false && $user == false) {
            $errorMessage = implode("<br>", $this->_classAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return view('class/edit', compact('class', 'course', 'user'));
    }


    public function update(Request $request, $id)
    {

        $data = $request->only([
            'class',
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
