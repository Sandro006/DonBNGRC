<?php

namespace app\models;

class Donateur extends BaseModel
{
    protected $table = 'donateur';
    protected $primaryKey = 'id';

    /**
     * Get all donors with their donation count
     */
    public function getAllWithDonationCount()
    {
        $query = "SELECT d.*, COUNT(dn.id) as dons_count, SUM(dn.quantite) as total_quantite
                  FROM {$this->table} d 
                  LEFT JOIN don dn ON d.id = dn.donateur_id 
                  GROUP BY d.id 
                  ORDER BY d.nom ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get donor with all their donations
     */
    public function getWithDonations($id)
    {
        $donor = $this->getById($id);
        if ($donor) {
            $query = "SELECT d.*, v.nom as ville_nom, c.libelle as categorie_nom
                      FROM don d
                      INNER JOIN ville v ON d.ville_id = v.id
                      INNER JOIN categorie c ON d.categorie_id = c.id
                      WHERE d.donateur_id = :donateur_id
                      ORDER BY d.date_don DESC";
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
