<?php
session_start();


// URL DE BASE ESSENTIELLE
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/bibliotheque/';

// Vérifiez que le chemin est correct
echo "Base URL: " . $base_url; // Debug temporaire

$host = "localhost";
$dbname = "bibliotheque_fac";
$username = "root";
$password = "";


try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Correction ici
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonctions utilitaires
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>