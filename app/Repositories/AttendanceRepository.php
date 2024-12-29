<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Enums\Status;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class AttendanceRepository extends Repository
{
    protected $_db;

    public function __construct(Attendance $attendance)
    {
        $this->_db = $attendance;
    }

    public function bulkSave($dataList)
    {
        $attendances = [];
        foreach ($dataList as $data) {
            $attendances[] = [
                "class_id" => $data['class_id'],
                "student_id" => $data['student_id'],
                "details" => $data['details'],
                "status" => $data['status'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ];
        }

        $this->_db->insert($attendances);

        return $data;
    }

    public function bulkUpdate($dataList, $date)
    {
        foreach ($dataList as $data) {
            $this->_db->where('class_id', $data['class_id'])
                ->where('student_id', $data['student_id'])
                ->whereRaw('DATE(created_at) = ?', [$date])
                ->update([
                    "details" => $data['details'],
                    "status" => $data['status'],
                ]);
        }
    }

    public function getByClassId($classId, $date)
    {
        return $this->_db->where('class_id', $classId)->whereRaw('DATE(created_at) = ?', [$date])->get();
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
        return DB::table('attendances')
            ->whereDate('created_at', $date)
            ->distinct('class_id')
            ->count('class_id');
    }

    public function getAttendedStudentCount($date)
    {
        return DB::table('attendances')
            ->whereDate('created_at', $date)
            ->whereIn('status', ['Present', 'Late', 'LeaveApproval'])
            ->count();
    }

    public function getUnavailableStudentCount($date)
    {
        return DB::table('attendances')
            ->whereDate('created_at', $date)
            ->whereIn('status', ['Medical', 'Absence'])
            ->count();
    }


    public function getStatusCounts($date)
    {
        // 获取不同状态的学生数量
        $totalStatusCounts = DB::table('attendances')
            ->selectRaw('status, COUNT(*) as count')
            ->whereDate('created_at', $date)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 确保每个状态都有返回值，避免为 null
        $statusList = ['Present', 'Absence', 'Medical', 'Late', 'LeaveApproval', 'NotSubmitted'];

        foreach ($statusList as $status) {
            if (!isset($totalStatusCounts[$status])) {
                $totalStatusCounts[$status] = 0;
            }
        }

        return [
            'total_status_counts' => $totalStatusCounts, // 每个状态对应的数量
        ];
    }

    public function getMonthlyAttendanceData(Carbon $startOfMonth, Carbon $endOfMonth, ?int $courseId, array $holidayDates)
    {
        $query = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->select('attendances.status', DB::raw('COUNT(attendances.id) as count'))
            ->whereBetween('attendances.created_at', [$startOfMonth, $endOfMonth]);

        if ($courseId) {
            $query->where('classes.id', $courseId);
        }

        if (!empty($holidayDates)) {
            $query->whereNotIn(DB::raw('DATE(attendances.created_at)'), $holidayDates);
        }

        $query->groupBy('attendances.status');

        return $query->pluck('count', 'status')->toArray();
    }


    public function getMonthlyUnavailableStudentCount(Carbon $startOfMonth, Carbon $endOfMonth, ?int $courseId, array $holidayDates)
    {
        $query = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereBetween('attendances.created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('attendances.status', ['Medical', 'Absence']);

        if ($courseId) {
            $query->where('classes.id', $courseId);
        }

        if (!empty($holidayDates)) {
            $query->whereNotIn(DB::raw('DATE(attendances.created_at)'), $holidayDates);
        }

        return $query->count();
    }


    public function getClassCountByCourse(?int $courseId)
    {
        $query = DB::table('classes')
            ->where('is_disabled', false); // 只篩選 is_disabled = false 的班級

        if ($courseId) {
            $query->where('course_id', $courseId); // 篩選特定課程的班級
        }

        return $query->count(); // 返回符合條件的班級數量
    }

    public function getPresentRelatedCount(Carbon $startOfMonth, Carbon $endOfMonth, ?int $courseId, array $holidayDates)
    {
        $query = DB::table('attendances')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereBetween('attendances.created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('attendances.status', ['Present', 'Late', 'LeaveApproval']);

        if ($courseId) {
            $query->where('classes.id', $courseId);
        }

        if (!empty($holidayDates)) {
            $query->whereNotIn(DB::raw('DATE(attendances.created_at)'), $holidayDates);
        }

        return $query->count();
    }


    public function getAttendanceRecords($classId, Carbon $startOfMonth, Carbon $endOfMonth)
    {
        $records = DB::table('attendances')
            ->where('class_id', $classId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'desc')
            ->get();

        $attendanceRecords = [];
        foreach ($records as $record) {
            $date = Carbon::parse($record->created_at)->toDateString();
            $attendanceRecords[$record->student_id][$date] = $record->status;
            $attendanceRecords[$record->student_id]['details'][$date] = $record->details;
        }

        return $attendanceRecords;
    }
}
