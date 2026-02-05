<?php include '../includes/header.php';
// Connexion à la base de données
require_once '../includes/db.php';
$requete_avis = "SELECT avis.note, 
                        avis.commentaire, 
                        avis.cree_le,
                        utilisateurs.prenom, 
                        utilisateurs.nom
                 FROM avis
                 JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
                 WHERE avis.valide = 1
                 ORDER BY avis.cree_le DESC
                 LIMIT 3";

$preparation_avis = $pdo->prepare($requete_avis);
$preparation_avis->execute();
$tous_les_avis = $preparation_avis->fetchAll();
?>


    <main>
        <div class="Bienvenue-container">
        <h1 class="Bienvenue">Bienvenue</h1>
        <h2 class="Accompagnement"><span>Vite & Gourmand</span> <br>vous accompagne dans votre evenement</h2>
        <H2 class="evenement">Noël • Pâques • Anniversaires • Mariages • Événements professionnels</H2>
        <a href="menus.php" class="btn-menu">Decouvrir menu</a>
        </div>
    </main>


    <section class='Qui-sommes-nous'>
        <h2 class="title-QSN">Qui somme nous ?</h2>
<!-- les cartes des membres de l'équipe -->
        <div class="team-cards">
            <div class="card">
                <h3 class="card-name">Julia</h3>
                <h4 class="card-title">Chef Culinaire & Co-fondatrice</h4>
                <p class="card-text">
                    Passionnée de gastronomie depuis l'enfance, Julia a transformé son amour de la cuisine en
                    carrière lumineuse. Depuis 25 ans, elle compose des plats savoureux alliant tradition et créativité,
                    incarnant la générosité du terroir et du savoir-faire.
                </p>
            </div>

            <div class="card">
                <h3 class="card-name">José</h3>
                <h4 class="card-title">Responsable Logistique & Co-fondateur</h4>
                <p class="card-text">
                    José, c'est la garantie d'événements qui se déroulent sans encoche. Avec 26 ans d'expérience,
                    il connaît tous les défis d'une livraison réussie : respect des horaires, présentation soignée,
                    gestion du matériel.
                </p>
            </div>
        </div>
    </section>

<!-- Section des plats vedettes -->
    <section class="vedettes">
        <h2 class="title-vedettes">Nos Menus Vedettes</h2>
        <div class="vedettes-container">
            <a href="menus-detail.php?id=3" class="vedette-card">
                <img src="../images/poulet.webp" alt="Menu festif" class="vedette-image">
                <div class="vedette-overlay">
                    <span class="vedette-text">Découvrir nos menus</span>
                </div>
            </a>
            <a href="menus-detail.php?id=8" class="vedette-card">
                <img src="../images/salade.jpg" alt="Menu spécial" class="vedette-image">
                <div class="vedette-overlay">
                    <span class="vedette-text">Découvrir nos menus</span>
                </div>
            </a>
        </div>
    </section>


    <!-- Section Expérience -->
    <section class="experience-section">
        <h2 class="experience-title">25ans d'experience</h2>
        <div class="experience-content">
            <div class="experience-image">
                <img src="../images/preparation.jpg" alt="Chef en cuisine">
            </div>
            <div class="experience-text">
                <p class="experience-description">
                    Fondée en 1999 par Julie et José, Vite & Gourmand <span class="highlight">est bien plus qu'une entreprise de traiteur</span>, c'est un partenaire de confiance pour la gastronomie et d'engagement envers nos clients bordelais.
                </p>
                <p class="experience-description">
                    Avec un quart de siècle d'expérience, nous avons développé une expertise unique dans la création de menus pour tous les événements de la vie : fêtes traditionnelles comme Noël et Pâques, célébrations privées (anniversaires, mariages), et événements professionnels (séminaires, inaugurations).
                </p>
                <p class="experience-description">
                    Julie, notre chef cuisinière, compose chaque menu avec créativité et rigueur. José coordonne chaque prestation avec une attention méticuleuse, de la commande à la livraison.
                </p>
            </div>
        </div>
</section>

    <!-- Section des avis clients -->
<section class="container-avis">
    <h2 class="text-center mb-4">Ils ont testé nos menus !</h2>
    
    <div class="row">
        <?php foreach($tous_les_avis as $avis): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <!-- Étoiles -->
                    <div class="mb-2">
                        <?php
                        // Afficher les étoiles selon la note
                        $note = $avis['note'];
                        for($i = 1; $i <= 5; $i++) {
                            if($i <= $note) {
                                echo '⭐'; // Étoile pleine
                            } else {
                                echo '☆'; // Étoile vide
                            }
                        }
                        ?>
                        <strong>(<?php echo $note; ?>/5)</strong>
                    </div>
                    
                    <!-- Commentaire -->
                    <p class="card-text">"<?php echo $avis['commentaire']; ?>"</p>
                    
                    <!-- Nom du client -->
                    <p class="text-muted mb-0">
                        <small>- <?php echo $avis['prenom']; ?> <?php echo substr($avis['nom'], 0, 1); ?>.</small>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Footer -->
 <?php include '../includes/footer.php'; ?>
    