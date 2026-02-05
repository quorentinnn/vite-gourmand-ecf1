<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Vérifier si employé connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

if($_SESSION['user_role'] != 'employe') {
    header('Location: ../public/connexion.php');
    exit;
}

// Connexion BDD
require_once '../includes/db.php';

// Récupérer toutes les commandes
$requete = "SELECT commandes.id, 
                   commandes.nb_personnes,
                   commandes.date_livraison,
                   commandes.prix_total,
                   commandes.statut,
                   utilisateurs.nom,
                   utilisateurs.prenom,
                   menus.titre
            FROM commandes
            JOIN utilisateurs ON commandes.utilisateur_id = utilisateurs.id
            JOIN menus ON commandes.menu_id = menus.id
            ORDER BY commandes.cree_le DESC";

$preparation = $pdo->prepare($requete);
$preparation->execute();
$commandes = $preparation->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes employé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container">
        <h1>Gestion des Commandes</h1>
        
        <h2>Liste de toutes les commandes</h2>
        
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Client</th>
                    <th>Menu</th>
                    <th>Personnes</th>
                    <th>Date</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($commandes as $commande): ?>
                <tr>
                    <!-- Numéro -->
                    <td><?php echo $commande['id']; ?></td>
                    
                    <!-- Client -->
                    <td><?php echo $commande['prenom']; ?> <?php echo $commande['nom']; ?></td>
                    
                    <!-- Menu -->
                    <td><?php echo $commande['titre']; ?></td>
                    
                    <!-- Personnes -->
                    <td><?php echo $commande['nb_personnes']; ?></td>
                    
                    <!-- Date -->
                    <td><?php echo $commande['date_livraison']; ?></td>
                    
                    <!-- Prix -->
                    <td><?php echo $commande['prix_total']; ?> €</td>
                    
                    <!-- Statut avec couleurs -->
                    <td>
                        <?php if($commande['statut'] == 'en_attente'): ?>
                            <span class="badge bg-warning text-dark">En attente</span>
                        
                        <?php elseif($commande['statut'] == 'acceptee'): ?>
                            <span class="badge bg-info">Acceptée</span>
                        
                        <?php elseif($commande['statut'] == 'en_preparation'): ?>
                            <span class="badge bg-primary">En préparation</span>
                        
                        <?php elseif($commande['statut'] == 'en_livraison'): ?>
                            <span class="badge bg-success">Prête (en livraison)</span>
                        
                        <?php elseif($commande['statut'] == 'livree'): ?>
                            <span class="badge bg-secondary">Livrée</span>
                        
                        <?php elseif($commande['statut'] == 'attente_retour_materiel'): ?>
                            <span class="badge bg-warning">Attente retour matériel</span>
                        
                        <?php elseif($commande['statut'] == 'terminee'): ?>
                            <span class="badge bg-secondary">Terminée</span>
                        
                        <?php else: ?>
                            <span class="badge bg-light text-dark"><?php echo $commande['statut']; ?></span>
                        
                        <?php endif; ?>
                    </td>
                    
                    <!-- Actions -->
                    <td>
                        <!-- Si statut = en_attente -->
                        <?php if($commande['statut'] == 'en_attente'): ?>
                            <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=accepter" class="btn btn-success btn-sm">
                                Accepter
                            </a>
                            <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=refuser" class="btn btn-danger btn-sm">
                                Refuser
                            </a>
                        <?php endif; ?>
                        
                        <!-- Si statut = acceptee -->
                        <?php if($commande['statut'] == 'acceptee'): ?>
                            <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=preparer" class="btn btn-warning btn-sm">
                                Préparer
                            </a>
                        <?php endif; ?>
                        
                        <!-- Si statut = en_preparation -->
                        <?php if($commande['statut'] == 'en_preparation'): ?>
                            <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=prete" class="btn btn-info btn-sm">
                                Prête pour livraison
                            </a>
                        <?php endif; ?>
                        
                        <!-- Si statut = en_livraison -->
                        <?php if($commande['statut'] == 'en_livraison'): ?>
                            <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=livrer" class="btn btn-primary btn-sm">
                                Livrer
                            </a>
                        <?php endif; ?>
                        
                        <!-- Si statut = terminée ou livrée -->
                        <?php if($commande['statut'] == 'terminee' || $commande['statut'] == 'livree' || $commande['statut'] == 'attente_retour_materiel'): ?>
                            <span class="text-muted">Terminée</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>