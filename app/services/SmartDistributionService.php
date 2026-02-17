<?php

namespace app\services;

use app\models\Besoin;
use app\models\DonGlobal;
use app\models\Distribution;

/**
 * Service pour la gestion intelligente des besoins et distributions
 * Profite des nouvelles fonctionnalités de priorité et des dons globaux
 */
class SmartDistributionService
{
    protected $besoinModel;
    protected $donGlobalModel;
    protected $distributionModel;

    public function __construct()
    {
        $this->besoinModel = new Besoin();
        $this->donGlobalModel = new DonGlobal();
        $this->distributionModel = new Distribution();
    }

    /**
     * Get dashboard statistics with priority insights
     */
    public function getDashboardStats()
    {
        // Besoins par priorité
        $urgentNeeds = $this->besoinModel->getUrgentNeeds();
        $totalUrgent = count($urgentNeeds);

        // Dons disponibles par type de donateur
        $donsByType = [
            'particulier' => $this->donGlobalModel->getByDonorType('particulier'),
            'entreprise' => $this->donGlobalModel->getByDonorType('entreprise'),
            'association' => $this->donGlobalModel->getByDonorType('association'),
            'ong' => $this->donGlobalModel->getByDonorType('ong')
        ];

        // Dons qui expirent bientôt
        $expiringSoon = $this->donGlobalModel->getExpiringSoon();

        // Statistics générales
        $donStats = $this->donGlobalModel->getStatistics();
        $besoinStats = $this->besoinModel->getStatistics();

        return [
            'urgent_needs_count' => $totalUrgent,
            'urgent_needs' => array_slice($urgentNeeds, 0, 10), // Top 10
            'expiring_donations' => $expiringSoon,
            'donations_by_donor_type' => array_map('count', $donsByType),
            'don_stats' => $donStats,
            'besoin_stats' => $besoinStats,
            'coverage_percent' => $this->calculateCoveragePercent($donStats, $besoinStats)
        ];
    }

    /**
     * Suggest optimal distributions based on priority and geography
     */
    public function suggestOptimalDistribution($categorie_id, $max_suggestions = 10)
    {
        // Get available donations for this category
        $availableDons = $this->donGlobalModel->getAvailableByCategory($categorie_id);
        
        // Get urgent needs for this category
        $urgentNeeds = $this->besoinModel->getUrgentNeeds();
        $categoryNeeds = array_filter($urgentNeeds, function($need) use ($categorie_id) {
            return $need['categorie_id'] == $categorie_id;
        });

        $suggestions = [];

        foreach ($availableDons as $don) {
            $remainingQty = $this->donGlobalModel->getRemainingQuantity($don['id']);
            
            if ($remainingQty['quantite_restante'] <= 0) {
                continue;
            }

            foreach ($categoryNeeds as $need) {
                // Score the matching based on priority and urgency
                $score = $this->calculateDistributionScore($don, $need);
                
                $suggestions[] = [
                    'don' => $don,
                    'besoin' => $need,
                    'quantity_available' => $remainingQty['quantite_restante'],
                    'quantity_needed' => $need['quantite'],
                    'suggested_quantity' => min($remainingQty['quantite_restante'], $need['quantite']),
                    'score' => $score,
                    'efficiency' => $this->calculateEfficiency($don, $need)
                ];
            }
        }

        // Sort by score (highest first) and limit results
        usort($suggestions, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($suggestions, 0, $max_suggestions);
    }

    /**
     * Execute a batch of distributions
     */
    public function executeBatchDistribution($distributions, $responsable = 'Système automatique')
    {
        $results = [
            'success' => [],
            'errors' => [],
            'summary' => [
                'total' => count($distributions),
                'successful' => 0,
                'failed' => 0
            ]
        ];

        foreach ($distributions as $dist) {
            try {
                $result = $this->distributionModel->addDistribution(
                    $dist['don_global_id'],
                    $dist['besoin_id'],
                    $dist['quantite_distribuee'],
                    [
                        'methode_distribution' => 'automatique',
                        'responsable' => $responsable,
                        'notes' => $dist['notes'] ?? 'Distribution automatique par le système'
                    ]
                );

                $results['success'][] = [
                    'don_id' => $dist['don_global_id'],
                    'besoin_id' => $dist['besoin_id'],
                    'quantite' => $dist['quantite_distribuee'],
                    'distribution_id' => $result
                ];

                $results['summary']['successful']++;

            } catch (\Exception $e) {
                $results['errors'][] = [
                    'don_id' => $dist['don_global_id'],
                    'besoin_id' => $dist['besoin_id'],
                    'error' => $e->getMessage()
                ];

                $results['summary']['failed']++;
            }
        }

        return $results;
    }

    /**
     * Calculate distribution score based on priority, urgency, and efficiency
     */
    private function calculateDistributionScore($don, $need)
    {
        $score = 0;

        // Priority weight (urgente: 40, haute: 30, normale: 20, basse: 10)
        $priorityWeights = ['urgente' => 40, 'haute' => 30, 'normale' => 20, 'basse' => 10];
        $score += $priorityWeights[$need['priorite']] ?? 20;

        // Urgency based on days waiting
        $daysWaiting = $need['jours_attente'] ?? 0;
        if ($daysWaiting > 14) $score += 20;
        elseif ($daysWaiting > 7) $score += 10;
        elseif ($daysWaiting > 3) $score += 5;

        // Quantity match efficiency
        $donQty = $don['quantite'];
        $needQty = $need['quantite'];
        
        if ($donQty >= $needQty) {
            // Can fully satisfy the need
            $score += 30;
            
            // Bonus for exact or close match (less waste)
            $ratio = $donQty / $needQty;
            if ($ratio <= 1.1) $score += 20; // Perfect match
            elseif ($ratio <= 1.5) $score += 10; // Close match
        } else {
            // Partial satisfaction
            $score += 10 + ($donQty / $needQty * 20);
        }

        // Recent donation bonus (encourage using fresh donations)
        $donAge = strtotime('now') - strtotime($don['date_don']);
        $daysSinceDon = $donAge / (24 * 3600);
        if ($daysSinceDon <= 1) $score += 10;
        elseif ($daysSinceDon <= 7) $score += 5;

        return $score;
    }

    /**
     * Calculate efficiency percentage
     */
    private function calculateEfficiency($don, $need)
    {
        $donQty = $don['quantite'];
        $needQty = $need['quantite'];
        
        if ($donQty >= $needQty) {
            // Efficiency = how well the donation fits the need (less waste is better)
            return min(100, (int)(($needQty / $donQty) * 100));
        } else {
            // Efficiency = percentage of need that can be satisfied
            return (int)(($donQty / $needQty) * 100);
        }
    }

    /**
     * Calculate coverage percentage
     */
    private function calculateCoveragePercent($donStats, $besoinStats)
    {
        $totalDonValue = $donStats['valeur_totale_estimee'] ?? 0;
        $totalBesoinValue = $besoinStats['total_amount'] ?? 0;
        
        if ($totalBesoinValue <= 0) {
            return 100;
        }
        
        return min(100, (int)(($totalDonValue / $totalBesoinValue) * 100));
    }

    /**
     * Get priority distribution recommendations
     */
    public function getPriorityRecommendations($limit = 20)
    {
        $recommendations = [];

        // Get all urgent needs
        $urgentNeeds = $this->besoinModel->getByPriorite('urgente');
        
        foreach ($urgentNeeds as $need) {
            $suggestions = $this->suggestOptimalDistribution($need['categorie_id'], 3);
            
            // Filter suggestions for this specific need
            $needSuggestions = array_filter($suggestions, function($sugg) use ($need) {
                return $sugg['besoin']['id'] == $need['id'];
            });

            if (!empty($needSuggestions)) {
                $recommendations = array_merge($recommendations, array_values($needSuggestions));
            }
        }

        // Sort by score and limit
        usort($recommendations, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($recommendations, 0, $limit);
    }
}