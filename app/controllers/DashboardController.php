<?php

namespace app\controllers;

use app\services\BesoinService;
use app\services\VilleService;
use app\services\SmartDistributionService;
use app\models\Categorie;
use app\models\DonGlobal;
use Flight;

class DashboardController
{
    protected $donGlobalModel;
    protected $besoinService;
    protected $villeService;
    protected $smartDistributionService;

    public function __construct()
    {
        $this->donGlobalModel = new DonGlobal();
        $this->besoinService = new BesoinService();
        $this->villeService = new VilleService();
        $this->smartDistributionService = new SmartDistributionService();
    }

    /**
     * Display dashboard with statistics and filters
     */
    public function index()
    {
        try {
            // Use the smart distribution service for comprehensive stats
            $dashboardStats = $this->smartDistributionService->getDashboardStats();
            
            // Get cities and categories for filters
            $cities = $this->villeService->getAll();
            $categorieModel = new Categorie();
            $categories = $categorieModel->getAllWithUsageCount();

            // Get filter parameters
            $req = Flight::request();
            $qs = $req->query;
            $start = !empty($qs['start_date']) ? $qs['start_date'] : null;
            $end = !empty($qs['end_date']) ? $qs['end_date'] : null;
            $ville_id = !empty($qs['ville_id']) ? $qs['ville_id'] : null;
            $categorie_id = !empty($qs['categorie_id']) ? $qs['categorie_id'] : null;

            // Get filtered data
            $all_dons = $this->donGlobalModel->getAllWithDetails();
            $all_besoins = $this->besoinService->getAllWithDetails();

            // Filter global donations (no ville_id for global donations)
            $filterDons = array_filter($all_dons, function ($d) use ($start, $end, $categorie_id) {
                if ($start && $end) {
                    $ts = !empty($d['date_don']) ? strtotime($d['date_don']) : null;
                    if (!$ts) return false;
                    $s = strtotime($start);
                    $e = strtotime($end) + 24 * 3600 - 1;
                    if ($ts < $s || $ts > $e) return false;
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

            // Get regional statistics for overview table
            $regional_stats = $this->besoinService->getStatisticsByRegion();
            $active_regions_count = $this->besoinService->getActiveRegionsCount();

            // Get recent records for display
            $recent_dons = array_slice(array_values($filterDons), 0, 20);
            $recent_besoins = array_slice(array_values($filterBesoins), 0, 20);

            // Get priority recommendations
            $priority_recommendations = $this->smartDistributionService->getPriorityRecommendations(10);

            Flight::render('Dashboard', [
                'message' => 'Tableau de bord intelligent',
                'don_stats' => $dashboardStats['don_stats'],
                'besoin_stats' => $dashboardStats['besoin_stats'],
                'cities' => $cities,
                'categories' => $categories,
                'dons' => $recent_dons,
                'besoins' => $recent_besoins,
                'regional_stats' => $regional_stats,
                'active_regions_count' => $active_regions_count,
                'coverage_percent' => $dashboardStats['coverage_percent'],
                'urgent_needs_count' => $dashboardStats['urgent_needs_count'],
                'urgent_needs' => $dashboardStats['urgent_needs'],
                'expiring_donations' => $dashboardStats['expiring_donations'],
                'donations_by_donor_type' => $dashboardStats['donations_by_donor_type'],
                'priority_recommendations' => $priority_recommendations,
                'category_stats' => $this->besoinService->getStatisticsByCategory(),
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
                'urgent_needs_count' => 0,
                'urgent_needs' => [],
                'expiring_donations' => [],
                'donations_by_donor_type' => [],
                'priority_recommendations' => [],
                'category_stats' => [],
            ]);
        }
    }
}
