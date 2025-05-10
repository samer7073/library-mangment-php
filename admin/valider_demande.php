<?php
require '../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$demande_id = (int)$_GET['id'];

// Mettre à jour le statut
$conn->exec("UPDATE demandes SET status = 'approved' WHERE id = $demande_id");

// Diminuer la quantité du livre
$stmt = $conn->query("SELECT livre_id FROM demandes WHERE id = $demande_id");
$livre_id = $stmt->fetchColumn();
$conn->exec("UPDATE livres SET quantite = quantite - 1 WHERE id = $livre_id");

$_SESSION['success'] = "Demande approuvée";
redirect('dashboard.php');
?>