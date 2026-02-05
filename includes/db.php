<?php

// Paramètres de connexion
// En local (XAMPP) ou en production (Railway)
// Railway utilise MYSQLHOST, MYSQLDATABASE, etc. (sans underscore)
$host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
$dbname = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'ecf_gourmand';
$username = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';
$port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: '3306';

try {
    // Création de la connexion PDO
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configuration PDO pour afficher les erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configuration pour récupérer les résultats en tableau associatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // En cas d'erreur de connexion
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>