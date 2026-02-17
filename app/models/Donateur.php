<?php

namespace app\models;

class Donateur extends BaseModel
{
    protected $table = 'bngrc_donateur';
    protected $primaryKey = 'id';

    /**
     * Get all donors with their donation count (from global donations)
     */
    public function getAllWithDonationCount()
    {
        $query = "SELECT d.*, COUNT(dg.id) as dons_count, SUM(dg.quantite) as total_quantite
                  FROM {$this->table} d 
                  LEFT JOIN bngrc_don_global dg ON d.id = dg.donateur_id 
                  GROUP BY d.id 
                  ORDER BY d.nom ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get donor with all their global donations
     */
    public function getWithDonations($id)
    {
        $donor = $this->getById($id);
        if ($donor) {
            $query = "SELECT dg.*, c.libelle as categorie_nom, dg.status_distribution
                      FROM bngrc_don_global dg
                      INNER JOIN bngrc_categorie c ON dg.categorie_id = c.id
                      WHERE dg.donateur_id = :donateur_id
                      ORDER BY dg.date_don DESC";
            $donor['donations'] = $this->db->fetchAll($query, [':donateur_id' => $id]);
        }
        return $donor;
    }

    /**
     * Search donors by name or email
     */
    public function search($term)
    {
        $query = "SELECT * FROM {$this->table} 
                  WHERE nom LIKE :term OR email LIKE :term OR telephone LIKE :term
                  ORDER BY nom ASC";
        return $this->db->fetchAll($query, [':term' => "%{$term}%"]);
    }
}
