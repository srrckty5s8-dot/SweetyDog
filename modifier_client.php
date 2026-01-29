<?php
require_once 'config/db.php';

// 1. On récupère les infos actuelles du client
if (!isset($_GET['id'])) {
    header('Location: liste_clients.php');
    exit();
}

$id_p = $_GET['id'];
$query = $bdd->prepare("SELECT * FROM Proprietaires WHERE id_proprietaire = ?");
$query->execute([$id_p]);
$proprio = $query->fetch(PDO::FETCH_ASSOC);

if (!$proprio) { die("Propriétaire introuvable."); }

// 2. Traitement de la mise à jour
if (isset($_POST['modifier'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $tel = trim($_POST['tel']);
    $email = trim($_POST['email']);
    $adresse = trim($_POST['adresse']);

    $update = $bdd->prepare("UPDATE Proprietaires SET nom = :n, prenom = :p, telephone = :t, email = :e, adresse = :a WHERE id_proprietaire = :id");
    $update->execute([
        'n' => $nom,
        'p' => $prenom,
        't' => $tel,
        'e' => $email,
        'a' => $adresse,
        'id' => $id_p
    ]);

    header('Location: liste_clients.php?success=update');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Client - SweetyDog</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .form-edit { max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        label { font-weight: bold; color: var(--vert-fonce); }
        .btn-save { background: var(--vert-fonce); color: white; border: none; padding: 15px; width: 100%; border-radius: 8px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="form-edit">
    <h2>✏️ Modifier le Propriétaire</h2>
    <form method="POST">
        <label>Nom</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($proprio['nom']) ?>" required>
        
        <label>Prénom</label>
        <input type="text" name="prenom" value="<?= htmlspecialchars($proprio['prenom']) ?>" required>
        
        <label>Téléphone</label>
        <input type="tel" name="tel" value="<?= htmlspecialchars($proprio['telephone']) ?>">
        
        <label>📧 Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($proprio['email'] ?? '') ?>">

        <label>📍 Adresse Postale</label>
        <textarea name="adresse" rows="3"><?= htmlspecialchars($proprio['adresse'] ?? '') ?></textarea>
        <button type="submit" name="modifier" class="btn-save">ENREGISTRER LES MODIFICATIONS</button>
        <a href="liste_clients.php" style="display:block; text-align:center; margin-top:15px; color:#666;">Annuler</a>
    </form>
</div>

</body>
</html>