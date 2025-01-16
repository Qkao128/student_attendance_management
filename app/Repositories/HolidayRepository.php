<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Holidays;
use Illuminate\Support\Facades\DB;

class HolidayRepository extends Repository
{
    protected $_db;

    public function __construct(Holidays $holiday)
    {
        $this->_db = $holiday;
    }

    public function save($data)
    {
        $model = new Holidays;
        $model->date_from = $data['date_from'];
        $model->date_to = $data['date_to'];
        $model->title = $data['title'];
        $model->background_color = $data['background_color'];
        $model->details = $data['details'];
        $model->is_holidays = $data['is_holidays'];

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->date_from = $data['date_from'] ?? $model->date_from;
        $model->date_to = $data['date_to'] ?? $model->date_to;
        $model->title = $data['title'] ?? $model->title;
        $model->background_color = $data['background_color'] ?? $model->background_color;
        $model->details = (array_key_exists('details', $data)) ? $data['details'] : $model->details;
        $model->is_holidays = $data['is_holidays'] ?? $model->is_holidays;

        $model->update();
        return $model;
    }

    public function getAllHolidays()
    {
        return $this->_db->select([
            'title as title',
            DB::raw('DATE(date_from) as start'),
            DB::raw('DATE(DATE_ADD(date_to, INTERVAL 1 DAY)) as end'),
            'background_color as backgroundColor',
            'background_color as borderColor',
            'details',
            'is_holidays as isHolidays'
        ])->get();
    }

    public function isDateHoliday($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        $holiday = DB::table('holidays')
            ->where(function ($query) use ($date) {
                $query->where(function ($q) use ($date) {
                    $q->where('date_from', '<=', $date)
                        ->where('date_to', '>=', $date)
                        ->where('is_holidays', true);
                });
            })
            ->exists();

        return $holiday;
    }

    public function getHolidaysAndActivities($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        $holidays = DB::table('holidays')
            ->where(function ($query) use ($date) {
                $query->where('date_from', '<=', $date)
                    ->where('date_to', '>=', $date);
            })
            ->select('title', 'background_color', 'is_holidays')
            ->get();

        return $holidays;
    }

    public function getHolidaysInRange(Carbon $startOfMonth, Carbon $endOfMonth)
    {
        return DB::table('holidays')
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('date_from', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('date_to', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('date_from', '<=', $startOfMonth)
                            ->where('date_to', '>=', $endOfMonth);
                    });
            })->where('is_holidays', true)
            ->get(['date_from', 'date_to'])
            ->toArray();
    }
}
