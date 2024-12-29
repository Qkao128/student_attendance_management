<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Enums\Status;
use Illuminate\Http\Request;
use App\Services\ClassAdminService;
use App\Services\AttendanceAdminService;
use Illuminate\Support\Facades\Redirect;
use App\Services\AttendanceStatisticsAdminService;


class AttendanceStatisticsAdminController extends Controller
{
    private $_attendanceStatisticsAdminService;
    private $_attendanceAdminService;
    private $_classAdminService;

    public function __construct(AttendanceStatisticsAdminService $attendanceStatisticsAdminService, AttendanceAdminService $attendanceAdminService, ClassAdminService $classAdminService)
    {
        $this->_attendanceStatisticsAdminService = $attendanceStatisticsAdminService;
        $this->_attendanceAdminService = $attendanceAdminService;
        $this->_classAdminService = $classAdminService;
    }

    public function index(Request $request)
    {
        $date = now()->format('Y-m-d');
        $dashboard = $this->_attendanceStatisticsAdminService->getDashboardData($date);

        return view('attendance/statistics/index', [
            'dashboards' => $dashboard,
            'filterDate' => $date,
            'isHoliday' => $dashboard['is_holiday'] ?? false,
        ]);
    }

    public function pieChartData(Request $request)
    {
        $date = now()->format('Y-m-d');
        $dashboard = $this->_attendanceStatisticsAdminService->getDashboardData($date);

        if (!$dashboard) {
            return response()->json(['message' => 'No data found'], 404);
        }

        $attendanceCounts = $this->_attendanceAdminService->getStatusCounts($date);
        $attendanceCounts['total_students'] = $dashboard['student_summary']['total'];

        return response()->json([
            'status_statistics' => $attendanceCounts,
            'student_summary' => $dashboard['student_summary'],
            'selectedDate' => $date,
        ]);
    }

    public function pieMonthlyChartData(Request $request)
    {
        $month = $request->input('month');
        $courseId = $request->input('course_id'); // 选中的课程 ID（可能为 null）

        $attendanceCounts = $this->_attendanceStatisticsAdminService->getMonthlyStatusCounts($month, $courseId);
        $unavailableCount = $this->_attendanceStatisticsAdminService->getMonthlyUnavailableStudentCount($month, $courseId);
        $classCount = $this->_attendanceStatisticsAdminService->getClassCountByCourse($courseId);
        $presentRelatedCount = $this->_attendanceStatisticsAdminService->getPresentRelatedCount($month, $courseId);

        if (!$attendanceCounts) {
            return response()->json(['message' => 'No data found'], 404);
        }

        return response()->json([
            'data' => [
                'present_quantity' => $presentRelatedCount,
                'unavailable_students' => $unavailableCount,
                'total_classes' => $classCount,
                'status_statistics' => [
                    'total_students' => array_sum($attendanceCounts),
                    'total_status_counts' => $attendanceCounts,
                ],
            ],
        ]);
    }

    public function show($id, $date = null)
    {
        $month = $date ?: now()->format('Y-m');
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();
        $class = $this->_classAdminService->getByIdWithDetails($id);

        $attendanceData = $this->_attendanceStatisticsAdminService->getAttendanceTable($id, $startOfMonth, $endOfMonth);

        return view('attendance/statistics/show', [
            'attendanceTable' => $attendanceData['table'],
            'nonPresentDetails' => $attendanceData['nonPresentDetails'],
            'month' => $month,
            'class' => $class
        ]);
    }
}
