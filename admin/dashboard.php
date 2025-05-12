<?php
require_once __DIR__ . '/../includes/config.php';

// Vérification de l'authentification et des droits admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

// Récupération des données
$livres = $conn->query("SELECT * FROM livres ORDER BY date_ajout DESC")->fetchAll();
$demandes = $conn->query("SELECT d.*, u.nom as user_nom, l.titre as livre_titre 
                         FROM demandes d
                         JOIN users u ON d.user_id = u.id
                         JOIN livres l ON d.livre_id = l.id
                         WHERE d.status = 'pending'")->fetchAll();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="admin-container">
    <h1>Tableau de bord Administrateur</h1>

    <section class="ajout-livre">
        <h2>Ajouter un nouveau livre</h2>
        <form action="gestion_livres.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="titre" placeholder="Titre" required class="form-control">
            </div>
            <div class="form-group">
                <input type="text" name="auteur" placeholder="Auteur" required class="form-control">
            </div>
            <div class="form-group">
                <input type="number" name="quantite" placeholder="Quantité" min="1" required class="form-control">
            </div>
            <div class="form-group">
                <label for="couverture">Couverture du livre :</label>
                <input type="file" name="couverture" id="couverture" accept="image/*" class="form-control">
            </div>
            <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
        </form>
    </section>

    <section class="liste-livres">
        <h2>Liste des livres</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Couverture</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Quantité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td><?= $livre['id'] ?></td>
                        <td>
                            <?php if (!empty($livre['image_path'])): ?>
                                <img src="<?= $base_url . $livre['image_path'] ?>" alt="Couverture" style="max-height: 50px;">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($livre['titre']) ?></td>
                        <td><?= htmlspecialchars($livre['auteur']) ?></td>
                        <td><?= $livre['quantite'] ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="modifier_livre.php?id=<?= $livre['id'] ?>"
                                    class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                                <a href="gestion_livres.php?supprimer=<?= $livre['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Êtes-vous sûr ?')">
                                    <i class="bi bi-trash"></i> Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="demandes-attente">
        <h2>Demandes en attente</h2>
        <?php if (count($demandes) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Livre</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demandes as $demande): ?>
                        <tr>
                            <td><?= htmlspecialchars($demande['user_nom']) ?></td>
                            <td><?= htmlspecialchars($demande['livre_titre']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($demande['date_demande'])) ?></td>
                            <td>
                                <a href="valider_demande.php?id=<?= $demande['id'] ?>" class="btn btn-success">Accepter</a>
                                <a href="refuser_demande.php?id=<?= $demande['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Êtes-vous sûr de vouloir refuser cette demande ?')">
                                    Refuser
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune demande en attente</p>
        <?php endif; ?>
    </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>