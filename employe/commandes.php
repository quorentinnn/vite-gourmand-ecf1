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
    <title>Gestion des Commandes - Employé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../CSS/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../CSS/admin.css?v=<?= time() ?>">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="admin-dashboard">
        <div class="container py-4">
            <div class="dashboard-card">
                <h1>Gestion des Commandes</h1>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>N&deg;</th>
                                <th>Client</th>
                                <th>Menu</th>
                                <th>Pers.</th>
                                <th>Date</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($commandes)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Aucune commande</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($commandes as $commande): ?>
                                <tr>
                                    <td><?php echo $commande['id']; ?></td>
                                    <td><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($commande['titre']); ?></td>
                                    <td><?php echo $commande['nb_personnes']; ?></td>
                                    <td><?php echo $commande['date_livraison']; ?></td>
                                    <td><strong><?php echo $commande['prix_total']; ?> &euro;</strong></td>
                                    <td>
                                        <?php if($commande['statut'] == 'en_attente'): ?>
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        <?php elseif($commande['statut'] == 'acceptee'): ?>
                                            <span class="badge bg-info">Acceptée</span>
                                        <?php elseif($commande['statut'] == 'en_preparation'): ?>
                                            <span class="badge bg-primary">En préparation</span>
                                        <?php elseif($commande['statut'] == 'en_livraison'): ?>
                                            <span class="badge bg-success">Prête</span>
                                        <?php elseif($commande['statut'] == 'livree'): ?>
                                            <span class="badge bg-secondary">Livrée</span>
                                        <?php elseif($commande['statut'] == 'attente_retour_materiel'): ?>
                                            <span class="badge bg-warning">Retour matériel</span>
                                        <?php elseif($commande['statut'] == 'terminee'): ?>
                                            <span class="badge bg-secondary">Terminée</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark"><?php echo $commande['statut']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-action-group">
                                            <?php if($commande['statut'] == 'en_attente'): ?>
                                                <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=accepter" class="btn btn-success btn-sm">Accepter</a>
                                                <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=refuser" class="btn btn-danger btn-sm">Refuser</a>
                                            <?php endif; ?>

                                            <?php if($commande['statut'] == 'acceptee'): ?>
                                                <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=preparer" class="btn btn-warning btn-sm">Préparer</a>
                                            <?php endif; ?>

                                            <?php if($commande['statut'] == 'en_preparation'): ?>
                                                <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=prete" class="btn btn-info btn-sm">Prête</a>
                                            <?php endif; ?>

                                            <?php if($commande['statut'] == 'en_livraison'): ?>
                                                <a href="modifier-commande.php?id=<?php echo $commande['id']; ?>&action=livrer" class="btn btn-primary btn-sm">Livrer</a>
                                            <?php endif; ?>

                                            <?php if(in_array($commande['statut'], ['terminee', 'livree', 'attente_retour_materiel'])): ?>
                                                <span class="text-muted">Terminée</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <a href="index.php" class="btn btn-secondary mt-3">
                    <i class="bi bi-arrow-left"></i> Retour au dashboard
                </a>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
