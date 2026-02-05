<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: ../public/connexion.php');
    exit;
}

// Vérifier si c'est un admin ou employé
if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'employe') {
    header('Location: ../public/connexion.php');
    exit;
}

// Connexion à la base de données
require_once '../includes/db.php';

$message = '';
$message_type = '';

// Traiter l'ajout d'un plat
if(isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $categorie = $_POST['categorie'];
    
    if(empty($nom) || empty($categorie)) {
        $message = "Le nom et la catégorie sont obligatoires";
        $message_type = 'danger';
    } else {
        $requete = "INSERT INTO plats (nom, description, categorie) 
                    VALUES (:nom, :description, :categorie)";
        $preparation = $pdo->prepare($requete);
        $preparation->execute([
            'nom' => $nom,
            'description' => $description,
            'categorie' => $categorie
        ]);
        
        $message = "Plat ajouté avec succès !";
        $message_type = 'success';
    }
}

// Traiter la suppression d'un plat
if(isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    
    // Vérifier si le plat est utilisé dans un menu
    $requete_verif = "SELECT COUNT(*) as nb FROM composer WHERE plat_id = :id";
    $prep_verif = $pdo->prepare($requete_verif);
    $prep_verif->execute(['id' => $id]);
    $resultat = $prep_verif->fetch();
    
    if($resultat['nb'] > 0) {
        $message = "Impossible de supprimer ce plat, il est utilisé dans des menus !";
        $message_type = 'danger';
    } else {
        $requete = "DELETE FROM plats WHERE id = :id";
        $preparation = $pdo->prepare($requete);
        $preparation->execute(['id' => $id]);
        
        $message = "Plat supprimé avec succès !";
        $message_type = 'success';
    }
}

// Traiter la modification d'un plat
if(isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $categorie = $_POST['categorie'];
    
    if(empty($nom) || empty($categorie)) {
        $message = "Le nom et la catégorie sont obligatoires";
        $message_type = 'danger';
    } else {
        $requete = "UPDATE plats 
                    SET nom = :nom, 
                        description = :description, 
                        categorie = :categorie 
                    WHERE id = :id";
        $preparation = $pdo->prepare($requete);
        $preparation->execute([
            'nom' => $nom,
            'description' => $description,
            'categorie' => $categorie,
            'id' => $id
        ]);
        
        $message = "Plat modifié avec succès !";
        $message_type = 'success';
    }
}

// Récupérer tous les plats
$requete_plats = "SELECT * FROM plats ORDER BY categorie, nom ASC";
$preparation_plats = $pdo->prepare($requete_plats);
$preparation_plats->execute();
$plats = $preparation_plats->fetchAll();

// Grouper par catégorie
$plats_par_categorie = [
    'entree' => [],
    'plat' => [],
    'dessert' => []
];

foreach($plats as $plat) {
    $plats_par_categorie[$plat['categorie']][] = $plat;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Plats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Gestion des Plats</h1>

        <!-- Messages -->
        <?php if($message != ''): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Ajouter un plat</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nom du plat *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label>Catégorie *</label>
                            <select name="categorie" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                <option value="entree">Entrée</option>
                                <option value="plat">Plat principal</option>
                                <option value="dessert">Dessert</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="ajouter" class="btn btn-primary">Ajouter le plat</button>
                </form>
            </div>
        </div>

        <!-- Liste des plats par catégorie -->
        <?php 
        $categories_labels = [
            'entree' => 'Entrées',
            'plat' => 'Plats principaux',
            'dessert' => 'Desserts'
        ];
        
        foreach($categories_labels as $cat => $label): 
            if(!empty($plats_par_categorie[$cat])): 
        ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3><?php echo $label; ?></h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="25%">Nom</th>
                                <th width="45%">Description</th>
                                <th width="10%">Catégorie</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($plats_par_categorie[$cat] as $plat): ?>
                                <tr>
                                    <td><?php echo $plat['id']; ?></td>
                                    <td><?php echo htmlspecialchars($plat['nom']); ?></td>
                                    <td>
                                        <small><?php echo htmlspecialchars(substr($plat['description'], 0, 100)); ?><?php echo strlen($plat['description']) > 100 ? '...' : ''; ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst($plat['categorie']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalModifier<?php echo $plat['id']; ?>">
                                            Modifier
                                        </button>
                                        <a href="?supprimer=<?php echo $plat['id']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Confirmer la suppression ?')">
                                            Supprimer
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- Modal modification -->
                                <div class="modal fade" id="modalModifier<?php echo $plat['id']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le plat</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $plat['id']; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label>Nom *</label>
                                                        <input type="text" name="nom" class="form-control" 
                                                               value="<?php echo htmlspecialchars($plat['nom']); ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label>Catégorie *</label>
                                                        <select name="categorie" class="form-control" required>
                                                            <option value="entree" <?php echo $plat['categorie'] == 'entree' ? 'selected' : ''; ?>>Entrée</option>
                                                            <option value="plat" <?php echo $plat['categorie'] == 'plat' ? 'selected' : ''; ?>>Plat principal</option>
                                                            <option value="dessert" <?php echo $plat['categorie'] == 'dessert' ? 'selected' : ''; ?>>Dessert</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label>Description</label>
                                                        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($plat['description']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" name="modifier" class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php 
            endif;
        endforeach; 
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>