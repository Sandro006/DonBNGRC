<?php

namespace app\services;

use app\models\Besoin;

class BesoinService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Besoin();
    }

    public function getAllWithDetails()
    {
        return $this->model->getAllWithDetails();
    }

    public function getByStatus($status_id)
    {
        return $this->model->getByStatus($status_id);
    }

    public function getStatistics()
    {
        return $this->model->getStatistics();
    }

    public function count()
    {
        return $this->model->count();
    }
}
