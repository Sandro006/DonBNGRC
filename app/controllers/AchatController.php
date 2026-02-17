<?php

namespace app\controllers;

use app\services\AchatService;
use app\services\BesoinService;
use app\services\DonService;
use app\models\Achat;
use app\models\Besoin;
use app\models\Don;
use app\models\Ville;
use flight\Engine;

class AchatController
{
    protected $achatService;
    protected $besoinService;
    protected $donService;

    public function __construct()
    {
        $this->achatService = new AchatService();
        $this->besoinService = new BesoinService();
        $this->donService = new DonService();
    }

    /**
     * Display list of all purchases
     */
    public function index(Engine $app)
    {
        try {
            $achats = $this->achatService->getAll();
            $stats = $this->achatService->getStatistics();

            $app->render('ListAchats', [
                'message' => 'Liste des achats',
                'achats' => $achats,
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController index error: ' . $e->getMessage());
            $app->halt(500, 'Erreur lors du chargement des achats');
        }
    }

    /**
     * Display purchase details
     */
    public function show(Engine $app, $id)
    {
        try {
            $achat = $this->achatService->getById($id);
            if (empty($achat)) {
                $app->halt(404, 'Achat introuvable');
            }

            $app->render('AchatDetails', [
                'achat' => $achat,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController show error: ' . $e->getMessage());
            $app->halt(500, 'Erreur lors du chargement de l\'achat');
        }
    }

    /**
     * Show purchase creation form
     */
    public function create(Engine $app)
    {
        try {
            $villeModel = new Ville();
            $besoinModel = new Besoin();
            $donModel = new Don();

            $villes = $villeModel->getAll();
            $besoins = $besoinModel->getAllWithDetails();
            $dons = $donModel->getAllWithDetails();

            $app->render('CreateAchat', [
                'villes' => $villes,
                'besoins' => $besoins,
                'dons' => $dons,
                'feePercent' => $this->achatService->getFeePercent(),
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController create GET error: ' . $e->getMessage());
            $app->halt(500, 'Erreur interne');
        }
    }

    /**
     * Handle purchase creation (POST)
     */
    public function store(Engine $app)
    {
        $req = $app->request();

        try {
            $ville_id = (int)($req->data->ville_id ?? 0);
            $besoin_id = (int)($req->data->besoin_id ?? 0);
            $montant = (float)($req->data->montant ?? 0);
            $don_id = !empty($req->data->don_id) ? (int)$req->data->don_id : null;

            // Validation
            if (empty($ville_id) || empty($besoin_id) || $montant <= 0) {
                $app->halt(400, 'Données invalides');
            }

            // Get donation details if provided
            $availableMoney = 0;
            if ($don_id) {
                $don = (new Don())->getById($don_id);
                if (!empty($don) && $don['categorie_id'] == 3) { // Assuming 3 is money category
                    $availableMoney = (float)$don['quantite'];
                }
            }

            // Calculate with fees and validate
            $calcResult = $this->achatService->calculateWithFees($montant, $availableMoney);

            if (!$calcResult['success']) {
                $app->halt(400, $calcResult['error']);
            }

            // Create purchase
            $result = $this->achatService->createPurchaseWithDonation(
                $ville_id,
                $besoin_id,
                $montant,
                $don_id
            );

            if (!$result['success']) {
                $app->halt(500, $result['error']);
            }

            // Store success message in session or redirect with success
            $app->redirect('/achat/' . $result['id']);
        } catch (\Throwable $e) {
            error_log('AchatController store error: ' . $e->getMessage());
            $app->halt(500, 'Erreur lors de la création de l\'achat');
        }
    }

    /**
     * Delete a purchase
     */
    public function delete(Engine $app, $id)
    {
        try {
            $achat = $this->achatService->getById($id);
            if (empty($achat)) {
                $app->halt(404, 'Achat introuvable');
            }

            $this->achatService->delete($id);
            $app->redirect('/achat');
        } catch (\Throwable $e) {
            error_log('AchatController delete error: ' . $e->getMessage());
            $app->halt(500, 'Erreur lors de la suppression');
        }
    }

    /**
     * Display list of non-money donations (Nature and Materials)
     */
    public function nonMoneyDonations(Engine $app)
    {
        try {
            $donModel = new Don();
            $dons = $donModel->getNonMoneyDonations();
            
            $app->render('Achat', [
                'message' => 'Dons (Nature & Matériaux)',
                'dons' => $dons,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController nonMoneyDonations error: ' . $e->getMessage());
            $app->halt(500, 'Erreur lors du chargement des dons');
        }
    }

    /**
     * API: Calculate purchase with fees
     */
    public function apiCalculate(Engine $app)
    {
        try {
            $req = $app->request();
            $montant = (float)($req->data->montant ?? 0);
            $available = (float)($req->data->available ?? 0);

            $result = $this->achatService->calculateWithFees($montant, $available);

            $app->json($result);
        } catch (\Throwable $e) {
            error_log('AchatController apiCalculate error: ' . $e->getMessage());
            $app->halt(500, ['error' => 'Erreur lors du calcul']);
        }
    }

    /**
     * API: Get purchase statistics
     */
    public function apiStats(Engine $app)
    {
        try {
            $stats = $this->achatService->getStatistics();
            $besoinStats = [
                'total' => $this->besoinService->getTotalBesoins(),
                'satisfaits' => $this->besoinService->getTotalSatisfaits(),
                'restants' => $this->besoinService->getTotalRestants(),
            ];

            $app->json([
                'success' => true,
                'achats' => $stats,
                'besoins' => $besoinStats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiStats error: ' . $e->getMessage());
            $app->halt(500, ['error' => 'Erreur lors de la récupération des statistiques']);
        }
    }

    /**
     * API: Get needs statistics (TASK 6, 7, 8)
     */
    public function apiNeedsStats(Engine $app)
    {
        try {
            $total = $this->besoinService->getTotalBesoins();
            $satisfaits = $this->besoinService->getTotalSatisfaits();
            $restants = $this->besoinService->getTotalRestants();

            $app->json([
                'success' => true,
                'total' => $total,
                'satisfaits' => $satisfaits,
                'restants' => $restants,
                'coverage_percent' => $total['total_amount'] > 0
                    ? round(($satisfaits['total_amount'] / $total['total_amount']) * 100)
                    : 0,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiNeedsStats error: ' . $e->getMessage());
            $app->halt(500, ['error' => 'Erreur lors de la récupération des statistiques']);
        }
    }

    /**
     * API: Get purchases by city
     */
    public function apiGetByCity(Engine $app, $ville_id)
    {
        try {
            $achats = $this->achatService->getByCity($ville_id);

            $app->json([
                'success' => true,
                'achats' => $achats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiGetByCity error: ' . $e->getMessage());
            $app->halt(500, ['error' => 'Erreur lors de la récupération des achats']);
        }
    }

    /**
     * Display list of all purchases
     */
    public function listAchats(Engine $app)
    {
        try {
            $achats = $this->achatService->getAllWithDetails();
            $stats = $this->achatService->getStatistics();

            $app->render('Achat', [
                'achats' => $achats,
                'stats' => $stats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController listAchats error: ' . $e->getMessage());
            $app->halt(500, 'Erreur lors du chargement des achats');
        }
    }

    /**
     * Show purchase fee configuration form
     */
    public function showFeeConfig(Engine $app, $id)
    {
        try {
            $achat = $this->achatService->getById($id);
            if (empty($achat)) {
                $app->halt(404, 'Achat introuvable');
            }

            // Get full details including ville_nom, categorie_nom
            $achatModel = new Achat();
            $achatDetails = $achatModel->getByIdWithDetails($id);

            $app->render('ConfigFraisPourcentage', [
                'achat' => $achatDetails,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController showFeeConfig error: ' . $e->getMessage());
            $app->halt(500, 'Erreur lors du chargement de la configuration');
        }
    }

    /**
     * Handle fee percentage update (POST)
     */
    public function updateFeePercent(Engine $app, $id)
    {
        $req = $app->request();

        try {
            $frais_percent = filter_var(
                $req->data->frais_percent,
                FILTER_VALIDATE_FLOAT
            );

            if ($frais_percent === false) {
                $app->halt(400, 'Pourcentage de frais invalide');
            }

            $result = $this->achatService->updateFeePercent($id, $frais_percent);

            if (!$result['success']) {
                // Re-render with error
                $achatModel = new Achat();
                $achatDetails = $achatModel->getByIdWithDetails($id);
                
                $app->render('ConfigFraisPourcentage', [
                    'achat' => $achatDetails,
                    'error_message' => $result['error'],
                ]);
                return;
            }

            // Redirect with success message
            $app->redirect('/achat/non-argent?success=1');
        } catch (\Throwable $e) {
            error_log('AchatController updateFeePercent error: ' . $e->getMessage());
            $app->halt(500, 'Erreur lors de la mise à jour');
        }
    }

    /**
     * API: Get all purchases
     */
    public function apiGetAll(Engine $app)
    {
        try {
            $achats = $this->achatService->getAllWithDetails();

            $app->json([
                'success' => true,
                'achats' => $achats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiGetAll error: ' . $e->getMessage());
            $app->halt(500, ['error' => 'Erreur lors de la récupération des achats']);
        }
    }

    /**
     * API: Get purchase by ID
     */
    public function apiGetById(Engine $app, $id)
    {
        try {
            $achat = $this->achatService->getById($id);

            if (empty($achat)) {
                $app->json([
                    'success' => false,
                    'error' => 'Achat introuvable',
                ]);
                return;
            }

            $app->json([
                'success' => true,
                'achat' => $achat,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiGetById error: ' . $e->getMessage());
            $app->halt(500, ['error' => 'Erreur lors de la récupération de l\'achat']);
        }
    }

    /**
     * API: Update fee percentage
     */
    public function apiUpdateFeePercent(Engine $app, $id)
    {
        $req = $app->request();

        try {
            $frais_percent = filter_var(
                $req->data->frais_percent,
                FILTER_VALIDATE_FLOAT
            );

            if ($frais_percent === false) {
                $app->json([
                    'success' => false,
                    'error' => 'Pourcentage de frais invalide',
                ]);
                return;
            }

            $result = $this->achatService->updateFeePercent($id, $frais_percent);

            $app->json($result);
        } catch (\Throwable $e) {
            error_log('AchatController apiUpdateFeePercent error: ' . $e->getMessage());
            $app->halt(500, ['error' => 'Erreur lors de la mise à jour']);
        }
    }
}
