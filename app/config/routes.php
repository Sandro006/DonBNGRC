<?php

use app\controllers\ApiExampleController;
use app\controllers\SimulationController;
use app\controllers\AchatController;
use app\controllers\HomeController;
use app\controllers\DashboardController;
use app\controllers\CityController;
use app\controllers\DonController;
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

	// Add donation: show form
	$router->get('/don/add', [DonController::class, 'create']);

	// Add donation: show form (French alias)
	$router->get('/don/ajouter', [DonController::class, 'create']);

	// Add donation: handle form submission
	$router->post('/don/add', [DonController::class, 'store']);

	// Add donation: handle form submission (French alias)
	$router->post('/don/ajouter', [DonController::class, 'store']);

	// Delete donation
	$router->delete('/don/@id:[0-9]+', [DonController::class, 'delete']);

	// Delete donation (POST version for form compatibility)
	$router->post('/don/supprimer/@id:[0-9]+', [DonController::class, 'delete']);

	// Simulation: show simulation page for donation dispatch
	$router->get('/simulation', [SimulationController::class, 'index']);
	$router->get('/simulation/@id:[0-9]+', [SimulationController::class, 'show']);

	// Achat: purchase management
	$router->get('/achat', [AchatController::class, 'index']);
	$router->get('/achat/add', [AchatController::class, 'create']);
	$router->get('/achat/ajouter', [AchatController::class, 'create']);
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

		// Don Frais API endpoints (requires DonFraisController implementation)
		// $router->get('/don/@id:[0-9]+/frais', [app\controllers\DonFraisController::class, 'getDonFrais']);
		// $router->post('/don/@id:[0-9]+/frais', [app\controllers\DonFraisController::class, 'updateDonFrais']);
	});
}, [SecurityHeadersMiddleware::class]);
