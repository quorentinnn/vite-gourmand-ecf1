<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

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

// Vérifier qu'on a bien un ID dans l'URL
if(!isset($_GET['id'])) {
    header('Location: gestion-menus.php');
    exit;
}

// Récupérer l'ID du menu à modifier
$menu_id = $_GET['id'];

// Récupérer les données actuelles du menu
$requete_menu = "SELECT id, titre, description, prix, image, theme_id, regime_id
                 FROM menus
                 WHERE id = :id";
$preparation_menu = $pdo->prepare($requete_menu);
$preparation_menu->execute(['id' => $menu_id]);
$menu = $preparation_menu->fetch();

// Si le menu n'existe pas, retour à la liste
if(!$menu) {
    header('Location: gestion-menus.php');
    exit;
}

// Récupérer tous les thèmes disponibles
$requete_themes = "SELECT id, nom FROM themes ORDER BY nom ASC";
$preparation_themes = $pdo->prepare($requete_themes);
$preparation_themes->execute();
$tous_les_themes = $preparation_themes->fetchAll();

// Récupérer tous les régimes disponibles
$requete_regimes = "SELECT id, nom FROM regimes ORDER BY nom ASC";
$preparation_regimes = $pdo->prepare($requete_regimes);
$preparation_regimes->execute();
$tous_les_regimes = $preparation_regimes->fetchAll();

// Variable pour le message d'erreur
$message_erreur = '';

// Si le formulaire a été envoyé
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];

    // Gérer l'upload de l'image (optionnel)
    $nom_image = $menu['image'];

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $fichier_tmp = $_FILES['image']['tmp_name'];
        $nom_fichier = $_FILES['image']['name'];
        $extension = pathinfo($nom_fichier, PATHINFO_EXTENSION);
        $nom_unique = uniqid() . '.' . $extension;
        $dossier_upload = __DIR__ . '/../uploads/';
        $chemin_final = $dossier_upload . $nom_unique;

        if(move_uploaded_file($fichier_tmp, $chemin_final)) {
            if($menu['image'] && file_exists($dossier_upload . $menu['image'])) {
                unlink($dossier_upload . $menu['image']);
            }
            $nom_image = $nom_unique;
        }
    }

    $theme_id = $_POST['theme_id'];
    $regime_id = $_POST['regime_id'];

    if(empty($titre)) {
        $message_erreur = 'Le titre est obligatoire';
    } elseif(empty($prix)) {
        $message_erreur = 'Le prix est obligatoire';
    } elseif(empty($theme_id)) {
        $message_erreur = 'Le thème est obligatoire';
    } elseif(empty($regime_id)) {
        $message_erreur = 'Le régime est obligatoire';
    } else {
        $requete = "UPDATE menus
                    SET titre = :titre,
                        description = :description,
                        prix = :prix,
                        image = :image,
                        theme_id = :theme_id,
                        regime_id = :regime_id
                    WHERE id = :id";
        $preparation = $pdo->prepare($requete);
        $preparation->execute([
            'titre' => $titre,
            'description' => $description,
            'prix' => $prix,
            'image' => $nom_image,
            'theme_id' => $theme_id,
            'regime_id' => $regime_id,
            'id' => $menu_id
        ]);

        $_SESSION['message'] = 'Menu modifié avec succès !';
        header('Location: gestion-menus.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Menu - Admin</title>
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
                <h1 class="text-center">Modifier le Menu</h1>

                <?php if($message_erreur != ''): ?>
                    <div class="alert alert-danger">
                        <?php echo $message_erreur; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="mt-4" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Titre du menu *</label>
                            <input type="text" class="form-control" name="titre"
                                   value="<?php echo htmlspecialchars($menu['titre']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prix en euros *</label>
                            <input type="number" class="form-control" name="prix"
                                   value="<?php echo $menu['prix']; ?>" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($menu['description']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thème *</label>
                            <select class="form-select" name="theme_id" required>
                                <option value="">-- Choisir un thème --</option>
                                <?php foreach($tous_les_themes as $theme): ?>
                                    <option value="<?php echo $theme['id']; ?>"
                                            <?php if($theme['id'] == $menu['theme_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($theme['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Régime alimentaire *</label>
                            <select class="form-select" name="regime_id" required>
                                <option value="">-- Choisir un régime --</option>
                                <?php foreach($tous_les_regimes as $regime): ?>
                                    <option value="<?php echo $regime['id']; ?>"
                                            <?php if($regime['id'] == $menu['regime_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($regime['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image actuelle</label><br>
                        <img src="/uploads/<?php echo htmlspecialchars($menu['image'] ?? ''); ?>"
                             style="width: 150px; height: 100px; object-fit: cover; border-radius: 8px;"
                             alt="Image actuelle"
                             onerror="this.onerror=null; this.src='/images/preparation.jpg';">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Changer l'image (optionnel)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Formats acceptés : JPG, PNG, GIF</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-admin">
                            <i class="bi bi-check-circle"></i> Modifier le menu
                        </button>
                        <a href="gestion-menus.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
