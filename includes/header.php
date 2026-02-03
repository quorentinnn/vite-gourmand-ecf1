<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite&Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../CSS/style-public.css?v=<?= time() ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #8B1538;">        <div class="container-fluid">
            <a class="navbar-brand text-white" href="index.php">
                <span class="Vite">Vite</span><span class="Gourmand">&Gourmand</span>
            </a>
            
            
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="menus.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="contact.php">Contact</a>
                    </li>

                    
                    <?php
                    // Vérifier si l'utilisateur est connecté
                    if(isset($_SESSION['user_id'])) {
                        
                        // Afficher le prénom
                        echo '<li class="nav-item">
                                <span class="nav-link" style="color: #FFD700;">Bonjour ' . htmlspecialchars($_SESSION['user_prenom']) . ' !</span>
                              </li>';
                        
                        // Si employé
                        if($_SESSION['user_role'] == 'employe') {
                            echo '<li class="nav-item">
                                    <a class="nav-link text-white" href="../employe/commandes.php">Gestion des commandes</a>
                                  </li>';
                        }
                        
                        // Si admin
                        if($_SESSION['user_role'] == 'admin') {
                            echo '<li class="nav-item">
                                    <a class="nav-link text-white" href="../admin/gestion-menus.php">Gestion des menus</a>
                                  </li>';
                        }
                        
                        // Si client
                        if($_SESSION['user_role'] == 'client' || $_SESSION['user_role'] == 'utilisateur') {
                            echo '<li class="nav-item">
                                    <a class="nav-link text-white" href="mon-compte.php">Mon Compte</a>
                                  </li>';
                        }
                        
                        // Déconnexion pour tous
                        echo '<li class="nav-item">
                                <a class="nav-link btn btn-outline-light" href="deconnexion.php">Déconnexion</a>
                              </li>';
                        
                    } else {
                        
                        // Pas connecté
                        echo '<li class="nav-item">
                                <a class="nav-link btn btn-outline-light" href="connexion.php">Connexion</a>
                              </li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Script Bootstrap (nécessaire pour le burger) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>