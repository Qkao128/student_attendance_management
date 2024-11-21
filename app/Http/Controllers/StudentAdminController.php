<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClassAdminService;
use App\Services\StudentAdminService;
use Illuminate\Support\Facades\Redirect;


class StudentAdminController extends Controller
{
    private $_studentAdminService;
    private $_classAdminService;

    public function __construct(StudentAdminService $studentAdminService, ClassAdminService $classAdminService)
    {
        $this->_studentAdminService = $studentAdminService;
        $this->_classAdminService = $classAdminService;
    }

    public function store(Request $request, $classId)
    {
        $data = $request->only([
            'student',
            'student.profile_image',
            'student.name',
            'student.gender',
        ]);

        $result = $this->_studentAdminService->createStudent($data, $classId);

        if ($result === null) {
            $errorMessage = implode("<br>", $this->_studentAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('class.show', $classId)->with('success', "Student successfully added.");
    }

    public function edit($classId, $id)
    {
        $class = $this->_classAdminService->getById($classId);
        $student = $this->_studentAdminService->getById($id);

        if ($student === false || $class === false) {
            abort(404);
        }

        if ($student === false || $class === false) {
            $errorMessage = implode("<br>", $this->_studentAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return view('class/student/edit', compact('class', 'student'));
    }


    public function update(Request $request, $classId, $id)
    {

        $data = $request->only([
            'profile_image',
            'name',
            'gender',
        ]);


        $result = $this->_studentAdminService->update($data, $classId, $id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_studentAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('class.show', $classId)->with('success', "Student details successfully updated.");
    }

    public function destroy($classId, $id)
    {
        $result = $this->_studentAdminService->deleteById($classId, $id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_studentAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('class.show', $classId)->with('success', "Student successfully deleted.");
    }

    public function datatable(Request $request)
    {
        $classId = $request->input('class_id');

        $data = $this->_studentAdminService->getDatatable($classId);

        return  $data;
    }
}
