<?php
// ============================================================
// PAGE STATISTIQUES - Admin
// Affiche les statistiques des commandes
// ============================================================

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

// Vérifier si c'est un admin
if($_SESSION['user_role'] != 'admin') {
    header('Location: ../public/connexion.php');
    exit;
}

// Connexion à la base de données
require_once '../includes/db.php';

// ============================================================
// RECUPERER LES MENUS (pour le filtre)
// ============================================================
$requete_menus = "SELECT id, titre FROM menus ORDER BY titre";
$preparation_menus = $pdo->prepare($requete_menus);
$preparation_menus->execute();
$tous_les_menus = $preparation_menus->fetchAll();

// ============================================================
// RECUPERER LES FILTRES
// ============================================================
$menu_id_filtre = isset($_GET['menu_id']) ? $_GET['menu_id'] : '';
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';

// ============================================================
// STATISTIQUES GLOBALES
// ============================================================
$sql_global = "SELECT
    COUNT(*) as total_commandes,
    COALESCE(SUM(prix_total), 0) as ca_total,
    COALESCE(SUM(nb_personnes), 0) as total_personnes,
    COALESCE(AVG(prix_total), 0) as panier_moyen
FROM commandes";

$preparation_global = $pdo->prepare($sql_global);
$preparation_global->execute();
$stats_globales = $preparation_global->fetch();

// ============================================================
// STATISTIQUES PAR MENU
// ============================================================
$sql_par_menu = "SELECT
    menus.titre as menu_titre,
    COUNT(commandes.id) as nb_commandes,
    SUM(commandes.prix_total) as ca_total,
    SUM(commandes.nb_personnes) as total_personnes
FROM commandes
JOIN menus ON commandes.menu_id = menus.id
GROUP BY menus.id, menus.titre
ORDER BY nb_commandes DESC";

$preparation_par_menu = $pdo->prepare($sql_par_menu);
$preparation_par_menu->execute();
$stats_par_menu = $preparation_par_menu->fetchAll();

// ============================================================
// STATISTIQUES PAR STATUT
// ============================================================
$sql_statuts = "SELECT statut, COUNT(*) as nombre FROM commandes GROUP BY statut";
$preparation_statuts = $pdo->prepare($sql_statuts);
$preparation_statuts->execute();
$stats_statuts = $preparation_statuts->fetchAll();

// Noms des statuts en français
$noms_statuts = [
    'en_attente' => 'En attente',
    'acceptee' => 'Acceptée',
    'en_preparation' => 'En préparation',
    'en_livraison' => 'En livraison',
    'livree' => 'Livrée',
    'terminee' => 'Terminée'
];

// Couleurs des badges
$couleurs_statuts = [
    'en_attente' => 'bg-warning text-dark',
    'acceptee' => 'bg-info',
    'en_preparation' => 'bg-primary',
    'en_livraison' => 'bg-success',
    'livree' => 'bg-secondary',
    'terminee' => 'bg-secondary'
];

// ============================================================
// PREPARER LES DONNEES POUR LES GRAPHIQUES
// ============================================================
$labels = [];
$donnees_commandes = [];
$donnees_ca = [];

foreach($stats_par_menu as $stat) {
    $labels[] = $stat['menu_titre'];
    $donnees_commandes[] = (int)$stat['nb_commandes'];
    $donnees_ca[] = (float)$stat['ca_total'];
}

// Convertir en JSON pour JavaScript
$labels_json = json_encode($labels);
$donnees_commandes_json = json_encode($donnees_commandes);
$donnees_ca_json = json_encode($donnees_ca);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Nos styles -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/admin.css">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="admin-dashboard">
        <div class="container py-4">
            <div class="dashboard-card">

                <!-- TITRE -->
                <h1 class="text-center">Statistiques</h1>
                <p class="text-muted mb-4 text-center">Vue d'ensemble des commandes</p>

                <!-- ============================================ -->
                <!-- CARTES STATISTIQUES                          -->
                <!-- ============================================ -->
                <div class="row mb-4 justify-content-center">

                    <!-- Carte 1: Total commandes -->
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-value">
                                <?php echo $stats_globales['total_commandes']; ?>
                            </div>
                            <div class="stat-label">Commandes</div>
                        </div>
                    </div>

                    <!-- Carte 2: Chiffre d'affaires -->
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card gold">
                            <div class="stat-value">
                                <?php echo number_format($stats_globales['ca_total'], 0, ',', ' '); ?> €
                            </div>
                            <div class="stat-label">Chiffre d'affaires</div>
                        </div>
                    </div>

                    <!-- Carte 3: Personnes servies -->
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card blue">
                            <div class="stat-value">
                                <?php echo $stats_globales['total_personnes']; ?>
                            </div>
                            <div class="stat-label">Personnes servies</div>
                        </div>
                    </div>

                    <!-- Carte 4: Panier moyen -->
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card green">
                            <div class="stat-value">
                                <?php echo number_format($stats_globales['panier_moyen'], 0, ',', ' '); ?> €
                            </div>
                            <div class="stat-label">Panier moyen</div>
                        </div>
                    </div>

                </div>

                <!-- ============================================ -->
                <!-- REPARTITION PAR STATUT                       -->
                <!-- ============================================ -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">Répartition par statut</h3>
                    </div>
                    <div class="card-body text-center">

                        <?php if(empty($stats_statuts)): ?>
                            <p class="text-muted">Aucune commande</p>
                        <?php else: ?>

                            <?php foreach($stats_statuts as $stat): ?>
                                <?php
                                // Récupérer le nom et la couleur du statut
                                $statut = $stat['statut'];
                                $nom = isset($noms_statuts[$statut]) ? $noms_statuts[$statut] : $statut;
                                $couleur = isset($couleurs_statuts[$statut]) ? $couleurs_statuts[$statut] : 'bg-light text-dark';
                                ?>
                                <span class="status-badge <?php echo $couleur; ?>">
                                    <?php echo $nom; ?>: <strong><?php echo $stat['nombre']; ?></strong>
                                </span>
                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>
                </div>

                <!-- ============================================ -->
                <!-- GRAPHIQUES                                   -->
                <!-- ============================================ -->
                <?php if(!empty($stats_par_menu)): ?>
                <div class="row mb-4">

                    <!-- Graphique 1: Commandes par menu -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h3 class="mb-0">Commandes par menu</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="graphiqueCommandes"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique 2: CA par menu -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h3 class="mb-0">CA par menu</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="graphiqueCA"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php endif; ?>

                <!-- ============================================ -->
                <!-- TABLEAU DETAILLE                             -->
                <!-- ============================================ -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">Détails par menu</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">Commandes</th>
                                        <th class="text-center">Personnes</th>
                                        <th class="text-end">CA Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php if(empty($stats_par_menu)): ?>
                                        <!-- Aucune donnée -->
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <p class="text-muted">Aucune commande</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>

                                        <!-- Afficher chaque menu -->
                                        <?php foreach($stats_par_menu as $stat): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($stat['menu_titre']); ?></strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?php echo $stat['nb_commandes']; ?></span>
                                                </td>
                                                <td class="text-center"><?php echo $stat['total_personnes']; ?></td>
                                                <td class="text-end">
                                                    <strong><?php echo number_format($stat['ca_total'], 2, ',', ' '); ?> €</strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <!-- Ligne TOTAL -->
                                        <tr class="table-dark">
                                            <td><strong>TOTAL</strong></td>
                                            <td class="text-center"><strong><?php echo $stats_globales['total_commandes']; ?></strong></td>
                                            <td class="text-center"><strong><?php echo $stats_globales['total_personnes']; ?></strong></td>
                                            <td class="text-end"><strong><?php echo number_format($stats_globales['ca_total'], 2, ',', ' '); ?> €</strong></td>
                                        </tr>

                                    <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Bouton retour -->
                <a href="index.php" class="btn btn-secondary mt-3">Retour au dashboard</a>

            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SCRIPT POUR LES GRAPHIQUES                   -->
    <!-- ============================================ -->
    <?php if(!empty($stats_par_menu)): ?>

    <!-- Charger Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // Attendre que la page soit chargée
        document.addEventListener('DOMContentLoaded', function() {

            // Données depuis PHP
            var labels = <?php echo $labels_json; ?>;
            var donneesCommandes = <?php echo $donnees_commandes_json; ?>;
            var donneesCA = <?php echo $donnees_ca_json; ?>;

            // Couleurs pour les graphiques
            var couleurs = [
                'rgba(139, 21, 56, 0.8)',   // Bordeaux
                'rgba(212, 175, 55, 0.8)',  // Or
                'rgba(54, 162, 235, 0.8)',  // Bleu
                'rgba(75, 192, 192, 0.8)',  // Turquoise
                'rgba(153, 102, 255, 0.8)', // Violet
                'rgba(255, 159, 64, 0.8)',  // Orange
                'rgba(255, 99, 132, 0.8)',  // Rose
                'rgba(46, 204, 113, 0.8)'   // Vert
            ];

            // ----------------------------------------
            // GRAPHIQUE 1: Commandes (barres)
            // ----------------------------------------
            var ctx1 = document.getElementById('graphiqueCommandes');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Commandes',
                        data: donneesCommandes,
                        backgroundColor: couleurs,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // ----------------------------------------
            // GRAPHIQUE 2: Chiffre d'affaires (donut)
            // ----------------------------------------
            var ctx2 = document.getElementById('graphiqueCA');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: donneesCA,
                        backgroundColor: couleurs,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });

        });
    </script>

    <?php endif; ?>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
