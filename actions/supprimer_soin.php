<?php
// actions/supprimer_soin.php
require_once '../config/db.php';

$id_soin = $_GET['id_soin'] ?? null;
$id_animal = $_GET['id_animal'] ?? null;

if ($id_soin && $id_animal) {
    try {
        $query = $bdd->prepare("DELETE FROM Prestations WHERE id_prestation = :id");
        $query->execute(['id' => $id_soin]);

        // Redirection vers la page de suivi de l'animal
        header('Location: ../suivi_toilettage.php?id=' . $id_animal . '&msg=deleted');
        exit();
    } catch (Exception $e) {
        die("Erreur lors de la suppression du soin : " . $e->getMessage());
    }
} else {
    header('Location: ../liste_clients.php');
    exit();
}