<?php 
// Doit être ABSOLUMENT la première ligne
require_once __DIR__ . '/includes/config.php';
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h2>Inscription Étudiant</h2>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <form action="register_process.php" method="post">
        <div class="form-group">
            <input type="text" name="nom" placeholder="Nom complet" required class="form-control">
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email universitaire" required class="form-control">
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Mot de passe" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
    <p class="mt-3">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>