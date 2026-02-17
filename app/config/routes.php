<?php

use app\controllers\ApiExampleController;
use app\controllers\SimulationController;
use app\controllers\AchatController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\models\Categorie;
use app\services\DonService;
use app\services\BesoinService;
use app\services\VilleService;
use app\models\Donateur;
use app\models\Don;


/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) use ($app) {

	$router->get('/', function () use ($app) {
		$app->render('welcome');
	});

	$router->get('/dashboard', function () use ($app) {
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

	// City details: donations & needs for a specific city
	$router->get('/ville/@id:[0-9]+', function ($id) use ($app) {
		try {
			$donService = new DonService();
			$besoinService = new BesoinService();
			$villeService = new VilleService();

			$ville = $villeService->getById($id);
			if (empty($ville)) {
				$app->halt(404, 'Ville introuvable');
			}

			$dons = $donService->getByCity($id);
			$besoins = $besoinService->getByCity($id);

			$app->render('CityDetails', [
				'ville' => $ville,
				'dons' => $dons,
				'besoins' => $besoins,
			]);
		} catch (\Throwable $e) {
			error_log('City details error: ' . $e->getMessage());
			$app->halt(500, 'Erreur interne');
		}
	});

	// Add donation: show form
	$router->get('/don/add', function () use ($app) {
		try {
			$categorieModel = new Categorie();
			$donateurModel = new Donateur();
			$categories = $categorieModel->getAll();
			$donateurs = $donateurModel->getAll();
			$req = $app->request();
			$ville_id = !empty($req->query->ville_id) ? $req->query->ville_id : null;

			$app->render('AddDon', [
				'categories' => $categories,
				'donateurs' => $donateurs,
				'ville_id' => $ville_id,
			]);
		} catch (\Throwable $e) {
			error_log('AddDon GET error: ' . $e->getMessage());
			$app->halt(500, 'Erreur interne');
		}
	});

	// Add donation: show form (French alias)
	$router->get('/don/ajouter', function () use ($app) {
		try {
			$categorieModel = new Categorie();
			$donateurModel = new Donateur();
			$categories = $categorieModel->getAll();
			$donateurs = $donateurModel->getAll();
			$req = $app->request();
			$ville_id = !empty($req->query->ville_id) ? $req->query->ville_id : null;

			$app->render('AddDon', [
				'categories' => $categories,
				'donateurs' => $donateurs,
				'ville_id' => $ville_id,
			]);
		} catch (\Throwable $e) {
			error_log('AddDon GET error: ' . $e->getMessage());
			$app->halt(500, 'Erreur interne');
		}
	});

	// Add donation: handle form submission
	$router->post('/don/add', function () use ($app) {
		$req = $app->request();
		$data = [];
		$data['ville_id'] = !empty($req->data->ville_id) ? $req->data->ville_id : null;
		$data['categorie_id'] = !empty($req->data->categorie_id) ? $req->data->categorie_id : null;
		$data['quantite'] = !empty($req->data->quantite) ? (int)$req->data->quantite : 0;
		// Normalize datetime-local (YYYY-MM-DDTHH:MM) to SQL DATETIME
		$dateInput = $req->data->date_don ?? null;
		if (!empty($dateInput)) {
			$dateInput = str_replace('T', ' ', $dateInput);
			if (strlen($dateInput) === 16) { // no seconds
				$dateInput .= ':00';
			}
			$data['date_don'] = $dateInput;
		} else {
			$data['date_don'] = date('Y-m-d H:i:s');
		}

		$donateur_id = $req->data->donateur_id ?? null;
		// Basic validation: require ville and categorie
		if (empty($data['ville_id']) || empty($data['categorie_id'])) {
			error_log('AddDon POST validation failed: ville_id or categorie_id missing');
			$app->halt(400, 'Ville et catégorie sont requises');
		}

		try {
			// If donor not selected, create new donor from provided fields
			if (empty($donateur_id)) {
				$donateurModel = new Donateur();
				$donateurData = [
					'nom' => $req->data->donateur_nom ?? 'Anonyme',
					'telephone' => $req->data->donateur_telephone ?? null,
					'email' => $req->data->donateur_email ?? null,
				];
				$donateur_id = $donateurModel->create($donateurData);
			}

			$data['donateur_id'] = $donateur_id;

			$donModel = new Don();
			$donModel->create($data);
		} catch (\Throwable $e) {
			error_log('AddDon POST error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
			$app->halt(500, 'Erreur interne lors de l\'enregistrement');
		}

		// Always redirect to city details when possible.
		$redirectVille = $data['ville_id'];
		// If ville_id not provided but the form had a libre field (optional), use it when numeric
		if (empty($redirectVille) && !empty($req->data->ville_libre) && is_numeric($req->data->ville_libre)) {
			$redirectVille = $req->data->ville_libre;
		}

		if (!empty($redirectVille)) {
			$app->redirect('/ville/' . $redirectVille);
		} else {
			$app->redirect('/');
		}
	});

	// Add donation: handle form submission (French alias)
	$router->post('/don/ajouter', function () use ($app) {
		$req = $app->request();
		$data = [];
		$data['ville_id'] = !empty($req->data->ville_id) ? $req->data->ville_id : null;
		$data['categorie_id'] = !empty($req->data->categorie_id) ? $req->data->categorie_id : null;
		$data['quantite'] = !empty($req->data->quantite) ? (int)$req->data->quantite : 0;
		$dateInput = $req->data->date_don ?? null;
		if (!empty($dateInput)) {
			$dateInput = str_replace('T', ' ', $dateInput);
			if (strlen($dateInput) === 16) {
				$dateInput .= ':00';
			}
			$data['date_don'] = $dateInput;
		} else {
			$data['date_don'] = date('Y-m-d H:i:s');
		}

		$donateur_id = $req->data->donateur_id ?? null;
		if (empty($data['ville_id']) || empty($data['categorie_id'])) {
			$app->halt(400, 'Ville et catégorie sont requises');
		}

		try {
			if (empty($donateur_id)) {
				$donateurModel = new Donateur();
				$donateurData = [
					'nom' => $req->data->donateur_nom ?? 'Anonyme',
					'telephone' => $req->data->donateur_telephone ?? null,
					'email' => $req->data->donateur_email ?? null,
				];
				$donateur_id = $donateurModel->create($donateurData);
			}

			$data['donateur_id'] = $donateur_id;

			$donModel = new Don();
			$donModel->create($data);
		} catch (\Throwable $e) {
			error_log('AddDon POST error: ' . $e->getMessage());
			$app->halt(500, 'Erreur interne');
		}

		$redirectVille = $data['ville_id'];
		if (empty($redirectVille) && !empty($req->data->ville_libre) && is_numeric($req->data->ville_libre)) {
			$redirectVille = $req->data->ville_libre;
		}

		if (!empty($redirectVille)) {
			$app->redirect('/ville/' . $redirectVille);
		} else {
			$app->redirect('/');
		}
	});

	// Delete donation
	$router->delete('/don/@id:[0-9]+', function ($id) use ($app) {
		try {
			// Get the donation to find the city
			$donModel = new Don();
			$don = $donModel->getById($id);

			if (empty($don)) {
				$app->halt(404, 'Don introuvable');
			}

			$ville_id = $don['ville_id'];
			$donService = new DonService();
			$donService->delete($id);

			// Redirect back to city details
			$app->redirect('/ville/' . $ville_id);
		} catch (\Throwable $e) {
			error_log('Delete donation error: ' . $e->getMessage());
			$app->halt(500, 'Erreur lors de la suppression du don');
		}
	});

	// Delete donation (POST version for form compatibility)
	$router->post('/don/supprimer/@id:[0-9]+', function ($id) use ($app) {
		try {
			// Get the donation to find the city
			$donModel = new Don();
			$don = $donModel->getById($id);

			if (empty($don)) {
				$app->halt(404, 'Don introuvable');
			}

			$ville_id = $don['ville_id'];
			$donService = new DonService();
			$donService->delete($id);

			// Redirect back to city details
			$app->redirect('/ville/' . $ville_id);
		} catch (\Throwable $e) {
			error_log('Delete donation error: ' . $e->getMessage());
			$app->halt(500, 'Erreur lors de la suppression du don');
		}
	});

	// Simulation: show simulation page for donation dispatch
	$router->get('/simulation', [SimulationController::class, 'index']);
	$router->get('/simulation/@id:[0-9]+', [SimulationController::class, 'show']);

	// Achat: purchase management
	$router->get('/achat', [AchatController::class, 'index']);
	$router->get('/achat/add', [AchatController::class, 'create']);
	$router->get('/achat/ajouter', [AchatController::class, 'create']);
	$router->get('/achat/non-argent', [AchatController::class, 'nonMoneyDonations']);
	$router->get('/achat/@id:[0-9]+', [AchatController::class, 'show']);
	$router->post('/achat', [AchatController::class, 'store']);
	$router->post('/achat/add', [AchatController::class, 'store']);
	$router->post('/achat/ajouter', [AchatController::class, 'store']);
	$router->delete('/achat/@id:[0-9]+', [AchatController::class, 'delete']);
	$router->post('/achat/supprimer/@id:[0-9]+', [AchatController::class, 'delete']);

	$router->group('/api', function () use ($router) {
		$router->get('/users', [ApiExampleController::class, 'getUsers']);
		$router->get('/users/@id:[0-9]', [ApiExampleController::class, 'getUser']);
		$router->post('/users/@id:[0-9]', [ApiExampleController::class, 'updateUser']);

		// Simulation API endpoints
		$router->post('/simulation/simulate', [SimulationController::class, 'apiSimulate']);
		$router->post('/simulation/validate', [SimulationController::class, 'apiValidate']);

		// Achat API endpoints
		$router->post('/achat/calculate', [AchatController::class, 'apiCalculate']);
		$router->get('/achat/stats', [AchatController::class, 'apiStats']);
		$router->get('/achat/needs-stats', [AchatController::class, 'apiNeedsStats']);
		$router->get('/achat/city/@id:[0-9]+', [AchatController::class, 'apiGetByCity']);
	});
}, [SecurityHeadersMiddleware::class]);
