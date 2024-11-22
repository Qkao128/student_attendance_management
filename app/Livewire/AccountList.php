<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AccountList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $users;
    public $filter = [
        'user' => null,
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function resetFilter()
    {
        $this->filter = [
            'user' => null,
        ];
        $this->applyFilter();
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
        $newData = DB::table('users')->select([
            'users.id',
            'users.username',
            'users.created_at',
        ])->whereNull('users.teacher_user_id')
            ->orderBy('users.created_at', 'DESC');

        if (isset($this->filter['users'])) {
            $newData = $newData->where('users.username', 'like', '%' . $this->filter['user'] . '%');
        }

        $newData = $newData->offset($this->limitPerPage * $this->page);
        $newData = $newData->limit($this->limitPerPage);
        $newData = $newData->get();

        if ($this->page == 0) {
            $this->users = $newData;
        } else {
            $this->users = [...$this->users, ...$newData];
        }

        return view('livewire.account-list');
    }
}
