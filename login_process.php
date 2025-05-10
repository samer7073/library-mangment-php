<?php
require 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nom'] = $user['nom'];
        
        redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'etudiant/dashboard.php');
    } else {
        $_SESSION['error'] = "Email ou mot de passe incorrect";
        redirect('login.php');
    }
}
?>