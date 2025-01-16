<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Enums\UserType;
use App\Models\student;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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


    #[On('updateDate')]
    public function handleUpdateDate($date)
    {
        $this->updateDate($date);
    }

    public function updateDate($date)
    {
        $this->filterDate = $date;
        $this->applyFilter();
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
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->orderBy('latest_updated_at', 'desc');

        // 可选的班级名称筛选条件
        if (!empty($this->filter['class'])) {
            $classesQuery->where('classes.name', 'like', '%' . $this->filter['class'] . '%');
        }

        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            // 使用當前用戶的 student_id 找到對應的班級
            $student = Student::where('id', Auth::user()->student_id)->first();

            if ($student) {
                // 只篩選 Monitor 的班級
                $classesQuery->where('classes.id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            // 使用當前用戶的 student_id 找到對應的班級
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                // 只篩選 Monitor 的班級
                $classesQuery->where('class_teachers.user_id', Auth::user()->id);
            }
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
        // 確認用戶身份
        $user = Auth::user();
        $query = DB::table('students')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->where('classes.id', $classId)
            ->where('classes.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->whereDate('students.created_at', '<=', $date);

        // 計算學生總數
        $studentCount = $query->count();

        // 考勤狀態統計
        $statusCounts = DB::table('attendances')
            ->selectRaw('status, COUNT(*) as count')
            ->where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->groupBy('status');


        $statusCounts = $statusCounts->pluck('count', 'status')->toArray();

        // 統計到達人數
        $arrivedCountQuery = DB::table('attendances')
            ->where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->whereIn('status', ['Present', 'Late', 'LeaveApproval']);

        $arrivedCount = $arrivedCountQuery->count();

        // 最新考勤時間
        $latestAttendanceTimeQuery = DB::table('attendances')
            ->where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->orderBy('updated_at', 'desc');


        $latestAttendanceTime = $latestAttendanceTimeQuery->value('updated_at');

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
