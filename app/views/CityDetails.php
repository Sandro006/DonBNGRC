<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Détails - Ville</title>
    <link href="<?php echo Flight::get('flight.base_url'); ?>/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold">Détails de la ville: <?= htmlspecialchars($ville['nom'] ?? 'N/A') ?></h3>
                <small class="text-muted">Région: <?= htmlspecialchars($ville['region_nom'] ?? '') ?></small>
            </div>
            <div>
                <a href="<?= Flight::get('flight.base_url') ?>" class="btn btn-link">← Retour au tableau de bord</a>
                <a href="<?= Flight::get('flight.base_url') ?>/don/ajouter?ville_id=<?= htmlspecialchars($ville['id'] ?? '') ?>" class="btn btn-primary ms-2">Ajouter don</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card p-3">
                    <h5 class="fw-bold">Dons pour <?= htmlspecialchars($ville['nom'] ?? '') ?></h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donateur</th>
                                    <th>Catégorie</th>
                                    <th>Quantité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($dons)) {
                                    foreach ($dons as $d) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($d['date_don'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($d['donateur_nom'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($d['categorie_nom'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($d['quantite'] ?? '') ?></td>
                                            <td>
                                                <a href="<?= Flight::get('flight.base_url') ?>/simulation/<?= htmlspecialchars($d['id'] ?? '') ?>" class="btn btn-sm btn-info">
                                                    Simuler
                                                </a>
                                                <form method="POST" action="<?= Flight::get('flight.base_url') ?>/don/supprimer/<?= htmlspecialchars($d['id'] ?? '') ?>" style="display:inline;">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Aucun don pour cette ville.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3">
                    <h5 class="fw-bold">Besoins pour <?= htmlspecialchars($ville['nom'] ?? '') ?></h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Montant total</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($besoins)) {
                                    foreach ($besoins as $b) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($b['categorie_nom'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($b['quantite'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($b['prix_unitaire'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($b['montant_total'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($b['status_nom'] ?? '') ?></td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Aucun besoin enregistré pour cette ville.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>