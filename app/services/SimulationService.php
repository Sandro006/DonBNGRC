<?php

namespace app\services;

use app\models\DonGlobal;
use app\models\Besoin; 
use app\models\Ville;
use app\models\Categorie;
use app\models\Distribution;

class SimulationService
{
    protected $donGlobalModel;
    protected $besoinModel;
    protected $villeModel;
    protected $categorieModel;
    protected $distributionModel;

    public function __construct()
    {
        $this->donGlobalModel = new DonGlobal();
        $this->besoinModel = new Besoin();
        $this->villeModel = new Ville();
        $this->categorieModel = new Categorie();
        $this->distributionModel = new Distribution();
    }

    /**
     * Simulate the distribution of a global donation to needs across all cities
     * Returns the simulation result without saving anything
     */
    public function simulateDistribution($don_global_id)
    {
        $don = $this->donGlobalModel->getByIdWithDetails($don_global_id);
        
        if (empty($don)) {
            return [
                'success' => false,
                'error' => 'Don global introuvable',
            ];
        }

        // Get unmet needs for the same category across all cities, ordered by priority
        $unmetNeeds = $this->getUnmetNeedsByCategory($don['categorie_id']);
        
        $simulation = [
            'don' => $don,
            'distribution_results' => [],
            'total_distributed' => 0,
            'remaining_quantity' => $don['quantite'],
            'cities_served' => 0,
            'needs_satisfied' => 0,
            'success' => true,
        ];

        // Simulate distribution to each unmet need (prioritized)
        $remainingQty = $don['quantite'];
        $citiesServed = [];
        
        foreach ($unmetNeeds as $need) {
            if ($remainingQty <= 0) {
                break;
            }

            $neededQty = $need['quantite']; // quantity still needed
            $distributedQty = min($remainingQty, $neededQty);

            $simulation['distribution_results'][] = [
                'besoin_id' => $need['id'],
                'besoin_description' => $need['description'] ?? 'Sans description',
                'besoin_quantite_needed' => $neededQty,
                'ville_nom' => $need['ville_nom'],
                'region_nom' => $need['region_nom'],
                'priorite' => $need['priorite'] ?? 'normale',
                'distributed_quantity' => $distributedQty,
                'new_need_quantity' => $neededQty - $distributedQty,
                'satisfaction_percent' => round(($distributedQty / $neededQty) * 100, 1),
            ];

            $simulation['total_distributed'] += $distributedQty;
            $remainingQty -= $distributedQty;
            
            if (!in_array($need['ville_nom'], $citiesServed)) {
                $citiesServed[] = $need['ville_nom'];
            }
            
            if ($distributedQty >= $neededQty) {
                $simulation['needs_satisfied']++;
            }
        }

        $simulation['remaining_quantity'] = $remainingQty;
        $simulation['cities_served'] = count($citiesServed);

        return $simulation;
    }

    /**
     * Validate and perform the actual distribution
     * Saves to database and updates needs
     */
    public function validateDistribution($don_global_id, $methode_distribution = 'automatique', $responsable = 'Système')
    {
        $don = $this->donGlobalModel->getByIdWithDetails($don_global_id);
        
        if (empty($don)) {
            return [
                'success' => false,
                'error' => 'Don global introuvable',
            ];
        }

        // Check if this donation has already been distributed
        $existingDistribution = $this->distributionModel->getByDonGlobal($don_global_id);
        if (!empty($existingDistribution)) {
            return [
                'success' => false,
                'error' => 'Ce don global a déjà été distribué',
            ];
        }

        // Get unmet needs for the same category across all cities
        $unmetNeeds = $this->getUnmetNeedsByCategory($don['categorie_id']);
        
        $result = [
            'don' => $don,
            'distribution_results' => [],
            'total_distributed' => 0,
            'remaining_quantity' => $don['quantite'],
            'cities_served' => 0,
            'needs_satisfied' => 0,
            'success' => true,
        ];

        try {
            // Start transaction to ensure data consistency
            $this->besoinModel->beginTransaction();

            // Distribute to each unmet need
            $remainingQty = $don['quantite'];
            $citiesServed = [];
            
            foreach ($unmetNeeds as $need) {
                if ($remainingQty <= 0) {
                    break;
                }

                $neededQty = $need['quantite']; // quantity still needed
                $distributedQty = min($remainingQty, $neededQty);

                // Update the need's quantity
                $newQuantity = $neededQty - $distributedQty;
                $this->besoinModel->update($need['id'], ['quantite' => $newQuantity]);

                // Create distribution record
                $distributionData = [
                    'don_global_id' => $don_global_id,
                    'besoin_id' => $need['id'],
                    'quantite_distribuee' => $distributedQty,
                    'methode_distribution' => $methode_distribution,
                    'responsable' => $responsable,
                    'notes' => "Distribution automatique - Don #{$don_global_id} vers Besoin #{$need['id']}"
                ];
                
                $distribution_id = $this->distributionModel->addDistribution($distributionData);

                $result['distribution_results'][] = [
                    'distribution_id' => $distribution_id,
                    'besoin_id' => $need['id'],
                    'besoin_description' => 'Besoin #' . $need['id'] . ' - ' . ($need['categorie_nom'] ?? ''),
                    'ville_nom' => $need['ville_nom'],
                    'besoin_quantite_before' => $neededQty,
                    'besoin_quantite_after' => $newQuantity,
                    'distributed_quantity' => $distributedQty,
                ];

                $result['total_distributed'] += $distributedQty;
                $remainingQty -= $distributedQty;
                
                if (!in_array($need['ville_nom'], $citiesServed)) {
                    $citiesServed[] = $need['ville_nom'];
                }
                
                if ($distributedQty >= $neededQty) {
                    $result['needs_satisfied']++;
                }
            }

            $result['remaining_quantity'] = $remainingQty;
            $result['cities_served'] = count($citiesServed);

            // Update donation status if fully distributed
            if ($remainingQty <= 0) {
                $this->donGlobalModel->updateDistributionStatus($don_global_id, 'distribue');
            } else if ($result['total_distributed'] > 0) {
                $this->donGlobalModel->updateDistributionStatus($don_global_id, 'partiel');
            }

            // Commit transaction
            $this->besoinModel->commitTransaction();

            return $result;
        } catch (\Throwable $e) {
            // Rollback transaction on error
            if ($this->besoinModel->inTransaction()) {
                $this->besoinModel->rollbackTransaction();
            }
            
            return [
                'success' => false,
                'error' => 'Erreur lors de la distribution: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get unmet needs for a specific category across all cities
     * Returns needs that haven't been fully satisfied yet, ordered by priority
     */
    private function getUnmetNeedsByCategory($categorie_id)
    {
        $query = "SELECT b.*, 
                  c.libelle as categorie_nom,
                  v.nom as ville_nom,
                  r.nom as region_nom
                  FROM bngrc_besoin b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  WHERE b.categorie_id = :categorie_id 
                  AND b.quantite > 0
                  ORDER BY 
                    CASE 
                        WHEN b.priorite = 'urgente' THEN 1
                        WHEN b.priorite = 'elevee' THEN 2  
                        WHEN b.priorite = 'normale' THEN 3
                        ELSE 4 
                    END,
                  b.created_at ASC";
        
        return $this->besoinModel->rawQuery($query, [
            ':categorie_id' => $categorie_id,
        ]);
    }

    /**
     * Get distribution suggestions for a global donation
     */
    public function getDistributionSuggestions($don_global_id)
    {
        $simulation = $this->simulateDistribution($don_global_id);
        
        if (!$simulation['success']) {
            return $simulation;
        }

        // Add intelligence suggestions
        $suggestions = [
            'efficiency_score' => 0,
            'urgency_coverage' => 0,
            'geographical_spread' => $simulation['cities_served'],
            'recommendations' => [],
        ];

        if (!empty($simulation['distribution_results'])) {
            // Calculate efficiency score (% of donation that will be used)
            $total_donation = $simulation['don']['quantite'];
            $will_be_used = $simulation['total_distributed'];
            $suggestions['efficiency_score'] = round(($will_be_used / $total_donation) * 100, 1);

            // Calculate urgent needs coverage
            $urgent_results = array_filter($simulation['distribution_results'], function($r) {
                return $r['priorite'] === 'urgente';
            });
            $suggestions['urgency_coverage'] = count($urgent_results);

            // Generate recommendations
            if ($suggestions['efficiency_score'] >= 90) {
                $suggestions['recommendations'][] = "✅ Excellente efficacité - {$suggestions['efficiency_score']}% du don sera utilisé";
            } elseif ($suggestions['efficiency_score'] >= 70) {
                $suggestions['recommendations'][] = "⚠️ Bonne efficacité - {$suggestions['efficiency_score']}% du don sera utilisé";
            } else {
                $suggestions['recommendations'][] = "❌ Efficacité faible - seulement {$suggestions['efficiency_score']}% sera utilisé";
            }

            if ($suggestions['urgency_coverage'] > 0) {
                $suggestions['recommendations'][] = "🚨 Couvrira {$suggestions['urgency_coverage']} besoin(s) urgent(s)";
            }

            if ($suggestions['geographical_spread'] >= 3) {
                $suggestions['recommendations'][] = "🌍 Large couverture géographique - {$suggestions['geographical_spread']} villes servies";
            }
        }

        return array_merge($simulation, ['suggestions' => $suggestions]);
    }
}
