<?php

namespace app\models;

class Distribution extends BaseModel
{
    protected $table = 'bngrc_distribution';
    protected $primaryKey = 'id';

    /**
     * Get all distributions with details
     */
    public function getAllWithDetails()
    {
        $query = "SELECT 
                    d.id,
                    d.quantite_distribuee,
                    d.date_distribution,
                    d.methode_distribution,
                    d.responsable,
                    d.notes as distribution_notes,
                    -- Don Global info
                    dg.id as don_global_id,
                    dg.quantite as don_quantite_totale,
                    dg.date_don,
                    dg.valeur_unitaire,
                    dg.notes as don_notes,
                    donateur.nom as donateur_nom,
                    donateur.telephone as donateur_telephone,
                    donateur.type_donateur,
                    -- Besoin info
                    b.id as besoin_id,
                    b.quantite as besoin_quantite_totale,
                    b.date_besoin,
                    b.priorite,
                    b.description as besoin_description,
                    -- Location info
                    v.nom as ville_nom,
                    r.nom as region_nom,
                    -- Category info
                    c.libelle as categorie_nom
                  FROM {$this->table} d
                  INNER JOIN bngrc_don_global dg ON d.don_global_id = dg.id
                  INNER JOIN bngrc_besoin b ON d.besoin_id = b.id
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_donateur donateur ON dg.donateur_id = donateur.id
                  ORDER BY d.date_distribution DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get distributions for a specific global donation
     */
    public function getByDonGlobal($don_global_id)
    {
        $query = "SELECT 
                    d.*,
                    b.quantite as besoin_quantite,
                    b.date_besoin,
                    v.nom as ville_nom,
                    r.nom as region_nom,
                    c.libelle as categorie_nom
                  FROM {$this->table} d
                  INNER JOIN bngrc_besoin b ON d.besoin_id = b.id
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  WHERE d.don_global_id = :don_global_id
                  ORDER BY d.date_distribution ASC";
        return $this->db->fetchAll($query, [':don_global_id' => $don_global_id]);
    }

    /**
     * Get distributions for a specific need
     */
    public function getByBesoin($besoin_id)
    {
        $query = "SELECT 
                    d.*,
                    dg.quantite as don_quantite_totale,
                    dg.date_don,
                    donateur.nom as donateur_nom,
                    c.libelle as categorie_nom
                  FROM {$this->table} d
                  INNER JOIN bngrc_don_global dg ON d.don_global_id = dg.id
                  INNER JOIN bngrc_donateur donateur ON dg.donateur_id = donateur.id
                  INNER JOIN bngrc_besoin b ON d.besoin_id = b.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  WHERE d.besoin_id = :besoin_id
                  ORDER BY d.date_distribution ASC";
        return $this->db->fetchAll($query, [':besoin_id' => $besoin_id]);
    }

    /**
     * Add a new distribution record
     */
    public function addDistribution($don_global_id, $besoin_id, $quantite_distribuee, $options = [])
    {
        // Validation
        if (!is_numeric($quantite_distribuee) || $quantite_distribuee <= 0) {
            throw new \Exception("La quantité distribuée doit être un nombre positif");
        }

        // Vérifier que le don global existe et a assez de quantité disponible
        $donGlobalModel = new \app\models\DonGlobal();
        $remainingQuantity = $donGlobalModel->getRemainingQuantity($don_global_id);
        
        if (!$remainingQuantity || $remainingQuantity['quantite_restante'] < $quantite_distribuee) {
            throw new \Exception("Quantité insuffisante disponible pour ce don");
        }

        // Vérifier que le besoin existe
        $besoinModel = new \app\models\Besoin();
        $besoin = $besoinModel->getById($besoin_id);
        if (!$besoin) {
            throw new \Exception("Besoin non trouvé");
        }

        // Préparer les données de distribution
        $data = [
            'don_global_id' => $don_global_id,
            'besoin_id' => $besoin_id,
            'quantite_distribuee' => $quantite_distribuee,
            'methode_distribution' => $options['methode_distribution'] ?? 'automatique'
        ];

        // Ajouter les champs optionnels
        if (!empty($options['responsable'])) {
            $data['responsable'] = $options['responsable'];
        }

        if (!empty($options['notes'])) {
            $data['notes'] = $options['notes'];
        }

        if (!empty($options['date_distribution'])) {
            $data['date_distribution'] = $options['date_distribution'];
        }

        // Ajouter la distribution
        $result = $this->create($data);

        if ($result) {
            // Mettre à jour le statut du don global si nécessaire
            $this->updateDonGlobalStatus($don_global_id);
            
            // Mettre à jour le statut du besoin si nécessaire
            $this->updateBesoinStatus($besoin_id);
        }

        return $result;
    }

    /**
     * Update the status of a global donation based on its distributions
     */
    private function updateDonGlobalStatus($don_global_id)
    {
        $donGlobalModel = new \app\models\DonGlobal();
        $remainingQuantity = $donGlobalModel->getRemainingQuantity($don_global_id);
        
        if ($remainingQuantity) {
            if ($remainingQuantity['quantite_restante'] == 0) {
                $donGlobalModel->updateDistributionStatus($don_global_id, 'distribue');
            } elseif ($remainingQuantity['quantite_distribuee'] > 0) {
                $donGlobalModel->updateDistributionStatus($don_global_id, 'reserve');
            }
        }
    }

    /**
     * Update the status of a need based on its distributions
     */
    private function updateBesoinStatus($besoin_id)
    {
        $query = "SELECT 
                    b.quantite as quantite_demandee,
                    COALESCE(SUM(d.quantite_distribuee), 0) as quantite_recue
                  FROM bngrc_besoin b
                  LEFT JOIN {$this->table} d ON b.id = d.besoin_id
                  WHERE b.id = :besoin_id
                  GROUP BY b.id, b.quantite";
                  
        $result = $this->db->fetchRow($query, [':besoin_id' => $besoin_id]);
        
        if ($result) {
            $besoinModel = new \app\models\Besoin();
            
            if ($result['quantite_recue'] >= $result['quantite_demandee']) {
                // Besoin complètement satisfait
                $besoinModel->update($besoin_id, ['status_id' => 3]); // Assumant que 3 = "satisfait"
            } elseif ($result['quantite_recue'] > 0) {
                // Besoin partiellement satisfait
                $besoinModel->update($besoin_id, ['status_id' => 2]); // Assumant que 2 = "partiellement_satisfait"
            }
        }
    }

    /**
     * Get distribution statistics by city
     */
    public function getStatsByCity()
    {
        $query = "SELECT 
                    v.id as ville_id,
                    v.nom as ville_nom,
                    r.nom as region_nom,
                    COUNT(DISTINCT d.id) as nombre_distributions,
                    COUNT(DISTINCT d.don_global_id) as nombre_dons_recus,
                    COUNT(DISTINCT d.besoin_id) as nombre_besoins_satisfaits,
                    SUM(d.quantite_distribuee) as quantite_totale_recue
                  FROM bngrc_ville v
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_besoin b ON v.id = b.ville_id
                  INNER JOIN {$this->table} d ON b.id = d.besoin_id
                  GROUP BY v.id, v.nom, r.nom
                  ORDER BY r.nom, v.nom";
        return $this->db->fetchAll($query);
    }

    /**
     * Get distribution statistics by category
     */
    public function getStatsByCategory()
    {
        $query = "SELECT 
                    c.id as categorie_id,
                    c.libelle as categorie_nom,
                    COUNT(d.id) as nombre_distributions,
                    SUM(d.quantite_distribuee) as quantite_totale_distribuee,
                    COUNT(DISTINCT d.don_global_id) as nombre_dons_utilises,
                    COUNT(DISTINCT d.besoin_id) as nombre_besoins_satisfaits,
                    AVG(d.quantite_distribuee) as quantite_moyenne_par_distribution
                  FROM bngrc_categorie c
                  INNER JOIN bngrc_besoin b ON c.id = b.categorie_id
                  INNER JOIN {$this->table} d ON b.id = d.besoin_id
                  GROUP BY c.id, c.libelle
                  ORDER BY c.libelle";
        return $this->db->fetchAll($query);
    }

    /**
     * Simulate automatic distribution algorithm
     */
    public function simulateDistribution($limit = 50)
    {
        $query = "SELECT 
                    b.id as besoin_id,
                    b.ville_id,
                    v.nom as ville_nom,
                    b.categorie_id,
                    c.libelle as categorie_nom,
                    b.quantite as quantite_demandee,
                    b.date_besoin,
                    COALESCE(SUM(d.quantite_distribuee), 0) as quantite_deja_recue,
                    (b.quantite - COALESCE(SUM(d.quantite_distribuee), 0)) as quantite_manquante,
                    DATEDIFF(NOW(), b.date_besoin) as jours_attente
                  FROM bngrc_besoin b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  LEFT JOIN {$this->table} d ON b.id = d.besoin_id
                  GROUP BY b.id, b.ville_id, v.nom, b.categorie_id, c.libelle, b.quantite, b.date_besoin
                  HAVING quantite_manquante > 0
                  ORDER BY b.date_besoin ASC, b.id ASC
                  LIMIT :limit";
                  
        return $this->db->fetchAll($query, [':limit' => $limit]);
    }
}