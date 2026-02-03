<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    // Si pas connecté, rediriger vers connexion
    header('Location: connexion.php');
    exit;
}

// Vérifier qu'on a bien un ID de commande dans l'URL
if(!isset($_GET['id'])) {
    // Si pas d'ID, retour à mon compte
    header('Location: mon-compte.php');
    exit;
}

// Récupérer l'ID de la commande à annuler
$commande_id = $_GET['id'];

// Récupérer l'ID de l'utilisateur connecté
$mon_id = $_SESSION['user_id'];

// Connexion à la base de données
require_once '../includes/db.php';

// Vérifier que cette commande appartient bien à cet utilisateur
$requete_verification = "SELECT id, statut 
                         FROM commandes 
                         WHERE id = :commande_id 
                         AND utilisateur_id = :mon_id";

$preparation_verif = $pdo->prepare($requete_verification);
$preparation_verif->execute([
    'commande_id' => $commande_id,
    'mon_id' => $mon_id
]);

$commande = $preparation_verif->fetch();

// Si la commande n'existe pas, retour
if(!$commande) {
    header('Location: mon-compte.php');
    exit;
}

// Si la commande n'est pas en_attente, on ne peut pas l'annuler
if($commande['statut'] != 'en_attente') {
    header('Location: mon-compte.php');
    exit;
}

// Tout est OK, on peut annuler la commande
// On met le statut à "terminee"
$requete_annulation = "UPDATE commandes 
                       SET statut = 'terminee' 
                       WHERE id = :commande_id";

$preparation_annulation = $pdo->prepare($requete_annulation);
$preparation_annulation->execute(['commande_id' => $commande_id]);

// Rediriger vers mon compte
header('Location: mon-compte.php');
exit;
?>