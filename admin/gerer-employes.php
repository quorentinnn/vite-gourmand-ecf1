<?php
// Démarrer la session
session_start();

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
require_once '../includes/messages.php';

// Variable pour le message d'erreur
$message_erreur = '';
$message_succes = '';

// Traiter la création d'un employé
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_employe'])) {
    
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    
    // Vérifier si l'email existe déjà
    $requete_verif = "SELECT id FROM utilisateurs WHERE email = :email";
    $prep_verif = $pdo->prepare($requete_verif);
    $prep_verif->execute(['email' => $email]);
    
    if($prep_verif->fetch()) {
        $message_erreur = "Cet email existe déjà !";
    } else {
        // Créer le compte employé
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        
        $requete = "INSERT INTO utilisateurs (email, mot_de_passe, role, actif, cree_le) 
                    VALUES (:email, :mot_de_passe, 'employe', 1, NOW())";
        
        $preparation = $pdo->prepare($requete);
        $preparation->execute([
            'email' => $email,
            'mot_de_passe' => $mot_de_passe_hash
        ]);
        
        // TODO : Envoyer un email à l'employé
        // mail($email, "Votre compte employé", "Un compte a été créé pour vous...");
        
        $message_succes = "Employé créé avec succès ! L'employé doit contacter l'admin pour obtenir son mot de passe.";
    }
}

// Traiter la désactivation/activation d'un employé
if(isset($_GET['toggle_actif'])) {
    $employe_id = $_GET['toggle_actif'];
    
    // Récupérer le statut actuel
    $requete_statut = "SELECT actif FROM utilisateurs WHERE id = :id";
    $prep_statut = $pdo->prepare($requete_statut);
    $prep_statut->execute(['id' => $employe_id]);
    $employe = $prep_statut->fetch();
    
    // Inverser le statut
    $nouveau_statut = $employe['actif'] == 1 ? 0 : 1;
    
    $requete_update = "UPDATE utilisateurs SET actif = :actif WHERE id = :id";
    $prep_update = $pdo->prepare($requete_update);
    $prep_update->execute([
        'actif' => $nouveau_statut,
        'id' => $employe_id
    ]);
    
    $message_succes = $nouveau_statut == 1 ? "Compte activé !" : "Compte désactivé !";
}

// Récupérer tous les employés
$requete_employes = "SELECT id, nom, prenom, email, actif, cree_le 
                     FROM utilisateurs 
                     WHERE role = 'employe' 
                     ORDER BY cree_le DESC";
$preparation_employes = $pdo->prepare($requete_employes);
$preparation_employes->execute();
$employes = $preparation_employes->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Employés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Gérer les Employés</h1>

        <!-- Messages -->
        <?php if($message_erreur != ''): ?>
            <div class="alert alert-danger">
                <?php echo $message_erreur; ?>
            </div>
        <?php endif; ?>

        <?php if($message_succes != ''): ?>
            <div class="alert alert-success">
                <?php echo $message_succes; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire de création d'employé -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Créer un nouveau compte employé</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Email (sera l'identifiant) *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label>Mot de passe *</label>
                        <input type="text" class="form-control" name="mot_de_passe" required>
                        <small class="text-muted">Ce mot de passe ne sera PAS envoyé par email. L'employé doit contacter l'admin.</small>
                    </div>

                    <button type="submit" name="creer_employe" class="btn btn-primary">Créer l'employé</button>
                </form>
            </div>
        </div>

        <!-- Liste des employés -->
        <div class="card">
            <div class="card-header">
                <h3>Liste des employés</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($employes as $employe): ?>
                            <tr>
                                <td><?php echo $employe['id']; ?></td>
                                <td><?php echo $employe['nom'] ?? 'Non renseigné'; ?></td>
                                <td><?php echo $employe['prenom'] ?? 'Non renseigné'; ?></td>
                                <td><?php echo htmlspecialchars($employe['email']); ?></td>
                                <td>
                                    <?php if($employe['actif'] == 1): ?>
                                        <span class="badge bg-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Désactivé</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($employe['cree_le'])); ?></td>
                                <td>
                                    <a href="?toggle_actif=<?php echo $employe['id']; ?>" 
                                       class="btn btn-sm <?php echo $employe['actif'] == 1 ? 'btn-warning' : 'btn-success'; ?>"
                                       onclick="return confirm('Confirmer le changement de statut ?')">
                                        <?php echo $employe['actif'] == 1 ? 'Désactiver' : 'Activer'; ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>