<?php

namespace app\models;

class Status extends BaseModel
{
    protected $table = 'status';
    protected $primaryKey = 'id';

    /**
     * Get all status with their usage count
     */
    public function getAllWithUsageCount()
    {
        $query = "SELECT s.*, 
                  COUNT(b.id) as besoins_count
                  FROM {$this->table} s 
                  LEFT JOIN besoin b ON s.id = b.status_id 
                  GROUP BY s.id 
                  ORDER BY s.libelle ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get status by libelle (name)
     */
    public function getByLibelle($libelle)
    {
        return $this->getOneBy(['libelle' => $libelle]);
    }
}
