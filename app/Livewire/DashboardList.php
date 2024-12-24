<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class DashboardList extends Component
{
    public $attendances; // 只保留考勤数据
    public $filterDate;
    public $filter = []; // 用来存储过滤条件
    public $page = 0;
    public $limitPerPage = 50;
    public $date_from;
    public $date_to;

    public function mount($date = null)
    {
        // 初始化日期
        $this->filterDate = $date ?? now()->format('Y-m-d');
        $this->loadDashboardData();
    }
    public function loadMore()
    {
        // 加载更多数据
        $this->page++;
        $this->loadDashboardData();
    }

    public function filterClass($className)
    {
        // 按班级名称过滤
        $this->filter['class'] = $className;
        $this->applyFilter();
    }

    public function applyFilter()
    {
        // 应用过滤条件，重置分页
        $this->page = 0;
        $this->attendances = [];
        $this->loadDashboardData();
    }


    #[On('updateDateFrom')]
    public function updateDateFrom($date)
    {
        $this->date_from = $date;

        if ($this->date_to && $this->date_to < $date) {
            $this->date_to = $date;
        }
    }

    #[On('updateDateTo')]
    public function updateDateTo($date)
    {
        $this->date_to = $date;

        if ($this->date_to < $this->date_from) {
            $this->date_from = $this->date_to;
        }
    }


    private function loadDashboardData()
    {
        $date = $this->filterDate;

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

        // 可选的班级名称筛选条件
        if (!empty($this->filter['class'])) {
            $classesQuery->where('classes.name', 'like', '%' . $this->filter['class'] . '%');
        }

        // 获取班级数据
        $classes = $classesQuery->get();

        // 过滤出当天有考勤记录的班级
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

    public function render()
    {
        return view('livewire.dashboard-list', [
            'attendances' => $this->attendances,
        ]);
    }
}
