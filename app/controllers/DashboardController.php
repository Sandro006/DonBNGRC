<?php

namespace app\controllers;

use app\services\DonService;
use app\services\BesoinService;
use app\services\VilleService;
use app\models\Categorie;
use Flight;

class DashboardController
{
    protected $donService;
    protected $besoinService;
    protected $villeService;

    public function __construct()
    {
        $this->donService = new DonService();
        $this->besoinService = new BesoinService();
        $this->villeService = new VilleService();
    }

    /**
     * Display dashboard with statistics and filters
     */
    public function index()
    {
        try {
            // Get all statistics
            $don_stats = $this->donService->getStatistics();
            $besoin_stats = $this->besoinService->getStatistics();
            $cities = $this->villeService->getAll();

            // Regional statistics for overview table
            $regional_stats = $this->besoinService->getStatisticsByRegion();
            $active_regions_count = $this->besoinService->getActiveRegionsCount();
            $category_stats = $this->besoinService->getStatisticsByCategory();

            // Calculate global coverage percentage (donations vs needs)
            $total_dons_qty = (int)($don_stats['total_quantity'] ?? 0);
            $total_besoins_qty = (int)($besoin_stats['total_quantity'] ?? 0);
            $coverage_percent = $total_besoins_qty > 0
                ? min(100, round(($total_dons_qty / $total_besoins_qty) * 100))
                : 0;

            // Get categories via model
            $categorieModel = new Categorie();
            $categories = $categorieModel->getAllWithUsageCount();

            // Get all data then filter in PHP based on query params
            $all_dons = $this->donService->getAllWithDetails();
            $all_besoins = $this->besoinService->getAllWithDetails();

            // Get filter parameters
            $req = Flight::request();
            $qs = $req->query;
            $start = !empty($qs['start_date']) ? $qs['start_date'] : null;
            $end = !empty($qs['end_date']) ? $qs['end_date'] : null;
            $ville_id = !empty($qs['ville_id']) ? $qs['ville_id'] : null;
            $categorie_id = !empty($qs['categorie_id']) ? $qs['categorie_id'] : null;

            // Filter donations
            $filterDons = array_filter($all_dons, function ($d) use ($start, $end, $ville_id, $categorie_id) {
                if ($start && $end) {
                    $ts = !empty($d['date_don']) ? strtotime($d['date_don']) : null;
                    if (!$ts) return false;
                    $s = strtotime($start);
                    $e = strtotime($end) + 24 * 3600 - 1;
                    if ($ts < $s || $ts > $e) return false;
                }
                if ($ville_id) {
                    if (empty($d['ville_id']) || (string)$d['ville_id'] !== (string)$ville_id) return false;
                }
                if ($categorie_id) {
                    if (empty($d['categorie_id']) || (string)$d['categorie_id'] !== (string)$categorie_id) return false;
                }
                return true;
            });

            // Filter needs
            $filterBesoins = array_filter($all_besoins, function ($b) use ($start, $end, $ville_id, $categorie_id) {
                if ($start && $end) {
                    $ts = !empty($b['created_at']) ? strtotime($b['created_at']) : (!empty($b['date']) ? strtotime($b['date']) : null);
                    if ($ts) {
                        $s = strtotime($start);
                        $e = strtotime($end) + 24 * 3600 - 1;
                        if ($ts < $s || $ts > $e) return false;
                    }
                }
                if ($ville_id) {
                    if (empty($b['ville_id']) || (string)$b['ville_id'] !== (string)$ville_id) return false;
                }
                if ($categorie_id) {
                    if (empty($b['categorie_id']) || (string)$b['categorie_id'] !== (string)$categorie_id) return false;
                }
                return true;
            });

            // Get recent records for display
            $recent_dons = array_slice(array_values($filterDons), 0, 20);
            $recent_besoins = array_slice(array_values($filterBesoins), 0, 20);

            Flight::render('Dashboard', [
                'message' => 'Tableau de bord',
                'don_stats' => $don_stats,
                'besoin_stats' => $besoin_stats,
                'cities' => $cities,
                'categories' => $categories,
                'dons' => $recent_dons,
                'besoins' => $recent_besoins,
                'regional_stats' => $regional_stats,
                'active_regions_count' => $active_regions_count,
                'coverage_percent' => $coverage_percent,
                'category_stats' => $category_stats,
            ]);
        } catch (\Throwable $e) {
            error_log('Dashboard error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            Flight::render('Dashboard', [
                'message' => 'Dashboard indisponible: ' . $e->getMessage(),
                'don_stats' => [],
                'besoin_stats' => [],
                'cities' => [],
                'categories' => [],
                'dons' => [],
                'besoins' => [],
                'regional_stats' => [],
                'active_regions_count' => 0,
                'coverage_percent' => 0,
                'category_stats' => [],
            ]);
        }
    }
}
