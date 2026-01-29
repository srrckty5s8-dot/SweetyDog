<?php
// actions/supprimer_animal.php
require_once '../config/db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $query = $bdd->prepare("DELETE FROM Animaux WHERE id_animal = :id");
        $query->execute(['id' => $id]);
        
        // Redirection vers la liste avec un message de succès
        header('Location: ../liste_clients.php?msg=deleted');
        exit();
    } catch (Exception $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header('Location: ../liste_clients.php');
    exit();
}
