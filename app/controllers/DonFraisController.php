<?php

namespace app\controllers;

use flight\Engine;
use app\models\Don;
use Flight;

class DonFraisController {

    protected Engine $app;
    protected Don $model;

    public function __construct() {
        $this->app = Flight::app();
        $this->model = new Don();
    }

    /**
     * Update frais_percent for a specific don
     * POST /api/don/{id}/frais
     */
    public function updateDonFrais($id) {
        try {
            $data = $this->app->request()->data;
            
            // Validate input
            if (empty($data->frais_percent) && $data->frais_percent !== '0' && $data->frais_percent !== 0) {
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

            // Get the don to check category
            $don = $this->model->getById($id);
            if (!$don) {
                return $this->app->json(
                    ['success' => false, 'error' => 'Don non trouvé'],
                    404
                );
            }

            // Check if category is not "Argent" (assuming ID 3 or libelle = 'Argent')
            $categorie = $this->model->getCategoryById($don['categorie_id']);
            if ($categorie && strtolower($categorie['libelle']) === 'argent') {
                return $this->app->json(
                    ['success' => false, 'error' => 'Les frais ne peuvent pas être configurés pour les dons d\'argent'],
                    400
                );
            }

            // Round to 2 decimal places
            $frais_percent = round($frais_percent, 2);

            // Update don
            $query = "UPDATE bngrc_don SET frais_percent = :frais_percent WHERE id = :id";
            $statement = $this->app->db()->runQuery($query, [
                ':frais_percent' => $frais_percent,
                ':id' => $id
            ]);

            if ($statement) {
                return $this->app->json([
                    'success' => true,
                    'message' => 'Frais mis à jour avec succès',
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
     * Get don details with frais info
     * GET /api/don/{id}/frais
     */
    public function getDonFrais($id) {
        try {
            $don = $this->model->getByIdWithDetails($id);
            
            if (!$don) {
                return $this->app->json(
                    ['success' => false, 'error' => 'Don non trouvé'],
                    404
                );
            }

            return $this->app->json([
                'success' => true,
                'don' => [
                    'id' => $don['id'],
                    'categorie_nom' => $don['categorie_nom'],
                    'frais_percent' => $don['frais_percent'] ?? 0,
                    'donateur_nom' => $don['donateur_nom'],
                ]
            ], 200);
        } catch (\Exception $e) {
            return $this->app->json(
                ['success' => false, 'error' => $e->getMessage()],
                500
            );
        }
    }
}
