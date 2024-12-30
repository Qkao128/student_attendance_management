<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\dashboardAdminService;
use App\Services\AttendanceAdminService;


class DashboardAdminController extends Controller
{
    private $_dashboardAdminService;
    private $_attendanceAdminService;

    public function __construct(DashboardAdminService $dashboardAdminService, AttendanceAdminService $attendanceAdminService)
    {
        $this->_dashboardAdminService = $dashboardAdminService;
        $this->_attendanceAdminService = $attendanceAdminService;
    }

    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $dashboard = $this->_dashboardAdminService->getDashboardData($date);

        return view('dashboard.index', [
            'dashboards' => $dashboard,
            'filterDate' => $date,
        ]);
    }

    public function data(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $dashboard = $this->_dashboardAdminService->getDashboardData($date);

        if (!$dashboard) {
            return response()->json(['message' => 'No data found'], 404);
        }

        return response()->json([
            'dashboards' => $dashboard,
            'selectedDate' => $date,
        ]);
    }

    public function pieChartData(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $dashboard = $this->_dashboardAdminService->getDashboardData($date);

        if (!$dashboard) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // 获取考勤状态统计
        $attendanceCounts = $this->_attendanceAdminService->getStatusCounts($date);

        // 将学生总数加入 status_statistics
        $attendanceCounts['total_students'] = $dashboard['student_summary']['total'];

        // 返回数据
        return response()->json([
            'status_statistics' => $attendanceCounts,
            'student_summary' => $dashboard['student_summary'],
            'selectedDate' => $date,
        ]);
    }

    public function getHolidayStatus(Request $request)
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json(['error' => 'Invalid date provided'], 400);
        }

        $dashboard = $this->_dashboardAdminService->getIsDateHoliday($date);

        return response()->json([
            'isHoliday' => $dashboard['is_holiday'] ?? false,
        ]);
    }
}
