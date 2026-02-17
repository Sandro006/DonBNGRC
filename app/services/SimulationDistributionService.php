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
        // Router vers la méthode appropriée selon le type de distribution
        if ($methode === 'distribution_proportionnelle') {
            return $this->simulateDistributionProportionnelle($parametres);
        }
        
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
                'description' => 'Privilégie les besoins avec les plus grosses quantités demandées, triés par date',
                'icone' => 'calendar-event',
                'parametres' => []
            ],
            'plus_petit_nombre' => [
                'nom' => 'Distribution par Plus Petit Nombre',
                'description' => 'Distribue en minimisant le nombre de besoins restants (satisfait le maximum de besoins)',
                'icone' => 'list-ol',
                'parametres' => []
            ],
            'distribution_proportionnelle' => [
                'nom' => 'Distribution Proportionnelle',
                'description' => 'Distribue les dons proportionnellement aux besoins: (besoin/total_besoins)*don, arrondi à l\'inférieur avec gestion des décimales pour les restes',
                'icone' => 'percent',
                'parametres' => []
            ]
        ];
    }

    /**
     * Simulate distribution using proportional method
     * Formula: (quantite_besoin / somme_total_besoins_categorie) * quantite_don
     * Quantities are floored, decimals are collected and distributed to highest decimals first
     */
    private function simulateDistributionProportionnelle($parametres = [])
    {
        $donsDisponibles = $this->donGlobalModel->getAllAvailable();
        $besoins = $this->getBesoinsSelonMethode('date', $parametres);  // On récupère tous les besoins
        
        // Grouper les besoins par catégorie
        $besoinsParCategorie = [];
        foreach ($besoins as $besoin) {
            if ($besoin['quantite_manquante'] > 0) {
                $categorieId = $besoin['categorie_id'];
                if (!isset($besoinsParCategorie[$categorieId])) {
                    $besoinsParCategorie[$categorieId] = [];
                }
                $besoinsParCategorie[$categorieId][] = $besoin;
            }
        }
        
        $simulationResults = [];
        $donsUtilises = [];
        $decimalesPourBesoin = [];  // Track decimals per besoin globally
        
        // Traiter chaque don
        foreach ($donsDisponibles as $don) {
            if ($don['status_distribution'] !== 'disponible') {
                continue;
            }
            
            $donId = $don['id'];
            $categorieId = $don['categorie_id'];
            $quantiteDon = $don['quantite'];
            
            // Vérifier si ce don a déjà été partiellement utilisé
            if (isset($donsUtilises[$donId])) {
                $quantiteDon -= $donsUtilises[$donId];
                if ($quantiteDon <= 0) {
                    continue;
                }
            }
            
            // Si pas de besoins pour cette catégorie
            if (!isset($besoinsParCategorie[$categorieId]) || empty($besoinsParCategorie[$categorieId])) {
                continue;
            }
            
            // Calculer la somme totale des besoins non satisfaits de cette catégorie
            $totalBesoinsCategorie = 0;
            foreach ($besoinsParCategorie[$categorieId] as $besoin) {
                if (isset($simulationResults[$besoin['besoin_id']])) {
                    $quantiteRestante = $besoin['quantite_manquante'] - $simulationResults[$besoin['besoin_id']]['quantite_satisfaite'];
                } else {
                    $quantiteRestante = $besoin['quantite_manquante'];
                }
                if ($quantiteRestante > 0) {
                    $totalBesoinsCategorie += $quantiteRestante;
                }
            }
            
            if ($totalBesoinsCategorie <= 0) {
                continue;
            }
            
            $quantiteDonDistribuee = 0;
            
            // Distribuer le don proportionnellement à chaque besoin
            foreach ($besoinsParCategorie[$categorieId] as $besoin) {
                $besoinId = $besoin['besoin_id'];
                
                // Calculer la quantité restante du besoin
                if (isset($simulationResults[$besoinId])) {
                    $quantiteRestanteBesoin = $besoin['quantite_manquante'] - $simulationResults[$besoinId]['quantite_satisfaite'];
                } else {
                    $quantiteRestanteBesoin = $besoin['quantite_manquante'];
                }
                
                if ($quantiteRestanteBesoin <= 0) {
                    continue;
                }
                
                // Calculer la part proportionnelle: (besoin_restant / total_besoins) * quantite_don
                $partProportionnelle = ($quantiteRestanteBesoin / $totalBesoinsCategorie) * $quantiteDon;
                $partEntiere = floor($partProportionnelle);
                $partDecimale = $partProportionnelle - $partEntiere;
                
                // CORRECTION: Limiter la part entière à ne pas dépasser le besoin restant
                $partEntiere = min($partEntiere, $quantiteRestanteBesoin);
                
                // Accumuler les décimales globalement par besoin
                if (!isset($decimalesPourBesoin[$besoinId])) {
                    $decimalesPourBesoin[$besoinId] = 0;
                }
                $decimalesPourBesoin[$besoinId] += $partDecimale;
                
                if ($partEntiere > 0) {
                    // Initialiser le résultat du besoin s'il n'existe pas
                    if (!isset($simulationResults[$besoinId])) {
                        $simulationResults[$besoinId] = [
                            'besoin_info' => $besoin,
                            'quantite_satisfaite' => 0,
                            'quantite_decimale_attribuee' => 0,
                            'distributions' => []
                        ];
                    }
                    
                    // Enregistrer cette distribution
                    $simulationResults[$besoinId]['distributions'][] = [
                        'don_global_id' => $donId,
                        'don_info' => $don,
                        'quantite_distribuee' => $partEntiere,
                        'part_proportionnelle' => $partProportionnelle,
                        'reste_don_apres' => $quantiteDon - $partEntiere
                    ];
                    
                    $simulationResults[$besoinId]['quantite_satisfaite'] += $partEntiere;
                    $quantiteDonDistribuee += $partEntiere;
                }
            }
            
            // Mettre à jour le total utilisé du don
            if (!isset($donsUtilises[$donId])) {
                $donsUtilises[$donId] = 0;
            }
            $donsUtilises[$donId] += $quantiteDonDistribuee;
        }
        
        // Redistribuer les décimales: donner priorité aux plus grosses décimales
        $decimalesTriees = $decimalesPourBesoin;
        arsort($decimalesTriees);  // Trier par ordre décroissant
        
        $quantiteDecimaleTotalDisponible = array_sum($decimalesTriees);
        
        // Distribuer les décimales complètes aux besoins avec les plus grandes décimales
        foreach ($decimalesTriees as $besoinId => $decimal) {
            if ($decimal >= 1) {
                $quantiteAjoutee = floor($decimal);
                
                // Initialiser le résultat du besoin s'il n'existe pas
                if (!isset($simulationResults[$besoinId])) {
                    $besoin = null;
                    // Chercher le besoin correspondant
                    foreach ($besoins as $b) {
                        if ($b['besoin_id'] === $besoinId) {
                            $besoin = $b;
                            break;
                        }
                    }
                    if ($besoin) {
                        $simulationResults[$besoinId] = [
                            'besoin_info' => $besoin,
                            'quantite_satisfaite' => 0,
                            'quantite_decimale_attribuee' => 0,
                            'distributions' => []
                        ];
                    }
                }
                
                if (isset($simulationResults[$besoinId])) {
                    // CORRECTION: Vérifier que le total ne dépasse pas le besoin
                    $quantiteActuellementSatisfaite = $simulationResults[$besoinId]['quantite_satisfaite'];
                    $quantiteManquanteBesoin = $simulationResults[$besoinId]['besoin_info']['quantite_manquante'];
                    $quantiteRestante = $quantiteManquanteBesoin - $quantiteActuellementSatisfaite;
                    
                    // Ne distribuer que ce qui reste à satisfaire
                    $quantiteAjoutee = min($quantiteAjoutee, $quantiteRestante);
                    
                    if ($quantiteAjoutee > 0) {
                        $simulationResults[$besoinId]['quantite_satisfaite'] += $quantiteAjoutee;
                        $simulationResults[$besoinId]['quantite_decimale_attribuee'] = $quantiteAjoutee;
                    }
                    $decimalesPourBesoin[$besoinId] -= $quantiteAjoutee;
                }
            }
        }
        
        // Formater les résultats de simulation
        $resultsFormatted = [];
        foreach ($besoins as $besoin) {
            $besoinId = $besoin['besoin_id'];
            
            if (isset($simulationResults[$besoinId])) {
                $quantiteSatisfaite = $simulationResults[$besoinId]['quantite_satisfaite'];
                $distributions = $simulationResults[$besoinId]['distributions'];
                $decimaleAttribuee = $simulationResults[$besoinId]['quantite_decimale_attribuee'];
            } else {
                $quantiteSatisfaite = 0;
                $distributions = [];
                $decimaleAttribuee = 0;
            }
            
            $quantiteManquante = $besoin['quantite_manquante'];
            $quantiteRestante = $quantiteManquante - $quantiteSatisfaite;
            
            $resultsFormatted[] = [
                'besoin_info' => $besoin,
                'quantite_satisfaite' => $quantiteSatisfaite,
                'quantite_restante_apres' => max(0, $quantiteRestante),
                'quantite_decimale_attribuee' => $decimaleAttribuee,
                'pourcentage_satisfaction' => $quantiteManquante > 0 ? ($quantiteSatisfaite / $quantiteManquante) * 100 : 0,
                'distributions' => $distributions,
                'statut_final' => $quantiteRestante <= 0 ? 'satisfait' : ($quantiteSatisfaite > 0 ? 'partiellement_satisfait' : 'non_satisfait')
            ];
        }
        
        // Calculer les résumés
        $resumeSimulation = $this->calculerResumeSimulation($resultsFormatted, $donsDisponibles, $donsUtilises);
        
        return [
            'details' => $resultsFormatted,
            'resume' => $resumeSimulation,
            'methode_utilisee' => 'distribution_proportionnelle',
            'parametres' => $parametres,
            'date_simulation' => date('Y-m-d H:i:s'),
            'description' => 'Distribution proportionnelle: (quantite_besoin / total_besoins_categorie) * quantite_don, avec redistribution des décimales aux plus grandes décimales'
        ];
    }

    /**
     * Get needs according to the selected distribution method
     */
    private function getBesoinsSelonMethode($methode, $parametres = [])
    {
        switch ($methode) {
            case 'date':
                // Priorité aux grosses quantités, triées par date
                return $this->getBesoinsParQuantiteDate();
                
            case 'plus_petit_nombre':
                // Priorité aux besoins les plus faciles à satisfaire
                return $this->getBesoinsParPlusPetitNombre();
                
            case 'distribution_proportionnelle':
                // Distribution proportionnelle au besoin
                return $this->getBesoinsParQuantiteDate();
                
            default:
                return $this->getBesoinsParQuantiteDate();
        }
    }

    /**
     * Get needs sorted by quantity (biggest first) and then by date
     * Méthode: Distribution par Date - Privilégie les grosses quantités
     */
    private function getBesoinsParQuantiteDate()
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
                  ORDER BY quantite_manquante DESC, b.date_besoin ASC";
        
        return $this->besoinModel->rawQuery($query);
    }

    /**
     * Get needs sorted to minimize the number of remaining needs
     * Méthode: Distribution par Plus Petit Nombre - satisfait le maximum de besoins
     * Priorité aux petites quantités pour satisfaire plus de besoins
     */
    private function getBesoinsParPlusPetitNombre()
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
                  ORDER BY quantite_manquante ASC, b.date_besoin ASC";
        
        return $this->besoinModel->rawQuery($query);
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
     * Simulate distribution using proportional method
     * Formula: (quantite_besoin / total_besoins_categorie) * quantite_don
     * Quantities are floored, decimals are collected and distributed
     */
    public function simulerDistributionProportionnelle()
    {
        return $this->simulateDistribution('distribution_proportionnelle', []);
    }

    /**
     * Actually perform proportional distribution
     * Saves the results to the database
     */
    public function distribuerProportionnellement()
    {
        return $this->effectuerDistributionAutomatique('distribution_proportionnelle', []);
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