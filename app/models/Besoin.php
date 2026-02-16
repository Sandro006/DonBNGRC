<?php

namespace app\models;

class Besoin extends BaseModel
{
    protected $table = 'bngrc_besoin';
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
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
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
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
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
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
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
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
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
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
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

    /**
     * Get needs statistics by region with category breakdown
     */
    public function getStatisticsByRegion()
    {
        $query = "SELECT 
                  r.id as region_id, r.nom as region_nom,
                  v.id as ville_id, v.nom as ville_nom,
                  COUNT(b.id) as besoins_count,
                  SUM(b.quantite) as total_quantity,
                  SUM(b.quantite * b.prix_unitaire) as total_amount,
                  SUM(CASE WHEN c.libelle LIKE '%nature%' OR c.libelle LIKE '%riz%' OR c.libelle LIKE '%huile%' THEN b.quantite ELSE 0 END) as nature_qty,
                  SUM(CASE WHEN c.libelle LIKE '%materiau%' OR c.libelle LIKE '%tole%' OR c.libelle LIKE '%clou%' THEN b.quantite ELSE 0 END) as materiel_qty,
                  SUM(CASE WHEN c.libelle LIKE '%argent%' OR c.libelle LIKE '%fond%' THEN b.quantite * b.prix_unitaire ELSE 0 END) as fonds_amount,
                  MAX(CASE WHEN s.libelle LIKE '%urgent%' OR s.libelle LIKE '%critique%' THEN 1 ELSE 0 END) as is_critical
                  FROM bngrc_region r
                  INNER JOIN bngrc_ville v ON r.id = v.region_id
                  LEFT JOIN {$this->table} b ON v.id = b.ville_id
                  LEFT JOIN bngrc_categorie c ON b.categorie_id = c.id
                  LEFT JOIN bngrc_status s ON b.status_id = s.id
                  GROUP BY r.id, r.nom, v.id, v.nom
                  HAVING besoins_count > 0
                  ORDER BY total_amount DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get count of active regions (regions with needs)
     */
    public function getActiveRegionsCount()
    {
        $query = "SELECT COUNT(DISTINCT r.id) as count
                  FROM bngrc_region r
                  INNER JOIN bngrc_ville v ON r.id = v.region_id
                  INNER JOIN {$this->table} b ON v.id = b.ville_id";
        $result = $this->db->fetchRow($query);
        return (int)($result['count'] ?? 0);
    }

    /**
     * Get statistics by category
     */
    public function getStatisticsByCategory()
    {
        $query = "SELECT 
                  c.id, c.libelle,
                  COUNT(b.id) as besoins_count,
                  SUM(b.quantite) as total_quantity,
                  SUM(b.quantite * b.prix_unitaire) as total_amount
                  FROM bngrc_categorie c
                  LEFT JOIN {$this->table} b ON c.id = b.categorie_id
                  GROUP BY c.id, c.libelle
                  ORDER BY total_amount DESC";
        return $this->db->fetchAll($query);
    }
}
