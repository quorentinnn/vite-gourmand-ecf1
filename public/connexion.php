<?php include '../includes/header.php'; 

session_start();
require_once '../includes/db.php';

$error = '';

// Si le formulaire est soumis
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Vérifier que les champs ne sont pas vides
    if(empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        // Chercher l'utilisateur dans la BDD
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        // Vérifier si l'utilisateur existe et le mot de passe est correct
        if($user && password_verify($password, $user['mot_de_passe'])) {
            // Connexion réussie ! Créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_role'] = $user['role'];
            
            // Rediriger selon le rôle
            if($user['role'] == 'admin') {
                header('Location: ../admin/index.php');
            } elseif($user['role'] == 'employe') {
                header('Location: ../employe/index.php');
            } else {
                header('Location: mon-compte.php');
            }
            exit;
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    }
}
?>

<?php if($error): ?>
    <div class="error-message">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

   <section class="connexion-hero">
        <h1 class="connexion-title">Connexion</h1>
        <p class="connexion-subtitle">Bienvenue de retour ! Veuillez vous connecter pour accéder à votre compte.</p>
    </section>
    

    <section class="connexion-form-section">
        <div class="container4">
            <form class="connexion-form" method="POST" action="">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" id="password" name="password" required>
                </div>
<button type="submit" class="btn w-100 mt-3" style="background-color: #8B1538; color: white; border: none; padding: 12px;">
    Se connecter
</button>            </form>
</div>
        <p class="text-compte"> Pas encore de compte ? <a href="inscription.php">Inscrivez-vous ici</a></p>
        <p class="text-compte"> mot de passe oublié ? <a href="mot-de-passe-oublie.php">Réinitialisez-le ici</a></p>
    </section>

<?php include '../includes/footer.php'; ?>