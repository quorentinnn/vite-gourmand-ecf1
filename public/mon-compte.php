<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    // Si pas connecté, rediriger vers connexion
    header('Location: connexion.php');
    exit;
}

// Connexion à la base de données
require_once '../includes/db.php';

// Récupérer l'ID de l'utilisateur connecté
$mon_id = $_SESSION['user_id'];

// Préparer la requête SQL pour récupérer MES commandes
$requete = "SELECT commandes.id, 
                   commandes.nb_personnes,
                   commandes.date_livraison,
                   commandes.prix_total,
                   commandes.statut,
                   menus.titre
            FROM commandes
            JOIN menus ON commandes.menu_id = menus.id
            WHERE commandes.utilisateur_id = :mon_id
            ORDER BY commandes.cree_le DESC";

// Exécuter la requête
$preparation = $pdo->prepare($requete);
$preparation->execute(['mon_id' => $mon_id]);

// Récupérer toutes mes commandes
$mes_commandes = $preparation->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte - Vite&Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container-compte">
        
        <?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>


        
        
        <div><h2 class="commande-text">Mes Commandes</h2></div>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Menu</th>
                    <th>Personnes</th>
                    <th>Date</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Boucle pour afficher chaque commande
                foreach($mes_commandes as $commande): 
                ?>
                <tr>
                    <!-- Numéro de commande -->
                    <td><?php echo $commande['id']; ?></td>
                    
                    <!-- Nom du menu -->
                    <td><?php echo $commande['titre']; ?></td>
                    
                    <!-- Nombre de personnes -->
                    <td><?php echo $commande['nb_personnes']; ?></td>
                    
                    <!-- Date de livraison -->
                    <td><?php echo $commande['date_livraison']; ?></td>
                    
                    <!-- Prix total -->
                    <td><?php echo $commande['prix_total']; ?> €</td>
                    
                    <!-- Statut avec badge coloré -->
                    <td>
                        <?php 
                        // Récupérer le statut
                        $statut = $commande['statut'];
                        
                        // Afficher le bon badge selon le statut
                        if($statut == 'en_attente') {
                            echo '<span class="badge bg-warning text-dark">En attente</span>';
                        }
                        elseif($statut == 'acceptee') {
                            echo '<span class="badge bg-info">Acceptée</span>';
                        }
                        elseif($statut == 'en_preparation') {
                            echo '<span class="badge bg-primary">En préparation</span>';
                        }
                        elseif($statut == 'en_livraison') {
                            echo '<span class="badge bg-success">Prête</span>';
                        }
                        elseif($statut == 'livree') {
                            echo '<span class="badge bg-secondary">Livrée</span>';
                        }
                        elseif($statut == 'terminee') {
                            echo '<span class="badge bg-secondary">Terminée</span>';
                        }
                        else {
                            echo '<span class="badge bg-light text-dark">' . $statut . '</span>';
                        }
                        ?>
                    </td>
                    
                    <!-- Boutons d'action -->
                    <td>
                        <?php 
                        // Si la commande est en attente, on peut l'annuler
                        if($statut == 'en_attente') {
                            echo '<a href="annuler-commande.php?id=' . $commande['id'] . '" class="btn btn-danger btn-sm">Annuler</a>';
                        }
                        // Si la commande est livrée, on peut laisser un avis
                        elseif($statut == 'livree') {
                            echo '<a href="ajouter-avis.php?id=' . $commande['id'] . '" class="btn btn-primary btn-sm">Laisser un avis</a>';
                        }
                        // Sinon rien
                        else {
                            echo '<span class="text-muted">-</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="menus.php" class="btn btn-primary mt-3">Commander à nouveau</a>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>