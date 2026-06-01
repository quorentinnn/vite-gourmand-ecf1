<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales - Vite & Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5 mb-5">
        <h1>Mentions Légales</h1>

        <div class="card mt-4">
            <div class="card-body">
                <h3>Éditeur du site</h3>
                <p>
                    <strong>Vite & Gourmand</strong><br>
                    SARL au capital de 10 000 €<br>
                    Siège social : 123 Rue de la Gastronomie, 33000 Bordeaux<br>
                    SIRET : 123 456 789 00012<br>
                    RCS Bordeaux B 123 456 789<br>
                    TVA intracommunautaire : FR12 123456789
                </p>

                <p>
                    <strong>Directeurs de publication :</strong> Julie et José<br>
                    <strong>Email :</strong> contact@vite-et-gourmand.fr<br>
                    <strong>Téléphone :</strong> 05 56 XX XX XX
                </p>

                <h3 class="mt-4">Hébergement</h3>
                <p>
                    Le site est hébergé par :<br>
                    <strong>OVH</strong><br>
                    2 rue Kellermann, 59100 Roubaix, France<br>
                    Téléphone : 09 72 10 10 07
                </p>

                <h3 class="mt-4">Propriété intellectuelle</h3>
                <p>
                    L'ensemble du contenu de ce site (textes, images, vidéos, etc.) est protégé par le droit d'auteur. 
                    Toute reproduction, même partielle, est interdite sans autorisation préalable.
                </p>

                <h3 class="mt-4">Données personnelles</h3>
                <p>
                    Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression 
                    de vos données personnelles. Pour exercer ce droit, contactez-nous à : 
                    <strong>contact@vite-et-gourmand.fr</strong>
                </p>

                <h3 class="mt-4">Cookies</h3>
                <p>
                    Ce site utilise des cookies techniques nécessaires à son bon fonctionnement. 
                    Aucun cookie de tracking ou publicitaire n'est utilisé.
                </p>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>