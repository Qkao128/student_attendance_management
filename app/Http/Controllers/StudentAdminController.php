<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Services\StudentAdminService;
use Illuminate\Support\Facades\Redirect;


class StudentAdminController extends Controller
{
    private $_studentAdminService;

    public function __construct(StudentAdminService $studentAdminService)
    {
        $this->_studentAdminService = $studentAdminService;
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

    public function edit($id)
    {
        $course = $this->_studentAdminService->getById($id);

        if ($course === false) {
            abort(404);
        }

        if ($course === false) {
            $errorMessage = implode("<br>", $this->_studentAdminService->_errorMessage);
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

        $result = $this->_studentAdminService->update($data, $id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_studentAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('class.index')->with('success', "Student details successfully updated.");
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
