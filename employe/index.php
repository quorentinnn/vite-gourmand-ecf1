<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../includes/db.php';

// Vérifier si l'employé est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

// Vérifier si c'est bien un employé
if($_SESSION['user_role'] != 'employe') {
    header('Location: ../public/connexion.php');
    exit;
}

// Récupérer les informations de l'employé
$user_nom = $_SESSION['user_nom'];
$user_prenom = $_SESSION['user_prenom'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Employé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../CSS/admin.css?v=<?= time() ?>">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="employe-dashboard">
        <div class="container py-4">
            <div class="dashboard-card">
                <h1>Dashboard Employé</h1>
                <p class="dashboard-welcome">Bienvenue <?php echo htmlspecialchars($user_prenom . ' ' . $user_nom); ?> !</p>

                <div class="dashboard-actions">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <a href="commandes.php" class="btn btn-admin d-block py-3">Voir les commandes</a>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <a href="logout.php" class="btn btn-logout d-block py-3">Se déconnecter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
