<?php 

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../includes/db.php';

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

require_once '../includes/header.php';
// recuperer donnees du formulaire de commande
$menu_id = $_POST['menu_id'];
$quantite = $_POST['quantite'];
$date_livraison = $_POST['date_livraison'];
$heure_livraison = $_POST['heure_livraison'];
$adresse_livraison = $_POST['adresse_livraison'];
$ville_livraison = $_POST['ville_livraison'];
$code_postal_livraison = $_POST['code_postal_livraison'];
// ==================================================
// ÉTAPE 1 : RÉCUPÉRER LE MENU DEPUIS LA BDD
// ==================================================

// Créer la requête SQL
// Le ? sera remplacé par la valeur de $menu_id
$requete_recuperer_menu = "SELECT * FROM menus WHERE id = ?";

// Préparer la requête (pour la sécurité)
$preparation_requete = $pdo->prepare($requete_recuperer_menu);

// Exécuter la requête en remplaçant le ? par $menu_id
$preparation_requete->execute([$menu_id]);

// Récupérer le résultat (le menu)
$menu_trouve = $preparation_requete->fetch();

// ==================================================
// ÉTAPE 2 : CALCULER LE PRIX TOTAL
// ==================================================

// Récupérer le prix du menu
$prix_du_menu = $menu_trouve['prix'];

// Calculer : prix du menu × quantité commandée
$prix_total = $prix_du_menu * $quantite;

// ==================================================
// ÉTAPE : ENREGISTRER LA COMMANDE DANS LA BDD
// ==================================================

// Récupérer l'ID de l'utilisateur connecté
$utilisateur_id = $_SESSION['user_id'];

// Créer la requête SQL pour insérer une commande
$requete_insertion = "INSERT INTO commandes (utilisateur_id, menu_id, nb_personnes, date_livraison, heure_livraison, adresse_livraison, ville_livraison, code_postal_livraison, prix_total, statut)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
// Préparer la requête
$preparation = $pdo->prepare($requete_insertion);

// Définir le statut de la commande
$statut_commande = 'en_attente';


// Exécuter la requête avec les valeurs dans l'ordre
$preparation->execute([
    $utilisateur_id,           // 1er ? → utilisateur_id
    $menu_id,                  // 2e ? → menu_id
    $quantite,                 // 3e ? → nb_personnes
    $date_livraison,           // 4e ? → date_livraison
    $heure_livraison,          // 5e ? → heure_livraison
    $adresse_livraison,        // 6e ? → adresse_livraison
    $ville_livraison,          // 7e ? → ville_livraison
    $code_postal_livraison,    // 8e ? → code_postal_livraison
    $prix_total,               // 9e ? → prix_total
    $statut_commande,          // 10e ? → statut
]);
// Récupérer le titre du menu pour MongoDB
$requete_menu = "SELECT titre FROM menus WHERE id = :menu_id";
$prep_menu = $pdo->prepare($requete_menu);
$prep_menu->execute(['menu_id' => $menu_id]);
$menu = $prep_menu->fetch();

// Enregistrer les statistiques dans MongoDB
if($menu) {
    require_once '../includes/mongodb.php';
    enregistrerStatCommande(
        $menu_id,
        $menu['titre'],
        $prix_total,
        $quantite,  // nombre de personnes
        $date_livraison
    );
}

?>

<!-- SECTION CONFIRMATION COMMANDE -->
<section class="confirmation-section">
    <div class="container2">
        <div class="confirmation-card">
            <!-- En-tête avec logo Vite&Gourmand -->
            <div class="confirmation-header">
                <h1><span class="Vite">Vite</span><span class="Gourmand">&Gourmand</span></h1>
                <p class="sous-titre">traiteur à Bordeaux depuis 25 ans Des menus gourmands pour tous vos évènements</p>
            </div>

            <!-- Message de succès -->
            <div class="success-message">
                <div class="success-icon">✓</div>
                <h2>Commande enregistrée avec succès !</h2>
            </div>

            <!-- Récapitulatif de commande -->
            <div class="recap-card">
                <h3 class="recap-title">Récapitulatif de votre commande</h3>

                <div class="recap-content">
                    <div class="recap-item">
                        <span class="recap-label">Menu</span>
                        <span class="recap-value"><?php echo htmlspecialchars($menu_trouve['titre']); ?></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">Quantité</span>
                        <span class="recap-value"><?php echo htmlspecialchars($quantite); ?> personnes</span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">Date de livraison</span>
                        <span class="recap-value"><?php echo htmlspecialchars($date_livraison); ?></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">Heure de livraison</span>
                        <span class="recap-value"><?php echo htmlspecialchars($heure_livraison); ?></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">Adresse</span>
                        <span class="recap-value"><?php echo htmlspecialchars($adresse_livraison); ?></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">Ville</span>
                        <span class="recap-value"><?php echo htmlspecialchars($ville_livraison); ?></span>
                    </div>

                    <div class="recap-item">
                        <span class="recap-label">Code postal</span>
                        <span class="recap-value"><?php echo htmlspecialchars($code_postal_livraison); ?></span>
                    </div>

                    <div class="recap-item-total">
                        <span class="recap-label-total">Prix total</span>
                        <span class="recap-value-total"><?php echo number_format($prix_total, 0); ?> €</span>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="contact-info">
                <h3>Contact</h3>
                <p class="contact-detail"><strong>123 Rue Principale</strong></p>
                <p class="contact-detail"><strong>33000 Bordeaux</strong></p>
                <p class="contact-detail">Tél: <strong>05 56 00 00 00</strong></p>
                <p class="contact-detail">Email: <strong>contact@vitegourmand.fr</strong></p>
            </div>
        </div>
    </div>
</section>

<style>
.confirmation-section {
    background-color: #FEF5F0;
    padding: 60px 20px;
    min-height: 100vh;
}

.confirmation-card {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.confirmation-header {
    background-color: #8B1538;
    color: white;
    padding: 40px 30px;
    text-align: center;
}

.confirmation-header h1 {
    margin: 0;
    font-size: 42px;
}

.Vite {
    color: #D4AF37;
}

.Gourmand {
    color: #ffffff;
}

.sous-titre {
    margin-top: 10px;
    font-size: 14px;
    opacity: 0.9;
}

.success-message {
    background: linear-gradient(135deg, #D4AF37 0%, #F4D03F 100%);
    padding: 30px;
    text-align: center;
    color: white;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: white;
    color: #D4AF37;
    font-size: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-weight: bold;
}

.success-message h2 {
    margin: 0;
    font-size: 28px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.recap-card {
    padding: 40px 30px;
}

.recap-title {
    color: #8B1538;
    font-size: 24px;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 3px solid #D4AF37;
}

.recap-content {
    background: #FEF5F0;
    padding: 30px;
    border-radius: 8px;
    border-left: 4px solid #D4AF37;
}

.recap-item {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #e0e0e0;
}

.recap-item:last-of-type {
    border-bottom: none;
}

.recap-label {
    font-weight: 600;
    color: #8B1538;
    text-transform: capitalize;
}

.recap-value {
    color: #333;
    text-align: right;
}

.recap-item-total {
    display: flex;
    justify-content: space-between;
    padding: 25px 0 0;
    margin-top: 20px;
    border-top: 3px solid #D4AF37;
}

.recap-label-total {
    font-weight: 700;
    color: #8B1538;
    font-size: 20px;
}

.recap-value-total {
    color: #D4AF37;
    font-size: 28px;
    font-weight: 700;
}

.contact-info {
    background-color: #8B1538;
    color: white;
    padding: 30px;
    text-align: center;
}

.contact-info h3 {
    color: #D4AF37;
    font-size: 22px;
    margin-bottom: 20px;
}

.contact-detail {
    margin: 10px 0;
    font-size: 16px;
}

.contact-detail strong {
    color: #D4AF37;
}

@media (max-width: 768px) {
    .confirmation-header h1 {
        font-size: 32px;
    }

    .success-message h2 {
        font-size: 22px;
    }

    .recap-card {
        padding: 30px 20px;
    }

    .recap-content {
        padding: 20px;
    }

    .recap-item {
        flex-direction: column;
        gap: 5px;
    }

    .recap-value {
        text-align: left;
        font-weight: 600;
    }
}
</style>
<?php include '../includes/footer.php'; ?>

