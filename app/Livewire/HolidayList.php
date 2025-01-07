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
    public $holidays = []; // 初始化為空數組
    public $year;
    public $month;

    public function mount($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        $this->applyFilter();
    }

    // 監聽 Livewire.dispatch('updateDate')，接收年份和月份數據
    #[On('updateDate')]
    public function handleUpdateDate(int $currentYear, int $currentMonth)
    {
        $this->year = $currentYear;
        $this->month = $currentMonth;
        $this->applyFilter(); // 當前年月發生變化時應用過濾條件
    }

    // 加載更多數據
    public function loadMore()
    {
        $this->page++;
        $this->render();
    }

    // 應用過濾條件並重置數據
    public function applyFilter()
    {
        $this->page = 0;
        $this->holidays = [];
        $this->render(); // 按新條件加載數據
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

        return view('livewire.holiday-list', [
            'holidays' => $this->holidays,
        ]);
    }
}
