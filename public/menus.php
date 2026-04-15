<?php require_once '../includes/db.php'; ?>
<?php require_once '../includes/header.php'; ?>


<?php 
// Se connecter à la base de données

// ========================================
// ÉTAPE 1 : RÉCUPÉRER LES VALEURS DU FORMULAIRE
// ========================================

// Créer des variables vides au départ
$prix_minimum = '';
$prix_maximum = '';
$theme_choisi = '';
$regime_choisi = '';
$nombre_personnes = '';

// Si l'utilisateur a rempli le prix minimum
if(isset($_GET['prix_min'])) {
    $prix_minimum = $_GET['prix_min'];
}

// Si l'utilisateur a rempli le prix maximum
if(isset($_GET['prix_max'])) {
    $prix_maximum = $_GET['prix_max'];
}

// Si l'utilisateur a choisi un thème
if(isset($_GET['theme'])) {
    $theme_choisi = $_GET['theme'];
}

// Si l'utilisateur a choisi un régime
if(isset($_GET['regime'])) {
    $regime_choisi = $_GET['regime'];
}

// Si l'utilisateur a rempli le nombre de personnes
if(isset($_GET['nb_personnes'])) {
    $nombre_personnes = $_GET['nb_personnes'];
}

// ========================================
// ÉTAPE 2 : CONSTRUIRE LA REQUÊTE SQL
// ========================================

// Commencer la requête de base (afficher tous les menus)
$requete_sql = "SELECT * FROM menus WHERE 1=1";

// ========================================
// ÉTAPE 3 : AJOUTER LES FILTRES UN PAR UN
// ========================================

// --- FILTRE PRIX MINIMUM ---
if($prix_minimum != '') {
    // Créer le morceau à ajouter
    $morceau_a_ajouter = " AND prix >= ";
    $morceau_a_ajouter = $morceau_a_ajouter . $prix_minimum;
    
    // L'ajouter à la requête
    $requete_sql = $requete_sql . $morceau_a_ajouter;
}

// --- FILTRE PRIX MAXIMUM ---
if($prix_maximum != '') {
    // Créer le morceau à ajouter
    $morceau_a_ajouter = " AND prix <= ";
    $morceau_a_ajouter = $morceau_a_ajouter . $prix_maximum;
    
    // L'ajouter à la requête
    $requete_sql = $requete_sql . $morceau_a_ajouter;
}

// --- FILTRE THÈME ---
if($theme_choisi != '') {
    // Créer le morceau à ajouter
    $morceau_a_ajouter = " AND theme_id = ";
    $morceau_a_ajouter = $morceau_a_ajouter . $theme_choisi;
    
    // L'ajouter à la requête
    $requete_sql = $requete_sql . $morceau_a_ajouter;
}

// --- FILTRE RÉGIME ---
if($regime_choisi != '') {
    // Créer le morceau à ajouter
    $morceau_a_ajouter = " AND regime_id = ";
    $morceau_a_ajouter = $morceau_a_ajouter . $regime_choisi;
    
    // L'ajouter à la requête
    $requete_sql = $requete_sql . $morceau_a_ajouter;
}

// --- FILTRE NOMBRE DE PERSONNES ---
if($nombre_personnes != '') {
    // Créer le morceau à ajouter
    $morceau_a_ajouter = " AND nb_personnes_min <= ";
    $morceau_a_ajouter = $morceau_a_ajouter . $nombre_personnes;
    
    // L'ajouter à la requête
    $requete_sql = $requete_sql . $morceau_a_ajouter;
}


// AJOUTER LE TRI (du plus récent au plus ancien)

$requete_sql = $requete_sql . " ORDER BY id DESC";

// ========================================
// ÉTAPE 5 : EXÉCUTER LA REQUÊTE ET RÉCUPÉRER LES MENUS
// ========================================
$stmt = $pdo->query($requete_sql);
$menus = $stmt->fetchAll();
?>


    <!-- SECTION TITRE -->
    <section class="menus-hero">
        <h1 class="menus-title">Decouvrez nos menus</h1>
        <p class="menus-subtitle">Des créations culinaires pour tous vos événements</p>
    </section>

    <!-- SECTION FILTRES -->
    <section class="filtres-section">
        <div class="container2">
            <h2 class="filtres-title">filtrer les menus</h2>
            
            <form class="filtres-form" method="GET" action="menus.php">
                <div class="filtres-inputs">
                    <div class="filtre-group">
                        <label>prix max</label>
                        <input type="number" name="prix_max" placeholder="ex: 4" class="filtre-input">
                    </div>
                    
                    <div class="filtre-group">
                        <label>prix min</label>
                        <input type="number" name="prix_min" placeholder="ex: 4" class="filtre-input">
                    </div>
                    
                    <div class="filtre-group">
                        <label>theme</label>
                        <select name="theme" class="filtre-select">
                            <option value="">tous les theme</option>
                            <option value="1">Classique</option>
                            <option value="2">Noël</option>
                            <option value="3">Pâques</option>
                            <option value="4">Été</option>
                            <option value="5">Gastronomique</option>
                            <option value="6">Végétarien</option>
                        </select>
                    </div>
                    
                    <div class="filtre-group">
                        <label>regime</label>
                        <select name="regime" class="filtre-select">
                            <option value="">tous les regimes</option>
                            <option value="1">Classique</option>
                            <option value="2">Végétarien</option>
                            <option value="3">Vegan</option>
                            <option value="4">Sans gluten</option>
                        </select>
                    </div>
                    
                    <div class="filtre-group">
                        <label>nombre personne</label>
                        <input type="number" name="nb_personnes" placeholder="ex: 4" class="filtre-input">
                    </div>
                </div>
                
                <div class="filtres-buttons">
                    <button type="submit" class="btn-filtrer">appliquer les filtres</button>
                    <a href="menus.php" class="btn-reinitialiser">Reinitialiser</a>
                </div>
            </form>
        </div>
    </section>

    <!-- SECTION GRILLE DE MENUS -->
    <section class="menus-grid-section">
        <div class="container2">
            <div class="menus-grid">
                
                <!-- CARTE MENU 1 -->
                 <?php foreach ($menus as $menu) : ?>
                <div class="menu-card">
                    <div class="menu-image">
                 <img src="/images/<?php echo htmlspecialchars($menu['image'] ?? ''); ?>"
                      alt="<?php echo htmlspecialchars($menu['titre']); ?>"
                      class="menu-image"
                      onerror="this.onerror=null; this.src='/images/preparation.jpg';">
             </div>
                    <div class="menu-content">
                        <h3 class="menu-name"><?php echo htmlspecialchars($menu['titre']); ?></h3>
                        <p class="menu-description"><?php echo htmlspecialchars($menu['description']); ?></p>
                        <p class="menu-min-personnes"><?php echo htmlspecialchars($menu['nb_personnes_min']); ?> personnes min</p>
                        <p class="menu-prix"><?php echo htmlspecialchars($menu['prix']); ?>e / <?php echo htmlspecialchars($menu['nb_personnes_min']); ?> personne</p>
                        <a href="menus-detail.php?id=<?php echo htmlspecialchars($menu['id']); ?>" class="btn-detail">voir le détail</a>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <script>
document.querySelector('.filtres-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    var params = new URLSearchParams(formData).toString();

    fetch('api_menus.php?' + params)
        .then(function(response) {
            return response.json();
        })
        .then(function(menus) {
            var grid = document.querySelector('.menus-grid');
            grid.innerHTML = '';

            if(menus.length === 0) {
                grid.innerHTML = '<p>Aucun menu trouvé.</p>';
                return;
            }

            menus.forEach(function(menu) {
                var image = menu.image ? menu.image : 'preparation.jpg';
                var carte = '<div class="menu-card">';
                carte += '<div class="menu-image"><img src="/images/' + image + '" alt="' + menu.titre + '" onerror="this.src=\'/images/preparation.jpg\'"></div>';
                carte += '<div class="menu-content">';
                carte += '<h3 class="menu-name">' + menu.titre + '</h3>';
                carte += '<p class="menu-description">' + menu.description + '</p>';
                carte += '<p class="menu-min-personnes">' + menu.nb_personnes_min + ' personnes min</p>';
                carte += '<p class="menu-prix">' + menu.prix + 'e / ' + menu.nb_personnes_min + ' personne</p>';
                carte += '<a href="menus-detail.php?id=' + menu.id + '" class="btn-detail">voir le détail</a>';
                carte += '</div></div>';
                grid.innerHTML += carte;
            });
        })
        .catch(function(erreur) {
            console.log('Erreur fetch : ' + erreur);
        });
});
</script>

<?php include '../includes/footer.php'; ?>