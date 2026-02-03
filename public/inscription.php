<?php include '../includes/header.php'; 
session_start();
require_once '../includes/db.php';
$error = '';
// Si le formulaire est soumis
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Vérifier que les champs ne sont pas vides
    if(empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        // Vérifier si l'email existe déjà
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch();
        
        if($existingUser) {
            $error = "Cet email est déjà utilisé";
        } else {
            // Insérer le nouvel utilisateur dans la BDD
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertSql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) 
                          VALUES (:nom, :prenom, :email, :mot_de_passe, 'client')";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'mot_de_passe' => $hashedPassword
            ]);
            
            // Rediriger vers la page de connexion après inscription réussie
            header('Location: connexion.php');
            exit;
        }
    }
}


?>

<?php if($error): ?>
    <div class="error-message">
        <?php echo $error; ?>
    </div>
<?php endif; ?>


<section class="inscription-hero">
        <h1 class="inscription-title">Inscription</h1>
        <p class="inscription-subtitle">Rejoignez-nous dès aujourd'hui et commencez votre aventure culinaire avec Vite&Gourmand !</p>
    </section>

    <section class="inscription-form-section">
        <div class="container4">
            <form class="inscription-form" method="POST" action="">
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-inscription2">S'inscrire</button>
            </form>
        </div>
        <p class="text-compte"> Déjà un compte ? <a href="connexion.php">Connectez-vous ici</a></p>
    </section>
<?php include '../includes/footer.php'; ?>
