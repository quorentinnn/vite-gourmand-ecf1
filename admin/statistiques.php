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
require_once '../includes/mongodb.php';

// Récupérer tous les menus pour le filtre
$requete_menus = "SELECT id, titre FROM menus ORDER BY titre ASC";
$prep_menus = $pdo->prepare($requete_menus);
$prep_menus->execute();
$tous_les_menus = $prep_menus->fetchAll();

// Gérer les filtres
$menu_id_filtre = isset($_GET['menu_id']) ? $_GET['menu_id'] : null;
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : null;
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : null;

// Récupérer les statistiques depuis MongoDB
$statistiques = getStatistiquesCommandes($menu_id_filtre, $date_debut, $date_fin);

// Calculer les données pour les graphiques
$commandes_par_menu = [];
$ca_par_menu = [];

foreach($statistiques as $stat) {
    $menu_titre = $stat['menu_titre'];

    if(!isset($commandes_par_menu[$menu_titre])) {
        $commandes_par_menu[$menu_titre] = 0;
        $ca_par_menu[$menu_titre] = 0;
    }

    $commandes_par_menu[$menu_titre]++;
    $ca_par_menu[$menu_titre] += $stat['prix_total'];
}

// Préparer les données pour Chart.js
$labels = json_encode(array_keys($commandes_par_menu));
$data_commandes = json_encode(array_values($commandes_par_menu));
$data_ca = json_encode(array_values($ca_par_menu));

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
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="admin-dashboard">
        <div class="container py-4">
            <div class="dashboard-card">
                <h1>Statistiques des Commandes</h1>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">Filtres</h3>
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
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-admin w-100">
                                    <i class="bi bi-funnel"></i> Filtrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card">
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
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h3 class="mb-0">Chiffre d'affaires par menu</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="graphiqueCA"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h3 class="mb-0">Détails par menu</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>Nombre de commandes</th>
                                        <th>Chiffre d'affaires total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($commandes_par_menu)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Aucune donnée disponible</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($commandes_par_menu as $menu_titre => $nb_commandes): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($menu_titre); ?></td>
                                                <td><?php echo $nb_commandes; ?></td>
                                                <td><?php echo number_format($ca_par_menu[$menu_titre], 2); ?> &euro;</td>
                                            </tr>
                                        <?php endforeach; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = <?php echo $labels; ?>;
            const dataCommandes = <?php echo $data_commandes; ?>;
            const dataCA = <?php echo $data_ca; ?>;

            if (labels.length === 0) return;

            const ctxCommandes = document.getElementById('graphiqueCommandes');
            if (ctxCommandes) {
                new Chart(ctxCommandes, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nombre de commandes',
                            data: dataCommandes,
                            backgroundColor: [
                                'rgba(139, 21, 56, 0.7)',
                                'rgba(212, 175, 55, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(153, 102, 255, 0.7)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: true, position: 'bottom' }
                        }
                    }
                });
            }

            const ctxCA = document.getElementById('graphiqueCA');
            if (ctxCA) {
                new Chart(ctxCA, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Chiffre d'affaires",
                            data: dataCA,
                            backgroundColor: [
                                'rgba(212, 175, 55, 0.7)',
                                'rgba(139, 21, 56, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 159, 64, 0.7)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: true, position: 'bottom' }
                        }
                    }
                });
            }
        });
    </script>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>
