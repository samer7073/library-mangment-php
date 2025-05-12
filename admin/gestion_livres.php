<?php
require_once __DIR__ . '/../includes/config.php';

// Vérification de l'authentification admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

// Traitement de l'ajout de livre
if (isset($_POST['ajouter'])) {
    $titre = sanitize($_POST['titre']);
    $auteur = sanitize($_POST['auteur']);
    $quantite = (int)$_POST['quantite'];
    $imagePath = null;

    // Gestion de l'upload de l'image
    if (isset($_FILES['couverture']) && $_FILES['couverture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/livres/';
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($_FILES['couverture']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        // Vérification du type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['couverture']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['couverture']['tmp_name'], $targetPath)) {
                $imagePath = 'uploads/livres/' . $filename;
            } else {
                $_SESSION['error'] = "Erreur lors de l'upload de l'image";
            }
        } else {
            $_SESSION['error'] = "Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WEBP";
        }
    }

    // Insertion en base de données
    try {
        $stmt = $conn->prepare("INSERT INTO livres (titre, auteur, quantite, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titre, $auteur, $quantite, $imagePath]);
        
        $_SESSION['success'] = "Livre ajouté avec succès";
        redirect('dashboard.php');
    } catch(PDOException $e) {
        // Supprimer l'image si l'insertion a échoué
        if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
            unlink(__DIR__ . '/../' . $imagePath);
        }
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
        redirect('dashboard.php');
    }
}

// Traitement de la suppression de livre
if (isset($_GET['supprimer'])) {
    $livre_id = (int)$_GET['supprimer'];
    
    try {
        $conn->beginTransaction();
        
        // Récupérer le chemin de l'image avant suppression
        $stmt = $conn->prepare("SELECT image_path FROM livres WHERE id = ?");
        $stmt->execute([$livre_id]);
        $livre = $stmt->fetch();
        
        // La suppression cascade grâce à ON DELETE CASCADE
        $conn->exec("DELETE FROM livres WHERE id = $livre_id");
        
        // Supprimer l'image associée si elle existe
        if ($livre && !empty($livre['image_path']) && file_exists(__DIR__ . '/../' . $livre['image_path'])) {
            unlink(__DIR__ . '/../' . $livre['image_path']);
        }
        
        $conn->commit();
        $_SESSION['success'] = "Livre supprimé avec succès";
    } catch(Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Erreur: " . $e->getMessage();
    }
    
    redirect('dashboard.php');
}

// Redirection si accès direct au fichier
redirect('dashboard.php');