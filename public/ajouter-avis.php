<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Connexion à la base de données
require_once '../includes/db.php';

// Vérifier qu'on a bien un ID de commande
if(!isset($_GET['id'])) {
    header('Location: mon-compte.php');
    exit;
}

// Récupérer l'ID de la commande
$commande_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Vérifier que la commande appartient bien à cet utilisateur ET qu'elle est livrée
$requete_verif = "SELECT id, statut FROM commandes WHERE id = :id AND utilisateur_id = :user_id";
$preparation_verif = $pdo->prepare($requete_verif);
$preparation_verif->execute(['id' => $commande_id, 'user_id' => $user_id]);
$commande = $preparation_verif->fetch();

// Si la commande n'existe pas ou n'est pas livrée
if(!$commande) {
    header('Location: mon-compte.php');
    exit;
}

if($commande['statut'] != 'livree') {
    header('Location: mon-compte.php');
    exit;
}

// Variable pour le message d'erreur
$message_erreur = '';

// Si le formulaire a été envoyé
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Récupérer la note
    $note = $_POST['note'];
    
    // Récupérer le commentaire
    $commentaire = $_POST['commentaire'];
    
    // Vérifier si la note est vide
    if(empty($note)) {
        $message_erreur = 'La note est obligatoire';
    }
    // Vérifier si le commentaire est vide
    elseif(empty($commentaire)) {
        $message_erreur = 'Le commentaire est obligatoire';
    }
    // Si tout est OK
    else {
        // Insérer l'avis dans la base de données
        $requete = "INSERT INTO avis (utilisateur_id, commande_id, note, commentaire, valide) 
                    VALUES (:utilisateur_id, :commande_id, :note, :commentaire, 1)";
        
        $preparation = $pdo->prepare($requete);
        $preparation->execute([
            'utilisateur_id' => $user_id,
            'commande_id' => $commande_id,
            'note' => $note,
            'commentaire' => $commentaire
        ]);
        
        // Rediriger vers mon compte
        $_SESSION['message'] = 'Merci pour votre avis !';
header('Location: mon-compte.php');
exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laisser un avis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Laisser un avis</h1>
        
        <p>Partagez votre expérience avec Vite & Gourmand !</p>
        
        <!-- Si il y a une erreur, on l'affiche -->
        <?php if($message_erreur != ''): ?>
            <div class="alert alert-danger">
                <?php echo $message_erreur; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire pour laisser un avis -->
        <form method="POST" class="mt-4">
            
            <!-- Champ pour la note -->
            <div class="mb-3">
                <label>Note sur 5 *</label>
                <select class="form-control" name="note" required>
                    <option value="">-- Choisir une note --</option>
                    <option value="5">⭐⭐⭐⭐⭐ (5/5 - Excellent)</option>
                    <option value="4">⭐⭐⭐⭐ (4/5 - Très bien)</option>
                    <option value="3">⭐⭐⭐ (3/5 - Bien)</option>
                    <option value="2">⭐⭐ (2/5 - Moyen)</option>
                    <option value="1">⭐ (1/5 - Mauvais)</option>
                </select>
            </div>
            
            <!-- Champ pour le commentaire -->
            <div class="mb-3">
                <label>Votre commentaire *</label>
                <textarea class="form-control" name="commentaire" rows="5" required></textarea>
            </div>
            
            <!-- Boutons -->
            <button type="submit" class="btn btn-success">Publier mon avis</button>
            <a href="mon-compte.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>