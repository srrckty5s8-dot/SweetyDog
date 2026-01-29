<?php

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            require __DIR__ . '/../../config/db.php'; // définit $bdd
            self::$pdo = $bdd;
        }

        return self::$pdo;
    }
}
