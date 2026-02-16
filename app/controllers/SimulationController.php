<?php

namespace app\controllers;

use flight\Engine;
use app\services\SimulationService;
use app\models\Don;
use Flight;

class SimulationController
{
    protected Engine $app;
    protected SimulationService $simulationService;

    public function __construct($app)
    {
        $this->app = $app;
        $this->simulationService = new SimulationService();
    }

    /**
     * List all donations available for simulation
     */
    public function index()
    {
        try {
            $donModel = new Don();
            $dons = $donModel->getAllWithDetails();

            $this->app->render('Simulation', [
                'dons' => $dons,
                'is_list' => true,
            ]);
        } catch (\Throwable $e) {
            error_log('SimulationController index error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur interne');
        }
    }

    /**
     * Show the simulation page for a donation
     */
    public function show($id)
    {
        try {
            $donModel = new Don();
            $don = $donModel->getByIdWithDetails($id);

            if (empty($don)) {
                $this->app->halt(404, 'Don introuvable');
            }

            // Get the simulation preview
            $simulation = $this->simulationService->simulateDispatch($id);

            $this->app->render('Simulation', [
                'don' => $don,
                'simulation' => $simulation,
                'don_id' => $id,
            ]);
        } catch (\Throwable $e) {
            error_log('SimulationController show error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur interne');
        }
    }

    /**
     * API endpoint: Simulate dispatch (preview only)
     */
    public function apiSimulate()
    {
        try {
            // Read JSON body
            $rawBody = file_get_contents('php://input');
            $data = json_decode($rawBody, true);
            $don_id = $data['don_id'] ?? null;

            if (empty($don_id)) {
                Flight::json(['success' => false, 'error' => 'don_id requis'], 400);
                return;
            }

            $simulation = $this->simulationService->simulateDispatch($don_id);

            Flight::json($simulation, $simulation['success'] ? 200 : 400);
        } catch (\Throwable $e) {
            error_log('SimulationController apiSimulate error: ' . $e->getMessage());
            Flight::json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API endpoint: Validate and perform dispatch
     */
    public function apiValidate()
    {
        try {
            // Read JSON body
            $rawBody = file_get_contents('php://input');
            $data = json_decode($rawBody, true);
            $don_id = $data['don_id'] ?? null;

            if (empty($don_id)) {
                Flight::json(['success' => false, 'error' => 'don_id requis'], 400);
                return;
            }

            $result = $this->simulationService->validateDispatch($don_id);

            Flight::json($result, $result['success'] ? 200 : 400);
        } catch (\Throwable $e) {
            error_log('SimulationController apiValidate error: ' . $e->getMessage());
            Flight::json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
