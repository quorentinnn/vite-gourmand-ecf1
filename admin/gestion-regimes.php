<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

// Vérifier si c'est un admin ou employé
if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'employe') {
    header('Location: ../public/connexion.php');
    exit;
}

// Connexion à la base de données
require_once '../includes/db.php';

$message = '';
$message_type = '';

// Traiter l'ajout d'un régime
if(isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    
    if(empty($nom)) {
        $message = "Le nom est obligatoire";
        $message_type = 'danger';
    } else {
        $requete = "INSERT INTO regimes (nom) VALUES (:nom)";
        $preparation = $pdo->prepare($requete);
        $preparation->execute(['nom' => $nom]);
        
        $message = "Régime ajouté avec succès !";
        $message_type = 'success';
    }
}

// Traiter la suppression d'un régime
if(isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    
    // Vérifier si le régime est utilisé dans des menus
    $requete_verif = "SELECT COUNT(*) as nb FROM menus WHERE regime_id = :id";
    $prep_verif = $pdo->prepare($requete_verif);
    $prep_verif->execute(['id' => $id]);
    $resultat = $prep_verif->fetch();
    
    if($resultat['nb'] > 0) {
        $message = "Impossible de supprimer ce régime, il est utilisé dans des menus !";
        $message_type = 'danger';
    } else {
        $requete = "DELETE FROM regimes WHERE id = :id";
        $preparation = $pdo->prepare($requete);
        $preparation->execute(['id' => $id]);
        
        $message = "Régime supprimé avec succès !";
        $message_type = 'success';
    }
}

// Traiter la modification d'un régime
if(isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    
    if(empty($nom)) {
        $message = "Le nom est obligatoire";
        $message_type = 'danger';
    } else {
        $requete = "UPDATE regimes SET nom = :nom WHERE id = :id";
        $preparation = $pdo->prepare($requete);
        $preparation->execute([
            'nom' => $nom,
            'id' => $id
        ]);
        
        $message = "Régime modifié avec succès !";
        $message_type = 'success';
    }
}

// Récupérer tous les régimes
$requete_regimes = "SELECT * FROM regimes ORDER BY nom ASC";
$preparation_regimes = $pdo->prepare($requete_regimes);
$preparation_regimes->execute();
$regimes = $preparation_regimes->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Régimes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Gestion des Régimes Alimentaires</h1>

        <!-- Messages -->
        <?php if($message != ''): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Ajouter un régime</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Nom du régime *</label>
                        <input type="text" name="nom" class="form-control" placeholder="Ex: Végétarien, Vegan, Sans gluten..." required>
                    </div>
                    <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>

        <!-- Liste des régimes -->
        <div class="card">
            <div class="card-header">
                <h3>Liste des régimes</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($regimes as $regime): ?>
                            <tr>
                                <td><?php echo $regime['id']; ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $regime['id']; ?>">
                                        <input type="text" name="nom" value="<?php echo htmlspecialchars($regime['nom']); ?>" class="form-control d-inline" style="width: 300px;">
                                        <button type="submit" name="modifier" class="btn btn-sm btn-warning">Modifier</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="?supprimer=<?php echo $regime['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Confirmer la suppression ?')">
                                        Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>