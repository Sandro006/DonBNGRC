<?php

namespace app\controllers;

use app\services\BesoinService;
use app\services\VilleService;
use Flight;

class CityController
{
    protected $besoinService;
    protected $villeService;

    public function __construct()
    {
        $this->besoinService = new BesoinService();
        $this->villeService = new VilleService();
    }

    /**
     * Display city details with needs only (no more city-specific donations)
     */
    public function show($id)
    {
        try {
            $ville = $this->villeService->getById($id);
            if (empty($ville)) {
                Flight::halt(404, 'Ville introuvable');
            }

            $besoins = $this->besoinService->getByCity($id);

            Flight::render('CityDetails', [
                'ville' => $ville,
                'besoins' => $besoins,
            ]);
        } catch (\Throwable $e) {
            error_log('City details error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur interne');
        }
    }
}
