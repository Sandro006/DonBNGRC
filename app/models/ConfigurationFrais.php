<?php

namespace app\models;

use app\models\BaseModel;
use Flight;

class ConfigurationFrais extends BaseModel {

    protected $table = 'bngrc_config_frais';
    protected $primaryKey = 'id';

    /**
     * Get or create the frais_percent configuration
     */
    public function getCurrentFraisPercent() {
        $query = "SELECT frais_percent FROM {$this->table} ORDER BY updated_at DESC LIMIT 1";
        $result = $this->db->fetchRow($query);
        return $result ? (float)$result['frais_percent'] : 0.00;
    }

    /**
     * Update frais_percent configuration
     */
    public function updateFraisPercent($frais_percent, $description = '') {
        try {
            // Check if we have an existing record
            $existing = $this->db->fetchRow("SELECT id FROM {$this->table} LIMIT 1");
            
            if ($existing) {
                // Update existing record
                $query = "UPDATE {$this->table} SET 
                    frais_percent = :frais_percent, 
                    description = :description, 
                    updated_at = NOW() 
                    WHERE id = :id";
                
                $statement = $this->db->runQuery($query, [
                    ':frais_percent' => $frais_percent,
                    ':description' => $description,
                    ':id' => $existing['id']
                ]);
            } else {
                // Insert new record
                $query = "INSERT INTO {$this->table} (frais_percent, description, created_at, updated_at) 
                    VALUES (:frais_percent, :description, NOW(), NOW())";
                
                $statement = $this->db->runQuery($query, [
                    ':frais_percent' => $frais_percent,
                    ':description' => $description
                ]);
            }
            
            return $statement;
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la mise à jour des frais: " . $e->getMessage());
        }
    }

    /**
     * Get frais history
     */
    public function getFraisHistory($limit = 10) {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY updated_at DESC LIMIT :limit";
            return $this->db->fetchAll($query, [':limit' => $limit]);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Initialize default configuration if not exists
     */
    public function initializeDefault() {
        try {
            $existing = $this->db->fetchRow("SELECT id FROM {$this->table} LIMIT 1");
            
            if (!$existing) {
                $query = "INSERT INTO {$this->table} (frais_percent, description, created_at, updated_at) 
                    VALUES (0.00, 'Configuration initiale', NOW(), NOW())";
                $this->db->runQuery($query);
            }
        } catch (\Exception $e) {
            // Table might not exist yet
        }
    }
}
