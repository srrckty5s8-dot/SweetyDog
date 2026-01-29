<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_connecte'])) { exit('Accès refusé'); }

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $bdd->prepare("DELETE FROM RendezVous WHERE id_rdv = ?");
    $query->execute([$id]);
}

// Retour au calendrier
header('Location: ../calendrier.php');
exit();
?>