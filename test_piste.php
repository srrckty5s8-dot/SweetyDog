<?php
require_once 'api_piste_handler.php';

$piste = new PisteService(__DIR__ . '/code.env');

echo "<h3>Diagnostic des identifiants :</h3>";
echo "CLIENT_ID chargé : " . (isset($_ENV['CLIENT_ID']) ? "✅ " . substr($_ENV['CLIENT_ID'], 0, 8) . "..." : "❌ NON TROUVÉ") . "<br>";
echo "CLIENT_SECRET chargé : " . (isset($_ENV['CLIENT_SECRET']) ? "✅ (Masqué pour sécurité)" : "❌ NON TROUVÉ") . "<br>";

echo "<h3>Tentative de connexion :</h3>";
$token = $piste->getToken();

if (strlen($token) > 50) {
    echo "<h2 style='color:green;'>✅ SUCCÈS ! Connexion établie.</h2>";
} else {
    echo "<h2 style='color:red;'>❌ ÉCHEC</h2>";
    echo "Détail renvoyé : <pre>" . htmlspecialchars($token) . "</pre>";
    
    if (strpos($token, '302') !== false) {
        echo "<p><strong>Conseil :</strong> L'erreur 302 confirme que vos identifiants (ID ou Secret) sont rejetés par Piste. Vérifiez qu'il n'y a pas d'espace caché dans votre fichier .env.</p>";
    }
}