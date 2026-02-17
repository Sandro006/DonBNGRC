<?php

namespace app\models;

class Categorie extends BaseModel
{
    protected $table = 'bngrc_categorie';
    protected $primaryKey = 'id';

    /**
     * Get all categories with their usage count (from global donations)
     */
    public function getAllWithUsageCount()
    {
        $query = "SELECT c.*, 
                  (SELECT COUNT(*) FROM bngrc_besoin WHERE categorie_id = c.id) as besoins_count,
                  (SELECT COUNT(*) FROM bngrc_don_global WHERE categorie_id = c.id) as dons_count
                  FROM {$this->table} c 
                  ORDER BY c.libelle ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get category by libelle (name)
     */
    public function getByLibelle($libelle)
    {
        return $this->getOneBy(['libelle' => $libelle]);
    }
}
