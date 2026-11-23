<?php
namespace App\Models;

class User
{
    private static function db(): ?\PDO { return Database::pdo(); }

    public static function findByEmail(string $email): ?array
    {
        if (!$pdo = self::db()) return null;
        $st = $pdo->prepare('SELECT * FROM app_user WHERE EMAIL=?'); $st->execute([$email]);
        return $st->fetch() ?: null;
    }
    public static function find(int $id): ?array
    {
        if (!$pdo = self::db()) return null;
        $st = $pdo->prepare('SELECT * FROM app_user WHERE ID_USER=?'); $st->execute([$id]);
        return $st->fetch() ?: null;
    }
    public static function roles(int $id): array
    {
        if (!$pdo = self::db()) return [];
        $st = $pdo->prepare('SELECT r.NOM_ROLE FROM user_role ur JOIN role r ON r.ID_ROLE=ur.ID_ROLE WHERE ur.ID_USER=?');
        $st->execute([$id]);
        return $st->fetchAll(\PDO::FETCH_COLUMN);
    }
    public static function create(array $d): bool
    {
        if (!($pdo = self::db()) || self::findByEmail($d['email'])) return false;
        $pdo->beginTransaction();
        try {
            $pdo->prepare("INSERT INTO app_user (NOM,PRENOM,EMAIL,PASSWORD,TELEPHONE,DATE_INSCRIPTION,STATUT) VALUES (?,?,?,?,?,NOW(),'ACTIF')")
                ->execute([$d['nom'], $d['prenom'], $d['email'], password_hash($d['password'], PASSWORD_DEFAULT), $d['telephone'] ?? '']);
            $id = (int)$pdo->lastInsertId();
            self::addRole($id, 'PASSAGER');
            if (!empty($d['conducteur'])) self::addRole($id, 'CONDUCTEUR');
            $pdo->prepare('INSERT INTO fidelite_compte (ID_USER) VALUES (?)')->execute([$id]);
            $pdo->commit(); return true;
        } catch (\Throwable $e) { $pdo->rollBack(); return false; }
    }
    public static function addRole(int $id, string $role): void
    {
        self::db()->prepare('INSERT IGNORE INTO user_role SELECT ?, ID_ROLE FROM role WHERE NOM_ROLE=?')->execute([$id, $role]);
    }
    public static function toggleRole(int $id, string $role): void
    {
        if (in_array($role, self::roles($id), true))
            self::db()->prepare('DELETE FROM user_role WHERE ID_USER=? AND ID_ROLE=(SELECT ID_ROLE FROM role WHERE NOM_ROLE=?)')->execute([$id, $role]);
        else self::addRole($id, $role);
    }
    public static function updateProfile(int $id, array $d): bool
    {
        if (!$pdo = self::db()) return false;
        return $pdo->prepare('UPDATE app_user SET NOM=?, PRENOM=?, TELEPHONE=?, ADRESSE=? WHERE ID_USER=?')
                   ->execute([$d['nom'], $d['prenom'], $d['telephone'], $d['adresse'], $id]);
    }
    public static function all(): array
    {
        if (!$pdo = self::db()) return [];
        return $pdo->query("SELECT u.*, GROUP_CONCAT(r.NOM_ROLE) AS ROLES FROM app_user u
            LEFT JOIN user_role ur ON ur.ID_USER=u.ID_USER LEFT JOIN role r ON r.ID_ROLE=ur.ID_ROLE
            GROUP BY u.ID_USER ORDER BY u.DATE_INSCRIPTION DESC")->fetchAll();
    }
    public static function setStatut(int $id, string $s): void { self::db()->prepare('UPDATE app_user SET STATUT=? WHERE ID_USER=?')->execute([$s, $id]); }
    public static function delete(int $id): bool
    {
        try {
            $pdo = self::db();
            $pdo->prepare('DELETE FROM user_role WHERE ID_USER=?')->execute([$id]);
            $pdo->prepare('DELETE FROM fidelite_compte WHERE ID_USER=?')->execute([$id]);
            return $pdo->prepare('DELETE FROM app_user WHERE ID_USER=?')->execute([$id]);
        } catch (\PDOException $e) { return false; } // l'utilisateur a des données liées : bloquez-le plutôt
    }
    public static function count(): int { return count(self::all()); }
}
