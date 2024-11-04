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
        $newData = Classes::with(['courseModal:id,course', 'userModal:id,name'])
            ->orderBy('created_at', 'asc');

        if (isset($this->filter['class'])) {
            $newData = $newData->where('classes.class', 'like', '%' . $this->filter['class'] . '%');
        }

        if (isset($this->filter['course_id'])) {
            $newData = $newData->where('course_id', $this->filter['course_id']);
        }

        if (isset($this->filter['user_id'])) {
            $newData = $newData->where('user_id', $this->filter['user_id']);
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
