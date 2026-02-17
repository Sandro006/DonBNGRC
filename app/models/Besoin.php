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
                  v.nom as ville_nom, v.region_id, v.nombre_sinistres,
                  r.nom as region_nom,
                  c.libelle as categorie_nom, c.description as categorie_description,
                  s.libelle as status_nom, s.description as status_description,
                  (b.quantite * b.prix_unitaire) as montant_total,
                  DATEDIFF(NOW(), b.date_besoin) as jours_attente,
                  CASE 
                    WHEN b.priorite = 'urgente' THEN 4
                    WHEN b.priorite = 'haute' THEN 3
                    WHEN b.priorite = 'normale' THEN 2
                    ELSE 1
                  END as priorite_score
                  FROM {$this->table} b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  ORDER BY priorite_score DESC, b.date_besoin ASC, b.id ASC";
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
                  SUM(b.quantite * b.prix_unitaire) as total_amount,
                  COUNT(CASE WHEN b.priorite = 'urgente' THEN 1 END) as urgents,
                  COUNT(CASE WHEN b.priorite = 'haute' THEN 1 END) as hauts,
                  COUNT(CASE WHEN b.priorite = 'normale' THEN 1 END) as normaux
                  FROM bngrc_categorie c
                  LEFT JOIN {$this->table} b ON c.id = b.categorie_id
                  GROUP BY c.id, c.libelle
                  ORDER BY total_amount DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get urgent needs (haute and urgente priority)
     */
    public function getUrgentNeeds()
    {
        $query = "SELECT b.*, 
                  v.nom as ville_nom, r.nom as region_nom,
                  c.libelle as categorie_nom,
                  s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total,
                  DATEDIFF(NOW(), b.date_besoin) as jours_attente
                  FROM {$this->table} b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  WHERE b.priorite IN ('haute', 'urgente')
                  AND b.status_id NOT IN (3, 5) -- Pas satisfait ou annulé
                  ORDER BY FIELD(b.priorite, 'urgente', 'haute'), b.date_besoin ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get needs by priority
     */
    public function getByPriorite($priorite)
    {
        $validPriorites = ['basse', 'normale', 'haute', 'urgente'];
        if (!in_array($priorite, $validPriorites)) {
            throw new \Exception("Priorité invalide");
        }

        $query = "SELECT b.*, 
                  v.nom as ville_nom, r.nom as region_nom,
                  c.libelle as categorie_nom, s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total
                  FROM {$this->table} b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  WHERE b.priorite = :priorite
                  ORDER BY b.date_besoin ASC";
        return $this->db->fetchAll($query, [':priorite' => $priorite]);
    }

    /**
     * Create a new need with validation
     */
    public function createBesoin($data)
    {
        // Validation des champs requis
        $requiredFields = ['ville_id', 'categorie_id', 'quantite', 'prix_unitaire'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Le champ {$field} est requis");
            }
        }

        // Validation de la quantité et du prix
        if (!is_numeric($data['quantite']) || $data['quantite'] <= 0) {
            throw new \Exception("La quantité doit être un nombre positif");
        }

        if (!is_numeric($data['prix_unitaire']) || $data['prix_unitaire'] <= 0) {
            throw new \Exception("Le prix unitaire doit être un nombre positif");
        }

        // Préparer les données
        $insertData = [
            'ville_id' => $data['ville_id'],
            'categorie_id' => $data['categorie_id'],
            'quantite' => $data['quantite'],
            'prix_unitaire' => $data['prix_unitaire'],
            'status_id' => $data['status_id'] ?? 1, // En attente par défaut
            'priorite' => $data['priorite'] ?? 'normale'
        ];

        // Ajouter les champs optionnels
        if (!empty($data['description'])) {
            $insertData['description'] = $data['description'];
        }

        if (!empty($data['date_besoin'])) {
            $insertData['date_besoin'] = $data['date_besoin'];
        }

        return $this->create($insertData);
    }

    /**
     * TASK 6: Get total needs (SUM quantity × unit_price)
     */
    public function getTotalBesoins()
    {
        $query = "SELECT 
                  COUNT(id) as count,
                  SUM(quantite) as total_quantity,
                  SUM(quantite * prix_unitaire) as total_amount,
                  AVG(prix_unitaire) as avg_unit_price
                  FROM {$this->table}";
        $result = $this->db->fetchRow($query);
        return $result ?? [
            'count' => 0,
            'total_quantity' => 0,
            'total_amount' => 0,
            'avg_unit_price' => 0
        ];
    }

    /**
     * TASK 7: Get total satisfied needs
     */
    public function getTotalSatisfaits()
    {
        $query = "SELECT 
                  COUNT(b.id) as count,
                  SUM(b.quantite) as total_quantity,
                  SUM(b.quantite * b.prix_unitaire) as total_amount
                  FROM {$this->table} b
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  WHERE LOWER(s.libelle) = 'satisfied' 
                     OR LOWER(s.libelle) LIKE '%satisf%'
                     OR LOWER(s.libelle) = 'completed'";
        $result = $this->db->fetchRow($query);
        return $result ?? [
            'count' => 0,
            'total_quantity' => 0,
            'total_amount' => 0
        ];
    }

    /**
     * TASK 8: Get total remaining needs
     */
    public function getTotalRestants()
    {
        $query = "SELECT 
                  COUNT(b.id) as count,
                  SUM(b.quantite) as total_quantity,
                  SUM(b.quantite * b.prix_unitaire) as total_amount
                  FROM {$this->table} b
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  WHERE LOWER(s.libelle) != 'satisfied' 
                    AND LOWER(s.libelle) NOT LIKE '%satisf%'
                    AND LOWER(s.libelle) != 'completed'";
        $result = $this->db->fetchRow($query);
        return $result ?? [
            'count' => 0,
            'total_quantity' => 0,
            'total_amount' => 0
        ];
    }

    /**
     * Delete a need
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->run($query, [':id' => $id]);
    }

    /**
     * Count total needs
     */
    public function count($conditions = [])
    {
        if (empty($conditions)) {
            $query = "SELECT COUNT(*) as count FROM {$this->table}";
            $result = $this->db->fetchRow($query);
            return $result['count'] ?? 0;
        }

        return parent::count($conditions);
    }

    /**
     * Get needs ordered by priority (oldest date_besoin first)
     */
    public function getByPriority($limit = null)
    {
        $query = "SELECT b.*, 
                  v.nom as ville_nom, v.region_id, r.nom as region_nom,
                  c.libelle as categorie_nom,
                  s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total,
                  DATEDIFF(NOW(), b.date_besoin) as jours_attente
                  FROM {$this->table} b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  WHERE s.libelle NOT IN ('satisfait', 'completed')
                  ORDER BY b.date_besoin ASC, b.id ASC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
            return $this->db->fetchAll($query, [':limit' => $limit]);
        }
        
        return $this->db->fetchAll($query);
    }

    /**
     * Get needs by category ordered by priority
     */
    public function getByCategoryPriority($categorie_id, $limit = null)
    {
        $query = "SELECT b.*, 
                  v.nom as ville_nom, v.region_id, r.nom as region_nom,
                  c.libelle as categorie_nom,
                  s.libelle as status_nom,
                  (b.quantite * b.prix_unitaire) as montant_total,
                  DATEDIFF(NOW(), b.date_besoin) as jours_attente,
                  COALESCE(SUM(d.quantite_distribuee), 0) as quantite_recue,
                  (b.quantite - COALESCE(SUM(d.quantite_distribuee), 0)) as quantite_manquante
                  FROM {$this->table} b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  LEFT JOIN bngrc_distribution d ON b.id = d.besoin_id
                  WHERE b.categorie_id = :categorie_id
                  AND s.libelle NOT IN ('satisfait', 'completed')
                  GROUP BY b.id, b.ville_id, v.nom, v.region_id, r.nom, b.categorie_id, c.libelle, 
                           s.libelle, b.quantite, b.prix_unitaire, b.date_besoin
                  HAVING quantite_manquante > 0
                  ORDER BY b.date_besoin ASC, b.id ASC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
            return $this->db->fetchAll($query, [':categorie_id' => $categorie_id, ':limit' => $limit]);
        }
        
        return $this->db->fetchAll($query, [':categorie_id' => $categorie_id]);
    }

    /**
     * Get needs with distribution information for simulation
     */
    public function getNeedsForSimulation()
    {
        $query = "SELECT 
                    b.id as besoin_id,
                    b.ville_id,
                    v.nom as ville_nom,
                    r.nom as region_nom,
                    b.categorie_id,
                    c.libelle as categorie_nom,
                    b.quantite as quantite_demandee,
                    b.prix_unitaire,
                    b.date_besoin,
                    s.libelle as status_nom,
                    COALESCE(SUM(d.quantite_distribuee), 0) as quantite_recue,
                    (b.quantite - COALESCE(SUM(d.quantite_distribuee), 0)) as quantite_manquante,
                    DATEDIFF(NOW(), b.date_besoin) as jours_attente,
                    (b.quantite * b.prix_unitaire) as montant_total
                  FROM {$this->table} b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  LEFT JOIN bngrc_distribution d ON b.id = d.besoin_id
                  GROUP BY b.id, b.ville_id, v.nom, r.nom, b.categorie_id, c.libelle, 
                           b.quantite, b.prix_unitaire, b.date_besoin, s.libelle
                  HAVING quantite_manquante > 0
                  AND s.libelle NOT IN ('satisfait', 'completed')
                  ORDER BY b.date_besoin ASC, b.id ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Add a new need with date_besoin
     */
    public function addBesoin($data)
    {
        // Validation des champs requis
        $requiredFields = ['ville_id', 'categorie_id', 'quantite', 'prix_unitaire'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Le champ {$field} est requis");
            }
        }

        // Validation de la quantité et prix
        if (!is_numeric($data['quantite']) || $data['quantite'] <= 0) {
            throw new \Exception("La quantité doit être un nombre positif");
        }
        if (!is_numeric($data['prix_unitaire']) || $data['prix_unitaire'] <= 0) {
            throw new \Exception("Le prix unitaire doit être un nombre positif");
        }

        $insertData = [
            'ville_id' => $data['ville_id'],
            'categorie_id' => $data['categorie_id'],
            'quantite' => $data['quantite'],
            'prix_unitaire' => $data['prix_unitaire'],
            'status_id' => $data['status_id'] ?? 1
        ];

        // Ajouter la date_besoin si fournie, sinon utiliser la valeur par défaut de la DB
        if (!empty($data['date_besoin'])) {
            $insertData['date_besoin'] = $data['date_besoin'];
        }

        return $this->create($insertData);
    }

    /**
     * Get priority statistics
     */
    public function getPriorityStats()
    {
        $query = "SELECT 
                    COUNT(b.id) as total_besoins,
                    SUM(CASE WHEN DATEDIFF(NOW(), b.date_besoin) > 30 THEN 1 ELSE 0 END) as urgent_30_jours,
                    SUM(CASE WHEN DATEDIFF(NOW(), b.date_besoin) > 15 THEN 1 ELSE 0 END) as urgent_15_jours,
                    SUM(CASE WHEN DATEDIFF(NOW(), b.date_besoin) > 7 THEN 1 ELSE 0 END) as urgent_7_jours,
                    AVG(DATEDIFF(NOW(), b.date_besoin)) as moyenne_attente_jours,
                    MIN(b.date_besoin) as besoin_le_plus_ancien,
                    MAX(b.date_besoin) as besoin_le_plus_recent
                  FROM {$this->table} b
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  WHERE s.libelle NOT IN ('satisfait', 'completed')";
        return $this->db->fetchRow($query);
    }
}
