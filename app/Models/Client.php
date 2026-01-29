<?php

class Client
{
    public static function getAllWithAnimaux(string $search = '')
    {
        $pdo = Database::getConnection();

        $sql = "SELECT a.*, p.nom, p.prenom, p.telephone, p.email, p.adresse
                FROM Animaux a
                INNER JOIN Proprietaires p ON a.id_proprietaire = p.id_proprietaire";

        if ($search) {
            $sql .= " WHERE p.nom LIKE :search
                      OR p.prenom LIKE :search
                      OR a.nom_animal LIKE :search
                      OR a.race LIKE :search";
        }

        $sql .= " ORDER BY p.nom ASC, a.nom_animal ASC";

        $stmt = $pdo->prepare($sql);

        if ($search) {
            $stmt->execute(['search' => "%$search%"]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function create(array $data): int
{
    $pdo = Database::getConnection();

    $sql = "INSERT INTO Proprietaires (nom, prenom, telephone, email, adresse)
            VALUES (:nom, :prenom, :telephone, :email, :adresse)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'telephone' => $data['telephone'],
        'email' => $data['email'],
        'adresse' => $data['adresse'],
    ]);

    return (int)$pdo->lastInsertId();
}
public static function getAllProprietaires(): array
{
    $pdo = Database::getConnection();

    $sql = "SELECT id_proprietaire, nom, prenom, telephone
            FROM Proprietaires
            ORDER BY nom ASC";

    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
public static function createProprietaire(array $data): int
{
    $pdo = Database::getConnection();

    $sql = "INSERT INTO Proprietaires (nom, prenom, telephone, email, adresse)
            VALUES (:nom, :prenom, :telephone, :email, :adresse)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'telephone' => $data['telephone'],
        'email' => $data['email'],
        'adresse' => $data['adresse'],
    ]);

    return (int)$pdo->lastInsertId();
}

public static function createAnimal(array $data): int
{
    $pdo = Database::getConnection();

    // ⚠️ adapte les noms de colonnes si besoin (je me base sur ton SQL existant)
    $sql = "INSERT INTO Animaux (id_proprietaire, nom_animal, espece, race, poids, steril)
            VALUES (:id_proprietaire, :nom_animal, :espece, :race, :poids, :steril)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id_proprietaire' => $data['id_proprietaire'],
        'nom_animal' => $data['nom_animal'],
        'espece' => $data['espece'],
        'race' => $data['race'],
        'poids' => $data['poids'],
        'steril' => $data['sterilise'],
    ]);

    return (int)$pdo->lastInsertId();
}
public static function findProprietaire(int $id): ?array
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("SELECT * FROM Proprietaires WHERE id_proprietaire = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

public static function updateProprietaire(int $id, array $data): bool
{
    $pdo = Database::getConnection();

    $sql = "UPDATE Proprietaires
            SET nom = :nom,
                prenom = :prenom,
                telephone = :telephone,
                email = :email,
                adresse = :adresse
            WHERE id_proprietaire = :id";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'telephone' => $data['telephone'],
        'email' => $data['email'],
        'adresse' => $data['adresse'],
        'id' => $id,
    ]);
}


}
