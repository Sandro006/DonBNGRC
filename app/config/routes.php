<?php

use app\controllers\ApiExampleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\models\Categorie;
use app\services\DonService;
use app\services\besoinservice;


/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) use ($app) {

	$router->get('/', function () use ($app) {
		// Dashboard: collect summary data from services and apply optional filters
		try {
			$donService = new app\services\DonService();
			$villeService = new app\services\VilleService();
			$besoinService = new app\services\BesoinService();

			$don_stats = $donService->getStatistics();
			$besoin_stats = $besoinService->getStatistics();
			$cities = $villeService->getAll();

			// Regional statistics for overview table
			$regional_stats = $besoinService->getStatisticsByRegion();
			$active_regions_count = $besoinService->getActiveRegionsCount();
			$category_stats = $besoinService->getStatisticsByCategory();

			// Calculate global coverage percentage (donations vs needs)
			$total_dons_qty = (int)($don_stats['total_quantity'] ?? 0);
			$total_besoins_qty = (int)($besoin_stats['total_quantity'] ?? 0);
			$coverage_percent = $total_besoins_qty > 0 
				? min(100, round(($total_dons_qty / $total_besoins_qty) * 100)) 
				: 0;

			// categories via model
			$categorieModel = new Categorie();
			$categories = $categorieModel->getAllWithUsageCount();

			// get all data then filter in PHP based on query params
			$all_dons = $donService->getAllWithDetails();
			$all_besoins = $besoinService->getAllWithDetails();

			$req = $app->request();
			$qs = $req->query;
			$start = !empty($qs['start_date']) ? $qs['start_date'] : null;
			$end = !empty($qs['end_date']) ? $qs['end_date'] : null;
			$ville_id = !empty($qs['ville_id']) ? $qs['ville_id'] : null;
			$categorie_id = !empty($qs['categorie_id']) ? $qs['categorie_id'] : null;

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

			// slice recent for display
			$recent_dons = array_slice(array_values($filterDons), 0, 20);
			$recent_besoins = array_slice(array_values($filterBesoins), 0, 20);

			$app->render('Dashboard', [
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
			$app->render('Dashboard', [
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
	});

	$router->get('/hello-world/@name', function ($name) {
		echo '<h1>Hello world! Oh hey ' . $name . '!</h1>';
	});

	$router->group('/api', function () use ($router) {
		$router->get('/users', [ApiExampleController::class, 'getUsers']);
		$router->get('/users/@id:[0-9]', [ApiExampleController::class, 'getUser']);
		$router->post('/users/@id:[0-9]', [ApiExampleController::class, 'updateUser']);
	});
}, [SecurityHeadersMiddleware::class]);
