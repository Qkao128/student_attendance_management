<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AttendanceStudentList extends Component
{
    public $students;
    public $classId;
    public $date;

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
            ])
            ->leftJoin('attendances', function ($join) {
                $join->on('students.id', '=', 'attendances.student_id')
                    ->where('attendances.class_id', '=', $this->classId)
                    ->whereRaw('DATE(attendances.created_at) = ?', [$this->date]);
            })
            ->where('students.class_id', $this->classId)
            ->orderBy('students.created_at', 'DESC')
            ->get();

        $this->students = $newData;

        return view('livewire.attendance-student-list', [
            'students' => $this->students,
        ]);
    }
}
