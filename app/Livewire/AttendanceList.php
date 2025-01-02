<?php

namespace App\Livewire;

use App\Enums\Status;
use App\Enums\UserType;
use App\Models\Classes;
use App\Models\Student;
use Livewire\Component;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceList extends Component
{

    public $filter = [
        'date' => null,
        'class_id' => null,
        'course_id' => null,
        'is_submitted' => null,
        'is_user' => false, // 新增篩選用戶的字段
        'user_id' => null,  // 用於篩選的用戶 ID
    ];
    public $userId;
    public $isHoliday = false; // 新增屬性標記是否為假期
    public $attendances = [];
    public $page = 0;
    public $limitPerPage = 50;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->filter['date'] = now()->format('Y-m-d');
        $this->filter['is_user'] = false; // 初始化為非高亮狀態
        $this->checkIfHoliday(); // 初始化時檢查是否是假期
        $this->loadData();
    }

    public function loadMore()
    {
        $this->page++;
        $this->loadData();
    }

    // 用於切換日期的邏輯方法
    public function changeDate($isForward)
    {
        $currentDate = Carbon::parse($this->filter['date']);
        if ($isForward) {
            $this->filter['date'] = $currentDate->addDay()->format('Y-m-d'); // 增加一天
        } else {
            $this->filter['date'] = $currentDate->subDay()->format('Y-m-d'); // 減少一天
        }
        $this->checkIfHoliday(); // 更新日期後重新檢查是否是假期
        $this->attendances = [];
        $this->page = 0;
        $this->loadData();
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
        $this->attendances = [];
        $this->loadData();
    }

    public function filterClass($className)
    {
        $this->filter['class'] = $className;
        $this->applyFilter(); // 重新加載數據
    }

    public function resetFilter()
    {
        foreach ($this->filter as $key => $value) {
            if ($key === 'date') {
                $this->filter[$key] = now()->format('Y-m-d');
            } else {
                $this->filter[$key] = null;
            }
        }

        $this->applyFilter(); // 應用過濾條件
    }

    public function updateSubmittedStatus($isSubmitted)
    {
        $this->filter['is_submitted'] = $isSubmitted;
        $this->attendances = [];
        $this->page = 0;
        $this->loadData();
    }

    public function applyFilter()
    {
        // 如果選擇的用戶是當前用戶，設置 is_user 為 true
        $this->filter['is_user'] = $this->filter['user_id'] == $this->userId;
        $this->checkIfHoliday();
        $this->page = 0;
        $this->attendances = [];
        $this->loadData();
    }

    public function checkIfHoliday()
    {
        $date = Carbon::parse($this->filter['date'])->format('Y-m-d');

        $this->isHoliday = DB::table('holidays')
            ->where(function ($query) use ($date) {
                $query->where('date_from', '<=', $date)
                    ->where('date_to', '>=', $date);
            })
            ->exists();
    }

    // 加載數據的邏輯方法
    private function loadData()
    {
        $classesQuery = Classes::query()
            ->select('classes.id as class_id', 'classes.name as class_name', 'courses.name as course_name', 'users.username as teacher_name', 'classes.created_at')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoinSub(
                Attendance::select('class_id', Attendance::raw('MAX(updated_at) as latest_updated_at'))
                    ->whereDate('created_at', $this->filter['date'])
                    ->groupBy('class_id'),
                'latest_attendance',
                'latest_attendance.class_id',
                'classes.id'
            )
            ->where('classes.is_disabled', false)
            ->orderBy('latest_attendance.latest_updated_at', 'desc');


        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            // 使用當前用戶的 student_id 找到對應的班級
            $student = Student::where('id', Auth::user()->student_id)->first();

            if ($student) {
                // 只篩選 Monitor 的班級
                $classesQuery->where('classes.id', $student->class_id);
            }
        }

        // 根據提交狀態篩選
        if (!is_null($this->filter['is_submitted'])) {
            if ($this->filter['is_submitted']) {
                // 已提交：有到達數據
                $classesQuery->whereHas('attendances', function ($query) {
                    $query->where('status', '!=', null)
                        ->whereDate('created_at', $this->filter['date']);
                });
            } else {
                // 未提交：到達數據為0
                $classesQuery->whereDoesntHave('attendances', function ($query) {
                    $query->where('status', '!=', null)
                        ->whereDate('created_at', $this->filter['date']);
                });
            }
        }

        if (!empty($this->filter['course_id'])) {
            $classesQuery->where('courses.id', '=', $this->filter['course_id']);
        }

        if (!empty($this->filter['user_id'])) {
            $classesQuery->where('users.id', '=', $this->filter['user_id']);
        }

        if (!empty($this->filter['class'])) {
            $classesQuery->where('classes.name', 'like', '%' . $this->filter['class'] . '%');
        }

        $classes = $classesQuery->offset($this->page * $this->limitPerPage)
            ->limit($this->limitPerPage)
            ->get();

        foreach ($classes as $class) {
            $attendanceSummary = $this->getAttendanceSummary($class->class_id, $this->filter['date']);
            $class->attendance_summary = $attendanceSummary;
        }

        if ($this->page == 0) {
            $this->attendances = $classes->toArray();
        } else {
            $this->attendances = array_merge($this->attendances, $classes->toArray());
        }
    }


    // 獲取考勤統計數據
    protected function getAttendanceSummary($classId, $date)
    {
        $studentCount = Student::where('class_id', $classId)
            ->whereDate('enrollment_date', '<=', $date)
            ->count();

        $statusCounts = Attendance::selectRaw('status, COUNT(*) as count')
            ->where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $arrivedCount = Attendance::where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->whereIn('status', [
                Status::Present()->key,
                Status::Late()->key,
                Status::LeaveApproval()->key
            ])->count();

        // 獲取該班級指定日期的最新的 updated_at 時間
        $latestAttendanceTime = Attendance::where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->orderBy('updated_at', 'desc')
            ->value('updated_at');

        // 檢查是否存在任何考勤記錄
        $hasAttendance = Attendance::where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->exists();

        return [
            'student_count' => $studentCount,
            'arrived_count' => $arrivedCount,
            'status_counts' => $statusCounts,
            'latest_attendance_time' => $latestAttendanceTime ? Carbon::parse($latestAttendanceTime)->format('d-m-Y h:i A') : null,
            'has_attendance' => $hasAttendance, // 新增字段
        ];
    }

    public function render()
    {
        return view('livewire.attendance-list', [
            'isHoliday' => $this->isHoliday, // 傳遞假期狀態到前端
        ]);
    }
}
