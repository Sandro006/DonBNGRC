<?php

namespace app\services;

use app\models\Don;
use app\models\Besoin;
use app\models\Ville;
use app\models\Categorie;

class SimulationService
{
    protected $donModel;
    protected $besoinModel;
    protected $villeModel;
    protected $categorieModel;

    public function __construct()
    {
        $this->donModel = new Don();
        $this->besoinModel = new Besoin();
        $this->villeModel = new Ville();
        $this->categorieModel = new Categorie();
    }

    /**
     * Simulate the dispatch of a donation to needs
     * Returns the simulation result without saving anything
     */
    public function simulateDispatch($don_id)
    {
        $don = $this->donModel->getByIdWithDetails($don_id);
        
        if (empty($don)) {
            return [
                'success' => false,
                'error' => 'Don introuvable',
            ];
        }

        // Get unmet needs in the same city and category
        $unmetNeeds = $this->getUnmetNeeds($don['ville_id'], $don['categorie_id']);
        
        $simulation = [
            'don' => $don,
            'dispatch_results' => [],
            'total_dispatched' => 0,
            'remaining_quantity' => $don['quantite'],
            'success' => true,
        ];

        // Simulate dispatch to each unmet need
        $remainingQty = $don['quantite'];
        foreach ($unmetNeeds as $need) {
            if ($remainingQty <= 0) {
                break;
            }

            $neededQty = $need['quantite']; // quantity still needed
            $dispatchedQty = min($remainingQty, $neededQty);

            $simulation['dispatch_results'][] = [
                'besoin_id' => $need['id'],
                'besoin_description' => $need['description'] ?? 'Sans description',
                'besoin_quantite_needed' => $neededQty,
                'besoin_ville_nom' => $need['ville_nom'],
                'dispatched_quantity' => $dispatchedQty,
                'new_need_quantity' => $neededQty - $dispatchedQty,
            ];

            $simulation['total_dispatched'] += $dispatchedQty;
            $remainingQty -= $dispatchedQty;
        }

        $simulation['remaining_quantity'] = $remainingQty;

        return $simulation;
    }

    /**
     * Validate and perform the actual dispatch
     * Saves to database and updates needs
     */
    public function validateDispatch($don_id)
    {
        $don = $this->donModel->getByIdWithDetails($don_id);
        
        if (empty($don)) {
            return [
                'success' => false,
                'error' => 'Don introuvable',
            ];
        }

        // Get unmet needs in the same city and category
        $unmetNeeds = $this->getUnmetNeeds($don['ville_id'], $don['categorie_id']);
        
        $result = [
            'don' => $don,
            'dispatch_results' => [],
            'total_dispatched' => 0,
            'remaining_quantity' => $don['quantite'],
            'success' => true,
        ];

        try {
            // Dispatch to each unmet need
            $remainingQty = $don['quantite'];
            foreach ($unmetNeeds as $need) {
                if ($remainingQty <= 0) {
                    break;
                }

                $neededQty = $need['quantite']; // quantity still needed
                $dispatchedQty = min($remainingQty, $neededQty);

                // Update the need's quantity
                $newQuantity = $neededQty - $dispatchedQty;
                $this->besoinModel->update($need['id'], ['quantite' => $newQuantity]);

                $result['dispatch_results'][] = [
                    'besoin_id' => $need['id'],
                    'besoin_quantite_before' => $neededQty,
                    'besoin_quantite_after' => $newQuantity,
                    'dispatched_quantity' => $dispatchedQty,
                ];

                $result['total_dispatched'] += $dispatchedQty;
                $remainingQty -= $dispatchedQty;
            }

            $result['remaining_quantity'] = $remainingQty;

            // Mark don as dispatched (you may need a status field in the don table)
            // For now, we'll keep it as is since don table might not have a status
            
            return $result;
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors du dispatch: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get unmet needs for a city and category
     * Returns needs that haven't been fully satisfied yet
     */
    private function getUnmetNeeds($ville_id, $categorie_id)
    {
        $query = "SELECT b.*, 
                  c.libelle as categorie_nom,
                  v.nom as ville_nom
                  FROM bngrc_besoin b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  WHERE b.ville_id = :ville_id 
                  AND b.categorie_id = :categorie_id 
                  AND b.quantite > 0
                  ORDER BY b.id ASC";
        
        return $this->besoinModel->rawQuery($query, [
            ':ville_id' => $ville_id,
            ':categorie_id' => $categorie_id,
        ]);
    }
}
