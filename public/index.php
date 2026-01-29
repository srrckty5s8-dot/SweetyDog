<?php
/**
 * ============================================================
 * POINT D'ENTRÉE PRINCIPAL DE L'APPLICATION
 * ============================================================
 * 
 * Ce fichier est le seul fichier accédé directement.
 * Tous les requêtes HTTP sont redirigées vers ce fichier via .htaccess
 * 
 * Flux :
 * 1. Utilisateur visite /clients
 * 2. .htaccess redirige vers /public/index.php
 * 3. Ce fichier initialise l'application
 * 4. Router analyse l'URL et appelle le contrôleur approprié
 * 
 * Étapes d'initialisation :
 * 1. Démarrer la session PHP
 * 2. Enregistrer l'autoloader (chargement automatique des classes)
 * 3. Charger les helpers (fonctions globales)
 * 4. Créer et lancer le routeur
 */

// ========== 1. DÉMARRER LA SESSION ==========
// La session permet de stocker des données entre les requêtes
// Utilisée pour l'authentification, les messages flash, etc.
session_start();

// ========== 2. ENREGISTRER L'AUTOLOADER ==========
/**
 * Chargement automatique des classes PHP
 * 
 * Quand on fait : new ClientController();
 * PHP cherche automatiquement le fichier ClientController.php
 * 
 * Ordre de recherche :
 * 1. app/Core/ClientController.php
 * 2. app/Controllers/ClientController.php
 * 3. app/Models/ClientController.php
 * 
 * Cela évite d'écrire require_once pour chaque classe
 */
spl_autoload_register(function ($class) {
    // Dossier de base de l'app
    $base = __DIR__ . '/../app/';

    // Chemins à chercher pour chaque classe
    $paths = [
        $base . 'Core/' . $class . '.php',           // Classe core (Router, Controller, Database)
        $base . 'Controllers/' . $class . '.php',    // Classe contrôleur (ClientController, etc.)
        $base . 'Models/' . $class . '.php',         // Classe modèle (Client, Animal, etc.)
    ];

    // Parcourir chaque chemin
    foreach ($paths as $file) {
        if (file_exists($file)) {
            // Fichier trouvé, le charger et arrêter
            require_once $file;
            return;
        }
    }
});

// ========== 3. CHARGER LES HELPERS ==========
/**
 * Les fonctions helpers (route(), redirect(), param(), etc.) 
 * sont chargées ici pour être disponibles partout dans l'app
 */
require_once __DIR__ . '/../app/helpers.php';

// ========== 4. CRÉER ET LANCER LE ROUTEUR ==========
/**
 * Créer une instance du routeur
 * Le routeur va :
 * 1. Charger la configuration des routes depuis app/routes.php
 * 2. Analyser l'URL actuelle
 * 3. Trouver la route correspondante
 * 4. Instancier le contrôleur et appeler l'action
 */
$router = new Router();

// Stocker le routeur dans $GLOBALS pour que les helpers y accèdent
// route(), redirect(), isCurrentRoute() ont besoin du routeur
$GLOBALS['router'] = $router;

// ========== 5. LANCER LE ROUTAGE ==========
/**
 * Exécuter le routeur
 * C'est ici que l'application fait vraiment quelque chose
 */

$router->run();
