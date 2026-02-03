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

// Récupérer l'ID du menu à supprimer
$menu_id = $_GET['id'];

// NOUVEAU : Vérifier si des commandes utilisent ce menu
$requete_verif = "SELECT COUNT(*) as nb_commandes FROM commandes WHERE menu_id = :menu_id";
$preparation_verif = $pdo->prepare($requete_verif);
$preparation_verif->execute(['menu_id' => $menu_id]);
$resultat = $preparation_verif->fetch();

// Si des commandes existent pour ce menu
if($resultat['nb_commandes'] > 0) {
    // On ne peut pas supprimer !
    $_SESSION['message_erreur'] = 'Impossible de supprimer ce menu car ' . $resultat['nb_commandes'] . ' commande(s) l\'utilisent.';
    header('Location: gestion-menus.php');
    exit;
}

// Sinon, on peut supprimer
$requete = "DELETE FROM menus WHERE id = :id";
$preparation = $pdo->prepare($requete);
$preparation->execute(['id' => $menu_id]);

// Message de succès
$_SESSION['message'] = 'Menu supprimé avec succès !';
header('Location: gestion-menus.php');
exit;
?>