<?php
require 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom']);
    $email = sanitize($_POST['email']);
    $password = password_hash(sanitize($_POST['password']), PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, 'etudiant')");
        $stmt->execute([$nom, $email, $password]);
        
        $_SESSION['success'] = "Inscription réussie ! Connectez-vous maintenant";
        redirect('login.php');
    } catch(PDOException $e) {
        $_SESSION['error'] = "L'email existe déjà";
        redirect('register.php');
    }
}
?>