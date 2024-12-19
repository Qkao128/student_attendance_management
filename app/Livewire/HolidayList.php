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
    public $holidays;
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

    #[On('updateDate')]
    public function updateDate($currentYear, $currentMonth)
    {
        $this->year = $currentYear;
        $this->month = $currentMonth;

        $this->applyFilter();
    }

    public function applyFilter()
    {
        $this->page = 0;
        $this->render();
    }

    public function render()
    {
        $newData = DB::table('holidays')
            ->select('date_from', 'date_to', 'title', 'background_color', 'details')
            ->whereYear('date_from', $this->year)
            ->whereMonth('date_from', $this->month)
            ->orderBy('date_from', 'DESC');

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
