<?php

namespace App\Livewire;

use App\Models\User;
use App\Enums\UserType;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AccountMonitorList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $users;
    public $teacherId;
    public $filter = [
        'user' => null,
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function filterUser($value)
    {
        $this->filter['user'] = $value;
        $this->applyFilter();
    }

    public function applyFilter()
    {
        $this->page = 0;
        $this->render();
    }


    public function render()
    {
        $newData = DB::table('users')
            ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftjoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftjoin('students', 'users.student_id', '=', 'students.id')
            ->leftjoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftjoin('courses', 'classes.course_id', '=', 'courses.id')
            ->select([
                'users.id',
                'users.username',
                'users.email',
                'users.student_id',
                'users.teacher_user_id',
                'users.created_at',
                'students.name as student_name',
                'classes.name as class_name',
                'courses.name as course_name',
            ])
            ->where('roles.name', '=', UserType::Monitor()->key)
            ->where('teacher_user_id', $this->teacherId)
            ->where('users.deleted_at', '=', null)
            ->orderBy('users.created_at', 'DESC');


        if (!empty($this->filter['user'])) {
            $newData->where('username', 'like', '%' . $this->filter['user'] . '%');
        }

        $newData = $newData->offset($this->limitPerPage * $this->page)
            ->limit($this->limitPerPage)
            ->get();

        if ($this->page == 0) {
            $this->users = $newData;
        } else {
            $this->users = [...$this->users, ...$newData];
        }


        return view('livewire.account-monitor-list');
    }
}
