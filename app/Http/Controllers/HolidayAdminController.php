<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HolidayAdminService;
use Illuminate\Support\Facades\Redirect;


class HolidayAdminController extends Controller
{
    private $_holidayAdminService;

    public function __construct(HolidayAdminService $holidayAdminService)
    {
        $this->_holidayAdminService = $holidayAdminService;
    }

    public function index()
    {
        return view('holiday/index');
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'date_from',
            'date_to',
            'title',
            'background_color',
            'details',
        ]);
        $result = $this->_holidayAdminService->createHolidays($data);

        if ($result === null) {
            $errorMessage = implode("<br>", $this->_holidayAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('holiday.index')->with('success', "Holiday successfully added.");
    }

    public function update(Request $request, $id)
    {

        $data = $request->only([
            'date_from',
            'date_to',
            'title',
            'background_color',
            'details',
        ]);

        $result = $this->_holidayAdminService->update($data, $id);

        if ($result == null) {
            $errorMessage = implode("<br>", $this->_holidayAdminService->_errorMessage);
            return back()->with('error', $errorMessage)->withInput();
        }

        return Redirect::route('holiday.index')->with('success', "Holiday details successfully updated.");
    }

    public function getHolidays()
    {
        $holidays = $this->_holidayAdminService->getAllHolidays();

        return response()->json($holidays);
    }
}
