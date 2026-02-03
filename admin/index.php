<?php 
session_start();
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .welcome {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 30px;
        }

        .logout-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #C41E3A;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-link:hover {
            background-color: #a0172e;
        }
        .btn {
            display: inline-block;
            margin: 10px 10px 0 0;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard Administrateur</h1>
        <p class="welcome">Bienvenue <?php echo htmlspecialchars($user_prenom . ' ' . $user_nom); ?> !</p>
        <a href="logout.php" class="logout-link">Se déconnecter</a>
        <a href="ajouter-menu.php" class="btn btn-primary">Ajouter un menu</a>
        <a href="gestion-menus.php" class="btn btn-primary">Gérer les menus</a>
        <a href="gestion-plats.php" class="btn btn-primary">Gérer les plats</a>
        <a href="gestion-regimes.php" class="btn btn-primary">Gérer les régimes</a>
        <a href="gestion-allergenes.php" class="btn btn-primary">Gérer les allergènes</a>
        <a href="gestion-themes.php" class="btn btn-primary">Gérer les thèmes</a>
        <a href="gerer-employes.php" class="btn btn-primary">Gérer les employés</a>
        <a href="statistiques.php" class="btn btn-primary">Voir les statistiques</a>
    </div>
</body>
</html>
