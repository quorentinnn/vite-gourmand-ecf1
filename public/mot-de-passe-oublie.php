<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../includes/db.php';

$message = '';
$message_type = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Vérifier si l'email existe
    $requete = "SELECT id FROM utilisateurs WHERE email = :email";
    $preparation = $pdo->prepare($requete);
    $preparation->execute(['email' => $email]);
    $user = $preparation->fetch();
    
    if($user) {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        
        // Stocker le token avec expiration dans 1 heure
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $requete_update = "UPDATE utilisateurs 
                          SET reset_token = :token, 
                              reset_token_expiration = :expiration 
                          WHERE id = :id";
        $prep_update = $pdo->prepare($requete_update);
        $prep_update->execute([
            'token' => $token,
            'expiration' => $expiration,
            'id' => $user['id']
        ]);
        
        // Créer le lien de réinitialisation (dynamique selon l'environnement)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $lien = $protocol . "://" . $host . "/reinitialiser-mot-de-passe.php?token=" . $token;

        // Envoyer l'email
        $sujet = "Réinitialisation de votre mot de passe - Vite & Gourmand";
        $contenu = "Bonjour,\n\n";
        $contenu .= "Vous avez demandé la réinitialisation de votre mot de passe.\n\n";
        $contenu .= "Cliquez sur ce lien pour choisir un nouveau mot de passe :\n";
        $contenu .= $lien . "\n\n";
        $contenu .= "Ce lien expire dans 1 heure.\n\n";
        $contenu .= "Si vous n'avez pas fait cette demande, ignorez cet email.\n\n";
        $contenu .= "L'équipe Vite & Gourmand";

        $headers = "From: noreply@vite-gourmand.fr\r\n";
        $headers .= "Reply-To: noreply@vite-gourmand.fr\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Essayer d'envoyer l'email
        $email_envoye = @mail($email, $sujet, $contenu, $headers);

        if($email_envoye) {
            $message = "Un email de réinitialisation a été envoyé à votre adresse.";
        } else {
            // Si mail() ne fonctionne pas (Railway), afficher le lien directement
            $message = "Cliquez sur ce lien pour réinitialiser votre mot de passe :<br><a href='$lien' class='btn btn-sm btn-primary mt-2'>Réinitialiser mon mot de passe</a>";
        }
        $message_type = 'success';
        
    } else {
        $message = "Aucun compte ne correspond à cet email.";
        $message_type = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Mot de passe oublié</h3>
                    </div>
                    <div class="card-body">
                        <?php if($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <p>Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="connexion.php">Retour à la connexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>