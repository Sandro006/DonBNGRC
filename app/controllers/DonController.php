<?php

namespace app\controllers;

use app\models\Categorie;
use app\models\Donateur;
use app\models\Ville;
use app\models\Don;
use app\services\DonService;
use Flight;

class DonController
{
    protected $donService;

    public function __construct()
    {
        $this->donService = new DonService();
    }

    /**
     * Show form to add a new donation
     */
    public function create()
    {
        try {
            $categorieModel = new Categorie();
            $donateurModel = new Donateur();
            $villeModel = new Ville();

            $categories = $categorieModel->getAll();
            $donateurs = $donateurModel->getAll();
            $villes = $villeModel->getAllWithRegion();

            $req = Flight::request();
            $ville_id = !empty($req->query->ville_id) ? $req->query->ville_id : null;

            Flight::render('AddDon', [
                'categories' => $categories,
                'donateurs' => $donateurs,
                'villes' => $villes,
                'ville_id' => $ville_id,
            ]);
        } catch (\Throwable $e) {
            error_log('DonController create error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur interne');
        }
    }

    /**
     * Store a new donation
     */
    public function store()
    {
        $req = Flight::request();
        $data = [];
        $data['ville_id'] = !empty($req->data->ville_id) ? $req->data->ville_id : null;
        $data['categorie_id'] = !empty($req->data->categorie_id) ? $req->data->categorie_id : null;
        $data['quantite'] = !empty($req->data->quantite) ? (int)$req->data->quantite : 0;

        // Normalize datetime-local (YYYY-MM-DDTHH:MM) to SQL DATETIME
        $dateInput = $req->data->date_don ?? null;
        if (!empty($dateInput)) {
            $dateInput = str_replace('T', ' ', $dateInput);
            if (strlen($dateInput) === 16) { // no seconds
                $dateInput .= ':00';
            }
            $data['date_don'] = $dateInput;
        } else {
            $data['date_don'] = date('Y-m-d H:i:s');
        }

        $donateur_id = $req->data->donateur_id ?? null;

        // Basic validation: require ville and categorie
        if (empty($data['ville_id']) || empty($data['categorie_id'])) {
            error_log('DonController store validation failed: ville_id or categorie_id missing');
            Flight::halt(400, 'Ville et catégorie sont requises');
        }

        try {
            // If donor not selected, create new donor from provided fields
            if (empty($donateur_id)) {
                $donateurModel = new Donateur();
                $donateurData = [
                    'nom' => $req->data->donateur_nom ?? 'Anonyme',
                    'telephone' => $req->data->donateur_telephone ?? null,
                    'email' => $req->data->donateur_email ?? null,
                ];
                $donateur_id = $donateurModel->create($donateurData);
            }

            $data['donateur_id'] = $donateur_id;

            $donModel = new Don();
            $donModel->create($data);
        } catch (\Throwable $e) {
            error_log('DonController store error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            Flight::halt(500, 'Erreur interne lors de l\'enregistrement');
        }

        // Always redirect to city details when possible
        $redirectVille = $data['ville_id'];
        if (empty($redirectVille) && !empty($req->data->ville_libre) && is_numeric($req->data->ville_libre)) {
            $redirectVille = $req->data->ville_libre;
        }

        if (!empty($redirectVille)) {
            Flight::redirect('/ville/' . $redirectVille);
        } else {
            Flight::redirect('/');
        }
    }

    /**
     * Delete a donation
     */
    public function delete($id)
    {
        try {
            // Get the donation to find the city
            $donModel = new Don();
            $don = $donModel->getById($id);

            if (empty($don)) {
                Flight::halt(404, 'Don introuvable');
            }

            $ville_id = $don['ville_id'];
            $this->donService->delete($id);

            // Redirect back to city details
            Flight::redirect('/ville/' . $ville_id);
        } catch (\Throwable $e) {
            error_log('DonController delete error: ' . $e->getMessage());
            Flight::halt(500, 'Erreur lors de la suppression du don');
        }
    }
}
