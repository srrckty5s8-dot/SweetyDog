<?php
session_start();
require_once '../config/db.php';

// 1. On récupère l'ID envoyé par le champ caché "id_animal"
// On utilise l'opérateur ?? pour éviter le message "Undefined array key"
$id_animal = $_POST['id_animal'] ?? null;
$titre     = $_POST['titre'] ?? 'Toilettage';
$debut     = $_POST['date_debut'] ?? null;
$fin       = $_POST['date_fin'] ?? null;

// 2. Vérification de sécurité : si l'ID est vide, on ne tente pas l'insertion
if (!$id_animal || !$debut || !$fin) {
    die("Erreur : Des informations sont manquantes (Animal, date de début ou de fin).");
}

try {
    // 3. Préparation de la requête
    $sql = "INSERT INTO RendezVous (id_animal, titre, date_debut, date_fin) 
            VALUES (:id_a, :titre, :debut, :fin)";
    
    $query = $bdd->prepare($sql);
    
    // 4. Exécution avec les bonnes clés
    $query->execute([
        'id_a'  => $id_animal,
        'titre' => $titre,
        'debut' => $debut,
        'fin'   => $fin
    ]);

    // 5. Redirection vers le calendrier avec un message de succès
    header('Location: ../calendrier.php?rdv=ok');
    exit();

} catch (Exception $e) {
    // En cas d'erreur (ex: problème de format de date), on affiche le message
    die("Erreur SQL : " . $e->getMessage());
}