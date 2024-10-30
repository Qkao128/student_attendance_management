<?php

namespace App\Services;

use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;

class UserAdminService extends Service
{
    protected $_userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->_userRepository = $userRepository;
    }

    public function getById($id)
    {
        try {
            $user = $this->_userRepository->getById($id);

            if ($user == null) {
                return false;
            }

            return $user;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get user details.");

            return null;
        }
    }

    public function getSelectOption($data)
    {
        try {
            $data['result_count'] = 5;
            $data['offset'] = ($data['page'] - 1) * $data['result_count'];

            $user = $this->_userRepository->getAllBySearchTerm($data);

            $totalCount = $this->_userRepository->getTotalCountBySearchTerm($data);

            $results = array(
                "results" => $user->toArray(),
                "pagination" => array(
                    "more" => $totalCount < $data['offset'] + $data['result_count'] ? false : true
                )
            );

            return $results;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get user select option.");
            DB::rollBack();
            return null;
        }
    }
}
