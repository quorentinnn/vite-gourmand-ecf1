<?php
require_once '../includes/db.php';

$prix_minimum = isset($_GET['prix_min']) ? $_GET['prix_min'] : '';
$prix_maximum = isset($_GET['prix_max']) ? $_GET['prix_max'] : '';
$theme_choisi = isset($_GET['theme']) ? $_GET['theme'] : '';
$regime_choisi = isset($_GET['regime']) ? $_GET['regime'] : '';
$nombre_personnes = isset($_GET['nb_personnes']) ? $_GET['nb_personnes'] : '';

$requete_sql = "SELECT * FROM menus WHERE 1=1";

if($prix_minimum != '') {
    $requete_sql .= " AND prix >= " . (float)$prix_minimum;
}
if($prix_maximum != '') {
    $requete_sql .= " AND prix <= " . (float)$prix_maximum;
}
if($theme_choisi != '') {
    $requete_sql .= " AND theme_id = " . (int)$theme_choisi;
}
if($regime_choisi != '') {
    $requete_sql .= " AND regime_id = " . (int)$regime_choisi;
}
if($nombre_personnes != '') {
    $requete_sql .= " AND nb_personnes_min <= " . (int)$nombre_personnes;
}

$requete_sql .= " ORDER BY id DESC";

$stmt = $pdo->query($requete_sql);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($menus);