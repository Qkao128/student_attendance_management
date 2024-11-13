<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(ClassTeacher::class, 'id', 'class_id');
    }
}
