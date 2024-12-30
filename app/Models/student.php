<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceForDate($date, $classId)
    {
        return $this->attendances()->where('class_id', $classId)->whereDate('created_at', $date)->first();
    }
}
