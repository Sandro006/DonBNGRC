<?php

namespace app\services;

use app\models\Ville;

class VilleService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Ville();
    }

    public function getAll()
    {
        return $this->model->getAllWithRegion();
    }

    public function getByRegion($region_id)
    {
        return $this->model->getByRegion($region_id);
    }

    public function getById($id)
    {
        return $this->model->getByIdWithRegion($id);
    }

    public function incrementDisasters($id, $count = 1)
    {
        return $this->model->incrementDisasters($id, $count);
    }

    public function count()
    {
        return $this->model->count();
    }
}
