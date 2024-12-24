<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class HolidayList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $holidays; // 初始化为空数组
    public $year;
    public $month;

    public function mount($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->applyFilter();
    }


    public function loadMore()
    {
        $this->page++;
        $this->render();
    }

    public function applyFilter()
    {
        $this->page = 0;
        $this->render();
    }

    public function render()
    {
        $newData = DB::table('holidays')
            ->select('id', 'date_from', 'date_to', 'title', 'background_color', 'details')
            ->whereYear('date_from', $this->year)
            ->whereMonth('date_from', $this->month)
            ->orderBy('date_from', 'ASC');

        $newData = $newData->offset($this->limitPerPage * $this->page);
        $newData = $newData->limit($this->limitPerPage);
        $newData = $newData->get();

        if ($this->page == 0) {
            $this->holidays = $newData;
        } else {
            $this->holidays = [...$this->holidays, ...$newData];
        }

        return view('livewire.holiday-list');
    }
}
