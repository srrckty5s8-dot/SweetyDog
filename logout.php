<?php
// On démarre la session pour pouvoir la manipuler
session_start();

// On efface toutes les variables de session
$_SESSION = array();

// On détruit physiquement la session sur le serveur
session_destroy();

// On redirige vers l'écran de connexion
header('Location: index.php');
exit();
?>