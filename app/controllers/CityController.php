<?php

namespace app\controllers;

use app\services\DonService;
use app\services\BesoinService;
use app\services\VilleService;
use Flight;

class CityController
{
    protected $donService;
    protected $besoinService;
    protected $villeService;

    public function __construct()
    {
        $this->donService = new DonService();
        $this->besoinService = new BesoinService();
        $this->villeService = new VilleService();
    }

    /**
     * Display city details with donations and needs
     */
    public function show($id)
    {
        try {
            $ville = $this->villeService->getById($id);
            if (empty($ville)) {
                Flight::halt(404, 'Ville introuvable');
            }

            $dons = $this->donService->getByCity($id);
            $besoins = $this->besoinService->getByCity($id);

            Flight::render('CityDetails', [
                'ville' => $ville,
                'dons' => $dons,
                'besoins' => $besoins,
            ]);
        } catch (\Throwable $e) {
            error_log('City details error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur interne');
        }
    }
}
