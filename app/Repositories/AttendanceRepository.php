<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Enums\Status;
use App\Enums\UserType;
use App\Models\student;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceRepository extends Repository
{
    protected $_db;

    public function __construct(Attendance $attendance)
    {
        $this->_db = $attendance;
    }

    public function bulkSave($dataList, $date)
    {
        $attendances = [];
        foreach ($dataList as $data) {
            $attendances[] = [
                "class_id" => $data['class_id'],
                "student_id" => $data['student_id'],
                "details" => $data['details'],
                "status" => $data['status'],
                "file" => $data['file'] ?? null,
                'created_at' => Carbon::parse($date)->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ];
        }

        $this->_db->insert($attendances);

        return $data;
    }

    public function bulkUpdate(array $records, string $date)
    {
        foreach ($records as $record) {
            $updateData = [
                'status' => $record['status'],
                'details' => $record['details'] ?? null,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];

            if (array_key_exists('file', $record)) {
                $updateData['file'] = $record['file'];
            }

            Attendance::where('student_id', $record['student_id'])
                ->where('class_id', $record['class_id'])
                ->whereDate('created_at', $date)
                ->update($updateData);
        }
    }

    public function getByClassId($classId, $date)
    {
        return $this->_db->where('class_id', $classId)->whereRaw('DATE(created_at) = ?', [$date])->get();
    }

    public function getAttendanceByClassId($classId, $date)
    {
        return $this->_db->where('class_id', $classId)
            ->whereRaw('DATE(created_at) = ?', [$date])
            ->get()
            ->keyBy('student_id'); // 確保返回的是鍵值對集合
    }

    public function getStatusCountsByClassId($classId, $date)
    {
        return Attendance::selectRaw('status, COUNT(*) as count')
            ->where('class_id', $classId)
            ->whereRaw('DATE(created_at) = ?', [$date])
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getStudentsByStatus($classId, $date)
    {
        return Attendance::with('student')
            ->where('class_id', $classId)
            ->whereRaw('DATE(created_at) = ?', [$date])
            ->get()
            ->groupBy('status')
            ->map(function ($group) {
                return $group->map(function ($attendance) {
                    return [
                        'student' => $attendance->student, // 學生基本資料
                        'details' => $attendance->details  // 出勤的詳細資訊
                    ];
                });
            })
            ->toArray();
    }

    public function getLatestUpdatedAt($classId, $date)
    {
        return Attendance::where('class_id', $classId)
            ->whereRaw('DATE(created_at) = ?', [$date])
            ->orderBy('updated_at', 'desc')
            ->value('updated_at');
    }

    public function getArrivedCountByClassId($classId, $date)
    {
        return Attendance::where('class_id', $classId)
            ->whereRaw('DATE(created_at) = ?', [$date])
            ->whereIn('status', [Status::Present()->key, Status::Late()->key, Status::LeaveApproval()->key])
            ->count();
    }

    public function getAttendedClassCount($date)
    {
        $query = DB::table('attendances')
            ->leftJoin('classes', 'attendances.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->whereDate('attendances.created_at', $date); // Ensure you're filtering by the correct table's date column

        // Apply additional filters based on user role
        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $query->where('classes.id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                $query->where('class_teachers.user_id', Auth::user()->id);
            }
        }

        $result = $query->distinct()->count('attendances.class_id');
        // Finally, get the count of distinct class IDs
        return $result;
    }

    public function getAttendedStudentCount($date)
    {
        $query = DB::table('attendances')
            ->leftJoin('classes', 'attendances.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->whereDate('attendances.created_at', $date)
            ->whereIn('attendances.status', ['Present', 'Late', 'LeaveApproval']);

        // Apply additional filters based on user role
        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $query->where('attendances.class_id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                $query->where('class_teachers.user_id', Auth::user()->id);
            }
        }

        // Count the matching students
        $result = $query->count();

        return $result;
    }

    public function getUnavailableStudentCount($date)
    {
        $query = DB::table('attendances')
            ->leftJoin('classes', 'attendances.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->whereDate('attendances.created_at', $date)
            ->whereIn('attendances.status', ['Medical', 'Absence']);

        // Apply additional filters based on user role
        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $query->where('attendances.class_id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                $query->where('class_teachers.user_id', Auth::user()->id);
            }
        }

        $result = $query->count();
        // Finally, get the count of distinct class IDs
        return $result;
    }

    public function getStatusCounts($date)
    {
        // 获取不同状态的学生数量
        $totalStatusCounts = DB::table('attendances')
            ->leftJoin('classes', 'attendances.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->selectRaw('attendances.status, COUNT(*) as count')
            ->whereDate('attendances.created_at', $date);

        // Apply additional filters based on user role
        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $totalStatusCounts->where('attendances.class_id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $totalStatusCounts->where('class_teachers.user_id', Auth::user()->id);
        }

        // 按状态分组并获取结果
        $totalStatusCounts = $totalStatusCounts->groupBy('attendances.status')
            ->pluck('count', 'status')
            ->toArray();

        // 确保每个状态都有返回值
        $statusList = ['Present', 'Absence', 'Medical', 'Late', 'LeaveApproval'];
        $totalStatusCounts = array_merge(array_fill_keys($statusList, 0), $totalStatusCounts);

        return [
            'total_status_counts' => $totalStatusCounts, // 每个状态对应的数量
        ];
    }

    public function getMonthlyAttendanceData(Carbon $startOfMonth, Carbon $endOfMonth, ?int $courseId, array $holidayDates)
    {
        $query = DB::table('attendances')
            ->leftJoin('students', 'attendances.student_id', '=', 'students.id')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->select('attendances.status', DB::raw('COUNT(attendances.id) as count'))
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->whereBetween('attendances.created_at', [$startOfMonth, $endOfMonth]);

        // 篩選特定課程
        if ($courseId) {
            $query->where('classes.course_id', $courseId);
        }

        // 根據角色應用條件
        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $query->where('students.class_id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $isAdminClassTeacher = DB::table('class_teachers')
                ->where('user_id', Auth::user()->id)
                ->exists();

            if ($isAdminClassTeacher) {
                $query->where('class_teachers.user_id', Auth::user()->id);
            } else {
                // 確保 Admin 用戶無對應記錄時，返回空結果
                return [];
            }
        }

        // 排除假期日期
        if (!empty($holidayDates)) {
            $query->whereNotIn(DB::raw('DATE(attendances.created_at)'), $holidayDates);
        }

        // 按狀態分組
        $result = $query->groupBy('attendances.status')->pluck('count', 'status')->toArray();


        // 返回統計數據
        return $result;
    }


    public function getMonthlyUnavailableStudentCount(Carbon $startOfMonth, Carbon $endOfMonth, ?int $courseId, array $holidayDates)
    {
        $query = DB::table('attendances')
            ->leftJoin('students', 'attendances.student_id', '=', 'students.id')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->whereBetween('attendances.created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('attendances.status', ['Medical', 'Absence']);

        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $query->where('attendances.class_id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                $query->where('class_teachers.user_id', Auth::user()->id);
            }
        }

        if (!empty($holidayDates)) {
            $query->whereNotIn(DB::raw('DATE(attendances.created_at)'), $holidayDates);
        }

        if ($courseId) {
            $query->where('courses.id', $courseId);
        }

        $result =  $query->count();

        return $result;
    }


    public function getClassCountByCourse(?int $courseId)
    {
        $query = DB::table('classes')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false); // 只篩選 is_disabled = false 的班級


        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $query->where('attendances.class_id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                $query->where('class_teachers.user_id', Auth::user()->id);
            }
        }

        if ($courseId) {
            $query->where('course_id', $courseId); // 篩選特定課程的班級
        }

        $result = $query->count();

        return $result; // 返回符合條件的班級數量
    }

    public function getPresentRelatedCount(Carbon $startOfMonth, Carbon $endOfMonth, ?int $courseId, array $holidayDates)
    {
        $query = DB::table('attendances')
            ->leftJoin('students', 'attendances.student_id', '=', 'students.id')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->where('classes.deleted_at', '=', null)
            ->where('courses.deleted_at', '=', null)
            ->where('users.deleted_at', '=', null)
            ->where('classes.is_disabled', false)
            ->whereBetween('attendances.created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('attendances.status', ['Present', 'Late', 'LeaveApproval']);

        if ($courseId) {
            $query->where('courses.id', $courseId);
        }

        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $query->where('attendances.class_id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                $query->where('class_teachers.user_id', Auth::user()->id);
            }
        }

        if (!empty($holidayDates)) {
            $query->whereNotIn(DB::raw('DATE(attendances.created_at)'), $holidayDates);
        }

        $result =  $query->count();

        return $result;
    }


    public function getAttendanceRecords($classId, Carbon $startOfMonth, Carbon $endOfMonth)
    {
        $records = DB::table('attendances')
            ->leftJoin('classes', 'attendances.class_id', '=', 'classes.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->where('attendances.class_id', $classId)
            ->whereBetween('attendances.created_at', [$startOfMonth, $endOfMonth]);

        if (Auth::user()->hasRole(UserType::Monitor()->key)) {
            $student = Student::where('id', Auth::user()->student_id)->first();
            if ($student) {
                $records->where('attendances.class_id', $student->class_id);
            }
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $classTeacher = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($classTeacher != null) {
                $records->where('class_teachers.user_id', Auth::user()->id);
            }

            $classTeacherIds = DB::table('class_teachers')
                ->where('user_id', Auth::user()->id)
                ->pluck('class_id')
                ->toArray();

            if (!in_array($classId, $classTeacherIds)) {
                abort(403, 'Unauthorized access to this class data.');
            }
        }

        $records = $records->orderBy('attendances.created_at', 'desc')->get();

        $attendanceRecords = [];
        foreach ($records as $record) {
            $date = Carbon::parse($record->created_at)->toDateString();
            $attendanceRecords[$record->student_id][$date] = $record->status;
            $attendanceRecords[$record->student_id]['details'][$date] = $record->details;
            $attendanceRecords[$record->student_id]['files'][$date] = $record->file; // 添加文件信息
        }

        return $attendanceRecords;
    }

    public function getByStudentAndClassAndDate($studentId, $classId, $date)
    {
        return Attendance::where('student_id', $studentId)
            ->where('class_id', $classId)
            ->whereDate('created_at', $date)
            ->first();
    }
}
