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
    <title>Gestion des Régimes - Admin</title>
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
                <h1>Gestion des Régimes Alimentaires</h1>

                <?php if($message != ''): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">Ajouter un régime</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nom du régime *</label>
                                <input type="text" name="nom" class="form-control" placeholder="Ex: Végétarien, Vegan, Sans gluten..." required>
                            </div>
                            <button type="submit" name="ajouter" class="btn btn-admin">
                                <i class="bi bi-plus-circle"></i> Ajouter
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">Liste des régimes</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($regimes)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Aucun régime enregistré</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($regimes as $regime): ?>
                                            <tr>
                                                <td><?php echo $regime['id']; ?></td>
                                                <td>
                                                    <form method="POST" class="inline-edit-form">
                                                        <input type="hidden" name="id" value="<?php echo $regime['id']; ?>">
                                                        <input type="text" name="nom" value="<?php echo htmlspecialchars($regime['nom']); ?>" class="form-control">
                                                        <button type="submit" name="modifier" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i> Modifier
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="?supprimer=<?php echo $regime['id']; ?>"
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Confirmer la suppression ?')">
                                                        <i class="bi bi-trash"></i> Supprimer
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

                <a href="index.php" class="btn btn-secondary mt-3">
                    <i class="bi bi-arrow-left"></i> Retour au dashboard
                </a>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
