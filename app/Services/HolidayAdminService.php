<?php

namespace App\Services;

use Exception;
use App\Enums\UserType;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CourseRepository;
use App\Repositories\HolidayRepository;
use Illuminate\Support\Facades\Validator;

class HolidayAdminService extends Service
{
    protected $_holidayRepository;

    public function __construct(
        HolidayRepository $holidayRepository,
    ) {
        $this->_holidayRepository = $holidayRepository;
    }

    public function createHolidays($data)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($data, [
                'date_from' => 'required|date',
                'date_to' => 'required|date',
                'title' => 'required|string|max:255',
                'background_color' => 'required|string|size:7',
                'details' => 'nullable|string|max:65535',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (!Auth::check() || !Auth::user()->hasRole(UserType::SuperAdmin()->key)) {
                throw new Exception('You do not have permission to perform this action.');
            }

            if (strtotime($data['date_to']) < strtotime($data['date_from'])) {
                throw new Exception('The date to must be greater than or equal to the date from.');
            }

            $holiday = $this->_holidayRepository->save($data);

            DB::commit();
            return $holiday;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to add holiday.");

            DB::rollBack();
            return null;
        }
    }

    public function getById($id)
    {
        try {
            $holiday = $this->_holidayRepository->getById($id);

            if ($holiday == null) {
                return false;
            }

            return $holiday;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get holiday details.");

            return null;
        }
    }

    public function update($data, $id)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($data, [
                'date_from' => 'required|date',
                'date_to' => 'required|date',
                'title' => 'required|string|max:255',
                'background_color' => 'required|string|size:7',
                'details' => 'nullable|string|max:65535',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    array_push($this->_errorMessage, $error);
                }
                return null;
            }

            if (!Auth::check() || !Auth::user()->hasRole(UserType::SuperAdmin()->key)) {
                throw new Exception('You do not have permission to perform this action.');
            }

            if (strtotime($data['date_to']) < strtotime($data['date_from'])) {
                throw new Exception('The date to must be greater than or equal to the date from.');
            }


            $holiday = $this->_holidayRepository->getById($id);

            if ($holiday == null) {
                throw new Exception();
            }

            $holiday = $this->_holidayRepository->update($data, $id);

            DB::commit();
            return $holiday;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to update holiday details.");
            DB::rollBack();
            return null;
        }
    }


    public function getAllHolidays()
    {
        try {
            $holiday = $this->_holidayRepository->getAllHolidays();

            if ($holiday == null) {
                return false;
            }

            return $holiday;
        } catch (Exception $e) {
            array_push($this->_errorMessage, "Fail to get holiday details.");

            return null;
        }
    }
}
