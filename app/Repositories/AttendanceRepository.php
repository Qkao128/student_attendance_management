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
}
