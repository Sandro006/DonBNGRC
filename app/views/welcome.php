<h1>Tableau de bord</h1>
<?php if (!empty($message)) { ?>
    <h3><?= $message ?></h3>
<?php } ?>

<?php if (!empty($don_stats)) { ?>
    <h4>Dons</h4>
    <ul>
        <li>Total de dons: <?= htmlspecialchars($don_stats['total_donations'] ?? 0) ?></li>
        <li>Quantité totale: <?= htmlspecialchars($don_stats['total_quantity'] ?? 0) ?></li>
        <li>Donateurs uniques: <?= htmlspecialchars($don_stats['unique_donors'] ?? 0) ?></li>
        <li>Villes aidées: <?= htmlspecialchars($don_stats['cities_helped'] ?? 0) ?></li>
    </ul>
<?php } ?>

<?php if (!empty($besoin_stats)) { ?>
    <h4>Besoins</h4>
    <ul>
        <li>Total de besoins: <?= htmlspecialchars($besoin_stats['total_needs'] ?? 0) ?></li>
        <li>Quantité totale: <?= htmlspecialchars($besoin_stats['total_quantity'] ?? 0) ?></li>
        <li>Montant total estimé: <?= htmlspecialchars($besoin_stats['total_amount'] ?? 0) ?></li>
    </ul>
<?php } ?>

<?php if (!empty($cities)) { ?>
    <h4>Villes (extrait)</h4>
    <ul>
        <?php foreach (array_slice($cities, 0, 10) as $c) { ?>
            <li><?= htmlspecialchars($c['nom'] ?? ($c['name'] ?? '')) ?> - Région: <?= htmlspecialchars($c['region_nom'] ?? '') ?></li>
        <?php } ?>
    </ul>
<?php } ?>

<?php if (!empty($recent_dons)) { ?>
    <h4>Derniers dons</h4>
    <table border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Ville</th>
            <th>Quantité</th>
            <th>Catégorie</th>
        </tr>
        <?php foreach ($recent_dons as $d) { ?>
            <tr>
                <td><?= htmlspecialchars($d['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($d['date_don'] ?? '') ?></td>
                <td><?= htmlspecialchars($d['ville_nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($d['quantite'] ?? '') ?></td>
                <td><?= htmlspecialchars($d['categorie_nom'] ?? '') ?></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if (!empty($recent_besoins)) { ?>
    <h4>Derniers besoins</h4>
    <table border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Ville</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Montant total</th>
            <th>Statut</th>
        </tr>
        <?php foreach ($recent_besoins as $b) { ?>
            <tr>
                <td><?= htmlspecialchars($b['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['ville_nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['quantite'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['prix_unitaire'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['montant_total'] ?? '') ?></td>
                <td><?= htmlspecialchars($b['status_nom'] ?? '') ?></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>