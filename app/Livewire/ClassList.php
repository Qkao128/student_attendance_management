<?php

namespace App\Livewire;

use App\Models\Classes;
use Livewire\Component;

class ClassList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $classes;
    public $filter = [
        'class' => null,
        'course_id' => null,
        'user_id' => null,
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function resetFilter()
    {
        $this->filter = [
            'class' => null,
            'course_id' => null,
            'user_id' => null,
        ];
        $this->applyFilter();
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

    public function render()
    {
        $newData = Classes::with(['courseModal:id,course', 'userModal:id,name'])
            ->orderBy('created_at', 'asc');

        if (!empty($this->filter['class'])) {
            $newData->where('classes.class', 'like', '%' . $this->filter['class'] . '%');
        }

        if (!empty($this->filter['course_id'])) {
            $newData->where('course_id', $this->filter['course_id']);
        }

        if (!empty($this->filter['user_id'])) {
            $newData->where('user_id', $this->filter['user_id']);
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
