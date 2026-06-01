<?php

class MenuRepository {
    
    // La connexion à la base de données
    private $pdo;
    
    // Constructeur : on passe la connexion PDO en paramètre
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Récupérer tous les menus
    public function getTousLesMenus() {
        $stmt = $this->pdo->query("SELECT * FROM menus ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    // Récupérer un menu par son id
    public function getMenuParId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM menus WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    // Récupérer les menus par thème
    public function getMenusParTheme($theme_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM menus WHERE theme_id = :theme_id ORDER BY id DESC");
        $stmt->execute([':theme_id' => $theme_id]);
        return $stmt->fetchAll();
    }
    
}
?>