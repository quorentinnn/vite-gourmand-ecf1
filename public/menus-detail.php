<?php include '../includes/header.php'; 

// Connexion à la base de données
require_once '../includes/db.php';

// Récupérer l'ID du menu depuis l'URL
$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Récupérer les infos du menu
$sql = "SELECT menus.*, themes.nom as theme_nom, regimes.nom as regime_nom 
        FROM menus 
        LEFT JOIN themes ON menus.theme_id = themes.id 
        LEFT JOIN regimes ON menus.regime_id = regimes.id 
        WHERE menus.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $menu_id]);
$menu = $stmt->fetch();

// Si le menu n'existe pas, rediriger vers menus.php
if(!$menu) {
    header('Location: menus.php');
    exit;
}

// Récupérer les plats du menu
$sql_plats = "SELECT plats.*, composer.menu_id
              FROM plats 
              INNER JOIN composer ON plats.id = composer.plat_id 
              WHERE composer.menu_id = :menu_id";
$stmt_plats = $pdo->prepare($sql_plats);
$stmt_plats->execute(['menu_id' => $menu_id]);
$plats = $stmt_plats->fetchAll();

// Organiser les plats par catégorie
$entrees = [];
$plats_principaux = [];
$desserts = [];

foreach($plats as $plat) {
    if($plat['categorie'] == 'entree') {
        $entrees[] = $plat;
    } elseif($plat['categorie'] == 'plat') {
        $plats_principaux[] = $plat;
    } elseif($plat['categorie'] == 'dessert') {
        $desserts[] = $plat;
    }
}
?>



    <!-- SECTION DÉTAIL MENU -->
    <section class="menu-detail-section">
        <div class="container2">
            <div class="menu-detail-layout">
                
                <!-- IMAGE GAUCHE -->
               <!-- IMAGE GAUCHE -->
<div class="menu-detail-image">
    <?php if($menu['image'] && file_exists(dirname(__DIR__) . "/uploads/" . $menu['image'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($menu['image']); ?>" alt="<?php echo htmlspecialchars($menu['titre']); ?>">
    <?php else: ?>
        <div class="menu-image-placeholder">
            <span>photo</span>
        </div>
    <?php endif; ?>
</div>

                <!-- INFOS DROITE -->
                <div class="menu-detail-info">
                    <h1 class="menu-detail-title"><?php echo htmlspecialchars($menu['titre']); ?></h1>
                    <span class="badge-theme"><?php echo htmlspecialchars($menu['regime_nom']); ?></span>

                    <p class="menu-detail-description"><?php echo htmlspecialchars($menu['description']); ?></p>

                    <!-- ENCADRÉ INFOS DORÉES -->
                    <div class="info-box">
                        <div class="info-item">
                            <span class="info-label">nombre de personne</span>
                            <span class="info-value"><?php echo htmlspecialchars($menu['nb_personnes_min']); ?> personnes min</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">regime</span>
                            <span class="info-value"><?php echo htmlspecialchars($menu['regime_nom']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">theme</span>
                            <span class="info-value"><?php echo htmlspecialchars($menu['theme_nom']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">delai commande</span>
                            <span class="info-value">48h avant</span>
                        </div>
                    </div>

                    <!-- PRIX -->
                    <div class="menu-prix-section">
                        <p class="menu-prix-grand"><?php echo number_format($menu['prix'], 0); ?>$ <span class="nb_personnes_min">pour <?php echo htmlspecialchars($menu['nb_personnes_min']); ?> personnes</span></p>
                    </div>

                    <!-- FORMULAIRE COMMANDE -->
                    <form method="POST" action="traiter-commande.php" class="form-commander">
                        <div class="form-group">
                            <input type="hidden" name="menu_id" value="<?php echo $menu['id']; ?>">
                            
                            <label for="quantite">Quantité (nombre de personnes)</label>
                            <input type="number" id="quantite" name="quantite" min="<?php echo $menu['nb_personnes_min']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="date_livraison">Date de livraison</label>
                            <input type="date" id="date_livraison" name="date_livraison" required>
                        </div>

                        <div class="form-group">
                            <label for="heure_livraison">Heure de livraison</label>
                            <input type="time" id="heure_livraison" name="heure_livraison" required>
                        </div>

                        <div class="form-group">
                            <label for="adresse_livraison">Adresse de livraison</label>
                            <input type="text" id="adresse_livraison" name="adresse_livraison" placeholder="Numéro et nom de rue" required>
                        </div>

                        <div class="form-group">
                            <label for="code_postal_livraison">Code postal</label>
                            <input type="text" id="code_postal_livraison" name="code_postal_livraison" placeholder="Ex: 75001" required>
                        </div>

                        <div class="form-group">
                            <label for="ville_livraison">Ville</label>
                            <input type="text" id="ville_livraison" name="ville_livraison" placeholder="Nom de la ville" required>
                        </div>

                        <button type="submit" class="btn-commander">Valider la commande</button>
                    </form>

                    <!-- STOCK -->
                    <p class="menu-stock">En stock - <?php echo htmlspecialchars($menu['stock']); ?> menus disponibles</p>
                </div>
                
            </div>
        </div>
    </section>


    <!-- SECTION CONDITIONS IMPORTANTES -->
    <section class="conditions-section">
        <div class="containe3">
            <h2 class="conditions-title">⚠️ Conditions importantes</h2>
            
            <ul class="conditions-list">
                <li>Commande à passer minimum 48 heures avant la prestation</li>
                <li>Produits frais, à consommer dans les 24h après livraison</li>
                <li>Conservation au réfrigérateur entre 0°C et 4°C</li>
                <li>Réchauffage au four recommandé pour les plats chauds</li>
            </ul>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>