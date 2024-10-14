<?php

namespace App\Repositories;


class Repository
{
    public function getAll()
    {
        $data = $this->_db->get();

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public function getById($id, $columns = ['*'], $with = [], $withTRashed = false)
    {
        $query = $this->_db;

        if ($withTRashed) {
            $query = $query->withTrashed();
        }

        $query = $query->select($columns);

        if (!empty($with)) {
            $query->with($with);
        }

        $data = $query->find($id);

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    public function deleteById($id)
    {
        $data = $this->getById($id);

        if (empty($data)) {
            return null;
        }

        $data->delete();
        return $data;
    }
}
