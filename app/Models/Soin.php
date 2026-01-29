<?php

class Soin
{
    public static function findByAnimal(int $id_animal): array
    {
        if ($id_animal <= 0) {
            return [];
        }

        $pdo = Database::getConnection();

        $sql = "
            SELECT
                p.id_prestation,
                p.id_animal,
                p.date_soin,
                p.type_soin,
                p.notes,
                p.prix
            FROM Prestations p
            WHERE p.id_animal = :id
            ORDER BY p.date_soin DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id_animal]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
