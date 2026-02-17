<?php

use app\controllers\ApiExampleController;
use app\controllers\SimulationController;
use app\controllers\AchatController;
use app\controllers\HomeController;
use app\controllers\DashboardController;
use app\controllers\CityController;
use app\controllers\DonGlobalController;
use app\controllers\BesoinController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;


/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) {

	$router->get('/', [HomeController::class, 'index']);

	$router->get('/dashboard', [DashboardController::class, 'index']);

	$router->get('/hello-world/@name', [HomeController::class, 'helloWorld']);

	// City details: donations & needs for a specific city
	$router->get('/ville/@id:[0-9]+', [CityController::class, 'show']);

	// Dons Globaux routes
	$router->get('/don-global', [DonGlobalController::class, 'index']);
	$router->get('/don-global/create', [DonGlobalController::class, 'create']);
	$router->get('/don-global/nouveau', [DonGlobalController::class, 'create']); // French alias
	$router->post('/don-global/store', [DonGlobalController::class, 'store']);
	$router->post('/don-global/ajouter', [DonGlobalController::class, 'store']); // French alias
	$router->get('/don-global/@id:[0-9]+', [DonGlobalController::class, 'show']);
	
	// Alias shorter routes /don instead of /don-global
	$router->get('/don', [DonGlobalController::class, 'index']);
	$router->get('/don/create', [DonGlobalController::class, 'create']);
	$router->get('/don/nouveau', [DonGlobalController::class, 'create']); // French alias
	$router->get('/don/ajouter', [DonGlobalController::class, 'create']); // GET alias for form
	$router->post('/don/store', [DonGlobalController::class, 'store']);
	$router->post('/don/ajouter', [DonGlobalController::class, 'store']); // French alias
	$router->get('/don/@id:[0-9]+', [DonGlobalController::class, 'show']);
	
	// Méthodes et Simulation de Distribution
	$router->get('/don-global/methodes', [DonGlobalController::class, 'methodes']);
	$router->get('/don-global/simulation', [DonGlobalController::class, 'simulation']);
	$router->post('/don-global/simulation', [DonGlobalController::class, 'simulation']);
	$router->post('/don-global/execute-distribution', [DonGlobalController::class, 'executeDistribution']);
	$router->post('/don-global/reset-distribution', [DonGlobalController::class, 'resetDistribution']);
	$router->post('/don-global/distribution-manuelle', [DonGlobalController::class, 'manualDistribution']);

	// Besoins routes - Gestion des besoins identifiés
	$router->get('/besoin', [BesoinController::class, 'index']);
	$router->get('/besoin/create', [BesoinController::class, 'create']);
	$router->get('/besoin/nouveau', [BesoinController::class, 'create']); // French alias
	$router->post('/besoin/store', [BesoinController::class, 'store']);
	$router->post('/besoin/ajouter', [BesoinController::class, 'store']); // French alias
	$router->get('/besoin/@id:[0-9]+', [BesoinController::class, 'show']);

	// Simulation: show simulation page for global donation distribution
	$router->get('/simulation', [SimulationController::class, 'index']);
	$router->get('/simulation/@id:[0-9]+', [SimulationController::class, 'show']);
	$router->get('/simulation/smart/@id:[0-9]+', [SimulationController::class, 'smartSuggestions']);
	$router->get('/simulation/distribute/@id:[0-9]+', [SimulationController::class, 'smartSuggestions']); // Alias for smart distribution

	// Achat: purchase management
	$router->get('/achat', [AchatController::class, 'index']);
	$router->get('/achat/add', [AchatController::class, 'create']);
	$router->get('/achat/ajouter', [AchatController::class, 'create']);
	$router->get('/achat/@id:[0-9]+/config-frais-pourcentage', [AchatController::class, 'configFraisPourcentage']);
	$router->post('/achat/@id:[0-9]+/frais-update', [AchatController::class, 'updateFraisPourcentage']);
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

		// Simulation API endpoints for global distribution
		$router->post('/simulation/simulate', [SimulationController::class, 'apiSimulate']);
		$router->post('/simulation/suggestions', [SimulationController::class, 'apiSuggestions']);
		$router->post('/simulation/validate', [SimulationController::class, 'apiValidate']);
		$router->post('/simulation/smart-distribute', [SimulationController::class, 'apiSmartDistribute']);

		// Achat API endpoints
		$router->post('/achat/calculate', [AchatController::class, 'apiCalculate']);
		$router->get('/achat/stats', [AchatController::class, 'apiStats']);
		$router->get('/achat/needs-stats', [AchatController::class, 'apiNeedsStats']);
		$router->get('/achat/city/@id:[0-9]+', [AchatController::class, 'apiGetByCity']);

		// Don Frais API endpoints (requires DonFraisController implementation)
		// $router->get('/don/@id:[0-9]+/frais', [app\controllers\DonFraisController::class, 'getDonFrais']);
		// $router->post('/don/@id:[0-9]+/frais', [app\controllers\DonFraisController::class, 'updateDonFrais']);

		// API endpoints pour les dons globaux
		$router->get('/don-global/categorie/@id:[0-9]+', [DonGlobalController::class, 'getAvailableByCategory']);
		$router->get('/don-global/suggestions/@categorie_id:[0-9]+', [DonGlobalController::class, 'getSuggestionsDistribution']);
		$router->get('/don-global/statistics', [DonGlobalController::class, 'getStatistics']);
		$router->put('/don-global/@id:[0-9]+/status', [DonGlobalController::class, 'updateStatus']);
		$router->post('/don-global/@id:[0-9]+/status', [DonGlobalController::class, 'updateStatus']); // POST alias for forms
	});
}, [SecurityHeadersMiddleware::class]);
