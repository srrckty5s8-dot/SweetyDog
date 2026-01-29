<?php
session_start();
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: index.php');
    exit();
}
// ... reste de ton code actuel ...

// facture.php
require_once 'config/db.php';

$id = $_GET['id'] ?? null;

$sql = "SELECT pr.*, a.nom_animal, a.espece, p.nom, p.prenom, p.telephone 
        FROM Prestations pr 
        LEFT JOIN Animaux a ON pr.id_animal = a.id_animal 
        LEFT JOIN Proprietaires p ON a.id_proprietaire = p.id_proprietaire 
        WHERE pr.id_prestation = :id";

$q = $bdd->prepare($sql);
$q->execute(['id' => $id]);
$data = $q->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Facture introuvable.");

}

// On appelle enfin la vue nettoyée
include __DIR__ . '/views/facture_view.php';