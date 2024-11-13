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

        return Redirect::route('course.index')->with('success', "Course successfully added.");
    }

    public function edit($id)
    {
        $course = $this->_courseAdminService->getById($id);

        if ($course === false) {
            abort(404);
        }

        if ($course === false) {
            $errorMessage = implode("<br>", $this->_courseAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return view('course/edit', compact('course'));
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

    public function selectOption(Request $request)
    {
        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
        ];

        $results = $this->_courseAdminService->getSelectOption($data);
        return $results;
    }
}
