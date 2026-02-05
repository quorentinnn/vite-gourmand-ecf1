<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../includes/messages.php';

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

// Vérifier si c'est un admin
if($_SESSION['user_role'] != 'admin') {
    header('Location: ../public/connexion.php');
    exit;
}

// Connexion à la base de données
require_once '../includes/db.php';

// Récupérer tous les menus
$requete = "SELECT id, titre, description, prix, image FROM menus ORDER BY titre ASC";
$preparation = $pdo->prepare($requete);
$preparation->execute();
$tous_les_menus = $preparation->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Menus - Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Gestion des Menus</h1>
        
        <!-- AJOUTER CE BLOC ICI -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['message_erreur'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['message_erreur']; ?>
    </div>
    <?php unset($_SESSION['message_erreur']); ?>
<?php endif; ?>
        
        <p>Bienvenue Admin !</p>
        
        <a href="ajouter-menu.php" class="btn btn-success mb-3">Ajouter un nouveau menu</a>
        
        <h2 class="mt-4">Liste de tous les menus</h2>
        <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Boucle pour chaque menu
                foreach($tous_les_menus as $menu): 
                ?>
                <tr>
                    <!-- ID du menu -->
                    <td><?php echo $menu['id']; ?></td>
                    
                    <!-- Image du menu -->
                    <td>
                        <img src="../uploads/<?php echo $menu['image']; ?>" style="width: 80px; height: 60px;">
                    </td>
                    
                    <!-- Titre du menu -->
                    <td><?php echo $menu['titre']; ?></td>
                    
                    <!-- Prix du menu -->
                    <td><?php echo $menu['prix']; ?> €</td>
                    
                    <!-- Boutons Modifier et Supprimer -->
                    <td>
                        <a href="modifier-menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-warning btn-sm">
                            Modifier
                        </a>
                        <a href="supprimer-menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-danger btn-sm">
                            Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
```

---

