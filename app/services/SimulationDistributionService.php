<?php

namespace app\services;

use app\models\DonGlobal;
use app\models\Besoin;
use app\models\Distribution;

class SimulationDistributionService
{
    private $donGlobalModel;
    private $besoinModel;
    private $distributionModel;
    
    public function __construct()
    {
        $this->donGlobalModel = new DonGlobal();
        $this->besoinModel = new Besoin();
        $this->distributionModel = new Distribution();
    }

    /**
     * Simulate distribution of global donations with specified method
     * Returns the simulation results without actually performing the distribution
     */
    public function simulateDistribution($methode = 'date', $parametres = [])
    {
        // Récupérer tous les dons globaux disponibles
        $donsDisponibles = $this->donGlobalModel->getAllAvailable();
        
        // Récupérer tous les besoins non satisfaits selon la méthode choisie
        $besoins = $this->getBesoinsSelonMethode($methode, $parametres);
        
        // Simuler la distribution
        $simulationResults = [];
        $donsUtilises = [];
        
        foreach ($besoins as $besoin) {
            $besoinId = $besoin['besoin_id'];
            $categorieId = $besoin['categorie_id'];
            $quantiteManquante = $besoin['quantite_manquante'];
            
            if ($quantiteManquante <= 0) {
                continue;
            }
            
            // Trouver les dons disponibles pour cette catégorie
            $donsCategorie = array_filter($donsDisponibles, function($don) use ($categorieId) {
                return $don['categorie_id'] == $categorieId && 
                       $don['status_distribution'] == 'disponible';
            });
            
            // Trier les dons par date (plus ancien en premier)
            usort($donsCategorie, function($a, $b) {
                return strtotime($a['date_don']) - strtotime($b['date_don']);
            });
            
            $distributionsPourCeBesoin = [];
            $quantiteRestante = $quantiteManquante;
            
            foreach ($donsCategorie as &$don) {
                if ($quantiteRestante <= 0) {
                    break;
                }
                
                $donId = $don['id'];
                $quantiteDisponible = $don['quantite'];
                
                // Vérifier si ce don a déjà été utilisé dans la simulation
                if (isset($donsUtilises[$donId])) {
                    $quantiteDisponible -= $donsUtilises[$donId];
                }
                
                if ($quantiteDisponible <= 0) {
                    continue;
                }
                
                $quantiteADistribuer = min($quantiteRestante, $quantiteDisponible);
                
                if ($quantiteADistribuer > 0) {
                    // Enregistrer cette distribution simulée
                    $distributionsPourCeBesoin[] = [
                        'don_global_id' => $donId,
                        'don_info' => $don,
                        'quantite_distribuee' => $quantiteADistribuer,
                        'reste_don_apres' => $quantiteDisponible - $quantiteADistribuer
                    ];
                    
                    // Mettre à jour les quantités utilisées
                    if (!isset($donsUtilises[$donId])) {
                        $donsUtilises[$donId] = 0;
                    }
                    $donsUtilises[$donId] += $quantiteADistribuer;
                    $quantiteRestante -= $quantiteADistribuer;
                    
                    // Mettre à jour le statut simulé du don
                    if ($donsUtilises[$donId] >= $don['quantite']) {
                        $don['status_distribution'] = 'distribue_simule';
                    } else {
                        $don['status_distribution'] = 'reserve_simule';
                    }
                }
            }
            
            if (!empty($distributionsPourCeBesoin)) {
                $quantiteTotaleSatisfaite = array_sum(array_column($distributionsPourCeBesoin, 'quantite_distribuee'));
                
                $simulationResults[] = [
                    'besoin_info' => $besoin,
                    'quantite_satisfaite' => $quantiteTotaleSatisfaite,
                    'quantite_restante_apres' => $quantiteManquante - $quantiteTotaleSatisfaite,
                    'pourcentage_satisfaction' => ($quantiteTotaleSatisfaite / $quantiteManquante) * 100,
                    'distributions' => $distributionsPourCeBesoin,
                    'statut_final' => ($quantiteManquante - $quantiteTotaleSatisfaite) <= 0 ? 'satisfait' : 'partiellement_satisfait'
                ];
            } else {
                $simulationResults[] = [
                    'besoin_info' => $besoin,
                    'quantite_satisfaite' => 0,
                    'quantite_restante_apres' => $quantiteManquante,
                    'pourcentage_satisfaction' => 0,
                    'distributions' => [],
                    'statut_final' => 'non_satisfait',
                    'raison' => 'Aucun don disponible pour cette catégorie'
                ];
            }
        }
        
        // Calculer les résumés
        $resumeSimulation = $this->calculerResumeSimulation($simulationResults, $donsDisponibles, $donsUtilises);
        
        return [
            'details' => $simulationResults,
            'resume' => $resumeSimulation,
            'methode_utilisee' => $methode,
            'parametres' => $parametres,
            'date_simulation' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Get available distribution methods
     */
    public function getMethodesDistribution()
    {
        return [
            'date' => [
                'nom' => 'Distribution par Date',
                'description' => 'Distribue aux besoins les plus anciens en premier (date de besoin)',
                'icone' => 'calendar-event',
                'parametres' => []
            ],
            'quantite' => [
                'nom' => 'Distribution par Quantité',
                'description' => 'Trie les besoins par quantité: choisissez de prioriser les petits besoins (répartition) ou les gros (efficacité)',
                'icone' => 'bar-chart-fill',
                'parametres' => [
                    'ordre' => ['asc' => 'Plus petites quantités d\'abord', 'desc' => 'Plus grosses quantités d\'abord']
                ]
            ],
            'region' => [
                'nom' => 'Distribution par Région',
                'description' => 'Distribue par ordre de priorité des régions',
                'icone' => 'geo-alt-fill',
                'parametres' => [
                    'regions_prioritaires' => 'Liste des régions par ordre de priorité'
                ]
            ],
            'categorie' => [
                'nom' => 'Distribution par Catégorie',
                'description' => 'Privilégie certaines catégories (urgence, nourriture, etc.)',
                'icone' => 'tags-fill',
                'parametres' => [
                    'categories_prioritaires' => 'Ordre de priorité des catégories'
                ]
            ],
            'urgence' => [
                'nom' => 'Distribution par Urgence',
                'description' => 'Basée sur les jours d\'attente (plus urgent = plus de jours d\'attente)',
                'icone' => 'exclamation-triangle-fill',
                'parametres' => [
                    'seuil_urgent' => 'Nombre de jours pour considérer comme urgent (défaut: 15)'
                ]
            ],
            'equilibre' => [
                'nom' => 'Distribution Équilibrée',
                'description' => 'Répartit équitablement entre toutes les régions',
                'icone' => 'diagram-3-fill',
                'parametres' => [
                    'max_par_region' => 'Quantité maximum par région par tour'
                ]
            ]
        ];
    }

    /**
     * Get needs according to the selected distribution method
     */
    private function getBesoinsSelonMethode($methode, $parametres = [])
    {
        switch ($methode) {
            case 'date':
                return $this->besoinModel->getNeedsForSimulation(); // Déjà trié par date
                
            case 'quantite':
                $ordre = $parametres['ordre'] ?? 'desc';
                return $this->getBesoinsParQuantite($ordre);
                
            case 'region':
                $regionsPrioritaires = $parametres['regions_prioritaires'] ?? [];
                return $this->getBesoinsParRegion($regionsPrioritaires);
                
            case 'categorie':
                $categoriesPrioritaires = $parametres['categories_prioritaires'] ?? [];
                return $this->getBesoinsParCategorie($categoriesPrioritaires);
                
            case 'urgence':
                $seuilUrgent = $parametres['seuil_urgent'] ?? 15;
                return $this->getBesoinsParUrgence($seuilUrgent);
                
            case 'equilibre':
                $maxParRegion = $parametres['max_par_region'] ?? null;
                return $this->getBesoinsEquilibres($maxParRegion);
                
            default:
                return $this->besoinModel->getNeedsForSimulation();
        }
    }

    /**
     * Get needs sorted by quantity
     * 
     * @param string $ordre 'asc' pour petits besoins d'abord, 'desc' pour gros besoins d'abord
     * - 'asc': Priorise les petits besoins (meilleure répartition, plus équitable)
     * - 'desc': Priorise les gros besoins (plus efficace, aide plus de quantité aux gros demandes)
     * 
     * @return array Besoins triés selon la quantité manquante
     */
    private function getBesoinsParQuantite($ordre = 'desc')
    {
        $orderBy = $ordre === 'asc' ? 'ASC' : 'DESC';
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
                  FROM bngrc_besoin b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  LEFT JOIN bngrc_distribution d ON b.id = d.besoin_id
                  GROUP BY b.id, b.ville_id, v.nom, r.nom, b.categorie_id, c.libelle, 
                           b.quantite, b.prix_unitaire, b.date_besoin, s.libelle
                  HAVING quantite_manquante > 0
                  AND s.libelle NOT IN ('satisfait', 'completed')
                  ORDER BY quantite_manquante {$orderBy}, b.date_besoin ASC";
        
        return $this->besoinModel->rawQuery($query);
    }

    /**
     * Get needs sorted by region priority
     */
    private function getBesoinsParRegion($regionsPrioritaires = [])
    {
        $besoins = $this->besoinModel->getNeedsForSimulation();
        
        if (empty($regionsPrioritaires)) {
            // Si aucune priorité définie, grouper par région alphabétique
            usort($besoins, function($a, $b) {
                $regionComp = strcmp($a['region_nom'], $b['region_nom']);
                if ($regionComp === 0) {
                    return strtotime($a['date_besoin']) - strtotime($b['date_besoin']);
                }
                return $regionComp;
            });
            return $besoins;
        }
        
        // Trier selon l'ordre des régions prioritaires
        usort($besoins, function($a, $b) use ($regionsPrioritaires) {
            $priorityA = array_search($a['region_nom'], $regionsPrioritaires);
            $priorityB = array_search($b['region_nom'], $regionsPrioritaires);
            
            $priorityA = $priorityA !== false ? $priorityA : 999;
            $priorityB = $priorityB !== false ? $priorityB : 999;
            
            if ($priorityA === $priorityB) {
                return strtotime($a['date_besoin']) - strtotime($b['date_besoin']);
            }
            
            return $priorityA - $priorityB;
        });
        
        return $besoins;
    }

    /**
     * Get needs sorted by category priority
     */
    private function getBesoinsParCategorie($categoriesPrioritaires = [])
    {
        $besoins = $this->besoinModel->getNeedsForSimulation();
        
        if (empty($categoriesPrioritaires)) {
            // Si aucune priorité définie, grouper par catégorie alphabétique
            usort($besoins, function($a, $b) {
                $catComp = strcmp($a['categorie_nom'], $b['categorie_nom']);
                if ($catComp === 0) {
                    return strtotime($a['date_besoin']) - strtotime($b['date_besoin']);
                }
                return $catComp;
            });
            return $besoins;
        }
        
        // Trier selon l'ordre des catégories prioritaires
        usort($besoins, function($a, $b) use ($categoriesPrioritaires) {
            $priorityA = array_search($a['categorie_nom'], $categoriesPrioritaires);
            $priorityB = array_search($b['categorie_nom'], $categoriesPrioritaires);
            
            $priorityA = $priorityA !== false ? $priorityA : 999;
            $priorityB = $priorityB !== false ? $priorityB : 999;
            
            if ($priorityA === $priorityB) {
                return strtotime($a['date_besoin']) - strtotime($b['date_besoin']);
            }
            
            return $priorityA - $priorityB;
        });
        
        return $besoins;
    }

    /**
     * Get needs sorted by urgency (days waiting)
     */
    private function getBesoinsParUrgence($seuilUrgent = 15)
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
                    (b.quantite * b.prix_unitaire) as montant_total,
                    CASE 
                        WHEN DATEDIFF(NOW(), b.date_besoin) >= {$seuilUrgent} THEN 1 
                        ELSE 0 
                    END as est_urgent
                  FROM bngrc_besoin b
                  INNER JOIN bngrc_ville v ON b.ville_id = v.id
                  INNER JOIN bngrc_region r ON v.region_id = r.id
                  INNER JOIN bngrc_categorie c ON b.categorie_id = c.id
                  INNER JOIN bngrc_status s ON b.status_id = s.id
                  LEFT JOIN bngrc_distribution d ON b.id = d.besoin_id
                  GROUP BY b.id, b.ville_id, v.nom, r.nom, b.categorie_id, c.libelle, 
                           b.quantite, b.prix_unitaire, b.date_besoin, s.libelle
                  HAVING quantite_manquante > 0
                  AND s.libelle NOT IN ('satisfait', 'completed')
                  ORDER BY est_urgent DESC, jours_attente DESC, b.id ASC";
        
        return $this->besoinModel->rawQuery($query);
    }

    /**
     * Get needs with balanced distribution across regions
     */
    private function getBesoinsEquilibres($maxParRegion = null)
    {
        $besoins = $this->besoinModel->getNeedsForSimulation();
        
        if ($maxParRegion === null) {
            // Distribution équilibrée simple: alterner entre régions
            $besoinsParRegion = [];
            foreach ($besoins as $besoin) {
                $region = $besoin['region_nom'];
                if (!isset($besoinsParRegion[$region])) {
                    $besoinsParRegion[$region] = [];
                }
                $besoinsParRegion[$region][] = $besoin;
            }
            
            // Entrelacer les besoins des différentes régions
            $besoinsEquilibres = [];
            $maxIndex = max(array_map('count', $besoinsParRegion));
            
            for ($i = 0; $i < $maxIndex; $i++) {
                foreach ($besoinsParRegion as $regionBesoins) {
                    if (isset($regionBesoins[$i])) {
                        $besoinsEquilibres[] = $regionBesoins[$i];
                    }
                }
            }
            
            return $besoinsEquilibres;
        }
        
        // Distribution avec limite par région
        return $besoins; // TODO: implémenter la limitation
    }

    /**
     * Distribute donations specifically by date priority (oldest needs first)
     * This is the main legacy method
     */
    public function distribuerParDate()
    {
        return $this->effectuerDistributionAutomatique('date', []);
    }

    /**
     * Simulate distribution by date priority
     */
    public function simulerDistributionParDate()
    {
        return $this->simulateDistribution('date', []);
    }

    /**
     * Actually perform the distribution based on specified method and priority
     */
    public function effectuerDistributionAutomatique($methode = 'date', $parametres = [])
    {
        $simulation = $this->simulateDistribution($methode, $parametres);
        $distributionsEffectuees = [];
        $erreurs = [];
        
        foreach ($simulation['details'] as $besoinSimulation) {
            if (empty($besoinSimulation['distributions'])) {
                continue;
            }
            
            foreach ($besoinSimulation['distributions'] as $distribution) {
                try {
                    $distributionId = $this->distributionModel->addDistribution(
                        $distribution['don_global_id'],
                        $besoinSimulation['besoin_info']['besoin_id'],
                        $distribution['quantite_distribuee']
                    );
                    
                    $distributionsEffectuees[] = [
                        'distribution_id' => $distributionId,
                        'besoin_id' => $besoinSimulation['besoin_info']['besoin_id'],
                        'don_global_id' => $distribution['don_global_id'],
                        'quantite' => $distribution['quantite_distribuee'],
                        'ville' => $besoinSimulation['besoin_info']['ville_nom'],
                        'categorie' => $besoinSimulation['besoin_info']['categorie_nom']
                    ];
                } catch (\Exception $e) {
                    $erreurs[] = [
                        'besoin_id' => $besoinSimulation['besoin_info']['besoin_id'],
                        'don_global_id' => $distribution['don_global_id'],
                        'erreur' => $e->getMessage()
                    ];
                }
            }
        }
        
        return [
            'distributions_effectuees' => $distributionsEffectuees,
            'nombre_distributions' => count($distributionsEffectuees),
            'erreurs' => $erreurs,
            'simulation_originale' => $simulation,
            'date_execution' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Get distribution suggestions for a specific category
     */
    public function getSuggestionsDistributionParCategorie($categorieId)
    {
        $donsDisponibles = $this->donGlobalModel->getAvailableByCategory($categorieId);
        $besoins = $this->besoinModel->getByCategoryPriority($categorieId);
        
        $suggestions = [];
        
        foreach ($besoins as $besoin) {
            $quantiteManquante = $besoin['quantite_manquante'];
            if ($quantiteManquante <= 0) {
                continue;
            }
            
            $distributionsSuggérées = [];
            $quantiteRestante = $quantiteManquante;
            
            foreach ($donsDisponibles as $don) {
                if ($quantiteRestante <= 0) {
                    break;
                }
                
                $quantiteADistribuer = min($quantiteRestante, $don['quantite']);
                
                if ($quantiteADistribuer > 0) {
                    $distributionsSuggérées[] = [
                        'don_global_id' => $don['id'],
                        'donateur_nom' => $don['donateur_nom'],
                        'quantite_a_distribuer' => $quantiteADistribuer,
                        'date_don' => $don['date_don']
                    ];
                    
                    $quantiteRestante -= $quantiteADistribuer;
                }
            }
            
            $suggestions[] = [
                'besoin' => $besoin,
                'distributions_suggerees' => $distributionsSuggérées,
                'quantite_satisfiable' => $quantiteManquante - $quantiteRestante,
                'pourcentage_satisfaction' => (($quantiteManquante - $quantiteRestante) / $quantiteManquante) * 100
            ];
        }
        
        return $suggestions;
    }

    /**
     * Calculer le résumé de la simulation
     */
    private function calculerResumeSimulation($simulationResults, $donsDisponibles, $donsUtilises)
    {
        $totalBesoins = count($simulationResults);
        $besoinsSatisfaits = 0;
        $besoinsPartiellementSatisfaits = 0;
        $besoinsNonSatisfaits = 0;
        $totalQuantiteDistribuee = 0;
        
        foreach ($simulationResults as $result) {
            $totalQuantiteDistribuee += $result['quantite_satisfaite'];
            
            switch ($result['statut_final']) {
                case 'satisfait':
                    $besoinsSatisfaits++;
                    break;
                case 'partiellement_satisfait':
                    $besoinsPartiellementSatisfaits++;
                    break;
                case 'non_satisfait':
                    $besoinsNonSatisfaits++;
                    break;
            }
        }
        
        $totalDonsDisponibles = count($donsDisponibles);
        $donsUtilisesCompletement = 0;
        $donsUtilisesPartiellement = 0;
        $quantiteTotaleDons = 0;
        $quantiteTotaleUtilisee = array_sum($donsUtilises);
        
        foreach ($donsDisponibles as $don) {
            $quantiteTotaleDons += $don['quantite'];
            
            if (isset($donsUtilises[$don['id']])) {
                if ($donsUtilises[$don['id']] >= $don['quantite']) {
                    $donsUtilisesCompletement++;
                } else {
                    $donsUtilisesPartiellement++;
                }
            }
        }
        
        return [
            'besoins' => [
                'total' => $totalBesoins,
                'satisfaits' => $besoinsSatisfaits,
                'partiellement_satisfaits' => $besoinsPartiellementSatisfaits,
                'non_satisfaits' => $besoinsNonSatisfaits,
                'pourcentage_satisfaction' => $totalBesoins > 0 ? ($besoinsSatisfaits / $totalBesoins) * 100 : 0
            ],
            'dons' => [
                'total_disponibles' => $totalDonsDisponibles,
                'utilises_completement' => $donsUtilisesCompletement,
                'utilises_partiellement' => $donsUtilisesPartiellement,
                'non_utilises' => $totalDonsDisponibles - $donsUtilisesCompletement - $donsUtilisesPartiellement,
                'pourcentage_utilisation' => $quantiteTotaleDons > 0 ? ($quantiteTotaleUtilisee / $quantiteTotaleDons) * 100 : 0
            ],
            'quantites' => [
                'totale_demandee' => array_sum(array_column($simulationResults, 'quantite_satisfaite')) + 
                                   array_sum(array_column($simulationResults, 'quantite_restante_apres')),
                'totale_distribuee' => $totalQuantiteDistribuee,
                'totale_disponible' => $quantiteTotaleDons,
                'pourcentage_couverture' => $quantiteTotaleDons > 0 && $totalQuantiteDistribuee > 0 ? 
                                          ($totalQuantiteDistribuee / $quantiteTotaleDons) * 100 : 0
            ]
        ];
    }
}