<?php
session_start();
require_once '../includes/db.php';

$message = '';
$message_type = '';
$token_valide = false;

// Vérifier le token
if(isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Vérifier si le token existe et n'est pas expiré
    $requete = "SELECT id FROM utilisateurs 
                WHERE reset_token = :token 
                AND reset_token_expiration > NOW()";
    $preparation = $pdo->prepare($requete);
    $preparation->execute(['token' => $token]);
    $user = $preparation->fetch();
    
    if($user) {
        $token_valide = true;
        
        // Traiter le changement de mot de passe
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nouveau_mdp = $_POST['nouveau_mdp'];
            $confirmer_mdp = $_POST['confirmer_mdp'];
            
            // Vérifier que les mots de passe correspondent
            if($nouveau_mdp !== $confirmer_mdp) {
                $message = "Les mots de passe ne correspondent pas.";
                $message_type = 'danger';
            }
            // Vérifier la longueur minimale
            elseif(strlen($nouveau_mdp) < 10) {
                $message = "Le mot de passe doit contenir au moins 10 caractères.";
                $message_type = 'danger';
            }
            else {
                // Hasher et sauvegarder le nouveau mot de passe
                $mdp_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
                
                $requete_update = "UPDATE utilisateurs 
                                  SET mot_de_passe = :mdp,
                                      reset_token = NULL,
                                      reset_token_expiration = NULL
                                  WHERE id = :id";
                $prep_update = $pdo->prepare($requete_update);
                $prep_update->execute([
                    'mdp' => $mdp_hash,
                    'id' => $user['id']
                ]);
                
                $message = "Mot de passe changé avec succès ! Vous pouvez maintenant vous connecter.";
                $message_type = 'success';
                $token_valide = false; // Ne plus afficher le formulaire
            }
        }
    }
    else {
        $message = "Ce lien de réinitialisation est invalide ou a expiré.";
        $message_type = 'danger';
    }
}
else {
    $message = "Aucun token fourni.";
    $message_type = 'danger';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Réinitialiser le mot de passe</h3>
                    </div>
                    <div class="card-body">
                        <?php if($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($token_valide): ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label>Nouveau mot de passe *</label>
                                    <input type="password" name="nouveau_mdp" class="form-control" required>
                                    <small class="text-muted">Minimum 10 caractères</small>
                                </div>

                                <div class="mb-3">
                                    <label>Confirmer le mot de passe *</label>
                                    <input type="password" name="confirmer_mdp" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Changer le mot de passe</button>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <a href="connexion.php" class="btn btn-primary">Retour à la connexion</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>