<?php

class Prestation{
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $sql = "INSERT INTO Prestations (id_animal, date_soin, type_soin, prix, notes)
                VALUES (:id_animal, :date_soin, :type_soin, :prix, :notes)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_animal' => $data['id_animal'],
            'date_soin' => $data['date_soin'],
            'type_soin' => $data['type_soin'],
            'prix'      => $data['prix'],
            'notes'     => $data['notes'],
        ]);
        return (int)$pdo->lastInsertId();
    }
}
