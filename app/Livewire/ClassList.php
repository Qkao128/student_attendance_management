<?php

namespace App\Livewire;

use App\Enums\UserType;
use App\Models\Classes;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClassList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $classes = [];
    public $userId;
    public $filter = [
        'class' => null,
        'course_id' => null,
        'user_id' => null,
        'is_disabled' => null,
    ];

    public function mount($userId)
    {
        $this->filter['is_disabled'] = null;
        $this->userId = $userId;
        $this->loadData();
    }

    public function loadMore()
    {
        $this->page++;
        $this->loadData();
    }

    public function updateDisabledStatus($isDisabled)
    {
        $this->filter['is_disabled'] = $isDisabled; // 可以為 true, false, 或 null
        $this->resetPagination();
        $this->loadData();
    }

    public function filterByCurrentUser()
    {
        $this->filter['user_id'] = $this->filter['user_id'] === $this->userId ? null : $this->userId;
        $this->resetPagination();
        $this->loadData();
    }

    public function filterClass($className)
    {
        $this->filter['class'] = $className;
        $this->resetPagination();
        $this->loadData(); // 重新加載數據
    }


    public function resetFilter()
    {
        $this->filter = [
            'class' => null,
            'course_id' => null,
            'user_id' => null,
            'is_disabled' => null,
        ];
        $this->resetPagination();
        $this->loadData();
    }

    private function loadData()
    {
        $query = DB::table('classes')
            ->select([
                'classes.id',
                'classes.name',
                'classes.is_disabled',
                'classes.date_from',
                'classes.date_to',
                'class_teachers.user_id',
                'courses.name as course_name',
                'users.username as user_name',
                DB::raw('COUNT(students.id) as member_count'),
            ])
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('students', 'classes.id', '=', 'students.class_id')
            ->groupBy(
                'classes.id',
                'classes.name',
                'classes.is_disabled',
                'classes.created_at',
                'class_teachers.user_id',
                'courses.name',
                'users.username'
            )
            ->whereNull('courses.deleted_at')
            ->whereNull('classes.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderBy('classes.created_at', 'DESC');

        if (!is_null($this->filter['class'])) {
            $query->where('classes.name', 'like', '%' . $this->filter['class'] . '%');
        }

        if (!is_null($this->filter['course_id'])) {
            $query->where('courses.id', '=', $this->filter['course_id']);
        }

        if (!is_null($this->filter['user_id'])) {
            $query->where('class_teachers.user_id', '=', $this->filter['user_id']);
        }

        if (!is_null($this->filter['is_disabled'])) {
            $query->where('classes.is_disabled', '=', $this->filter['is_disabled']);
        }

        if (Auth::user()->hasRole(UserType::Admin()->key)) {
            $class = DB::table('classes')
                ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
                ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
                ->where('classes.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('classes.is_disabled', false)
                ->first();

            if ($class != null) {
                $query->where('class_teachers.user_id', Auth::user()->id)->where('classes.is_disabled', false);
            }
        }

        $newData = $query->offset($this->limitPerPage * $this->page)
            ->limit($this->limitPerPage)
            ->get();

        // 確保 `array_merge` 使用數組
        if ($this->page === 0) {
            $this->classes = $newData->toArray();
        } else {
            $this->classes = array_merge($this->classes, $newData->toArray());
        }
    }

    private function resetPagination()
    {
        $this->page = 0;
        $this->classes = [];
    }

    public function render()
    {
        return view('livewire.class-list', [
            'classes' => $this->classes,
        ]);
    }
}
