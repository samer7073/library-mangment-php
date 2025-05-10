<?php
require '../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

if (isset($_POST['ajouter'])) {
    $titre = sanitize($_POST['titre']);
    $auteur = sanitize($_POST['auteur']);
    $quantite = (int)$_POST['quantite'];

    $stmt = $conn->prepare("INSERT INTO livres (titre, auteur, quantite) VALUES (?, ?, ?)");
    $stmt->execute([$titre, $auteur, $quantite]);
    
    $_SESSION['success'] = "Livre ajouté avec succès";
    redirect('dashboard.php');
}

// Gestion suppression livre
if (isset($_GET['supprimer'])) {
    $livre_id = (int)$_GET['supprimer'];
    $conn->exec("DELETE FROM livres WHERE id = $livre_id");
    $_SESSION['success'] = "Livre supprimé";
    redirect('dashboard.php');
}
?>