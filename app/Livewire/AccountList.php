<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AccountList extends Component
{
    public $page = 0;
    public $limitPerPage = 50;
    public $users;
    public $filter = [
        'user' => null,
        'role' => null,
    ];

    public function loadMore()
    {
        $this->page++;
    }

    public function filterUser($value)
    {
        $this->filter['user'] = $value;
        $this->applyFilter();
    }

    public function filterByRole($role)
    {
        $this->filter['role'] = $role;
        $this->applyFilter();
    }

    public function applyFilter()
    {
        $this->page = 0;
        $this->render();
    }

    public function render()
    {
        $query = User::select(['id', 'username', 'email', 'created_at'])
            ->whereNull('teacher_user_id')
            ->where('users.deleted_at', '=', null)
            ->with('roles') // 預加載角色關係
            ->orderBy('created_at', 'DESC');

        if (!empty($this->filter['user'])) {
            $query->where('username', 'like', '%' . $this->filter['user'] . '%');
        }

        if (!empty($this->filter['role'])) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->filter['role']);
            });
        }

        $newData = $query->offset($this->limitPerPage * $this->page)
            ->limit($this->limitPerPage)
            ->get();

        if ($this->page == 0) {
            $this->users = $newData;
        } else {
            $this->users = [...$this->users, ...$newData];
        }

        return view('livewire.account-list');
    }
}
