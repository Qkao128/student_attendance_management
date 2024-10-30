<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    public function courseModal()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function userModal()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
