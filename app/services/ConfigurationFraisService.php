<?php

namespace app\services;

use app\models\ConfigurationFrais;

class ConfigurationFraisService {

    protected $model;

    public function __construct() {
        $this->model = new ConfigurationFrais();
    }

    /**
     * Get current frais_percent
     */
    public function getCurrentFraisPercent() {
        try {
            return $this->model->getCurrentFraisPercent();
        } catch (\Exception $e) {
            return 0.00;
        }
    }

    /**
     * Update frais_percent with history tracking
     */
    public function updateFraisPercent($frais_percent, $description = '') {
        try {
            // Validate input
            if (!is_numeric($frais_percent) || $frais_percent < 0 || $frais_percent > 100) {
                throw new \Exception('Le pourcentage doit être un nombre entre 0 et 100');
            }

            // Round to 2 decimal places
            $frais_percent = round((float)$frais_percent, 2);

            // Add default description if empty
            if (empty($description)) {
                $description = 'Mise à jour du pourcentage de frais à ' . $frais_percent . '%';
            }

            return $this->model->updateFraisPercent($frais_percent, $description);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get frais configuration history
     */
    public function getFraisHistory($limit = 10) {
        try {
            return $this->model->getFraisHistory($limit);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Initialize default configuration
     */
    public function initializeDefault() {
        try {
            $this->model->initializeDefault();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
