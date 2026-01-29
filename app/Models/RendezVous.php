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

    public static function getCalendarEvents(): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT r.id_rdv,
                       r.titre,
                       r.date_debut,
                       r.date_fin,
                       a.nom_animal
                FROM RendezVous r
                JOIN Animaux a ON r.id_animal = a.id_animal";

        $stmt = $pdo->query($sql);
        $rdvs = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $events = [];
        foreach ($rdvs as $r) {
            $events[] = [
                'id' => $r['id_rdv'],
                'title' => $r['nom_animal'] . " - " . $r['titre'],
                'start' => $r['date_debut'],
                'end' => $r['date_fin'],
            ];
        }

        return $events;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO RendezVous (id_animal, titre, date_debut, date_fin)
                VALUES (:id_animal, :titre, :date_debut, :date_fin)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_animal' => $data['id_animal'],
            'titre' => $data['titre'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function delete(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM RendezVous WHERE id_rdv = :id");
        return $stmt->execute(['id' => $id]);
    }
}
