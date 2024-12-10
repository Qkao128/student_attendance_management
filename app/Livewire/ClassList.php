<?php

namespace App\Livewire;

use App\Models\Classes;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ClassList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $classes;
    public $filter = [
        'class' => null,
        'course_id' => null,
        'user_id' => null,
        'is_disabled' => null,
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function filterClass($value)
    {
        $this->filter['class'] = $value;
        $this->applyFilter();
    }

    public function applyFilter()
    {
        $this->page = 0;
        $this->render();
    }

    public function resetFilter()
    {
        foreach ($this->filter as $key => $value) {
            $this->filter[$key] = null;
        }

        $this->applyFilter();
    }


    public function render()
    {
        $newData = DB::table('classes')
            ->select([
                'classes.id',
                'classes.name',
                'classes.is_disabled',
                'classes.created_at',
                'class_teachers.user_id',
                'courses.name as course_name',
                'users.username as user_name',
                DB::raw('COUNT(students.id) as member_count'),
            ])
            ->leftJoin('courses', 'classes.course_id', '=', 'courses.id')
            ->leftJoin('class_teachers', 'classes.id', '=', 'class_teachers.class_id')
            ->leftJoin('users', 'class_teachers.user_id', '=', 'users.id')
            ->leftJoin('students', 'classes.id', '=', 'students.class_id')
            ->groupBy('classes.id', 'class_teachers.user_id', 'courses.name', 'users.username', 'classes.created_at')
            ->orderBy('classes.created_at', 'DESC');

        if (isset($this->filter['class'])) {
            $newData = $newData->where('classes.name', 'like', '%' . $this->filter['class'] . '%');
        }

        if (isset($this->filter['course_id'])) {
            $newData = $newData->where('course_id', $this->filter['course_id']);
        }

        if (isset($this->filter['user_id'])) {
            $newData = $newData->where('class_teachers.user_id', '=', $this->filter['user_id']);
        }

        if (isset($this->filter['is_disabled'])) {
            $newData = $newData->where('classes.is_disabled', '=', $this->filter['is_disabled']);
        }

        $newData = $newData->offset($this->limitPerPage * $this->page);
        $newData = $newData->limit($this->limitPerPage);
        $newData = $newData->get();

        if ($this->page == 0) {
            $this->classes = $newData;
        } else {
            $this->classes = [...$this->classes, ...$newData];
        }

        return view('livewire.class-list');
    }
}
