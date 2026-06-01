<?php
// Fonction pour ajouter un message de succès
function ajouterMessageSucces($message) {
    $_SESSION['message_succes'] = $message;
}

// Fonction pour ajouter un message d'erreur
function ajouterMessageErreur($message) {
    $_SESSION['message_erreur'] = $message;
}

// Fonction pour afficher les messages
function afficherMessages() {
    // Message de succès
    if(isset($_SESSION['message_succes'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo $_SESSION['message_succes'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        
        // Supprimer le message après affichage
        unset($_SESSION['message_succes']);
    }
    
    // Message d'erreur
    if(isset($_SESSION['message_erreur'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        echo $_SESSION['message_erreur'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        
        // Supprimer le message après affichage
        unset($_SESSION['message_erreur']);
    }
}
?>