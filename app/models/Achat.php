<?php

namespace app\models;

class Achat extends BaseModel
{
    protected $table = 'bngrc_achat';
    protected $primaryKey = 'id';

    /**
     * Get all purchases with details
     */
    public function getAllWithDetails()
    {
        $query = "SELECT a.*, 
                  v.nom as ville_nom,
                  b.quantite, b.prix_unitaire,
                  c.libelle as categorie_nom
                  FROM {$this->table} a
                  INNER JOIN bngrc_ville v ON a.ville_id = v.id
                  INNER JOIN bngrc_besoin b ON a.besoin_id = b.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  ORDER BY a.date_achat DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get purchase by ID with details
     */
    public function getByIdWithDetails($id)
    {
        $query = "SELECT a.*, 
                  v.nom as ville_nom,
                  b.quantite, b.prix_unitaire,
                  c.libelle as categorie_nom
                  FROM {$this->table} a
                  INNER JOIN bngrc_ville v ON a.ville_id = v.id
                  INNER JOIN bngrc_besoin b ON a.besoin_id = b.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  WHERE a.id = :id";
        return $this->db->fetchRow($query, [':id' => $id]);
    }

    /**
     * Get purchases by city
     */
    public function getByCity($ville_id)
    {
        $query = "SELECT a.*, 
                  b.quantite, b.prix_unitaire,
                  c.libelle as categorie_nom
                  FROM {$this->table} a
                  INNER JOIN bngrc_besoin b ON a.besoin_id = b.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  WHERE a.ville_id = :ville_id
                  ORDER BY a.date_achat DESC";
        return $this->db->fetchAll($query, [':ville_id' => $ville_id]);
    }

    /**
     * Get purchases by need
     */
    public function getByBesoin($besoin_id)
    {
        $query = "SELECT a.*, 
                  v.nom as ville_nom,
                  c.libelle as categorie_nom
                  FROM {$this->table} a
                  INNER JOIN bngrc_ville v ON a.ville_id = v.id
                  INNER JOIN bngrc_besoin b ON a.besoin_id = b.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  WHERE a.besoin_id = :besoin_id
                  ORDER BY a.date_achat DESC";
        return $this->db->fetchAll($query, [':besoin_id' => $besoin_id]);
    }

    /**
     * Get total purchases statistics
     */
    public function getStatistics()
    {
        $query = "SELECT 
                  COUNT(id) as total_count,
                  SUM(montant) as total_montant,
                  SUM(frais_percent) as total_frais_percent,
                  SUM(montant_total) as total_with_fees
                  FROM {$this->table}";
        return $this->db->fetchRow($query) ?? [
            'total_count' => 0,
            'total_montant' => 0,
            'total_frais_percent' => 0,
            'total_with_fees' => 0
        ];
    }

    /**
     * Delete a purchase
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->run($query, [':id' => $id]);
    }

    /**
     * Count total purchases
     */
    public function count($conditions = [])
    {
        $where = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $result = $this->db->fetchRow($query, $params);
        return $result['total'] ?? 0;
    }
}
