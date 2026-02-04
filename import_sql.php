<?php
// Script temporaire d'import SQL - À SUPPRIMER après utilisation !

// Lecture du fichier SQL
$sqlFile = __DIR__ . '/ecf_gourmand.sql';
$sql = file_get_contents($sqlFile);

if ($sql === false) {
    die("Erreur : fichier SQL introuvable !");
}

// Connexion à la base
require_once __DIR__ . '/includes/db.php';

try {
    // Exécution du SQL
    $pdo->exec($sql);
    echo "✅ Import réussi ! Base de données créée avec succès.";
    echo "<br><br>⚠️ IMPORTANT : Supprime maintenant le fichier import_sql.php de ton serveur !";
} catch (PDOException $e) {
    echo "❌ Erreur lors de l'import : " . $e->getMessage();
}
?>