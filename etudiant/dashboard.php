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
            <div class="livre-card">
                <h3><?= htmlspecialchars($livre['titre']) ?></h3>
                <p>Auteur : <?= htmlspecialchars($livre['auteur']) ?></p>
                <form action="emprunter.php" method="post">
                    <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                    <button type="submit" class="btn btn-primary">Emprunter</button>
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
                            $emprunt['status'] === 'approved' ? 'success' : 
                            ($emprunt['status'] === 'rejected' ? 'danger' : 'warning') 
                        ?>">
                            <?= $emprunt['status'] === 'approved' ? 'Approuvé' : 
                               ($emprunt['status'] === 'rejected' ? 'Rejeté' : 'En attente') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>