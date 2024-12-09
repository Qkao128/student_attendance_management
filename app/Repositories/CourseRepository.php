<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CourseRepository extends Repository
{
    protected $_db;

    public function __construct(Course $course)
    {
        $this->_db = $course;
    }

    public function save($data)
    {
        $model = new Course;
        $model->name = $data['name'];

        $model->save();
        return $model->fresh();
    }

    public function update($data, $id)
    {
        $model = $this->_db->find($id);
        $model->name = $data['name'] ?? $model->name;

        $model->update();
        return $model;
    }

    public function getAllBySearchTerm($data)
    {

        $course = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name')
            ->where('name', 'LIKE', "%$course%")
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTerm($data)
    {

        $course = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->where('name', 'LIKE', "%$course%")
            ->count();

        return $totalCount;
    }

    public function getByCourseId($courseId)
    {
        return $this->_db->where('id', $courseId)->first();
    }
}
