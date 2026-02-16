<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Ajouter Don</title>
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold">Ajouter un don</h3>
            <a href="<?= Flight::get('flight.base_url') ?>" class="btn btn-link">← Retour</a>
        </div>

        <div class="card p-3">
            <form method="post" action="<?= Flight::get('flight.base_url') ?>/don/ajouter">

                <input type="hidden" name="ville_id" value="<?= htmlspecialchars($ville_id ?? '') ?>" />

                <div class="mb-3">
                    <label class="form-label">Ville</label>
                    <?php if (!empty($ville_id)) { ?>
                        <input type="text" class="form-control" value="Ville ID: <?= htmlspecialchars($ville_id) ?>" disabled />
                    <?php } else { ?>
                        <input type="text" name="ville_libre" class="form-control" placeholder="ID de la ville (optionnel)" />
                    <?php } ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catégorie</label>
                    <select name="categorie_id" class="form-select" required>
                        <option value="">-- Choisir une catégorie --</option>
                        <?php foreach ($categories as $c) { ?>
                            <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['libelle']) ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="quantite" class="form-control" min="1" required />
                </div>

                <div class="mb-3">
                    <label class="form-label">Date du don</label>
                    <input type="datetime-local" name="date_don" class="form-control" />
                </div>

                <hr />
                <h6>Donateur</h6>

                <div class="mb-3">
                    <label class="form-label">Sélectionner un donateur existant (optionnel)</label>
                    <select name="donateur_id" class="form-select">
                        <option value="">-- Nouveau donateur --</option>
                        <?php foreach ($donateurs as $d) { ?>
                            <option value="<?= htmlspecialchars($d['id']) ?>"><?= htmlspecialchars($d['nom']) ?> <?= !empty($d['telephone']) ? '(' . htmlspecialchars($d['telephone']) . ')' : '' ?></option>
                        <?php } ?>
                    </select>
                </div>

                <p class="text-muted">Si vous n'avez pas choisi de donateur, remplissez ci-dessous pour en créer un nouveau.</p>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="donateur_nom" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="donateur_telephone" class="form-control" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="donateur_email" class="form-control" />
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Enregistrer le don</button>
                    <a class="btn btn-secondary" href="<?= Flight::get('flight.base_url') ?>">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
