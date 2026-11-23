<?php
namespace App\Models;

class Vehicle
{
    public static function forUser(int $uid): array
    {
        $st = Database::pdo()->prepare('SELECT * FROM vehicle WHERE ID_USER=? ORDER BY ID_VEHICLE DESC'); $st->execute([$uid]);
        return $st->fetchAll();
    }
    public static function find(int $id, int $uid): ?array
    {
        $st = Database::pdo()->prepare('SELECT * FROM vehicle WHERE ID_VEHICLE=? AND ID_USER=?'); $st->execute([$id, $uid]);
        return $st->fetch() ?: null;
    }
    public static function save(array $d, int $uid, ?int $id = null): bool
    {
        $v = [$d['marque'], $d['modele'], $d['immatriculation'], $d['couleur'], (int)$d['annee'], (int)$d['nb_places'], $d['type']];
        $pdo = Database::pdo();
        if ($id) return $pdo->prepare('UPDATE vehicle SET MARQUE=?,MODELE=?,IMMATRICULATION=?,COULEUR=?,ANNEE=?,NB_PLACES=?,TYPE_VEHICLE=? WHERE ID_VEHICLE=? AND ID_USER=?')
                            ->execute([...$v, $id, $uid]);
        return $pdo->prepare('INSERT INTO vehicle (MARQUE,MODELE,IMMATRICULATION,COULEUR,ANNEE,NB_PLACES,TYPE_VEHICLE,ID_USER) VALUES (?,?,?,?,?,?,?,?)')
                   ->execute([...$v, $uid]);
    }
    public static function delete(int $id, int $uid): bool
    {
        try { return Database::pdo()->prepare('DELETE FROM vehicle WHERE ID_VEHICLE=? AND ID_USER=?')->execute([$id, $uid]); }
        catch (\PDOException $e) { return false; }
    }
}
