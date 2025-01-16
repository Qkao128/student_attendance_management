<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Enums\UserType;
use Illuminate\Http\Request;
use App\Services\ClassAdminService;
use App\Services\CourseAdminService;
use Illuminate\Support\Facades\Auth;
use App\Services\StudentAdminService;
use App\Services\AttendanceAdminService;
use Illuminate\Support\Facades\Redirect;


class AttendanceAdminController extends Controller
{
    private $_attendanceAdminService;
    private $_classAdminService;
    private $_studentAdminService;
    private $_courseAdminService;

    public function __construct(AttendanceAdminService $attendanceAdminService, ClassAdminService $classAdminService, StudentAdminService $studentAdminService, CourseAdminService $courseAdminService)
    {
        $this->_attendanceAdminService = $attendanceAdminService;
        $this->_classAdminService = $classAdminService;
        $this->_studentAdminService = $studentAdminService;
        $this->_courseAdminService = $courseAdminService;
    }

    public function index()
    {
        return view('attendance/index');
    }

    public function store($classId, $date, Request $request)
    {
        $data = $request->only([
            'students',
            'students.student_id',
            'students.file',
            'students.details',
            'students.status',
        ]);

        $result = $this->_attendanceAdminService->createOrUpdateAttendance($classId, $date, $data);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_attendanceAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('attendance.index')->with('success', "Attendance successfully processed.");
    }

    public function show($id, $date)
    {
        $class = $this->_classAdminService->getById($id);
        $holidaysAndActivities = $this->_attendanceAdminService->getIsDateHoliday($date);

        $isHolidays = $this->_attendanceAdminService->getIsDateTrueHoliday($date);


        $students = $this->_studentAdminService->getByClassId($class->id);

        if ($class === false || $students === false) {
            abort(404);
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $relatedClass = $this->_classAdminService->getByTeacherId($class->id);

            if ($relatedClass === false || $relatedClass != Auth::user()->id) {
                abort(403, 'Unauthorized access.');
            }
        }

        $date = $date ?? now()->format('Y-m-d');

        if ($class == null || $students == null) {
            $errorMessage = implode("<br>", $this->_attendanceAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        $attendanceCounts = $this->_attendanceAdminService->getStatusCountsByClassId($class->id, $date);

        $studentsByStatus = $this->_attendanceAdminService->getStudentsByStatus($class->id, $date);

        $studentsByStatusFormatted = [];
        foreach ($studentsByStatus as $status => $studentsList) {
            $key = array_search($status, Status::asArray());
            if ($key !== false) {
                $studentsByStatusFormatted[$key] = $studentsList;
            }
        }

        $course = $this->_courseAdminService->getByCourseId($class->course_id);

        $studentCount = $this->_studentAdminService->getStudentCountByClassId($class->id, $date);

        $arrivedCount = $this->_attendanceAdminService->getArrivedCountByClassId($class->id, $date);

        $latestAttendanceUpdatedAt = $this->_attendanceAdminService->getLatestAttendanceUpdatedAt($class->id, $date);

        $attendanceSummary = [
            'student_count' => $studentCount,
            'arrived_count' => $arrivedCount,
        ];

        return view('attendance/show', compact(
            'class',
            'students',
            'course',
            'holidaysAndActivities',
            'isHolidays',
            'attendanceCounts',
            'studentsByStatus',
            'attendanceSummary',
            'date',
            'latestAttendanceUpdatedAt',
        ));
    }
}
