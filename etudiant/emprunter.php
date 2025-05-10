<?php
require __DIR__ . '/../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'etudiant') {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livre_id = (int)$_POST['livre_id'];
    $user_id = $_SESSION['user_id'];

    // Vérifie si le livre est disponible
    $stmt = $conn->prepare("SELECT quantite FROM livres WHERE id = ?");
    $stmt->execute([$livre_id]);
    $livre = $stmt->fetch();

    if ($livre && $livre['quantite'] > 0) {
        // Crée la demande
        $conn->prepare("INSERT INTO demandes (user_id, livre_id) VALUES (?, ?)")
             ->execute([$user_id, $livre_id]);
        
        $_SESSION['success'] = "Demande d'emprunt envoyée avec succès";
    } else {
        $_SESSION['error'] = "Ce livre n'est plus disponible";
    }
}

redirect('dashboard.php');