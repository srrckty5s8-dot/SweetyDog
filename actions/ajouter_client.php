<?php
require_once '../config/db.php';

if (isset($_POST['valider'])) {
    try {
        $id_proprietaire_existant = $_POST['id_proprietaire_existant'] ?? 'nouveau';
        $nom_animal = trim($_POST['nom_chien']);

        // 1. GESTION DU PROPRIÉTAIRE
        if ($id_proprietaire_existant !== 'nouveau') {
            // On utilise le propriétaire sélectionné
            $id_maitre = $id_proprietaire_existant;
        } else {
            // Création d'un nouveau propriétaire
            $nom_maitre = trim($_POST['nom']);
            $prenom_maitre = trim($_POST['prenom']);
            $tel = $_POST['tel'] ?? '';
            $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
            $adresse = !empty($_POST['adresse']) ? trim($_POST['adresse']) : null;

            $req1 = $bdd->prepare("INSERT INTO Proprietaires (nom, prenom, telephone, email, adresse) VALUES (:n, :p, :t, :e, :a)");
            $req1->execute([
                'n' => $nom_maitre, 
                'p' => $prenom_maitre, 
                't' => $tel,
                'e' => $email,
                'a' => $adresse
            ]);
            $id_maitre = $bdd->lastInsertId();
        }

        // 2. VÉRIFICATION DE L'ANIMAL (IDEM)
        $checkAnimal = $bdd->prepare("SELECT id_animal FROM Animaux WHERE nom_animal = :na AND id_proprietaire = :idp");
        $checkAnimal->execute(['na' => $nom_animal, 'idp' => $id_maitre]);
        
        if ($checkAnimal->fetch()) {
            header("Location: ../ajout_client.php?error=animal_exists&nom_chien=$nom_animal");
            exit();
        }

        // 3. INSERTION DE L'ANIMAL
        $steril = $_POST['steril'] ?? 0;
        $race = !empty($_POST['race']) ? $_POST['race'] : null;
        $poids = !empty($_POST['poids']) ? $_POST['poids'] : 0;

        $req2 = $bdd->prepare("INSERT INTO Animaux (nom_animal, espece, race, poids, id_proprietaire, steril, date_inscription) 
                               VALUES (:na, :esp, :race, :poids, :idp, :st, NOW())");
        $req2->execute([
            'na'    => $nom_animal,
            'esp'   => $_POST['espece'],
            'race'  => $race,
            'poids' => $poids,
            'idp'   => $id_maitre,
            'st'    => $steril 
        ]);

        header('Location: ../liste_clients.php?success=1');
        exit();

    } catch (Exception $e) {
        die("Erreur lors de l'ajout : " . $e->getMessage());
    }
}