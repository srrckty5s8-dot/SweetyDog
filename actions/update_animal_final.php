<?php
require_once '../config/db.php';

if (isset($_POST['modifier'])) {
    try {
        // 1. Préparation des variables numériques/optionnelles
        $poids = !empty($_POST['poids']) ? $_POST['poids'] : 0;
        $steril = isset($_POST['steril']) ? $_POST['steril'] : 0;
        
        // On récupère les nouveaux champs
        $email = !empty($_POST['email_proprio']) ? $_POST['email_proprio'] : null;
        $adresse = !empty($_POST['adresse_proprio']) ? $_POST['adresse_proprio'] : null;

        // 2. Mise à jour du Propriétaire (Ajout de email et adresse)
        $upProprio = $bdd->prepare("
            UPDATE Proprietaires 
            SET nom = :n, 
                prenom = :p, 
                telephone = :t,
                email = :email,
                adresse = :adr
            WHERE id_proprietaire = :idp
        ");
        
        $upProprio->execute([
            'n'     => $_POST['nom_proprio'],
            'p'     => $_POST['prenom_proprio'],
            't'     => $_POST['tel_proprio'],
            'email' => $email,
            'adr'   => $adresse,
            'idp'   => $_POST['id_proprietaire']
        ]);

        // 3. Mise à jour de l'Animal
        $upAnimal = $bdd->prepare("
            UPDATE Animaux 
            SET nom_animal = :nom, 
                espece     = :esp, 
                race       = :ra, 
                poids      = :pd, 
                steril     = :st 
            WHERE id_animal = :id_a
        ");

        $upAnimal->execute([
            'nom'  => $_POST['nom_animal'],
            'esp'  => $_POST['espece'],
            'ra'   => $_POST['race'],
            'pd'   => $poids,
            'st'   => $steril,
            'id_a' => $_POST['id_animal']
        ]);

        // Redirection vers la liste avec succès
        header('Location: ../liste_clients.php?updated=1');
        exit();

    } catch (Exception $e) {
        // En cas d'erreur SQL, on affiche le message
        die("Erreur de mise à jour : " . $e->getMessage());
    }
} else {
    // Si on accède au fichier sans valider le formulaire
    header('Location: ../liste_clients.php');
    exit();
}
?>