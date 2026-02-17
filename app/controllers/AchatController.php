<?php

namespace app\controllers;

use flight\Engine;
use app\services\AchatService;
use app\services\BesoinService;
use app\models\Achat;
use app\models\Besoin;
use app\models\Ville;
use app\models\Categorie;

class AchatController
{
    private $app;
    protected $achatService;
    protected $besoinService;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->achatService = new AchatService();
        $this->besoinService = new BesoinService();
    }

    /**
     * Display list of all purchases
     */
    public function index()
    {
        try {
            $achats = $this->achatService->getAll();
            $stats = $this->achatService->getStatistics();
            $villeModel = new Ville();
            $categorieModel = new Categorie();
            
            $villes = $villeModel->getAll();
            $categories = $categorieModel->getAll();

            $this->app->render('layout/Achat', [
                'achats' => $achats,
                'stats' => $stats,
                'villes' => $villes,
                'categories' => $categories,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController index error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur lors du chargement des achats');
        }
    }

    /**
     * Display purchase details
     */
    public function show($id)
    {
        try {
            $achat = $this->achatService->getById($id);
            if (empty($achat)) {
                $this->app->halt(404, 'Achat introuvable');
            }

            $this->app->render('AchatDetails', [
                'achat' => $achat,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController show error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur lors du chargement de l\'achat');
        }
    }

    /**
     * Show purchase creation form
     */
    public function create()
    {
        try {
            $villeModel = new Ville();
            $besoinModel = new Besoin();
            $donGlobalModel = new \app\models\DonGlobal();

            $villes = $villeModel->getAll();
            $besoins = $besoinModel->getAllWithDetails();
            $dons = $donGlobalModel->getAllWithDetails();

            $this->app->render('CreateAchat', [
                'villes' => $villes,
                'besoins' => $besoins,
                'dons' => $dons,
                'feePercent' => $this->achatService->getFeePercent(),
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController create GET error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur interne');
        }
    }

    /**
     * Handle purchase creation (POST)
     */
    public function store()
    {
        $req = $this->app->request();

        try {
            $ville_id = (int)($req->data->ville_id ?? 0);
            $besoin_id = (int)($req->data->besoin_id ?? 0);
            $montant = (float)($req->data->montant ?? 0);
            $don_id = !empty($req->data->don_id) ? (int)$req->data->don_id : null;

            // Validation
            if (empty($ville_id) || empty($besoin_id) || $montant <= 0) {
                $this->app->halt(400, 'Données invalides');
            }

            // Get donation details if provided
            $availableMoney = 0;
            if ($don_id) {
                $don = (new \app\models\DonGlobal())->getById($don_id);
                if (!empty($don) && $don['categorie_id'] == 3) { // Assuming 3 is money category
                    $availableMoney = (float)$don['quantite'];
                }
            }

            // Calculate with fees and validate
            $calcResult = $this->achatService->calculateWithFees($montant, $availableMoney);

            if (!$calcResult['success']) {
                $this->app->halt(400, $calcResult['error']);
            }

            // Create purchase
            $result = $this->achatService->createPurchaseWithDonation(
                $ville_id,
                $besoin_id,
                $montant,
                $don_id
            );

            if (!$result['success']) {
                $this->app->halt(500, $result['error']);
            }

            // Store success message in session or redirect with success
            $this->app->redirect('/achat/' . $result['id']);
        } catch (\Throwable $e) {
            error_log('AchatController store error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur lors de la création de l\'achat');
        }
    }

    /**
     * Delete a purchase
     */
    public function delete($id)
    {
        try {
            $achat = $this->achatService->getById($id);
            if (empty($achat)) {
                $this->app->halt(404, 'Achat introuvable');
            }

            $this->achatService->delete($id);
            $this->app->redirect('/achat');
        } catch (\Throwable $e) {
            error_log('AchatController delete error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur lors de la suppression');
        }
    }

    /**
     * API: Calculate purchase with fees
     */
    public function apiCalculate()
    {
        try {
            $req = $this->app->request();
            $montant = (float)($req->data->montant ?? 0);
            $available = (float)($req->data->available ?? 0);

            $result = $this->achatService->calculateWithFees($montant, $available);

            $this->app->json($result);
        } catch (\Throwable $e) {
            error_log('AchatController apiCalculate error: ' . $e->getMessage());
            $this->app->halt(500, ['error' => 'Erreur lors du calcul']);
        }
    }

    /**
     * API: Get purchase statistics
     */
    public function apiStats()
    {
        try {
            $stats = $this->achatService->getStatistics();
            $besoinStats = [
                'total' => $this->besoinService->getTotalBesoins(),
                'satisfaits' => $this->besoinService->getTotalSatisfaits(),
                'restants' => $this->besoinService->getTotalRestants(),
            ];

            $this->app->json([
                'success' => true,
                'achats' => $stats,
                'besoins' => $besoinStats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiStats error: ' . $e->getMessage());
            $this->app->halt(500, ['error' => 'Erreur lors de la récupération des statistiques']);
        }
    }

    /**
     * API: Get needs statistics (TASK 6, 7, 8)
     */
    public function apiNeedsStats()
    {
        try {
            $total = $this->besoinService->getTotalBesoins();
            $satisfaits = $this->besoinService->getTotalSatisfaits();
            $restants = $this->besoinService->getTotalRestants();

            $this->app->json([
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
            $this->app->halt(500, ['error' => 'Erreur lors de la récupération des statistiques']);
        }
    }

    /**
     * API: Get purchases by city
     */
    public function apiGetByCity($ville_id)
    {
        try {
            $achats = $this->achatService->getByCity($ville_id);

            $this->app->json([
                'success' => true,
                'achats' => $achats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiGetByCity error: ' . $e->getMessage());
            $this->app->halt(500, ['error' => 'Erreur lors de la récupération des achats']);
        }
    }

    /**
     * Display config frais pourcentage page for a purchase
     */
    public function configFraisPourcentage($id)
    {
        try {
            $achat = $this->achatService->getById($id);
            if (empty($achat)) {
                $this->app->halt(404, 'Achat introuvable');
            }

            $this->app->render('layout/ConfigFraisPourcentage', [
                'achat' => $achat,
                'feePercent' => $this->achatService->getFeePercent(),
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController configFraisPourcentage error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur lors du chargement de la configuration');
        }
    }

    /**
     * Update frais pourcentage for a purchase
     */
    public function updateFraisPourcentage($id)
    {
        $req = $this->app->request();

        try {
            $frais_percent = (float)($req->data->frais_percent ?? 0);

            // Validation
            if ($frais_percent < 0 || $frais_percent > 100) {
                $this->app->halt(400, 'Le pourcentage de frais doit être entre 0 et 100');
            }

            // Get current achat
            $achat = $this->achatService->getById($id);
            if (empty($achat)) {
                $this->app->halt(404, 'Achat introuvable');
            }

            // Calculate new total with fees
            $montant_total = $achat['montant'] + ($achat['montant'] * $frais_percent / 100);

            // Update achat
            $updateData = [
                'frais_percent' => $frais_percent,
                'montant_total' => $montant_total,
            ];

            $achatModel = new Achat();
            $achatModel->update($id, $updateData);

            // Redirect to list with success
            $this->app->redirect('/achat');
        } catch (\Throwable $e) {
            error_log('AchatController updateFraisPourcentage error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur lors de la mise à jour');
        }
    }
}
