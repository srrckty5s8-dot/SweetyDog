<?php

class RendezVous
{
    public static function getToday()
    {
        $pdo = Database::getConnection();

        $today = date('Y-m-d');

        $sql = "SELECT r.*, a.nom_animal
                FROM RendezVous r
                JOIN Animaux a ON r.id_animal = a.id_animal
                WHERE DATE(r.date_debut) = :today
                ORDER BY r.date_debut ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['today' => $today]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
