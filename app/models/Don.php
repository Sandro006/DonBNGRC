<?php

namespace app\models;

class Don extends BaseModel
{
    protected $table = 'bngrc_don';
    protected $primaryKey = 'id';

    /**
     * Get all donations with related information
     */
    public function getAllWithDetails()
    {
        $query = "SELECT d.*, 
                  v.nom as ville_nom, v.region_id, r.nom as region_nom,
                  c.libelle as categorie_nom,
                  dn.nom as donateur_nom, dn.email as donateur_email, dn.telephone as donateur_telephone
                  FROM {$this->table} d
                  INNER JOIN bngrc_ville v ON d.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON d.categorie_id = c.id
                  INNER JOIN bngrc_donateur dn ON d.donateur_id = dn.id
                  ORDER BY d.date_don DESC, d.id DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get donation by ID with all details
     */
    public function getByIdWithDetails($id)
    {
        $query = "SELECT d.*, 
                  v.nom as ville_nom, v.region_id, r.nom as region_nom,
                  c.libelle as categorie_nom,
                  dn.nom as donateur_nom, dn.email as donateur_email, dn.telephone as donateur_telephone
                  FROM {$this->table} d
                  INNER JOIN bngrc_ville v ON d.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON d.categorie_id = c.id
                  INNER JOIN bngrc_donateur dn ON d.donateur_id = dn.id
                  WHERE d.id = :id";
        return $this->db->fetchRow($query, [':id' => $id]);
    }

    /**
     * Get donations by city
     */
    public function getByCity($ville_id)
    {
        $query = "SELECT d.*, 
                  c.libelle as categorie_nom,
                  dn.nom as donateur_nom
                  FROM {$this->table} d
                  INNER JOIN bngrc_categorie c ON d.categorie_id = c.id
                  INNER JOIN bngrc_donateur dn ON d.donateur_id = dn.id
                  WHERE d.ville_id = :ville_id
                  ORDER BY d.date_don DESC";
        return $this->db->fetchAll($query, [':ville_id' => $ville_id]);
    }

    /**
     * Get donations by donor
     */
    public function getByDonor($donateur_id)
    {
        $query = "SELECT d.*, 
                  v.nom as ville_nom,
                  c.libelle as categorie_nom
                  FROM {$this->table} d
                  INNER JOIN bngrc_ville v ON d.ville_id = v.id
                  INNER JOIN bngrc_categorie c ON d.categorie_id = c.id
                  WHERE d.donateur_id = :donateur_id
                  ORDER BY d.date_don DESC";
        return $this->db->fetchAll($query, [':donateur_id' => $donateur_id]);
    }

    /**
     * Get donations by category
     */
    public function getByCategory($categorie_id)
    {
        $query = "SELECT d.*, 
                  v.nom as ville_nom,
                  dn.nom as donateur_nom
                  FROM {$this->table} d
                  INNER JOIN bngrc_ville v ON d.ville_id = v.id
                  INNER JOIN bngrc_donateur dn ON d.donateur_id = dn.id
                  WHERE d.categorie_id = :categorie_id
                  ORDER BY d.date_don DESC";
        return $this->db->fetchAll($query, [':categorie_id' => $categorie_id]);
    }

    /**
     * Get donations by date range
     */
    public function getByDateRange($start_date, $end_date)
    {
        $query = "SELECT d.*, 
                  v.nom as ville_nom,
                  c.libelle as categorie_nom,
                  dn.nom as donateur_nom
                  FROM {$this->table} d
                  INNER JOIN bngrc_ville v ON d.ville_id = v.id
                  INNER JOIN bngrc_categorie c ON d.categorie_id = c.id
                  INNER JOIN bngrc_donateur dn ON d.donateur_id = dn.id
                  WHERE DATE(d.date_don) BETWEEN :start_date AND :end_date
                  ORDER BY d.date_don DESC";
        return $this->db->fetchAll($query, [
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
    }

    /**
     * Get recent donations
     */
    public function getRecent($limit = 10)
    {
        return $this->getAllWithDetails();
    }

    /**
     * Get statistics about donations
     */
    public function getStatistics()
    {
        $query = "SELECT 
                  COUNT(*) as total_donations,
                  SUM(quantite) as total_quantity,
                  AVG(quantite) as avg_quantity,
                  COUNT(DISTINCT donateur_id) as unique_donors,
                  COUNT(DISTINCT ville_id) as cities_helped
                  FROM {$this->table}";
        return $this->db->fetchRow($query);
    }

    /**
     * Get donations statistics by category
     */
    public function getStatisticsByCategory()
    {
        $query = "SELECT 
                  c.id, c.libelle,
                  COUNT(d.id) as dons_count,
                  SUM(d.quantite) as total_quantity
                  FROM bngrc_categorie c
                  LEFT JOIN {$this->table} d ON c.id = d.categorie_id
                  GROUP BY c.id, c.libelle
                  ORDER BY total_quantity DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get donations statistics by city
     */
    public function getStatisticsByCity()
    {
        $query = "SELECT 
                  v.id, v.nom, r.nom as region_nom,
                  COUNT(d.id) as dons_count,
                  SUM(d.quantite) as total_quantity
                  FROM bngrc_ville v
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  LEFT JOIN {$this->table} d ON v.id = d.ville_id
                  GROUP BY v.id, v.nom, r.nom
                  ORDER BY total_quantity DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($categorie_id)
    {
        $query = "SELECT * FROM bngrc_categorie WHERE id = :id";
        return $this->db->fetchRow($query, [':id' => $categorie_id]);
    }
}