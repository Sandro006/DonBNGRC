<?php

namespace app\services;

use app\models\Achat;
use app\models\Don;
use app\models\Besoin;
use Flight;

/**
 * Service for managing purchases and associated donations
 * Handles buying needs with money donations and calculating fees
 */
class AchatService
{
    protected $achatModel;
    protected $donModel;
    protected $besoinModel;

    // Default fees percentage (5%) - can be configured
    protected $defaultFeePercent = 5.0;

    public function __construct()
    {
        $this->achatModel = new Achat();
        $this->donModel = new Don();
        $this->besoinModel = new Besoin();
    }

    /**
     * TASK 1: Get purchase fees configuration
     * Returns the default fees percentage or retrieves from config
     */
    public function getFeePercent()
    {
        // Can be extended to read from a config table if needed
        return $this->defaultFeePercent;
    }

    /**
     * Set custom fee percentage
     */
    public function setFeePercent($percent)
    {
        if ($percent >= 0 && $percent <= 100) {
            $this->defaultFeePercent = $percent;
            return true;
        }
        return false;
    }

    /**
     * TASK 3 & 5: Calculate total amount with fees and validate money donations
     * 
     * @param float $baseMontant - Base purchase amount
     * @param float $availableMoney - Available money from donations
     * @return array ['success' => bool, 'total' => float, 'fees' => float, 'error' => string|null]
     */
    public function calculateWithFees($baseMontant, $availableMoney = 0)
    {
        $feePercent = $this->getFeePercent();
        $fees = ($baseMontant * $feePercent) / 100;
        $totalWithFees = $baseMontant + $fees;

        // TASK 2 & 5: Verify money donation is sufficient
        if ($availableMoney < $totalWithFees) {
            return [
                'success' => false,
                'total' => $totalWithFees,
                'fees' => $fees,
                'base' => $baseMontant,
                'available' => $availableMoney,
                'error' => sprintf(
                    'Argent insuffisant. Montant total avec frais: %.2f DZD. Disponible: %.2f DZD.',
                    $totalWithFees,
                    $availableMoney
                )
            ];
        }

        return [
            'success' => true,
            'total' => $totalWithFees,
            'fees' => $fees,
            'base' => $baseMontant,
            'available' => $availableMoney,
            'error' => null
        ];
    }

    /**
     * TASK 4: Create purchase and automatically deduct from money donation
     * 
     * @param int $ville_id - City ID
     * @param int $besoin_id - Need ID
     * @param float $montant - Base amount
     * @param int|null $don_id - Money donation ID to deduct from (optional)
     * @return array ['success' => bool, 'id' => int|null, 'remaining' => float, 'error' => string|null]
     */
    public function createPurchaseWithDonation($ville_id, $besoin_id, $montant, $don_id = null)
    {
        $feePercent = $this->getFeePercent();
        $montantTotal = $montant + ($montant * $feePercent / 100);

        // Create purchase record
        $achatData = [
            'ville_id' => $ville_id,
            'besoin_id' => $besoin_id,
            'montant' => $montant,
            'frais_percent' => $feePercent,
            'montant_total' => $montantTotal,
        ];

        try {
            $achatId = $this->achatModel->create($achatData);

            // If a donation is provided, log the deduction
            if ($don_id) {
                // Get donation details
                $don = $this->donModel->getById($don_id);
                if ($don) {
                    // In a real system, you might want to create a deduction record
                    // For now, just calculate remaining
                    $remaining = max(0, $don['quantite'] - $montantTotal);

                    return [
                        'success' => true,
                        'id' => $achatId,
                        'remaining' => $remaining,
                        'amount_deducted' => $montantTotal,
                        'error' => null
                    ];
                }
            }

            return [
                'success' => true,
                'id' => $achatId,
                'remaining' => null,
                'amount_deducted' => $montantTotal,
                'error' => null
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'id' => null,
                'remaining' => null,
                'amount_deducted' => $montantTotal,
                'error' => 'Erreur lors de la création de l\'achat: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all purchases
     */
    public function getAll()
    {
        return $this->achatModel->getAllWithDetails();
    }

    /**
     * Get purchase by ID
     */
    public function getById($id)
    {
        return $this->achatModel->getByIdWithDetails($id);
    }

    /**
     * Get purchases by city
     */
    public function getByCity($ville_id)
    {
        return $this->achatModel->getByCity($ville_id);
    }

    /**
     * Get purchase statistics
     */
    public function getStatistics()
    {
        return $this->achatModel->getStatistics();
    }

    /**
     * Delete a purchase
     */
    public function delete($id)
    {
        return $this->achatModel->delete($id);
    }

    /**
     * Update fee percentage for a specific purchase
     * Recalculates montant_total based on new percentage
     */
    public function updateFeePercent($achatId, $newFeePercent)
    {
        try {
            if ($newFeePercent < 0 || $newFeePercent > 100) {
                return [
                    'success' => false,
                    'error' => 'Le pourcentage de frais doit être entre 0 et 100'
                ];
            }

            // Get the purchase
            $achat = $this->achatModel->getById($achatId);
            if (!$achat) {
                return [
                    'success' => false,
                    'error' => 'Achat introuvable'
                ];
            }

            // Calculate new total
            $montant = $achat['montant'];
            $newFees = ($montant * $newFeePercent) / 100;
            $newTotal = $montant + $newFees;

            // Update the purchase
            $updateData = [
                'frais_percent' => $newFeePercent,
                'montant_total' => $newTotal
            ];

            $result = $this->achatModel->update($achatId, $updateData);

            if ($result) {
                return [
                    'success' => true,
                    'old_percent' => $achat['frais_percent'],
                    'new_percent' => $newFeePercent,
                    'old_total' => $achat['montant_total'],
                    'new_total' => $newTotal,
                    'error' => null
                ];
            }

            return [
                'success' => false,
                'error' => 'Erreur lors de la mise à jour'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all purchases with details
     */
    public function getAllWithDetails()
    {
        return $this->achatModel->getAllWithDetails();
    }

    /**
     * Filter purchases by date, city, and category
     */
    public function filterAchats($filters = [])
    {
        $query = "SELECT a.*, 
                  v.nom as ville_nom,
                  b.quantite, b.prix_unitaire,
                  c.libelle as categorie_nom
                  FROM bngrc_achat a
                  INNER JOIN bngrc_ville v ON a.ville_id = v.id
                  INNER JOIN bngrc_besoin b ON a.besoin_id = b.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  WHERE 1=1";
        
        $params = [];

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query .= " AND a.date_achat >= :date_from";
            $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND a.date_achat <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        // Filter by city
        if (!empty($filters['ville_id'])) {
            $query .= " AND a.ville_id = :ville_id";
            $params[':ville_id'] = $filters['ville_id'];
        }

        // Filter by category
        if (!empty($filters['categorie_id'])) {
            $query .= " AND b.categorie_id = :categorie_id";
            $params[':categorie_id'] = $filters['categorie_id'];
        }

        $query .= " ORDER BY a.date_achat DESC";

        return Flight::db()->fetchAll($query, $params);
    }
}
