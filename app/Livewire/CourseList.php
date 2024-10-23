<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CourseList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $courses;
    public $filter = [
        'name' => null,
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function resetFilter()
    {
        $this->filter = [
            'name' => null,
        ];
        $this->applyFilter();
    }

    public function filterCourse($value)
    {
        $this->filter['name'] = $value;
        $this->applyFilter();
    }

    public function applyFilter()
    {
        $this->page = 0;
        $this->render();
    }

    public function render()
    {
        $newData = DB::table('courses')->select([
            'courses.id',
            'courses.name',
            'courses.created_at',
        ])->orderBy('courses.created_at', 'asc');

        if (!empty($this->filter['name'])) {
            $newData->where('courses.name', 'like', '%' . $this->filter['name'] . '%');
        }

        $newData = $newData->offset($this->limitPerPage * $this->page);
        $newData = $newData->limit($this->limitPerPage);
        $newData = $newData->get();
        $newData = $newData->toArray();

        if ($this->page == 0) {
            $this->courses = $newData;
        } else {
            $this->courses = [...$this->courses, ...$newData];
        }

        return view('livewire.course-list');
    }
}
