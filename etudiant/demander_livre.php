<?php
require '../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'etudiant') {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $livre_id = (int)$_POST['livre_id'];

    // Vérifier la disponibilité
    $stmt = $conn->query("SELECT quantite FROM livres WHERE id = $livre_id");
    $livre = $stmt->fetch();
    
    if ($livre['quantite'] > 0) {
        $stmt = $conn->prepare("INSERT INTO demandes (user_id, livre_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $livre_id]);
        $_SESSION['success'] = "Demande envoyée";
    } else {
        $_SESSION['error'] = "Livre non disponible";
    }
}

redirect('dashboard.php');
?>