<?php

namespace app\models;

class Besoin extends BaseModel
{
    protected $table = 'besoin';
    protected $primaryKey = 'id';

    /**
     * Get all needs with related information
     */
    public function getAllWithDetails()
    {
        $query = "SELECT b.*, 
                  v.nom as ville_nom, v.region_id, r.nom as region_nom,
                  c.libelle as categorie_nom,
                  s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total
                  FROM {$this->table} b
                  INNER JOIN ville v ON b.ville_id = v.id
                  INNER JOIN region r ON v.region_id = r.id
                  INNER JOIN categorie c ON b.categorie_id = c.id
                  INNER JOIN status s ON b.status_id = s.id
                  ORDER BY b.id DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get need by ID with all details
     */
    public function getByIdWithDetails($id)
    {
        $query = "SELECT b.*, 
                  v.nom as ville_nom, v.region_id, r.nom as region_nom,
                  c.libelle as categorie_nom,
                  s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total
                  FROM {$this->table} b
                  INNER JOIN ville v ON b.ville_id = v.id
                  INNER JOIN region r ON v.region_id = r.id
                  INNER JOIN categorie c ON b.categorie_id = c.id
                  INNER JOIN status s ON b.status_id = s.id
                  WHERE b.id = :id";
        return $this->db->fetchRow($query, [':id' => $id]);
    }

    /**
     * Get needs by city
     */
    public function getByCity($ville_id)
    {
        $query = "SELECT b.*, 
                  c.libelle as categorie_nom,
                  s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total
                  FROM {$this->table} b
                  INNER JOIN categorie c ON b.categorie_id = c.id
                  INNER JOIN status s ON b.status_id = s.id
                  WHERE b.ville_id = :ville_id
                  ORDER BY b.id DESC";
        return $this->db->fetchAll($query, [':ville_id' => $ville_id]);
    }

    /**
     * Get needs by status
     */
    public function getByStatus($status_id)
    {
        $query = "SELECT b.*, 
                  v.nom as ville_nom,
                  c.libelle as categorie_nom,
                  s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total
                  FROM {$this->table} b
                  INNER JOIN ville v ON b.ville_id = v.id
                  INNER JOIN categorie c ON b.categorie_id = c.id
                  INNER JOIN status s ON b.status_id = s.id
                  WHERE b.status_id = :status_id
                  ORDER BY b.id DESC";
        return $this->db->fetchAll($query, [':status_id' => $status_id]);
    }

    /**
     * Get needs by category
     */
    public function getByCategory($categorie_id)
    {
        $query = "SELECT b.*, 
                  v.nom as ville_nom,
                  c.libelle as categorie_nom,
                  s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total
                  FROM {$this->table} b
                  INNER JOIN ville v ON b.ville_id = v.id
                  INNER JOIN categorie c ON b.categorie_id = c.id
                  INNER JOIN status s ON b.status_id = s.id
                  WHERE b.categorie_id = :categorie_id
                  ORDER BY b.id DESC";
        return $this->db->fetchAll($query, [':categorie_id' => $categorie_id]);
    }

    /**
     * Update need status
     */
    public function updateStatus($id, $status_id)
    {
        return $this->update($id, ['status_id' => $status_id]);
    }

    /**
     * Get statistics about needs
     */
    public function getStatistics()
    {
        $query = "SELECT 
                  COUNT(*) as total_needs,
                  SUM(quantite) as total_quantity,
                  SUM(quantite * prix_unitaire) as total_amount,
                  AVG(prix_unitaire) as avg_price
                  FROM {$this->table}";
        return $this->db->fetchRow($query);
    }
}
