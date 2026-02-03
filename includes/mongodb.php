<?php
// Connexion à MongoDB
function getMongoClient() {
    try {
        return new MongoDB\Driver\Manager("mongodb://localhost:27017");
    } catch (Exception $e) {
        die("Erreur MongoDB : " . $e->getMessage());
    }
}

// Enregistrer une statistique de commande dans MongoDB
function enregistrerStatCommande($menu_id, $menu_titre, $prix_total, $nombre_personnes, $date_commande) {
    try {
        $client = getMongoClient();
        
        // Préparer les données
        $document = [
            'menu_id' => (int)$menu_id,
            'menu_titre' => $menu_titre,
            'prix_total' => (float)$prix_total,
            'nombre_personnes' => (int)$nombre_personnes,
            'date_commande' => new MongoDB\BSON\UTCDateTime(strtotime($date_commande) * 1000),
            'date_enregistrement' => new MongoDB\BSON\UTCDateTime()
        ];
        
        // Insérer dans MongoDB
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($document);
        
        $client->executeBulkWrite('ecf_gourmand.statistiques_commandes', $bulk);
        
        return true;
    } catch (Exception $e) {
        error_log("Erreur MongoDB : " . $e->getMessage());
        return false;
    }
}

// Récupérer les statistiques pour les graphiques
function getStatistiquesCommandes($menu_id = null, $date_debut = null, $date_fin = null) {
    try {
        $client = getMongoClient();
        
        // Construire le filtre
        $filter = [];
        
        if ($menu_id) {
            $filter['menu_id'] = (int)$menu_id;
        }
        
        if ($date_debut && $date_fin) {
            $filter['date_commande'] = [
                '$gte' => new MongoDB\BSON\UTCDateTime(strtotime($date_debut) * 1000),
                '$lte' => new MongoDB\BSON\UTCDateTime(strtotime($date_fin) * 1000)
            ];
        }
        
        // Créer la requête
        $query = new MongoDB\Driver\Query($filter);
        
        // Exécuter la requête
        $cursor = $client->executeQuery('ecf_gourmand.statistiques_commandes', $query);
        
        // Convertir en tableau
        $resultats = [];
        foreach ($cursor as $document) {
            $resultats[] = [
                'menu_id' => $document->menu_id,
                'menu_titre' => $document->menu_titre,
                'prix_total' => $document->prix_total,
                'nombre_personnes' => $document->nombre_personnes,
                'date_commande' => date('Y-m-d', $document->date_commande->toDateTime()->getTimestamp())
            ];
        }
        
        return $resultats;
        
    } catch (Exception $e) {
        error_log("Erreur MongoDB : " . $e->getMessage());
        return [];
    }
}
?>