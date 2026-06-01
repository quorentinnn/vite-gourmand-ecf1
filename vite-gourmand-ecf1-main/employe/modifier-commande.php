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

// Vérifier qu'on a bien un ID et une action
if(!isset($_GET['id'])) {
    header('Location: commandes.php');
    exit;
}

if(!isset($_GET['action'])) {
    header('Location: commandes.php');
    exit;
}

$commande_id = $_GET['id'];
$action = $_GET['action'];

// Connexion BDD
require_once '../includes/db.php';

// Déterminer le nouveau statut selon l'action
$nouveau_statut = '';

if($action == 'accepter') {
    $nouveau_statut = 'acceptee';
}
elseif($action == 'refuser') {
    $nouveau_statut = 'terminee';
}
elseif($action == 'preparer') {
    $nouveau_statut = 'en_preparation';
}
elseif($action == 'prete') {
    $nouveau_statut = 'en_livraison';
}
elseif($action == 'livrer') {
    $nouveau_statut = 'livree';
}
else {
    // Action non valide
    header('Location: commandes.php');
    exit;
}

// Mettre à jour le statut
$requete = "UPDATE commandes SET statut = :statut WHERE id = :id";
$preparation = $pdo->prepare($requete);
$preparation->execute([
    'statut' => $nouveau_statut,
    'id' => $commande_id
]);

// Rediriger vers la page des commandes
header('Location: commandes.php');
exit;
?>