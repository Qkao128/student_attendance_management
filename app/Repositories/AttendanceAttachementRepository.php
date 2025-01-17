<?php

namespace App\Repositories;

use App\Models\AttendanceAttachements;

class AttendanceAttachementRepository extends Repository
{
    protected $_db;

    public function __construct(AttendanceAttachements $attendanceAttachement)
    {
        $this->_db = $attendanceAttachement;
    }

    public function save($data)
    {
        $model = new AttendanceAttachements;
        $model->class_id = $data['class_id'];
        $model->file = $data['file'];
        $model->date = $data['date'];

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->class_id = $data['class_id'] ?? $model->class_id;
        $model->file = $data['file'] ?? $model->file;
        $model->date = $data['date'] ?? $model->date;


        $model->update();
        return $model;
    }



    public function getByClassId($classId, $date)
    {
        return $this->_db->where('class_id', $classId)->where('created_at', $date)->get();
    }



    public function getByClassIdAndDate($classId, $date)
    {
        $result = $this->_db
            ->where('class_id', $classId)
            ->whereDate('date', $date) // 按日期過濾
            ->first();

        return $result;
    }

    /**
     * 根據 class_id 和日期刪除附件
     */
    public function deleteByClassId($classId, $date)
    {
        return $this->_db
            ->where('class_id', $classId)
            ->whereDate('date', $date)
            ->delete(); // 批量刪除符合條件的記錄
    }
}
