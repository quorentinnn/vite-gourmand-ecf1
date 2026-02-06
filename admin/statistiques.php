<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

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

// Récupérer tous les menus pour le filtre
$requete_menus = "SELECT id, titre FROM menus ORDER BY titre ASC";
$prep_menus = $pdo->prepare($requete_menus);
$prep_menus->execute();
$tous_les_menus = $prep_menus->fetchAll();

// Gérer les filtres
$menu_id_filtre = isset($_GET['menu_id']) ? $_GET['menu_id'] : null;
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : null;
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : null;

// Construire la requête avec filtres
$sql = "SELECT
            menus.titre as menu_titre,
            COUNT(commandes.id) as nb_commandes,
            SUM(commandes.prix_total) as ca_total,
            SUM(commandes.nb_personnes) as total_personnes
        FROM commandes
        JOIN menus ON commandes.menu_id = menus.id
        WHERE 1=1";

$params = [];

if ($menu_id_filtre) {
    $sql .= " AND commandes.menu_id = :menu_id";
    $params['menu_id'] = $menu_id_filtre;
}

if ($date_debut) {
    $sql .= " AND commandes.date_livraison >= :date_debut";
    $params['date_debut'] = $date_debut;
}

if ($date_fin) {
    $sql .= " AND commandes.date_livraison <= :date_fin";
    $params['date_fin'] = $date_fin;
}

$sql .= " GROUP BY menus.id, menus.titre ORDER BY nb_commandes DESC";

$prep = $pdo->prepare($sql);
$prep->execute($params);
$statistiques = $prep->fetchAll();

// Statistiques globales
$sql_global = "SELECT
                COUNT(*) as total_commandes,
                COALESCE(SUM(prix_total), 0) as ca_total,
                COALESCE(SUM(nb_personnes), 0) as total_personnes,
                COALESCE(AVG(prix_total), 0) as panier_moyen
               FROM commandes";

if ($menu_id_filtre || $date_debut || $date_fin) {
    $sql_global .= " WHERE 1=1";
    if ($menu_id_filtre) $sql_global .= " AND menu_id = :menu_id";
    if ($date_debut) $sql_global .= " AND date_livraison >= :date_debut";
    if ($date_fin) $sql_global .= " AND date_livraison <= :date_fin";
}

$prep_global = $pdo->prepare($sql_global);
$prep_global->execute($params);
$stats_globales = $prep_global->fetch();

// Commandes par statut
$sql_statuts = "SELECT statut, COUNT(*) as nb FROM commandes";
if ($menu_id_filtre || $date_debut || $date_fin) {
    $sql_statuts .= " WHERE 1=1";
    if ($menu_id_filtre) $sql_statuts .= " AND menu_id = :menu_id";
    if ($date_debut) $sql_statuts .= " AND date_livraison >= :date_debut";
    if ($date_fin) $sql_statuts .= " AND date_livraison <= :date_fin";
}
$sql_statuts .= " GROUP BY statut";

$prep_statuts = $pdo->prepare($sql_statuts);
$prep_statuts->execute($params);
$stats_statuts = $prep_statuts->fetchAll(PDO::FETCH_KEY_PAIR);

// Préparer les données pour Chart.js
$labels = [];
$data_commandes = [];
$data_ca = [];

foreach($statistiques as $stat) {
    $labels[] = $stat['menu_titre'];
    $data_commandes[] = (int)$stat['nb_commandes'];
    $data_ca[] = (float)$stat['ca_total'];
}

$labels_json = json_encode($labels);
$data_commandes_json = json_encode($data_commandes);
$data_ca_json = json_encode($data_ca);

// Labels des statuts
$statuts_labels = [
    'en_attente' => 'En attente',
    'acceptee' => 'Acceptée',
    'en_preparation' => 'En préparation',
    'en_livraison' => 'En livraison',
    'livree' => 'Livrée',
    'terminee' => 'Terminée',
    'attente_retour_materiel' => 'Retour matériel'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../CSS/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../CSS/admin.css?v=<?= time() ?>">
    <style>
        .stat-card {
            background: linear-gradient(135deg, #8B1538 0%, #a91d47 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.gold {
            background: linear-gradient(135deg, #D4AF37 0%, #f0c850 100%);
        }
        .stat-card.blue {
            background: linear-gradient(135deg, #2c5282 0%, #3182ce 100%);
        }
        .stat-card.green {
            background: linear-gradient(135deg, #276749 0%, #38a169 100%);
        }
        .stat-card .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            margin: 5px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="admin-dashboard">
        <div class="container py-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <h1 class="mb-0"><i class="bi bi-graph-up"></i> Statistiques</h1>
                </div>

                <!-- Cartes statistiques globales -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-cart-check"></i></div>
                            <div class="stat-value"><?php echo $stats_globales['total_commandes']; ?></div>
                            <div class="stat-label">Commandes totales</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card gold">
                            <div class="stat-icon"><i class="bi bi-currency-euro"></i></div>
                            <div class="stat-value"><?php echo number_format($stats_globales['ca_total'], 0, ',', ' '); ?> &euro;</div>
                            <div class="stat-label">Chiffre d'affaires</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card blue">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-value"><?php echo $stats_globales['total_personnes']; ?></div>
                            <div class="stat-label">Personnes servies</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stat-card green">
                            <div class="stat-icon"><i class="bi bi-basket"></i></div>
                            <div class="stat-value"><?php echo number_format($stats_globales['panier_moyen'], 0, ',', ' '); ?> &euro;</div>
                            <div class="stat-label">Panier moyen</div>
                        </div>
                    </div>
                </div>

                <!-- Statuts des commandes -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="mb-0"><i class="bi bi-pie-chart"></i> Répartition par statut</h3>
                    </div>
                    <div class="card-body text-center">
                        <?php if(empty($stats_statuts)): ?>
                            <p class="text-muted">Aucune commande</p>
                        <?php else: ?>
                            <?php foreach($stats_statuts as $statut => $nb): ?>
                                <?php
                                $badge_class = match($statut) {
                                    'en_attente' => 'bg-warning text-dark',
                                    'acceptee' => 'bg-info',
                                    'en_preparation' => 'bg-primary',
                                    'en_livraison' => 'bg-success',
                                    'livree', 'terminee' => 'bg-secondary',
                                    default => 'bg-light text-dark'
                                };
                                ?>
                                <span class="status-badge <?php echo $badge_class; ?>">
                                    <?php echo $statuts_labels[$statut] ?? $statut; ?>: <strong><?php echo $nb; ?></strong>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="mb-0"><i class="bi bi-funnel"></i> Filtrer les données</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Menu</label>
                                <select name="menu_id" class="form-select">
                                    <option value="">Tous les menus</option>
                                    <?php foreach($tous_les_menus as $menu): ?>
                                        <option value="<?php echo $menu['id']; ?>"
                                            <?php echo $menu_id_filtre == $menu['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($menu['titre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date début</label>
                                <input type="date" name="date_debut" class="form-control"
                                       value="<?php echo $date_debut; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="date_fin" class="form-control"
                                       value="<?php echo $date_fin; ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-admin">
                                    <i class="bi bi-search"></i> Filtrer
                                </button>
                                <?php if($menu_id_filtre || $date_debut || $date_fin): ?>
                                    <a href="statistiques.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-x"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Graphiques -->
                <?php if(!empty($statistiques)): ?>
                <div class="row mb-4">
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h3 class="mb-0"><i class="bi bi-bar-chart"></i> Commandes par menu</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="graphiqueCommandes"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h3 class="mb-0"><i class="bi bi-currency-euro"></i> CA par menu</h3>
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

                <!-- Tableau détaillé -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h3 class="mb-0"><i class="bi bi-table"></i> Détails par menu</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">Commandes</th>
                                        <th class="text-center">Personnes</th>
                                        <th class="text-end">CA Total</th>
                                        <th class="text-end">CA Moyen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($statistiques)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                                <p class="text-muted mt-2 mb-0">Aucune commande enregistrée</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($statistiques as $stat): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($stat['menu_titre']); ?></strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?php echo $stat['nb_commandes']; ?></span>
                                                </td>
                                                <td class="text-center"><?php echo $stat['total_personnes']; ?></td>
                                                <td class="text-end"><strong><?php echo number_format($stat['ca_total'], 2, ',', ' '); ?> &euro;</strong></td>
                                                <td class="text-end"><?php echo number_format($stat['ca_total'] / $stat['nb_commandes'], 2, ',', ' '); ?> &euro;</td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="table-dark">
                                            <td><strong>TOTAL</strong></td>
                                            <td class="text-center"><strong><?php echo $stats_globales['total_commandes']; ?></strong></td>
                                            <td class="text-center"><strong><?php echo $stats_globales['total_personnes']; ?></strong></td>
                                            <td class="text-end"><strong><?php echo number_format($stats_globales['ca_total'], 2, ',', ' '); ?> &euro;</strong></td>
                                            <td class="text-end"><strong><?php echo number_format($stats_globales['panier_moyen'], 2, ',', ' '); ?> &euro;</strong></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <a href="index.php" class="btn btn-secondary mt-3">
                    <i class="bi bi-arrow-left"></i> Retour au dashboard
                </a>
            </div>
        </div>
    </div>

    <?php if(!empty($statistiques)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = <?php echo $labels_json; ?>;
            const dataCommandes = <?php echo $data_commandes_json; ?>;
            const dataCA = <?php echo $data_ca_json; ?>;

            const colors = [
                'rgba(139, 21, 56, 0.8)',
                'rgba(212, 175, 55, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(46, 204, 113, 0.8)'
            ];

            // Graphique commandes (bar)
            new Chart(document.getElementById('graphiqueCommandes'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Commandes',
                        data: dataCommandes,
                        backgroundColor: colors,
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

            // Graphique CA (doughnut)
            new Chart(document.getElementById('graphiqueCA'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataCA,
                        backgroundColor: colors,
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
                            position: 'bottom',
                            labels: { padding: 15 }
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
