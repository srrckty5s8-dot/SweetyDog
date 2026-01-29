<?php
session_start();
// Sécurité : on vérifie que l'admin est connecté
if (!isset($_SESSION['admin_connecte'])) { header('Location: index.php'); exit(); }

require_once 'config/db.php';

$message = null;
$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirm_mdp = $_POST['confirm_mdp'] ?? '';

    // 1. On récupère le mot de passe actuel en base
    $query = $bdd->prepare("SELECT mot_de_passe FROM Utilisateurs WHERE id_utilisateur = :id");
    $query->execute(['id' => $_SESSION['admin_id']]);
    $user = $query->fetch();

    // 2. Vérifications
    if (!password_verify($ancien_mdp, $user['mot_de_passe'])) {
        $erreur = "L'ancien mot de passe est incorrect.";
    } elseif ($nouveau_mdp !== $confirm_mdp) {
        $erreur = "Les nouveaux mots de passe ne correspondent pas.";
    } elseif (strlen($nouveau_mdp) < 6) {
        $erreur = "Le nouveau mot de passe doit faire au moins 6 caractères.";
    } else {
        // 3. Mise à jour
        $nouveau_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
        $update = $bdd->prepare("UPDATE Utilisateurs SET mot_de_passe = :mdp WHERE id_utilisateur = :id");
        $update->execute([
            'mdp' => $nouveau_hash,
            'id'  => $_SESSION['admin_id']
        ]);
        $message = "Mot de passe mis à jour avec succès !";
    }
}

include 'views/parametres_view.php';