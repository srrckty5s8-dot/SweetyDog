<?php

class Animal
{
    public static function findWithOwner(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $pdo = Database::getConnection();

        $sql = "SELECT a.*, p.nom, p.prenom
                FROM Animaux a
                INNER JOIN Proprietaires p 
                  ON p.id_proprietaire = a.id_proprietaire
                WHERE a.id_animal = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();

        $sql = "UPDATE Animaux
                SET nom_animal = :nom_animal,
                    espece = :espece,
                    race = :race,
                    poids = :poids,
                    steril = :steril
                WHERE id_animal = :id";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'nom_animal' => $data['nom_animal'],
            'espece' => $data['espece'],
            'race' => $data['race'],
            'poids' => $data['poids'],
            'steril' => $data['steril'],
            'id' => $id,
        ]);
    }
}
