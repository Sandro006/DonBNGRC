<?php

namespace app\controllers;

use flight\Engine;
use app\models\DonGlobal;
use app\models\Distribution;
use app\models\Donateur;
use app\models\Categorie;
use app\services\SimulationDistributionService;
use app\services\SmartDistributionService;

class DonGlobalController
{
    private $app;
    private $donGlobalModel;
    private $distributionModel;
    private $simulationService;
    private $smartDistributionService;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->donGlobalModel = new DonGlobal();
        $this->distributionModel = new Distribution();
        $this->simulationService = new SimulationDistributionService();
        $this->smartDistributionService = new SmartDistributionService();
    }

    /**
     * Display the global donations management page
     */
    public function index()
    {
        try {
            // Get all global donations with details
            $donsGlobaux = $this->donGlobalModel->getAllWithDetails();
            
            // Get statistics
            $stats = $this->donGlobalModel->getStatsByCategory();
            
            // Get distributions statistics
            $distributionStats = $this->distributionModel->getStatsByCategory();
            
            $this->app->render('DonGlobalIndex', [
                'dons_globaux' => $donsGlobaux,
                'stats' => $stats,
                'distribution_stats' => $distributionStats,
                'title' => 'Gestion des Dons Globaux'
            ]);
        } catch (\Exception $e) {
            $this->app->halt(500, "Erreur lors du chargement des dons globaux: " . $e->getMessage());
        }
    }

    /**
     * Display form to add a new global donation
     */
    public function create()
    {
        try {
            // Get donateurs and categories for the form
            $donateurModel = new Donateur();
            $categorieModel = new Categorie();
            
            $donateurs = $donateurModel->getAll();
            $categories = $categorieModel->getAll();
            
            $this->app->render('DonGlobalCreate', [
                'donateurs' => $donateurs,
                'categories' => $categories,
                'title' => 'Ajouter un Don Global'
            ]);
        } catch (\Exception $e) {
            $this->app->halt(500, "Erreur lors du chargement du formulaire: " . $e->getMessage());
        }
    }

    /**
     * Store a new global donation
     */
    public function store()
    {
        try {
            $isAjax = $this->app->request()->getHeader('Content-Type') === 'application/json' || 
                     $this->app->request()->getHeader('X-Requested-With') === 'XMLHttpRequest';
            
            $data = [];
            if ($isAjax) {
                $data = $this->app->request()->data->getData();
            } else {
                // Handle regular form submission
                $req = $this->app->request();
                $data['categorie_id'] = $req->data->categorie_id ?? null;
                $data['quantite'] = $req->data->quantite ?? null;
                $data['date_don'] = $req->data->date_don ?? null;
                $data['valeur_unitaire'] = $req->data->valeur_unitaire ?? null;
                $data['notes'] = $req->data->notes ?? null;
                
                // Handle donateur like in DonController
                $donateur_id = $req->data->donateur_id ?? null;
                
                if (empty($donateur_id)) {
                    // Create new donateur
                    $donateurModel = new \app\models\Donateur();
                    $donateurData = [
                        'nom' => $req->data->donateur_nom ?? 'Anonyme',
                        'telephone' => $req->data->donateur_telephone ?? null,
                        'email' => $req->data->donateur_email ?? null,
                        'type_donateur' => $req->data->type_donateur ?? 'particulier',
                        'adresse' => $req->data->donateur_adresse ?? null,
                    ];
                    $donateur_id = $donateurModel->create($donateurData);
                }
                
                $data['donateur_id'] = $donateur_id;
                
                // Handle date format
                if (!empty($data['date_don'])) {
                    $data['date_don'] = str_replace('T', ' ', $data['date_don']);
                    if (strlen($data['date_don']) === 16) {
                        $data['date_don'] .= ':00';
                    }
                }
            }
            
            // Validate required fields
            $requiredFields = ['categorie_id', 'donateur_id', 'quantite'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    if ($isAjax) {
                        $this->app->response()->status(400);
                        $this->app->json([
                            'success' => false,
                            'message' => "Le champ {$field} est requis"
                        ]);
                        return;
                    } else {
                        $this->app->halt(400, "Le champ {$field} est requis");
                    }
                }
            }
            
            // Add the global donation
            $donGlobalId = $this->donGlobalModel->addDonGlobal($data);
            
            if ($donGlobalId) {
                if ($isAjax) {
                    $this->app->json([
                        'success' => true,
                        'message' => 'Don global ajouté avec succès',
                        'don_global_id' => $donGlobalId
                    ]);
                } else {
                    // Redirect to don-global index with success message
                    $this->app->redirect(
                        Flight::get('flight.base_url') . '/don-global?success=1&id=' . $donGlobalId
                    );
                }
            } else {
                if ($isAjax) {
                    $this->app->response()->status(500);
                    $this->app->json([
                        'success' => false,
                        'message' => 'Erreur lors de l\'ajout du don global'
                    ]);
                } else {
                    $this->app->halt(500, 'Erreur lors de l\'ajout du don global');
                }
            }
        } catch (\Exception $e) {
            $isAjax = $this->app->request()->getHeader('Content-Type') === 'application/json' || 
                     $this->app->request()->getHeader('X-Requested-With') === 'XMLHttpRequest';
                     
            if ($isAjax) {
                $this->app->response()->status(500);
                $this->app->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                $this->app->halt(500, "Erreur lors de l'ajout du don global: " . $e->getMessage());
            }
        }
    }

    /**
     * Show details of a specific global donation
     */
    public function show($id)
    {
        try {
            $donGlobal = $this->donGlobalModel->getByIdWithDetails($id);
            
            if (!$donGlobal) {
                $this->app->halt(404, "Don global non trouvé");
            }
            
            // Get distributions for this donation
            $distributions = $this->distributionModel->getByDonGlobal($id);
            
            // Get remaining quantity
            $remainingQuantity = $this->donGlobalModel->getRemainingQuantity($id);
            
            $this->app->render('DonGlobal/Show', [
                'don_global' => $donGlobal,
                'distributions' => $distributions,
                'remaining_quantity' => $remainingQuantity,
                'title' => 'Détails du Don Global #' . $id
            ]);
        } catch (\Exception $e) {
            $this->app->halt(500, "Erreur lors du chargement du don global: " . $e->getMessage());
        }
    }

    /**
     * Display distribution methods selection page
     */
    public function methodes()
    {
        try {
            $methodes = $this->simulationService->getMethodesDistribution();
            
            $this->app->render('MethodesDistribution', [
                'methodes' => $methodes,
                'title' => 'Méthodes de Distribution'
            ]);
        } catch (\Exception $e) {
            $this->app->halt(500, "Erreur lors du chargement des méthodes: " . $e->getMessage());
        }
    }

    /**
     * Display simulation page with selected method
     */
    public function simulation()
    {
        try {
            // Get method and parameters from request
            $methode = $this->app->request()->data->methode ?? $this->app->request()->query->methode ?? 'date';
            $parametres = $this->app->request()->data->parametres ?? $this->app->request()->query->parametres ?? [];
            $action = $this->app->request()->data->action ?? 'simuler';
            
            // If action is execute, perform distribution and redirect
            if ($action === 'executer') {
                $result = $this->simulationService->effectuerDistributionAutomatique($methode, $parametres);
                
                // Redirect with success message
                $this->app->redirect(
                    Flight::get('flight.base_url') . '/don-global/simulation?' . 
                    http_build_query([
                        'methode' => $methode, 
                        'executed' => '1',
                        'distributions' => $result['nombre_distributions']
                    ])
                );
                return;
            }
            
            // Otherwise, run simulation
            $simulationResults = $this->simulationService->simulateDistribution($methode, $parametres);
            
            // Check if execution was just performed
            $justExecuted = $this->app->request()->query->executed === '1';
            $nbDistributions = $this->app->request()->query->distributions ?? 0;
            
            $this->app->render('SimulationDistribution', [
                'simulation' => $simulationResults,
                'methode_courante' => $methode,
                'parametres_courants' => $parametres,
                'just_executed' => $justExecuted,
                'nb_distributions' => $nbDistributions,
                'available_methods' => $this->simulationService->getMethodesDistribution(),
                'title' => 'Simulation de Distribution - ' . ucfirst($methode)
            ]);
        } catch (\Exception $e) {
            $this->app->halt(500, "Erreur lors de la simulation: " . $e->getMessage());
        }
    }

    /**
     * Execute distribution with specified method
     */
    public function executeDistribution()
    {
        try {
            $data = $this->app->request()->data->getData();
            $methode = $data['methode'] ?? 'date';
            $parametres = $data['parametres'] ?? [];
            
            $result = $this->simulationService->effectuerDistributionAutomatique($methode, $parametres);
            
            $this->app->json([
                'success' => true,
                'message' => "Distribution effectuée avec succès (méthode: {$methode})",
                'result' => $result
            ]);
        } catch (\Exception $e) {
            $this->app->response()->status(500);
            $this->app->json([
                'success' => false,
                'message' => "Erreur lors de la distribution: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available donations for a specific category (AJAX)
     */
    public function getAvailableByCategory($categorieId)
    {
        try {
            $donsDisponibles = $this->donGlobalModel->getAvailableByCategory($categorieId);
            
            $this->app->json([
                'success' => true,
                'data' => $donsDisponibles
            ]);
        } catch (\Exception $e) {
            $this->app->response()->status(500);
            $this->app->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get distribution suggestions for a category
     */
    public function getSuggestionsDistribution($categorieId)
    {
        try {
            $suggestions = $this->simulationService->getSuggestionsDistributionParCategorie($categorieId);
            
            $this->app->json([
                'success' => true,
                'data' => $suggestions
            ]);
        } catch (\Exception $e) {
            $this->app->response()->status(500);
            $this->app->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Manual distribution - distribute specific amounts to specific needs
     */
    public function manualDistribution()
    {
        try {
            $data = $this->app->request()->data->getData();
            
            if (empty($data['distributions']) || !is_array($data['distributions'])) {
                $this->app->response()->status(400);
                $this->app->json([
                    'success' => false,
                    'message' => 'Aucune distribution spécifiée'
                ]);
                return;
            }
            
            $distributionsEffectuees = [];
            $erreurs = [];
            
            foreach ($data['distributions'] as $distribution) {
                try {
                    $distributionId = $this->distributionModel->addDistribution(
                        $distribution['don_global_id'],
                        $distribution['besoin_id'],
                        $distribution['quantite_distribuee']
                    );
                    
                    $distributionsEffectuees[] = [
                        'distribution_id' => $distributionId,
                        'don_global_id' => $distribution['don_global_id'],
                        'besoin_id' => $distribution['besoin_id'],
                        'quantite' => $distribution['quantite_distribuee']
                    ];
                } catch (\Exception $e) {
                    $erreurs[] = [
                        'don_global_id' => $distribution['don_global_id'],
                        'besoin_id' => $distribution['besoin_id'],
                        'erreur' => $e->getMessage()
                    ];
                }
            }
            
            $this->app->json([
                'success' => true,
                'message' => 'Distribution manuelle effectuée',
                'distributions_effectuees' => $distributionsEffectuees,
                'erreurs' => $erreurs
            ]);
        } catch (\Exception $e) {
            $this->app->response()->status(500);
            $this->app->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'dons_par_categorie' => $this->donGlobalModel->getStatsByCategory(),
                'distributions_par_ville' => $this->distributionModel->getStatsByCity(),
                'distributions_par_categorie' => $this->distributionModel->getStatsByCategory()
            ];
            
            $this->app->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            $this->app->response()->status(500);
            $this->app->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update donation distribution status
     */
    public function updateStatus($id)
    {
        try {
            $data = $this->app->request()->data->getData();
            
            if (empty($data['status'])) {
                $this->app->response()->status(400);
                $this->app->json([
                    'success' => false,
                    'message' => 'Statut requis'
                ]);
                return;
            }
            
            $result = $this->donGlobalModel->updateDistributionStatus($id, $data['status']);
            
            if ($result) {
                $this->app->json([
                    'success' => true,
                    'message' => 'Statut mis à jour avec succès'
                ]);
            } else {
                $this->app->response()->status(500);
                $this->app->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du statut'
                ]);
            }
        } catch (\Exception $e) {
            $this->app->response()->status(500);
            $this->app->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}