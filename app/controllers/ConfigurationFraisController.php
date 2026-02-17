<?php

namespace app\controllers;

use flight\Engine;
use app\services\ConfigurationFraisService;
use Flight;

class ConfigurationFraisController {

    protected Engine $app;
    protected ConfigurationFraisService $service;

    public function __construct() {
        $this->app = Flight::app();
        $this->service = new ConfigurationFraisService();
    }

    /**
     * Display the configuration form
     */
    public function index() {
        try {
            $current_frais_percent = $this->service->getCurrentFraisPercent();
            $frais_history = $this->service->getFraisHistory();

            $this->app->render('ConfigurationFrais', [
                'current_frais_percent' => $current_frais_percent,
                'frais_history' => $frais_history,
            ]);
        } catch (\Exception $e) {
            $this->app->render('ConfigurationFrais', [
                'error_message' => 'Erreur lors du chargement: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the frais_percent via API
     */
    public function updateFrais() {
        try {
            $data = $this->app->request()->data;
            
            // Validate input
            if (empty($data->frais_percent)) {
                return $this->app->json(
                    ['success' => false, 'error' => 'Le pourcentage de frais est requis'],
                    400
                );
            }

            $frais_percent = (float)$data->frais_percent;
            
            // Validate percentage range
            if ($frais_percent < 0 || $frais_percent > 100) {
                return $this->app->json(
                    ['success' => false, 'error' => 'Le pourcentage doit être entre 0 et 100'],
                    400
                );
            }

            $description = isset($data->description) ? trim($data->description) : '';
            
            // Update frais
            $result = $this->service->updateFraisPercent($frais_percent, $description);

            if ($result) {
                return $this->app->json([
                    'success' => true,
                    'message' => 'Configuration des frais mise à jour avec succès',
                    'frais_percent' => $frais_percent,
                ], 200);
            } else {
                return $this->app->json(
                    ['success' => false, 'error' => 'Erreur lors de la mise à jour'],
                    500
                );
            }
        } catch (\Exception $e) {
            return $this->app->json(
                ['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get current frais configuration via API
     */
    public function getFraisConfig() {
        try {
            $current_frais_percent = $this->service->getCurrentFraisPercent();
            
            return $this->app->json([
                'success' => true,
                'current_frais_percent' => $current_frais_percent,
            ], 200);
        } catch (\Exception $e) {
            return $this->app->json(
                ['success' => false, 'error' => $e->getMessage()],
                500
            );
        }
    }
}
