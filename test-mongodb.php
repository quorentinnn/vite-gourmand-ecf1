<?php
// Vérifier si l'extension MongoDB est chargée
if (extension_loaded('mongodb')) {
    echo "✅ Extension MongoDB est installée !";
} else {
    echo "❌ Extension MongoDB n'est PAS installée !";
}
?>