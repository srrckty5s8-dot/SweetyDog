<?php
/**
 * Page de connexion - SweetyDog
 * Gère l'authentification sécurisée des administrateurs
 */

// 1. Démarrage de la session pour maintenir la connexion
session_start();

// 2. Inclusion de la connexion à la base de données
require_once 'config/db.php';

// 3. Initialisation de la variable d'erreur (évite le Warning dans la vue)
$erreur = null;

// 4. Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupération sécurisée des données du formulaire
    $identifiant = $_POST['identifiant'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    if (!empty($identifiant) && !empty($mdp)) {
        try {
            // Recherche de l'utilisateur dans la base de données
            $query = $bdd->prepare("SELECT * FROM Utilisateurs WHERE identifiant = :id");
            $query->execute(['id' => $identifiant]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // Vérification : l'utilisateur existe ET le mot de passe correspond au hash
            if ($user && password_verify($mdp, $user['mot_de_passe'])) {
                
                // On stocke les informations de connexion en session
                $_SESSION['admin_connecte'] = true;
                $_SESSION['admin_id'] = $user['id_utilisateur'];
                $_SESSION['admin_nom'] = $user['identifiant'];

                // Redirection vers la page d'accueil du logiciel
                header('Location: liste_clients.php');
                exit();
                
            } else {
                // Message générique pour ne pas aider les pirates
                $erreur = "Identifiant ou mot de passe incorrect.";
            }
        } catch (Exception $e) {
            $erreur = "Une erreur technique est survenue. Veuillez réessayer.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}

// 5. Affichage de l'interface de connexion
include __DIR__ . '/views/login_view.php';