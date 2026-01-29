<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_connecte'])) { 
    header('Location: index.php'); 
    exit(); 
}

require_once 'config/db.php';

try {
    // 1. Charger la liste enrichie des animaux (Nom + Race + Nom du Maître)
    // Note : J'utilise LEFT JOIN pour ne pas perdre un animal même si son maître n'est pas rempli
    $sqlAnimaux = "SELECT a.id_animal, a.nom_animal, a.race, c.nom as nom_client, c.prenom as prenom_client 
                   FROM Animaux a 
                   LEFT JOIN Proprietaires c ON a.id_proprietaire = c.id_proprietaire 
                   ORDER BY a.nom_animal ASC";
    
    $queryAnimaux = $bdd->query($sqlAnimaux);
    $liste_animaux = $queryAnimaux->fetchAll(PDO::FETCH_ASSOC);

    // 2. Charger les rendez-vous existants pour l'affichage sur le calendrier
    $queryEvents = $bdd->query("SELECT r.*, a.nom_animal 
                                FROM RendezVous r 
                                JOIN Animaux a ON r.id_animal = a.id_animal");
    $rdvs = $queryEvents->fetchAll(PDO::FETCH_ASSOC);
    
    $events = [];
    foreach($rdvs as $r) {
        $events[] = [
            'id'    => $r['id_rdv'],
            'title' => $r['nom_animal'] . " - " . $r['titre'],
            'start' => $r['date_debut'],
            'end'   => $r['date_fin']
        ];
    }

} catch (Exception $e) {
    die("Erreur SQL : " . $e->getMessage());
}

// 3. Appeler la vue
include 'views/calendrier_view.php';
?>