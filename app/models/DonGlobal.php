<?php

namespace app\models;

class DonGlobal extends BaseModel
{
    protected $table = 'bngrc_don_global';
    protected $primaryKey = 'id';

    /**
     * Get all global donations with related information
     */
    public function getAllWithDetails()
    {
        $query = "SELECT dg.*, 
                  c.libelle as categorie_nom,
                  dn.nom as donateur_nom, 
                  dn.email as donateur_email, 
                  dn.telephone as donateur_telephone
                  FROM {$this->table} dg
                  INNER JOIN bngrc_categorie c ON dg.categorie_id = c.id
                  INNER JOIN bngrc_donateur dn ON dg.donateur_id = dn.id
                  ORDER BY dg.date_don DESC, dg.id DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get global donation by ID with all details
     */
    public function getByIdWithDetails($id)
    {
        $query = "SELECT dg.*, 
                  c.libelle as categorie_nom,
                  dn.nom as donateur_nom, 
                  dn.email as donateur_email, 
                  dn.telephone as donateur_telephone
                  FROM {$this->table} dg
                  INNER JOIN bngrc_categorie c ON dg.categorie_id = c.id
                  INNER JOIN bngrc_donateur dn ON dg.donateur_id = dn.id
                  WHERE dg.id = :id";
        return $this->db->fetchRow($query, [':id' => $id]);
    }

    /**
     * Get available global donations by category
     */
    public function getAvailableByCategory($categorie_id)
    {
        $query = "SELECT dg.*, 
                  c.libelle as categorie_nom,
                  dn.nom as donateur_nom
                  FROM {$this->table} dg
                  INNER JOIN bngrc_categorie c ON dg.categorie_id = c.id
                  INNER JOIN bngrc_donateur dn ON dg.donateur_id = dn.id
                  WHERE dg.categorie_id = :categorie_id 
                  AND dg.status_distribution = 'disponible'
                  ORDER BY dg.date_don ASC";
        return $this->db->fetchAll($query, [':categorie_id' => $categorie_id]);
    }

    /**
     * Get all available global donations
     */
    public function getAllAvailable()
    {
        $query = "SELECT dg.*, 
                  c.libelle as categorie_nom,
                  dn.nom as donateur_nom
                  FROM {$this->table} dg
                  INNER JOIN bngrc_categorie c ON dg.categorie_id = c.id
                  INNER JOIN bngrc_donateur dn ON dg.donateur_id = dn.id
                  WHERE dg.status_distribution = 'disponible'
                  ORDER BY dg.date_don ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Add a new global donation
     */
    public function addDonGlobal($data)
    {
        // Validation des champs requis
        $requiredFields = ['categorie_id', 'donateur_id', 'quantite'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Le champ {$field} est requis");
            }
        }

        // Validation de la quantité
        if (!is_numeric($data['quantite']) || $data['quantite'] <= 0) {
            throw new \Exception("La quantité doit être un nombre positif");
        }

        $insertData = [
            'categorie_id' => $data['categorie_id'],
            'donateur_id' => $data['donateur_id'],
            'quantite' => $data['quantite'],
            'status_distribution' => 'disponible'
        ];

        // Ajouter la date si fournie, sinon utiliser la valeur par défaut de la DB
        if (!empty($data['date_don'])) {
            $insertData['date_don'] = $data['date_don'];
        }

        return $this->create($insertData);
    }

    /**
     * Update donation distribution status
     */
    public function updateDistributionStatus($id, $status)
    {
        $validStatuses = ['disponible', 'distribue', 'reserve'];
        if (!in_array($status, $validStatuses)) {
            throw new \Exception("Statut de distribution invalide");
        }

        return $this->update($id, ['status_distribution' => $status]);
    }

    /**
     * Get donations statistics by category
     */
    public function getStatsByCategory()
    {
        $query = "SELECT 
                    c.libelle as categorie,
                    COUNT(dg.id) as nombre_dons,
                    SUM(dg.quantite) as quantite_totale,
                    SUM(CASE WHEN dg.status_distribution = 'disponible' THEN dg.quantite ELSE 0 END) as quantite_disponible,
                    SUM(CASE WHEN dg.status_distribution = 'distribue' THEN dg.quantite ELSE 0 END) as quantite_distribuee,
                    SUM(CASE WHEN dg.status_distribution = 'reserve' THEN dg.quantite ELSE 0 END) as quantite_reservee
                  FROM bngrc_categorie c
                  LEFT JOIN {$this->table} dg ON c.id = dg.categorie_id
                  GROUP BY c.id, c.libelle
                  ORDER BY c.libelle";
        return $this->db->fetchAll($query);
    }

    /**
     * Get donations by donor
     */
    public function getByDonor($donateur_id)
    {
        $query = "SELECT dg.*, 
                  c.libelle as categorie_nom
                  FROM {$this->table} dg
                  INNER JOIN bngrc_categorie c ON dg.categorie_id = c.id
                  WHERE dg.donateur_id = :donateur_id
                  ORDER BY dg.date_don DESC";
        return $this->db->fetchAll($query, [':donateur_id' => $donateur_id]);
    }

    /**
     * Get remaining quantity for a donation (considering distributions)
     */
    public function getRemainingQuantity($don_global_id)
    {
        $query = "SELECT 
                    dg.quantite as quantite_originale,
                    COALESCE(SUM(dist.quantite_distribuee), 0) as quantite_distribuee,
                    (dg.quantite - COALESCE(SUM(dist.quantite_distribuee), 0)) as quantite_restante
                  FROM {$this->table} dg
                  LEFT JOIN bngrc_distribution dist ON dg.id = dist.don_global_id
                  WHERE dg.id = :don_global_id
                  GROUP BY dg.id, dg.quantite";
        return $this->db->fetchRow($query, [':don_global_id' => $don_global_id]);
    }
}