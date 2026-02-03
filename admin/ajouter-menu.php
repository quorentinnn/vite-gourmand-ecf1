<?php
// Démarrer la session
session_start();
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
// Récupérer tous les allergènes disponibles
$requete_allergenes = "SELECT id, nom FROM allergenes ORDER BY nom ASC";
$preparation_allergenes = $pdo->prepare($requete_allergenes);
$preparation_allergenes->execute();
$tous_les_allergenes = $preparation_allergenes->fetchAll();
// Récupérer tous les plats disponibles
$requete_plats = "SELECT id, nom FROM plats ORDER BY nom ASC";
$preparation_plats = $pdo->prepare($requete_plats);
$preparation_plats->execute();
$tous_les_plats = $preparation_plats->fetchAll();



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
    
   // Gérer l'upload de l'image
$nom_image = '';

if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    
    $fichier_tmp = $_FILES['image']['tmp_name'];
    $nom_fichier = $_FILES['image']['name'];
    
    $extension = pathinfo($nom_fichier, PATHINFO_EXTENSION);
    $nom_unique = uniqid() . '.' . $extension;
    
    $dossier_upload = '../uploads/';
    $chemin_final = $dossier_upload . $nom_unique;
    
    if(move_uploaded_file($fichier_tmp, $chemin_final)) {
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
        // Préparer la requête pour ajouter le menu
        $requete = "INSERT INTO menus (titre, description, prix, image, theme_id, regime_id) 
                    VALUES (:titre, :description, :prix, :image, :theme_id, :regime_id)";
        
        // Préparer la requête
        $preparation = $pdo->prepare($requete);
        
        // Exécuter la requête avec les valeurs
        $preparation->execute([
            'titre' => $titre,
            'description' => $description,
            'prix' => $prix,
            'image' => $nom_image,
            'theme_id' => $theme_id,
            'regime_id' => $regime_id
        ]);
        
        // Rediriger vers la page de gestion
ajouterMessageSucces('Menu ajouté avec succès !');
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
    <title>Ajouter un Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Ajouter un Nouveau Menu</h1>
        
        <!-- Si il y a une erreur, on l'affiche -->
        <?php if($message_erreur != ''): ?>
            <div class="alert alert-danger">
                <?php echo $message_erreur; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire pour ajouter un menu -->
        <form method="POST" enctype="multipart/form-data" class="mt-4">
            
            <!-- Champ pour le titre -->
            <div class="mb-3">
                <label>Titre du menu *</label>
                <input type="text" class="form-control" name="titre" required>
            </div>
            
            <!-- Champ pour la description -->
            <div class="mb-3">
                <label>Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            
            <!-- Champ pour le prix -->
            <div class="mb-3">
                <label>Prix en euros *</label>
                <input type="number" class="form-control" name="prix" step="0.01" required>
            </div>
            
            <!-- Champ pour choisir le thème -->
            <div class="mb-3">
                <label>Thème *</label>
                <select class="form-control" name="theme_id" required>
                    <option value="">-- Choisir un thème --</option>
                    <?php foreach($tous_les_themes as $theme): ?>
                        <option value="<?php echo $theme['id']; ?>">
                            <?php echo $theme['nom']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Champ pour choisir le régime -->
            <div class="mb-3">
                <label>Régime alimentaire *</label>
                <select class="form-control" name="regime_id" required>
                    <option value="">-- Choisir un régime --</option>
                    <?php foreach($tous_les_regimes as $regime): ?>
                        <option value="<?php echo $regime['id']; ?>">
                            <?php echo $regime['nom']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Champ pour choisir le allergene -->
              <div class="mb-3">
                <label>Régime allergene *</label>
                <select class="form-control" name="allergene_id" required>
                    <option value="">-- Choisir un allergène --</option>
                    <?php foreach($tous_les_allergenes as $allergene): ?>
                        <option value="<?php echo $allergene['id']; ?>">
                            <?php echo $allergene['nom']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>plat *</label>
                <select class="form-control" name="plat_id" required>
                    <option value="">-- Choisir un plat --</option>
                    <?php foreach($tous_les_plats as $plat): ?>
                        <option value="<?php echo $plat['id']; ?>">
                            <?php echo $plat['nom']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Champ pour uploader une image -->
<div class="mb-3">
    <label>Image du menu</label>
    <input type="file" class="form-control" name="image" accept="image/*">
    <small class="text-muted">Formats acceptés : JPG, PNG, GIF</small>
</div>
            
            <!-- Bouton pour envoyer le formulaire -->
            <button type="submit" class="btn btn-success">Ajouter le menu</button>
            
            <!-- Bouton pour annuler -->
            <a href="gestion-menus.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>