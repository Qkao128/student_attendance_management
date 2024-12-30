<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AttendanceStudentList extends Component
{
    public $students;
    public $classId;
    public $date;
    public $isHoliday;

    public function mount($classId, $date = null)
    {
        $this->classId = $classId;
        $this->date = $date ?? now()->format('Y-m-d');
    }


    public function render()
    {
        $newData = DB::table('students')
            ->select([
                'students.id',
                'students.profile_image',
                'students.name',
                'attendances.status as attendance_status',
                'attendances.details as attendance_details',
                'attendances.file as attendance_file',
            ])
            ->leftJoin('attendances', function ($join) {
                $join->on('students.id', '=', 'attendances.student_id')
                    ->where('attendances.class_id', '=', $this->classId)
                    ->whereRaw('DATE(attendances.created_at) = ?', [$this->date]);
            })
            ->where('students.class_id', $this->classId)
            ->whereDate('students.enrollment_date', '<=', $this->date) // 添加过滤条件
            ->orderBy('students.name', 'asc')
            ->get();

        $this->students = $newData;

        return view('livewire.attendance-student-list', [
            'students' => $this->students,
        ]);
    }
}
