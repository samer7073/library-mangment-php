<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Bibliothèque</title>
    <style>
        /* ================ */
        /* STYLES GÉNÉRAUX */
        /* ================ */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        nav {
            background: #2c3e50;
            padding: 15px 20px;
            color: white;
            margin-bottom: 30px;
        }

        nav a {
            color: #3498db;
            text-decoration: none;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* ===================== */
        /* STYLES FORMULAIRES */
        /* ===================== */
        .login-form,
        .register-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #2980b9;
        }

        /* ====================== */
        /* STYLES SPÉCIFIQUE ADMIN */
        /* ====================== */
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>.admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .admin-container h1 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .btn-admin {
            background: #2c3e50;
        }

        .btn-admin:hover {
            background: #1a252f;
        }

        <?php endif; ?>

        /* ====================== */
        /* STYLES SPÉCIFIQUE ÉTUDIANT */
        /* ====================== */
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'etudiant'): ?>.etudiant-dashboard {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .livres-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .livre-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .livre-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .badge-success {
            background-color: #2ecc71;
            color: white;
        }

        .badge-warning {
            background-color: #f39c12;
            color: white;
        }

        .badge-danger {
            background-color: #e74c3c;
            color: white;
        }

        .table th {
            position: sticky;
            top: 0;
            background-color: #212529 !important;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .badge {
            font-size: 0.85em;
            padding: 0.5em 0.75em;
        }

        <?php endif; ?>
    </style>
</head>

<body>
    <nav>
        <?php if (isLoggedIn()): ?>
            Connecté en tant que <?= htmlspecialchars($_SESSION['email']) ?>
            (<a href="<?= $base_url ?>logout.php">Déconnexion</a>)
        <?php endif; ?>
    </nav>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>