<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditions Générales de Vente - Vite & Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <div class="container mt-5 mb-5">
        <h1>Conditions Générales de Vente</h1>

        <div class="card mt-4">
            <div class="card-body">
                <h3>Article 1 - Objet</h3>
                <p>
                    Les présentes conditions générales de vente régissent les relations contractuelles 
                    entre Vite & Gourmand et ses clients dans le cadre de prestations de traiteur.
                </p>

                <h3 class="mt-4">Article 2 - Commandes</h3>
                <p>
                    Les commandes doivent être passées au minimum 48h avant la date de livraison souhaitée. 
                    Pour certains menus spécifiques, un délai plus long peut être requis (voir détails du menu).
                </p>
                <p>
                    Toute commande validée est ferme et définitive après acceptation par nos équipes.
                </p>

                <h3 class="mt-4">Article 3 - Prix</h3>
                <p>
                    Les prix sont indiqués en euros TTC. Les frais de livraison sont calculés en fonction de la distance :
                </p>
                <ul>
                    <li>Livraison dans Bordeaux : 5€ fixe</li>
                    <li>Hors Bordeaux : 5€ + 0,59€ par kilomètre</li>
                </ul>
                <p>
                    Une réduction de 10% est appliquée pour toute commande de 5 personnes ou plus au-delà du nombre minimum.
                </p>

                <h3 class="mt-4">Article 4 - Paiement</h3>
                <p>
                    Le paiement s'effectue en ligne au moment de la commande par carte bancaire. 
                    Aucune commande ne sera traitée sans paiement préalable.
                </p>

                <h3 class="mt-4">Article 5 - Livraison</h3>
                <p>
                    Les livraisons sont effectuées à l'adresse indiquée lors de la commande. 
                    Le client s'engage à être présent ou à désigner une personne pour réceptionner la commande.
                </p>
                <p>
                    Vite & Gourmand ne saurait être tenu responsable des retards de livraison dus à des 
                    circonstances indépendantes de sa volonté (intempéries, grèves, accidents, etc.).
                </p>

                <h3 class="mt-4">Article 6 - Annulation et modification</h3>
                <p>
                    Le client peut annuler ou modifier sa commande tant qu'elle n'a pas été acceptée par nos équipes 
                    (statut "en attente"). Une fois acceptée, toute annulation ou modification nécessite un contact direct 
                    avec notre service client.
                </p>

                <h3 class="mt-4">Article 7 - Matériel prêté</h3>
                <p>
                    Si du matériel est prêté au client (vaisselle, plats, etc.), celui-ci doit être restitué dans un délai de 10 jours ouvrés. 
                    Passé ce délai, des frais de 600€ seront facturés au client.
                </p>

                <h3 class="mt-4">Article 8 - Réclamations</h3>
                <p>
                    Toute réclamation doit être formulée dans les 48h suivant la livraison par email à : 
                    <strong>contact@vite-et-gourmand.fr</strong>
                </p>

                <h3 class="mt-4">Article 9 - Données personnelles</h3>
                <p>
                    Les données collectées sont utilisées uniquement pour traiter les commandes. 
                    Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression.
                </p>

                <h3 class="mt-4">Article 10 - Droit applicable</h3>
                <p>
                    Les présentes CGV sont soumises au droit français. Tout litige sera porté devant les tribunaux compétents de Bordeaux.
                </p>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>