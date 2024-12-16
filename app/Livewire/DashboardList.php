<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardList extends Component
{
    public $dashboards;
    public $attendances;
    public $filter = [
        'date' => null,
    ];
    public $page = 0;
    public $limitPerPage = 50;

    public function mount()
    {
        $this->filter['date'] = now()->format('Y-m-d');
        $this->loadDashboardData();
    }

    public function loadMore()
    {
        $this->page++;
        $this->loadDashboardData();
    }

    public function changeDate($isForward)
    {
        $currentDate = Carbon::parse($this->filter['date']);
        $this->filter['date'] = $isForward
            ? $currentDate->addDay()->format('Y-m-d')
            : $currentDate->subDay()->format('Y-m-d');

        $this->loadDashboardData();

        $this->dispatch('update-pie-chart', [
            'statusStatistics' => $this->dashboards['status_statistics'],
        ]);
    }

    public function filterClass($className)
    {
        $this->filter['class'] = $className;
        $this->applyFilter();
    }


    public function applyFilter()
    {
        $this->page = 0;
        $this->attendances = [];
        $this->loadDashboardData();
    }


    private function loadDashboardData()
    {
        $date = $this->filter['date'];

        // 查詢班級數據
        $classesQuery = DB::table('classes')
            ->select(
                'classes.id as class_id',
                'classes.name as class_name',
                'courses.name as course_name',
                'users.username as teacher_name',
                DB::raw("(SELECT MAX(updated_at) FROM attendances WHERE attendances.class_id = classes.id AND DATE(attendances.created_at) = '$date') as latest_updated_at")
            )
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.is_disabled', false)
            ->orderBy('latest_updated_at', 'desc');

        // 可選的班級名稱篩選條件
        if (!empty($this->filter['class'])) {
            $classesQuery->where('classes.name', 'like', '%' . $this->filter['class'] . '%');
        }

        // 獲取班級
        $classes = $classesQuery->get();

        // 過濾出當天有提交記錄的班級
        $this->attendances = $classes->map(function ($class) use ($date) {
            $hasAttendance = DB::table('attendances')
                ->where('class_id', $class->class_id)
                ->whereDate('created_at', $date)
                ->exists();

            if ($hasAttendance) {
                $attendanceSummary = $this->getAttendanceSummary($class->class_id, $date);
                $class->attendance_summary = $attendanceSummary;
                return (array) $class;
            }
            return null;
        })->filter()->toArray();

        // 班級統計數據
        $classCount = DB::table('classes')
            ->where('is_disabled', false)
            ->whereDate('created_at', '<=', $date)
            ->count();

        $attendedClasses = count($this->attendances);

        // 學生統計數據
        $totalStudentsInClasses = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->where('classes.is_disabled', false)
            ->whereDate('students.created_at', '<=', $date)
            ->count();

        $attendedStudents = array_sum(array_column(array_column($this->attendances, 'attendance_summary'), 'arrived_count'));

        $unavailableStudents = DB::table('attendances')
            ->whereDate('created_at', $date)
            ->whereIn('status', ['Medical', 'Absence'])
            ->count();

        $statusStatistics = $this->getStatusStatistics($date);

        $this->dashboards = [
            'class_summary' => [
                'attended' => $attendedClasses,
                'total' => $classCount,
            ],
            'student_summary' => [
                'attended' => $attendedStudents,
                'total' => $totalStudentsInClasses,
                'unavailable' => $unavailableStudents,
            ],
            'status_statistics' => $statusStatistics,
        ];
    }

    protected function getAttendanceSummary($classId, $date)
    {
        // 获取学生总数
        $studentCount = DB::table('students')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->where('classes.id', $classId)
            ->where('classes.is_disabled', false)
            ->whereDate('students.created_at', '<=', $date) // 确保学生创建时间小于等于指定日期
            ->count();

        $totalStudents = DB::table('students')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->where('classes.is_disabled', false)
            ->whereDate('students.created_at', '<=', $date)
            ->count();



        // 统计不同考勤状态的数量
        $statusCounts = DB::table('attendances')
            ->selectRaw('status, COUNT(*) as count')
            ->where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 统计到达人数（包括 Present、Late、LeaveApproval 状态）
        $arrivedCount = DB::table('attendances')
            ->where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->whereIn('status', ['Present', 'Late', 'LeaveApproval']) // 包括多个状态的学生
            ->count();

        // 获取最新的考勤更新时间
        $latestAttendanceTime = DB::table('attendances')
            ->where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->orderBy('updated_at', 'desc')
            ->value('updated_at');

        return [
            'student_count' => $studentCount,
            'arrived_count' => $arrivedCount,
            'status_counts' => $statusCounts,
            'latest_attendance_time' => $latestAttendanceTime ? Carbon::parse($latestAttendanceTime)->format('d-m-Y h:i A') : null,
        ];
    }

    public function getStatusStatistics($date)
    {

        // 获取不同状态的学生数量
        $totalStatusCounts = DB::table('attendances')
            ->selectRaw('status, COUNT(*) as count')
            ->whereDate('created_at', $date)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total_status_counts' => $totalStatusCounts, // 每个状态对应的数量
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-list', [
            'dashboards' => $this->dashboards,
            'attendances' => $this->attendances,
        ]);
    }
}
