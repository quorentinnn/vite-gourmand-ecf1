<?php
try {
    // Se connecter à MongoDB
    $client = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    
    // Tester la connexion
    $command = new MongoDB\Driver\Command(['ping' => 1]);
    $client->executeCommand('admin', $command);
    
    echo "✅ Connexion à MongoDB réussie !";
    
} catch (Exception $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage();
}
?>