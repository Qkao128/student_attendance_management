<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classes extends Model
{
    use HasFactory, SoftDeletes;

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(ClassTeacher::class, 'id', 'class_id');
    }


    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id', 'id');
    }
}
