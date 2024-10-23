<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Services\CourseAdminService;


class CourseAdminController extends Controller
{
    private $_courseAdminService;

    public function __construct(CourseAdminService $courseAdminService)
    {
        $this->_courseAdminService = $courseAdminService;
    }

    public function index()
    {
        return view('course/index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'name',
        ]);
        $result = $this->_courseAdminService->createCourse($data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_courseAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('course.index', $result->id)->with('success', "Course successfully added.");
    }


    public function update(Request $request, $id)
    {

        $data = $request->only([
            'name',
        ]);

        $result = $this->_courseAdminService->update($data, $id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_courseAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('course.index')->with('success', "Course details successfully updated.");
    }

    public function destroy($id)
    {
        $result = $this->_courseAdminService->deleteById($id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_courseAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('course.index')->with('success', "Course successfully deleted.");
    }
}
