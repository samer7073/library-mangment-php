<?php
require_once __DIR__ . '/../includes/config.php';

// Vérifier que l'utilisateur est admin et connecté
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("Accès refusé");
}

// Vérifier que l'ID de la demande est présent
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    die("ID de demande invalide");
}

$demande_id = (int)$_GET['id'];

try {
    // Commencer une transaction
    $conn->beginTransaction();

    // 1. Vérifier que la demande existe et est en attente
    $stmt = $conn->prepare("SELECT livre_id FROM demandes WHERE id = ? AND status = 'pending'");
    $stmt->execute([$demande_id]);
    $demande = $stmt->fetch();

    if (!$demande) {
        throw new Exception("Demande introuvable ou déjà traitée");
    }

    // 2. Mettre à jour le statut de la demande
    $stmt = $conn->prepare("UPDATE demandes SET status = 'rejected' WHERE id = ?");
    $stmt->execute([$demande_id]);

    // 3. Envoyer une notification (exemple basique)
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, message, type) 
        SELECT user_id, 'Votre demande d\'emprunt a été refusée', 'rejection'
        FROM demandes WHERE id = ?
    ");
    $stmt->execute([$demande_id]);

    // Valider la transaction
    $conn->commit();

    $_SESSION['success'] = "La demande a été refusée avec succès";
    
} catch (Exception $e) {
    // Annuler en cas d'erreur
    $conn->rollBack();
    $_SESSION['error'] = "Erreur lors du refus : " . $e->getMessage();
}

// Redirection vers le tableau de bord admin
header("Location: " . $base_url . "admin/dashboard.php");
exit;