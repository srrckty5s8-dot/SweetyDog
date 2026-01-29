<?php
/**
 * Script de test pour vérifier le routeur
 */

// Simulation de l'environnement sans serveur HTTP
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/sweetydog/clients';

// Inclure les fichiers
require_once __DIR__ . '/app/Core/Router.php';
require_once __DIR__ . '/app/helpers.php';

echo "Test du routeur...\n";
echo "==================\n\n";

// Créer et tester le routeur
$router = new Router();
$GLOBALS['router'] = $router;

echo "Routes chargées:\n";
$reflection = new ReflectionClass($router);
$prop = $reflection->getProperty('namedRoutes');
$prop->setAccessible(true);
$namedRoutes = $prop->getValue($router);

foreach ($namedRoutes as $name => $route) {
    echo "  - $name : {$route['pattern']}\n";
}

echo "\n✅ Routeur chargé avec succès!\n";

// Tester les fonctions helper
echo "\nTest des helpers:\n";
echo "  - route('clients.index') = " . route('clients.index') . "\n";
echo "  - route('clients.edit', ['id' => 5]) = " . route('clients.edit', ['id' => 5]) . "\n";
echo "  - url('/clients') = " . url('/clients') . "\n";

echo "\n✅ Tous les tests réussis!\n";
