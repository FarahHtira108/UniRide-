<?php
namespace App\Models;

class Database
{
    private static ?\PDO $pdo = null;
    public static function pdo(): ?\PDO
    {
        if (self::$pdo) return self::$pdo;
        try {
            return self::$pdo = new \PDO('mysql:host=localhost;dbname=uniride;charset=utf8mb4', 'root', '', [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        } catch (\PDOException $e) { return null; } // mode démo si la BD n'existe pas encore
    }
}
