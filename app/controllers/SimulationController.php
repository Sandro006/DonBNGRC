<?php

namespace app\controllers;

use app\services\SimulationService;
use app\services\SmartDistributionService;
use app\models\DonGlobal;
use Flight;

class SimulationController
{
    protected SimulationService $simulationService;
    protected SmartDistributionService $smartDistributionService;

    public function __construct()
    {
        $this->simulationService = new SimulationService();
        $this->smartDistributionService = new SmartDistributionService();
    }

    /**
     * List all global donations available for distribution
     */
    public function index()
    {
        try {
            $donGlobalModel = new DonGlobal();
            $dons = $donGlobalModel->getAllWithDetails();

            // Filter only available donations (not yet distributed)
            $availableDons = array_filter($dons, function($don) {
                return ($don['status_distribution'] ?? '') === 'disponible';
            });

            Flight::render('Simulation', [
                'dons' => $availableDons,
                'all_dons' => $dons,
                'is_list' => true,
                'title' => 'Simulation de Distribution Globale'
            ]);
        } catch (\Throwable $e) {
            error_log('SimulationController index error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur interne');
        }
    }

    /**
     * Show the distribution simulation page for a global donation
     */
    public function show($id)
    {
        try {
            $donGlobalModel = new DonGlobal();
            $don = $donGlobalModel->getByIdWithDetails($id);

            if (empty($don)) {
                Flight::halt(404, 'Don global introuvable');
            }

            // Get the distribution simulation preview
            $simulation = $this->simulationService->simulateDistribution($id);
            
            // Get smart suggestions
            $suggestions = $this->simulationService->getDistributionSuggestions($id);

            Flight::render('Simulation', [
                'don' => $don,
                'simulation' => $simulation,
                'suggestions' => $suggestions,
                'don_id' => $id,
                'is_global_system' => true,
            ]);
        } catch (\Throwable $e) {
            error_log('SimulationController show error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur interne');
        }
    }

    /**
     * Smart distribution suggestions using AI service
     */
    public function smartSuggestions($id)
    {
        try {
            $recommendations = $this->smartDistributionService->suggestOptimalDistribution($id);
            
            Flight::render('SimulationDistribution', [
                'don_id' => $id,
                'recommendations' => $recommendations,
                'is_smart_mode' => true,
            ]);
        } catch (\Throwable $e) {
            error_log('SimulationController smartSuggestions error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur interne: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint: Simulate distribution (preview only)
     */
    public function apiSimulate()
    {
        try {
            // Read JSON body
            $rawBody = file_get_contents('php://input');
            $data = json_decode($rawBody, true);
            $don_id = $data['don_id'] ?? null;

            if (empty($don_id)) {
                Flight::app()->json(['success' => false, 'error' => 'don_id requis'], 400);
                return;
            }

            $simulation = $this->simulationService->simulateDistribution($don_id);

            Flight::app()->json($simulation, $simulation['success'] ? 200 : 400);
        } catch (\Throwable $e) {
            error_log('SimulationController apiSimulate error: ' . $e->getMessage());
            Flight::app()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API endpoint: Get distribution suggestions
     */
    public function apiSuggestions()
    {
        try {
            $rawBody = file_get_contents('php://input');
            $data = json_decode($rawBody, true);
            $don_id = $data['don_id'] ?? null;

            if (empty($don_id)) {
                Flight::app()->json(['success' => false, 'error' => 'don_id requis'], 400);
                return;
            }

            $suggestions = $this->simulationService->getDistributionSuggestions($don_id);

            Flight::app()->json($suggestions, $suggestions['success'] ? 200 : 400);
        } catch (\Throwable $e) {
            error_log('SimulationController apiSuggestions error: ' . $e->getMessage());
            Flight::app()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API endpoint: Validate and perform distribution
     */
    public function apiValidate()
    {
        try {
            // Read JSON body
            $rawBody = file_get_contents('php://input');
            $data = json_decode($rawBody, true);
            $don_id = $data['don_id'] ?? null;
            $methode = $data['methode_distribution'] ?? 'automatique';
            $responsable = $data['responsable'] ?? 'Système Auto';

            if (empty($don_id)) {
                Flight::app()->json(['success' => false, 'error' => 'don_id requis'], 400);
                return;
            }

            $result = $this->simulationService->validateDistribution($don_id, $methode, $responsable);

            Flight::app()->json($result, $result['success'] ? 200 : 400);
        } catch (\Throwable $e) {
            error_log('SimulationController apiValidate error: ' . $e->getMessage());
            Flight::app()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API endpoint: Execute smart distribution using AI
     */
    public function apiSmartDistribute()
    {
        try {
            $rawBody = file_get_contents('php://input');
            $data = json_decode($rawBody, true);
            $don_id = $data['don_id'] ?? null;
            $options = $data['options'] ?? [];

            if (empty($don_id)) {
                Flight::app()->json(['success' => false, 'error' => 'don_id requis'], 400);
                return;
            }

            $result = $this->smartDistributionService->executeBatchDistribution($don_id, $options);

            Flight::app()->json($result, $result['success'] ? 200 : 400);
        } catch (\Throwable $e) {
            error_log('SimulationController apiSmartDistribute error: ' . $e->getMessage());
            Flight::app()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
