<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ClassList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $classes;
    public $filter = [
        'class' => null,
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function resetFilter()
    {
        $this->filter = [
            'class' => null,
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
        $newData = DB::table('classes')->select([
            'classes.id',
            'classes.class',
            'classes.created_at',
        ])->orderBy('classes.created_at', 'asc');

        if (!empty($this->filter['class'])) {
            $newData->where('classes.class', 'like', '%' . $this->filter['class'] . '%');
        }

        $newData = $newData->offset($this->limitPerPage * $this->page);
        $newData = $newData->limit($this->limitPerPage);
        $newData = $newData->get();
        $newData = $newData->toArray();

        if ($this->page == 0) {
            $this->classes = $newData;
        } else {
            $this->classes = [...$this->classes, ...$newData];
        }

        return view('livewire.class-list');
    }
}
