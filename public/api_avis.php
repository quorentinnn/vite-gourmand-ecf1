<?php
require_once '../includes/db.php';

$requete = "SELECT avis.note, avis.commentaire, utilisateurs.nom 
            FROM avis 
            JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id 
            WHERE avis.valide = 1 
            ORDER BY avis.cree_le DESC 
            LIMIT 3";

$preparation = $pdo->prepare($requete);
$preparation->execute();
$tous_les_avis = $preparation->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($tous_les_avis);
?>
