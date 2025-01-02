<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class AttendanceStatisticsClassList extends Component
{
    public $attendances_statistics = []; // 保留考勤數據
    public $filterMonth;
    public $filter = [
        'courseId' => null,
        'is_user' => false,
        'user_id' => null,
    ]; // 用来存储过滤条件
    public $userId;
    public $page = 0;
    public $limitPerPage = 50;


    public function mount($userId)
    {
        $this->userId = $userId;
        $this->filter['is_user'] = false;
        $this->loadStatisticslistData();
    }

    public function loadMore()
    {
        $this->page++;
        $this->loadStatisticslistData();
    }

    #[On('updateCourse')]
    public function handleUpdateCourse($courseId)
    {
        $this->updateCourse($courseId);
    }

    public function updateCourse($courseId)
    {
        $this->filter['course_id'] = $courseId;
        $this->applyFilter();
    }

    #[On('updateMonth')]
    public function handleUpdateMonth($month)
    {
        $this->updateMonth($month);
    }

    public function updateMonth($month)
    {
        $this->filterMonth = $month;
        $this->applyFilter();
    }

    public function filterByCurrentUser()
    {
        $this->filter['is_user'] = !$this->filter['is_user'];

        if ($this->filter['is_user']) {
            $this->filter['user_id'] = $this->userId;
        } else {
            $this->filter['user_id'] = null;
        }

        $this->page = 0;
        $this->attendances_statistics = [];
        $this->loadStatisticslistData();
    }

    public function applyFilter()
    {
        $this->filter['is_user'] = $this->filter['user_id'] == $this->userId;
        $this->page = 0;
        $this->attendances_statistics = [];
        $this->loadStatisticslistData();
    }

    public function loadStatisticslistData()
    {
        $startOfMonth = Carbon::parse($this->filterMonth . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($this->filterMonth . '-01')->endOfMonth();

        // 获取该月的假期日期
        $holidays = DB::table('holidays')
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('date_from', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('date_to', [$startOfMonth, $endOfMonth]);
            })
            ->get();

        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $holidayRange = Carbon::parse($holiday->date_from)->daysUntil($holiday->date_to);
            foreach ($holidayRange as $day) {
                $holidayDates[] = $day->toDateString();
            }
        }

        // 班级查询
        $classesQuery = DB::table('classes')
            ->select(
                'classes.id as class_id',
                'classes.name as class_name',
                'courses.name as course_name'
            )
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false);

        if (!empty($this->filter['course_id'])) {
            $classesQuery->where('classes.course_id', $this->filter['course_id']);
        }

        if (!empty($this->filter['user_id'])) {
            $classesQuery->where('users.id', '=', $this->filter['user_id']);
        }

        // 確保結果為非空陣列
        $newClasses = $classesQuery
            ->offset($this->limitPerPage * $this->page)
            ->limit($this->limitPerPage)
            ->get();

        foreach ($newClasses as $class) {
            // 总学生数
            $studentCount = DB::table('students')
                ->where('class_id', $class->class_id)
                ->where('students.created_at', '<=', $endOfMonth)
                ->count();

            // 当月考勤数据
            $attendanceQuery = DB::table('attendances')
                ->where('class_id', $class->class_id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->whereNotIn(DB::raw('DATE(created_at)'), $holidayDates);

            $totalSubmissions = $attendanceQuery->count();

            $statusCounts = $attendanceQuery
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();

            // 獲取 Present 的數量
            $presentCount = $statusCounts['Present'] ?? 0;

            // 將其他相關狀態的數量納入 Present
            $presentCount += ($statusCounts['Late'] ?? 0) + ($statusCounts['LeaveApproval'] ?? 0);

            // 總數量
            $totalStatusCount = array_sum($statusCounts);

            // 計算 Present 的百分比
            $presentPercentage = $totalSubmissions > 0
                ? ($presentCount / $totalSubmissions) * 100
                : 0;

            $class->attendance_summary = [
                'student_count' => $studentCount,
                'present_count' => $presentCount,
                'total_submissions' => $totalSubmissions,
                'total_status_count' => $totalStatusCount,
                'present_percentage' => $presentPercentage, // 新增百分比數據
            ];

            $this->attendances_statistics[] = (array) $class;
        }
    }

    public function render()
    {
        return view('livewire.attendance-statistics-class-list', [
            'statistics' => $this->attendances_statistics,
        ]);
    }
}
