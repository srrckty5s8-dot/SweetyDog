<?php
echo "Dossier actuel : " . __DIR__ . "<br>";
echo "Fichier .env existe : " . (file_exists(__DIR__ . '/code.env') ? "OUI" : "NON") . "<br>";
if (file_exists(__DIR__ . '/code.env')) {
    echo "Contenu du fichier : <pre>" . htmlspecialchars(file_get_contents(__DIR__ . '/code.env')) . "</pre>";
}
?>