<?php

namespace app\controllers;

use flight\Engine;
use app\services\SimulationService;
use app\models\Don;

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
     * Show the simulation page for a donation
     */
    public function show($don_id)
    {
        try {
            $donModel = new Don();
            $don = $donModel->getByIdWithDetails($don_id);

            if (empty($don)) {
                $this->app->halt(404, 'Don introuvable');
            }

            // Get the simulation preview
            $simulation = $this->simulationService->simulateDispatch($don_id);

            $this->app->render('Simulation', [
                'don' => $don,
                'simulation' => $simulation,
                'don_id' => $don_id,
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
            $req = $this->app->request();
            $don_id = $req->data->don_id ?? null;

            if (empty($don_id)) {
                $this->app->json(['success' => false, 'error' => 'don_id requis'], 400);
                return;
            }

            $simulation = $this->simulationService->simulateDispatch($don_id);

            $this->app->json($simulation, $simulation['success'] ? 200 : 400);
        } catch (\Throwable $e) {
            error_log('SimulationController apiSimulate error: ' . $e->getMessage());
            $this->app->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API endpoint: Validate and perform dispatch
     */
    public function apiValidate()
    {
        try {
            $req = $this->app->request();
            $don_id = $req->data->don_id ?? null;

            if (empty($don_id)) {
                $this->app->json(['success' => false, 'error' => 'don_id requis'], 400);
                return;
            }

            $result = $this->simulationService->validateDispatch($don_id);

            $this->app->json($result, $result['success'] ? 200 : 400);
        } catch (\Throwable $e) {
            error_log('SimulationController apiValidate error: ' . $e->getMessage());
            $this->app->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
