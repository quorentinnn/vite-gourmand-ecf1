<?php
// Connexion à MongoDB (optionnel - fonctionne sans si le driver n'est pas installé)

function isMongoAvailable() {
    return class_exists('MongoDB\Driver\Manager');
}

function getMongoClient() {
    if (!isMongoAvailable()) {
        return null;
    }

    try {
        $mongoUrl = getenv('MONGO_URL') ?: 'mongodb://localhost:27017';
        return new MongoDB\Driver\Manager($mongoUrl);
    } catch (Exception $e) {
        error_log("Erreur MongoDB : " . $e->getMessage());
        return null;
    }
}

// Enregistrer une statistique de commande dans MongoDB
function enregistrerStatCommande($menu_id, $menu_titre, $prix_total, $nombre_personnes, $date_commande) {
    if (!isMongoAvailable()) {
        return false;
    }

    try {
        $client = getMongoClient();
        if (!$client) return false;

        $document = [
            'menu_id' => (int)$menu_id,
            'menu_titre' => $menu_titre,
            'prix_total' => (float)$prix_total,
            'nombre_personnes' => (int)$nombre_personnes,
            'date_commande' => new MongoDB\BSON\UTCDateTime(strtotime($date_commande) * 1000),
            'date_enregistrement' => new MongoDB\BSON\UTCDateTime()
        ];

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
    if (!isMongoAvailable()) {
        return [];
    }

    try {
        $client = getMongoClient();
        if (!$client) return [];

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

        $query = new MongoDB\Driver\Query($filter);
        $cursor = $client->executeQuery('ecf_gourmand.statistiques_commandes', $query);

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
