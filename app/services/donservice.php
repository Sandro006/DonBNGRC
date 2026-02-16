<?php

namespace app\services;

use app\models\Don;

class DonService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Don();
    }

    public function getAllWithDetails()
    {
        return $this->model->getAllWithDetails();
    }

    public function getRecent($limit = 10)
    {
        // Model placeholder returns all details; callers can slice if needed
        $all = $this->model->getAllWithDetails();
        return array_slice($all, 0, $limit);
    }

    public function getStatistics()
    {
        return $this->model->getStatistics();
    }

    public function getStatisticsByCategory()
    {
        return $this->model->getStatisticsByCategory();
    }

    public function getByCity($ville_id)
    {
        return $this->model->getByCity($ville_id);
    }

    public function getStatisticsByCity()
    {
        return $this->model->getStatisticsByCity();
    }

    public function count()
    {
        return $this->model->count();
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }
}
