<?php

namespace app\models;

class Ville extends BaseModel
{
    protected $table = 'ville';
    protected $primaryKey = 'id';

    /**
     * Get all cities with region information
     */
    public function getAllWithRegion()
    {
        $query = "SELECT v.*, r.nom as region_nom 
                  FROM {$this->table} v 
                  INNER JOIN region r ON v.region_id = r.id 
                  ORDER BY r.nom ASC, v.nom ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get cities by region
     */
    public function getByRegion($region_id)
    {
        return $this->getBy(['region_id' => $region_id]);
    }

    /**
     * Get city with region information
     */
    public function getByIdWithRegion($id)
    {
        $query = "SELECT v.*, r.nom as region_nom 
                  FROM {$this->table} v 
                  INNER JOIN region r ON v.region_id = r.id 
                  WHERE v.id = :id";
        return $this->db->fetchRow($query, [':id' => $id]);
    }

    /**
     * Update disaster count for a city
     */
    public function incrementDisasters($id, $count = 1)
    {
        $query = "UPDATE {$this->table} SET nombre_sinistres = nombre_sinistres + :count 
                  WHERE id = :id";
        return $this->db->runQuery($query, [':id' => $id, ':count' => $count]);
    }
}
