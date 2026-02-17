<?php

namespace app\controllers;

use app\services\AchatService;
use app\services\BesoinService;
use app\models\Achat;
use app\models\Besoin;
use app\models\Ville;
use app\models\Categorie;
use Flight;

class AchatController
{
    protected $achatService;
    protected $besoinService;

    public function __construct()
    {
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

            Flight::render('layout/Achat', [
                'achats' => $achats,
                'stats' => $stats,
                'villes' => $villes,
                'categories' => $categories,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController index error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur lors du chargement des achats');
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
                Flight::halt(404, 'Achat introuvable');
            }

            Flight::render('AchatDetails', [
                'achat' => $achat,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController show error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur lors du chargement de l\'achat');
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

            Flight::render('CreateAchat', [
                'villes' => $villes,
                'besoins' => $besoins,
                'dons' => $dons,
                'feePercent' => $this->achatService->getFeePercent(),
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController create GET error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur interne');
        }
    }

    /**
     * Handle purchase creation (POST)
     */
    public function store()
    {
        $req = Flight::request();

        try {
            $ville_id = (int)($req->data->ville_id ?? 0);
            $besoin_id = (int)($req->data->besoin_id ?? 0);
            $montant = (float)($req->data->montant ?? 0);
            $don_id = !empty($req->data->don_id) ? (int)$req->data->don_id : null;

            // Validation
            if (empty($ville_id) || empty($besoin_id) || $montant <= 0) {
                Flight::halt(400, 'Données invalides');
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
                Flight::halt(400, $calcResult['error']);
            }

            // Create purchase
            $result = $this->achatService->createPurchaseWithDonation(
                $ville_id,
                $besoin_id,
                $montant,
                $don_id
            );

            if (!$result['success']) {
                Flight::halt(500, $result['error']);
            }

            // Store success message in session or redirect with success
            Flight::redirect('/achat/' . $result['id']);
        } catch (\Throwable $e) {
            error_log('AchatController store error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur lors de la création de l\'achat');
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
                Flight::halt(404, 'Achat introuvable');
            }

            $this->achatService->delete($id);
            Flight::redirect('/achat');
        } catch (\Throwable $e) {
            error_log('AchatController delete error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur lors de la suppression');
        }
    }

    /**
     * API: Calculate purchase with fees
     */
    public function apiCalculate()
    {
        try {
            $req = Flight::request();
            $montant = (float)($req->data->montant ?? 0);
            $available = (float)($req->data->available ?? 0);

            $result = $this->achatService->calculateWithFees($montant, $available);

            Flight::json($result);
        } catch (\Throwable $e) {
            error_log('AchatController apiCalculate error: ' . $e->getMessage());
            Flight::halt(500, ['error' => 'Erreur lors du calcul']);
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

            Flight::json([
                'success' => true,
                'achats' => $stats,
                'besoins' => $besoinStats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiStats error: ' . $e->getMessage());
            Flight::halt(500, ['error' => 'Erreur lors de la récupération des statistiques']);
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

            Flight::json([
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
            Flight::halt(500, ['error' => 'Erreur lors de la récupération des statistiques']);
        }
    }

    /**
     * API: Get purchases by city
     */
    public function apiGetByCity($ville_id)
    {
        try {
            $achats = $this->achatService->getByCity($ville_id);

            Flight::json([
                'success' => true,
                'achats' => $achats,
            ]);
        } catch (\Throwable $e) {
            error_log('AchatController apiGetByCity error: ' . $e->getMessage());
            Flight::halt(500, ['error' => 'Erreur lors de la récupération des achats']);
        }
    }
}
