<?php
require_once '../includes/db.php';

$requete = "SELECT avis.note,
                   avis.commentaire,
                   utilisateurs.nom,
                   utilisateurs.prenom
            FROM avis
            JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
            WHERE avis.valide = 1
            ORDER BY avis.cree_le DESC";

$preparation = $pdo->prepare($requete);
$preparation->execute();
$avis = $preparation->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($avis);
