<?php

namespace App\Repositories;

use App\Models\Classes;
use Illuminate\Support\Facades\DB;

class ClassRepository extends Repository
{
    protected $_db;

    public function __construct(Classes $class)
    {
        $this->_db = $class;
    }

    public function save($data)
    {
        $model = new Classes;
        $model->class = $data['class'];
        $model->course_id = $data['course_id'];
        $model->user_id = $data['user_id'];

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

    public function updatePackageByParentCourseCategoryId($package, $parentCourseCategoryId)
    {
        $this->_db->where("parent_course_category_id", '=', $parentCourseCategoryId)->update([
            'package' => $package
        ]);

        return true;
    }

    public function getMainCourseCategoryByName($name)
    {
        $model = $this->_db
            ->where('name', '=', $name)
            ->where('parent_course_category_id', '=', null)
            ->first();

        return $model;
    }

    public function getByNameAndParentCourseCategoryId($name, $parentCourseCategoryId)
    {
        $model = $this->_db
            ->where('name', '=', $name)
            ->where('parent_course_category_id', '=', $parentCourseCategoryId)
            ->first();

        return $model;
    }

    public function getAllBySearchTermAndParentCategoryId($data)
    {

        $name = $data['search_term'] ?? '';

        $data = $this->_db->select('id', 'name')
            ->where('name', 'LIKE', "%$name%")
            ->where('parent_course_category_id', '=', $data['parent_course_category_id'])
            ->skip($data['offset'])->take($data['result_count'])
            ->get();

        if (empty($data)) {
            return null;
        }
        return $data;
    }

    public function getTotalCountBySearchTermAndParentCategoryId($data)
    {

        $name = $data['search_term'] ?? '';

        $totalCount = $this->_db
            ->where('name', 'LIKE', "%$name%")
            ->where('parent_course_category_id', '=', $data['parent_course_category_id'])
            ->count();

        return $totalCount;
    }

    public function updateDisplayOrder($data)
    {
        $this->_db->whereIn('id', array_column($data, 'id'))
            ->update([
                'display_order' => DB::raw('CASE id ' . implode(' ', array_map(function ($item) {
                    return 'WHEN ' . $item['id'] . ' THEN ' . $item['course_category_display_order'] . ' ';
                }, $data)) . 'END')
            ]);

        return true;
    }
}
