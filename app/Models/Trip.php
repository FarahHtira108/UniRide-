<?php
namespace App\Models;

class Trip
{
    private static function demo(): array
    {
        return [
            ['ID_TRIP'=>1,'CONDUCTEUR'=>'Salma B.','LIEU_DEPART'=>'Tunis','LIEU_ARRIVEE'=>'ESPRIT','DATE_DEPART'=>'2026-12-01 08:00:00','PRIX'=>25,'NB_PLACES_DISPONIBLES'=>3,'DESCRIPTION'=>'Voiture climatisée, départ à l\'heure.'],
            ['ID_TRIP'=>2,'CONDUCTEUR'=>'Ahmed M.','LIEU_DEPART'=>'Tunis','LIEU_ARRIVEE'=>'ESPRIT','DATE_DEPART'=>'2026-12-01 08:30:00','PRIX'=>25,'NB_PLACES_DISPONIBLES'=>2,'DESCRIPTION'=>'Trajet confortable.'],
            ['ID_TRIP'=>3,'CONDUCTEUR'=>'Aya K.','LIEU_DEPART'=>'La Marsa','LIEU_ARRIVEE'=>'ESPRIT','DATE_DEPART'=>'2026-12-01 09:00:00','PRIX'=>20,'NB_PLACES_DISPONIBLES'=>1,'DESCRIPTION'=>'Pas de bagages volumineux.'],
        ];
    }
    public static function all(array $f = []): array
    {
        $pdo = Database::pdo();
        if (!$pdo) {
            return array_values(array_filter(self::demo(), fn($t) =>
                (empty($f['depart']) || stripos($t['LIEU_DEPART'], $f['depart']) !== false) &&
                (empty($f['arrivee']) || stripos($t['LIEU_ARRIVEE'], $f['arrivee']) !== false) &&
                (empty($f['prix_max']) || $t['PRIX'] <= $f['prix_max'])));
        }
        $sql = "SELECT t.*, CONCAT(u.PRENOM,' ',LEFT(u.NOM,1),'.') AS CONDUCTEUR FROM trip t
                JOIN app_user u ON u.ID_USER=t.ID_USER WHERE t.STATUT='EN_LIGNE' AND t.NB_PLACES_DISPONIBLES>0";
        $p = [];
        if (!empty($f['depart']))   { $sql .= ' AND t.LIEU_DEPART LIKE ?';  $p[] = '%' . $f['depart'] . '%'; }
        if (!empty($f['arrivee']))  { $sql .= ' AND t.LIEU_ARRIVEE LIKE ?'; $p[] = '%' . $f['arrivee'] . '%'; }
        if (!empty($f['prix_max'])) { $sql .= ' AND t.PRIX <= ?';           $p[] = $f['prix_max']; }
        $st = $pdo->prepare($sql . ' ORDER BY t.DATE_DEPART'); $st->execute($p);
        return $st->fetchAll();
    }
    public static function find(int $id): ?array
    {
        foreach (self::all() as $t) if ((int)$t['ID_TRIP'] === $id) return $t;
        return null;
    }
    public static function create(array $d): bool
    {
        $pdo = Database::pdo(); if (!$pdo) return false;
        $st = $pdo->prepare("INSERT INTO trip (ID_USER,LIEU_DEPART,LIEU_ARRIVEE,DATE_DEPART,PRIX,NB_PLACES_DISPONIBLES,DESCRIPTION,STATUT)
                             VALUES (?,?,?,?,?,?,?,'EN_LIGNE')");
        return $st->execute([$d['id_user'], $d['depart'], $d['arrivee'], $d['date'], $d['prix'], $d['places'], $d['description'] ?? '']);
    }
}
