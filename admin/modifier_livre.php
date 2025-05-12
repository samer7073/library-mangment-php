<?php
require_once __DIR__ . '/../includes/config.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

// Récupérer les infos du livre à modifier
$livre = null;
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM livres WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $livre = $stmt->fetch();
    
    if (!$livre) {
        $_SESSION['error'] = "Livre introuvable";
        redirect('dashboard.php');
    }
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $titre = sanitize($_POST['titre']);
    $auteur = sanitize($_POST['auteur']);
    $quantite = (int)$_POST['quantite'];
    
    // Gestion de l'image
    $imagePath = $livre['image_path'] ?? null;
    
    if (isset($_FILES['couverture']) && $_FILES['couverture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/livres/';
        $extension = pathinfo($_FILES['couverture']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['couverture']['tmp_name'], $targetPath)) {
            // Supprimer l'ancienne image si elle existe
            if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
                unlink(__DIR__ . '/../' . $imagePath);
            }
            $imagePath = 'uploads/livres/' . $filename;
        }
    }
    
    try {
        $stmt = $conn->prepare("UPDATE livres SET titre = ?, auteur = ?, quantite = ?, image_path = ? WHERE id = ?");
        $stmt->execute([$titre, $auteur, $quantite, $imagePath, $id]);
        
        $_SESSION['success'] = "Livre modifié avec succès";
        redirect('dashboard.php');
    } catch(PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la modification : " . $e->getMessage();
        redirect("modifier_livre.php?id=$id");
    }
}

$title = "Modifier un livre";
include __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <h1>Modifier le livre</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $livre['id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($livre['titre']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Auteur</label>
                    <input type="text" name="auteur" class="form-control" value="<?= htmlspecialchars($livre['auteur']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Quantité disponible</label>
                    <input type="number" name="quantite" class="form-control" value="<?= $livre['quantite'] ?>" min="0" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Couverture actuelle</label>
                    <?php if (!empty($livre['image_path'])): ?>
                        <div class="mb-2">
                            <img src="<?= $base_url . $livre['image_path'] ?>" alt="Couverture actuelle" style="max-height: 150px;" class="img-thumbnail">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="supprimer_image" id="supprimer_image">
                                <label class="form-check-label" for="supprimer_image">
                                    Supprimer cette image
                                </label>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Aucune image actuellement</p>
                    <?php endif; ?>
                    
                    <label class="form-label">Nouvelle couverture</label>
                    <input type="file" name="couverture" class="form-control" accept="image/*">
                </div>
                
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="dashboard.php" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>