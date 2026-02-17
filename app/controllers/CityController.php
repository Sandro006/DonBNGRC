<?php

namespace app\controllers;

use flight\Engine;
use app\services\BesoinService;
use app\services\VilleService;

class CityController
{
    private $app;
    protected $besoinService;
    protected $villeService;

    public function __construct(Engine $app)
    {
        $this->app = $app;
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
                $this->app->halt(404, 'Ville introuvable');
            }

            $besoins = $this->besoinService->getByCity($id);

            $this->app->render('CityDetails', [
                'ville' => $ville,
                'besoins' => $besoins,
            ]);
        } catch (\Throwable $e) {
            error_log('City details error: ' . $e->getMessage());
            $this->app->halt(500, 'Erreur interne');
        }
    }
}
