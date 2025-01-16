<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use Illuminate\Http\Request;
use App\Services\ClassAdminService;
use App\Services\CourseAdminService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class ClassAdminController extends Controller
{
    private $_classAdminService;
    private $_courseAdminService;

    public function __construct(ClassAdminService $classAdminService, CourseAdminService $courseAdminService)
    {
        $this->_classAdminService = $classAdminService;
        $this->_courseAdminService = $courseAdminService;
    }

    public function index()
    {
        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            return redirect()->route('dashboard');
        }

        return view('class/index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'name',
            'course_id',
            'user_id',
            'date_from',
            'date_to'
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
        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            return redirect()->route('dashboard');
        }


        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $relatedClass = $this->_classAdminService->getByTeacherId($id);

            if ($relatedClass === false || $relatedClass != Auth::user()->id) {
                abort(403, 'Unauthorized access.');
            }

            $class = $this->_classAdminService->getByIdWithDetails($id);

            if ($class === false) {
                abort(404);
            }

            if ($class == null) {
                $errorMessage = implode("<br>", $this->_classAdminService->_errorMessage);
                return back()->with('error', $errorMessage)->withInput();
            }

            return view('class/show', compact('class'));
        }


        $class = $this->_classAdminService->getByIdWithDetails($id);

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

        if ($class === false) {
            abort(404);
        }

        if ($class == null) {
            $errorMessage = implode("<br>", $this->_classAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return view('class/edit', compact('class'));
    }


    public function update(Request $request, $id)
    {

        $data = $request->only([
            'name',
            'course_id',
            'user_id',
            'is_disabled',
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

    public function selectOption(Request $request)
    {
        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
            "course_id" => $request->course_id ?? null,
            "teacher_id" => $request->teacher_id ?? null, // 接收 teacher_id
        ];

        $results = $this->_classAdminService->getSelectOption($data);

        return $results;
    }
}
