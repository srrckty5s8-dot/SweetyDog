<?php

// Compatibilité legacy : rediriger vers la route MVC de suivi.
$idAnimal = $_GET['id'] ?? null;

$scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = $scriptPath !== '/' ? $scriptPath : '';

if ($idAnimal) {
    header('Location: ' . $basePath . '/animals/' . urlencode($idAnimal) . '/tracking');
    exit;
}

header('Location: ' . $basePath . '/clients');
exit;
