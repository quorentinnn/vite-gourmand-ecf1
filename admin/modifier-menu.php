<?php
// Démarrer la session
session_start();

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
    
    // Récupérer le titre
    $titre = $_POST['titre'];
    
    // Récupérer la description
    $description = $_POST['description'];
    
    // Récupérer le prix
    $prix = $_POST['prix'];
    
    // Récupérer le nom de l'image
    // Gérer l'upload de l'image (optionnel)
$nom_image = $menu['image']; // Par défaut, on garde l'ancienne image

// Si une nouvelle image est uploadée
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    
    $fichier_tmp = $_FILES['image']['tmp_name'];
    $nom_fichier = $_FILES['image']['name'];
    
    $extension = pathinfo($nom_fichier, PATHINFO_EXTENSION);
    $nom_unique = uniqid() . '.' . $extension;
    
    $dossier_upload = '../uploads/';
    $chemin_final = $dossier_upload . $nom_unique;
    
    if(move_uploaded_file($fichier_tmp, $chemin_final)) {
        // Supprimer l'ancienne image si elle existe
        if($menu['image'] && file_exists($dossier_upload . $menu['image'])) {
            unlink($dossier_upload . $menu['image']);
        }
        $nom_image = $nom_unique;
    }
}
    
    // Récupérer le theme_id
    $theme_id = $_POST['theme_id'];
    
    // Récupérer le regime_id
    $regime_id = $_POST['regime_id'];
    
    // Vérifier si le titre est vide
    if(empty($titre)) {
        $message_erreur = 'Le titre est obligatoire';
    }
    // Vérifier si le prix est vide
    elseif(empty($prix)) {
        $message_erreur = 'Le prix est obligatoire';
    }
    // Vérifier si le thème est vide
    elseif(empty($theme_id)) {
        $message_erreur = 'Le thème est obligatoire';
    }
    // Vérifier si le régime est vide
    elseif(empty($regime_id)) {
        $message_erreur = 'Le régime est obligatoire';
    }
    // Si tout est OK
    else {
        // Préparer la requête pour MODIFIER le menu
        $requete = "UPDATE menus 
                    SET titre = :titre, 
                        description = :description, 
                        prix = :prix, 
                        image = :image, 
                        theme_id = :theme_id, 
                        regime_id = :regime_id 
                    WHERE id = :id";
        
        // Préparer la requête
        $preparation = $pdo->prepare($requete);
        
        // Exécuter la requête avec les valeurs
        $preparation->execute([
            'titre' => $titre,
            'description' => $description,
            'prix' => $prix,
            'image' => $nom_image,
            'theme_id' => $theme_id,
            'regime_id' => $regime_id,
            'id' => $menu_id
        ]);
        
        // Rediriger vers la page de gestion
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
    <title>Modifier un Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Modifier le Menu</h1>
        
        <!-- Si il y a une erreur, on l'affiche -->
        <?php if($message_erreur != ''): ?>
            <div class="alert alert-danger">
                <?php echo $message_erreur; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire pour modifier le menu -->
        <form method="POST" class="mt-4" enctype="multipart/form-data">
            
            <!-- Champ pour le titre (pré-rempli) -->
            <div class="mb-3">
                <label>Titre du menu *</label>
                <input type="text" 
                       class="form-control" 
                       name="titre" 
                       value="<?php echo $menu['titre']; ?>" 
                       required>
            </div>
            
            <!-- Champ pour la description (pré-rempli) -->
            <div class="mb-3">
                <label>Description</label>
                <textarea class="form-control" name="description" rows="3"><?php echo $menu['description']; ?></textarea>
            </div>
            
            <!-- Champ pour le prix (pré-rempli) -->
            <div class="mb-3">
                <label>Prix en euros *</label>
                <input type="number" 
                       class="form-control" 
                       name="prix" 
                       value="<?php echo $menu['prix']; ?>" 
                       step="0.01" 
                       required>
            </div>
            
            <!-- Champ pour choisir le thème (pré-sélectionné) -->
            <div class="mb-3">
                <label>Thème *</label>
                <select class="form-control" name="theme_id" required>
                    <option value="">-- Choisir un thème --</option>
                    <?php foreach($tous_les_themes as $theme): ?>
                        <option value="<?php echo $theme['id']; ?>" 
                                <?php if($theme['id'] == $menu['theme_id']) echo 'selected'; ?>>
                            <?php echo $theme['nom']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Champ pour choisir le régime (pré-sélectionné) -->
            <div class="mb-3">
                <label>Régime alimentaire *</label>
                <select class="form-control" name="regime_id" required>
                    <option value="">-- Choisir un régime --</option>
                    <?php foreach($tous_les_regimes as $regime): ?>
                        <option value="<?php echo $regime['id']; ?>" 
                                <?php if($regime['id'] == $menu['regime_id']) echo 'selected'; ?>>
                            <?php echo $regime['nom']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

    <!-- Champ pour uploader une nouvelle image -->
<div class="mb-3">
    <label>Image actuelle</label><br>
    <?php if($menu['image'] && file_exists("../uploads/" . $menu['image'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($menu['image']); ?>" style="width: 150px; height: 150px; object-fit: cover;" alt="Image actuelle">
    <?php else: ?>
        <p>Aucune image</p>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label>Changer l'image (optionnel)</label>
    <input type="file" class="form-control" name="image" accept="image/*">
    <small class="text-muted">Formats acceptés : JPG, PNG, GIF</small>
</div>
            
            <!-- Bouton pour envoyer le formulaire -->
            <button type="submit" class="btn btn-success">Ajouter le menu</button>
            
            <!-- Bouton pour annuler -->
            <a href="gestion-menus.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
            
            <!-- Bouton pour envoyer le formulaire -->
            <button type="submit" class="btn btn-warning">Modifier le menu</button>
            
            <!-- Bouton pour annuler -->
            <a href="gestion-menus.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>