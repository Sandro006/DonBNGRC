<?php

use app\controllers\ApiExampleController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) use ($app) {

	$router->get('/', function () use ($app) {
		// Dashboard: collect summary data from services
		try {
			$donService = new \app\services\DonService();
			$villeService = new \app\services\VilleService();
			$besoinService = new \app\services\BesoinService();

			$don_stats = $donService->getStatistics();
			$besoin_stats = $besoinService->getStatistics();
			$cities = $villeService->getAll();
			$recent_dons = $donService->getRecent(10);
			$recent_besoins = $besoinService->getAllWithDetails();

			$app->render('welcome', [
				'message' => 'Tableau de bord',
				'don_stats' => $don_stats,
				'besoin_stats' => $besoin_stats,
				'cities' => $cities,
				'recent_dons' => $recent_dons,
				'recent_besoins' => $recent_besoins,
			]);
		} catch (\Throwable $e) {
			// Fallback to a simple message if services/db are not available
			$app->render('welcome', ['message' => 'You are gonna do great things! (dashboard unavailable)']);
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
