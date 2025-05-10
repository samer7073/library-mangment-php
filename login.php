<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="login-form">
    <h2>Connexion</h2>
    <form action="login_process.php" method="post">
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
        </div>
        <button type="submit" class="btn">Se connecter</button>
    </form>
    <p class="text-center">Pas de compte ? <a href="register.php">S'inscrire</a></p>
</div>

<?php include 'includes/footer.php'; ?>