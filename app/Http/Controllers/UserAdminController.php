<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserAdminService;

class UserAdminController extends Controller
{
    private $_userAdminService;

    public function __construct(UserAdminService $userAdminService)
    {
        $this->_userAdminService = $userAdminService;
    }

    public function selectOption(Request $request)
    {
        $data = [
            "search_term" => $request->search_term ?? null,
            "page" => $request->page ?? 1,
        ];

        $results = $this->_userAdminService->getSelectOption($data);
        return $results;
    }
}
