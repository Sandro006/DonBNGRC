<?php

namespace app\models;

class Region extends BaseModel
{
    protected $table = 'bngrc_region';
    protected $primaryKey = 'id';

    /**
     * Get all regions with their cities count
     */
    public function getAllWithCitiesCount()
    {
        $query = "SELECT r.*, COUNT(v.id) as cities_count 
                  FROM {$this->table} r 
                  LEFT JOIN bngrc_ville v ON r.id = v.region_id 
                  GROUP BY r.id 
                  ORDER BY r.nom ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get region with all related cities
     */
    public function getWithCities($id)
    {
        $region = $this->getById($id);
        if ($region) {
            $query = "SELECT * FROM bngrc_ville WHERE region_id = :region_id ORDER BY nom ASC";
            $region['villes'] = $this->db->fetchAll($query, [':region_id' => $id]);
        }
        return $region;
    }
}
