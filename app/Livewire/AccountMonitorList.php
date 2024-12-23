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
        $query = DB::table('users')
            ->leftJoin('students', 'users.student_id', '=', 'students.id')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
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
                DB::raw('GROUP_CONCAT(roles.name) as roles') // 將角色名稱合併
            ])
            ->where('users.teacher_user_id', $this->teacherId)
            ->whereNull('users.deleted_at')
            ->groupBy('users.id') // 防止重複
            ->orderBy('users.created_at', 'DESC');

        // 篩選條件
        if (!empty($this->filter['user'])) {
            $query->where('username', 'like', '%' . $this->filter['user'] . '%');
        }

        // 分頁邏輯
        $newData = $query->offset($this->limitPerPage * $this->page)
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
