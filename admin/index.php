<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../includes/db.php';

// Vérifier si l'administrateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

// Vérifier si c'est bien un administrateur
if($_SESSION['user_role'] != 'admin') {
    header('Location: ../public/connexion.php');
    exit;
}

// Récupérer les informations de l'administrateur
$user_nom = $_SESSION['user_nom'];
$user_prenom = $_SESSION['user_prenom'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../CSS/admin.css?v=<?= time() ?>">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="admin-dashboard">
        <div class="container py-4">
            <div class="dashboard-card">
                <h1>Dashboard Administrateur</h1>
                <p class="dashboard-welcome">Bienvenue <?php echo htmlspecialchars($user_prenom . ' ' . $user_nom); ?> !</p>

                <div class="dashboard-actions">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="ajouter-menu.php" class="btn btn-admin d-block py-3">Ajouter un menu</a>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="gestion-menus.php" class="btn btn-admin d-block py-3">Gérer les menus</a>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="gestion-plats.php" class="btn btn-admin d-block py-3">Gérer les plats</a>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="gestion-regimes.php" class="btn btn-admin d-block py-3">Gérer les régimes</a>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="gestion-allergenes.php" class="btn btn-admin d-block py-3">Gérer les allergènes</a>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="gestion-themes.php" class="btn btn-admin d-block py-3">Gérer les thèmes</a>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="gerer-employes.php" class="btn btn-admin d-block py-3">Gérer les employés</a>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="statistiques.php" class="btn btn-admin d-block py-3">Voir les statistiques</a>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <a href="logout.php" class="btn btn-logout d-block py-3">Se déconnecter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
