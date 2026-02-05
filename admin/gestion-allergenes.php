<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

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

// Traiter l'ajout d'un allergène
if(isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    
    if(empty($nom)) {
        $message = "Le nom est obligatoire";
        $message_type = 'danger';
    } else {
        $requete = "INSERT INTO allergenes (nom) VALUES (:nom)";
        $preparation = $pdo->prepare($requete);
        $preparation->execute(['nom' => $nom]);
        
        $message = "Allergène ajouté avec succès !";
        $message_type = 'success';
    }
}

// Traiter la suppression d'un allergène
if(isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    
    $requete = "DELETE FROM allergenes WHERE id = :id";
    $preparation = $pdo->prepare($requete);
    $preparation->execute(['id' => $id]);
    
    $message = "Allergène supprimé avec succès !";
    $message_type = 'success';
}

// Traiter la modification d'un allergène
if(isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    
    if(empty($nom)) {
        $message = "Le nom est obligatoire";
        $message_type = 'danger';
    } else {
        $requete = "UPDATE allergenes SET nom = :nom WHERE id = :id";
        $preparation = $pdo->prepare($requete);
        $preparation->execute([
            'nom' => $nom,
            'id' => $id
        ]);
        
        $message = "Allergène modifié avec succès !";
        $message_type = 'success';
    }
}

// Récupérer tous les allergènes
$requete_allergenes = "SELECT * FROM allergenes ORDER BY nom ASC";
$preparation_allergenes = $pdo->prepare($requete_allergenes);
$preparation_allergenes->execute();
$allergenes = $preparation_allergenes->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Allergènes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Gestion des Allergènes</h1>

        <!-- Messages -->
        <?php if($message != ''): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Ajouter un allergène</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Nom de l'allergène *</label>
                        <input type="text" name="nom" class="form-control" placeholder="Ex: Gluten, Arachides, Lactose..." required>
                    </div>
                    <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>

        <!-- Liste des allergènes -->
        <div class="card">
            <div class="card-header">
                <h3>Liste des allergènes</h3>
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
                        <?php if(empty($allergenes)): ?>
                            <tr>
                                <td colspan="3" class="text-center">Aucun allergène enregistré</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($allergenes as $allergene): ?>
                                <tr>
                                    <td><?php echo $allergene['id']; ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $allergene['id']; ?>">
                                            <input type="text" name="nom" value="<?php echo htmlspecialchars($allergene['nom']); ?>" class="form-control d-inline" style="width: 300px;">
                                            <button type="submit" name="modifier" class="btn btn-sm btn-warning">Modifier</button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="?supprimer=<?php echo $allergene['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Confirmer la suppression ?')">
                                            Supprimer
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>