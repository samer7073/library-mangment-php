<?php
require __DIR__ . '/../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'etudiant') {
    redirect('../login.php');
}

// Récupère les livres disponibles
$livres = $conn->query("SELECT * FROM livres WHERE quantite > 0")->fetchAll();

// Récupère les emprunts de l'étudiant
$emprunts = $conn->prepare("
    SELECT l.titre, l.auteur, d.date_demande, d.status 
    FROM demandes d
    JOIN livres l ON d.livre_id = l.id
    WHERE d.user_id = ?
");
$emprunts->execute([$_SESSION['user_id']]);
$emprunts = $emprunts->fetchAll();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="etudiant-dashboard">
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?> !</h1>

    <section class="livres-disponibles">
        <h2>Livres disponibles</h2>
        <div class="livres-list">
            <?php foreach ($livres as $livre): ?>
                <div class="livre-card text-center p-3 mb-4 bg-light rounded">
                    <!-- Debug (à désactiver en production) -->
                    <div style="display:none;">
                        <p>Chemin image: <?= __DIR__ . '/../uploads/livres/' . basename($livre['image_path']) ?></p>
                        <?php if (!empty($livre['image_path'])): ?>
                            <?php
                            $imagePath = __DIR__ . '/../uploads/livres/' . basename($livre['image_path']);
                            $imageExists = file_exists($imagePath);
                            ?>
                            <p>Image existe: <?= $imageExists ? 'Oui' : 'Non' ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Affichage de l'image -->
                    <div class="livre-image-container mb-3">
                        <?php if (!empty($livre['image_path']) && file_exists(__DIR__ . '/../uploads/livres/' . basename($livre['image_path']))): ?>
                            <img src="<?= $base_url ?>uploads/livres/<?= basename($livre['image_path']) ?>"
                                alt="Couverture de <?= htmlspecialchars($livre['titre']) ?>"
                                class="img-fluid border rounded shadow-sm"
                                style="max-height: 200px; width: auto;">
                        <?php else: ?>
                            <img src="<?= $base_url ?>assets/default-book.png"
                                alt="Image non disponible"
                                class="img-fluid border rounded shadow-sm"
                                style="max-height: 200px; width: auto;">
                        <?php endif; ?>
                    </div>

                    <!-- Affichage des informations du livre -->
                    <div class="livre-info">
                        <h4 class="livre-titre h5 mb-1"><?= htmlspecialchars($livre['titre']) ?></h4>
                        <p class="livre-auteur text-muted small mb-2">
                            <em><?= htmlspecialchars($livre['auteur']) ?></em>
                        </p>
                        <p class="livre-dispo">
                            <span class="badge <?= $livre['quantite'] > 0 ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $livre['quantite'] > 0 ? 'Disponible' : 'Indisponible' ?>
                            </span>
                        </p>
                    </div>

                    <!-- Bouton d'emprunt -->
                    <form action="emprunter.php" method="post" class="mt-2">
                        <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-primary" <?= $livre['quantite'] <= 0 ? 'disabled' : '' ?>>
                            <i class="bi bi-bookmark-plus"></i> Emprunter
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mes-emprunts">
        <h2>Mes demandes d'emprunt</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Auteur</th>
                    <th>Date demande</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $emprunt): ?>
                    <tr>
                        <td><?= htmlspecialchars($emprunt['titre']) ?></td>
                        <td><?= htmlspecialchars($emprunt['auteur']) ?></td>
                        <td><?= date('d/m/Y', strtotime($emprunt['date_demande'])) ?></td>
                        <td>
                            <span class="badge badge-<?=
                                                        $emprunt['status'] === 'approved' ? 'success' : ($emprunt['status'] === 'rejected' ? 'danger' : 'warning')
                                                        ?>">
                                <?= $emprunt['status'] === 'approved' ? 'Approuvé' : ($emprunt['status'] === 'rejected' ? 'Rejeté' : 'En attente') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>