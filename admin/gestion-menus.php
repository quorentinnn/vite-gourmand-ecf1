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
                <h1 class="text-center">Gestion des Menus</h1>
                <div class="text-center mb-3">
                    <a href="ajouter-menu.php" class="btn btn-admin">
                        <i class="bi bi-plus-circle"></i> Ajouter un menu
                    </a>
                </div>

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

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
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
                            <?php if(empty($tous_les_menus)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Aucun menu enregistré</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($tous_les_menus as $menu): ?>
                                <tr>
                                    <td><?php echo $menu['id']; ?></td>
                                    <td>
                                            <img src="/images/<?php echo htmlspecialchars($menu['image'] ?? ''); ?>"
                                                 style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;"
                                                 onerror="this.onerror=null; this.src='/images/preparation.jpg';">
                                    </td>
                                    <td><?php echo htmlspecialchars($menu['titre']); ?></td>
                                    <td><strong><?php echo $menu['prix']; ?> &euro;</strong></td>
                                    <td>
                                        <div class="btn-action-group">
                                            <a href="modifier-menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Modifier
                                            </a>
                                            <a href="supprimer-menu.php?id=<?php echo $menu['id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Confirmer la suppression de ce menu ?')">
                                                <i class="bi bi-trash"></i> Supprimer
                                            </a>
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
