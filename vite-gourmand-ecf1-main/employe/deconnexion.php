<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header('Location: ../public/index.php');
exit;
?>