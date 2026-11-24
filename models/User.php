<?php
require_once __DIR__ . '/../config.php';

class User {
    private static function db() { return Config::getConnexion(); }

    public static function emailExiste($email) {
        $s = self::db()->prepare("SELECT COUNT(*) FROM appuser WHERE email = ?");
        $s->execute([$email]);
        return $s->fetchColumn() > 0;
    }

    public static function trouverParEmail($email) {
        $s = self::db()->prepare("SELECT * FROM appuser WHERE email = ?");
        $s->execute([$email]);
        return $s->fetch();
    }

    public static function trouverParId($id) {
        $s = self::db()->prepare("SELECT * FROM appuser WHERE idUser = ?");
        $s->execute([$id]);
        return $s->fetch();
    }

    // Crée l'utilisateur et lui attribue le rôle par défaut
    public static function creer($d, $roles = ['Utilisateur']) {
        $db = self::db();
        $db->beginTransaction();
        try {
            $s = $db->prepare("INSERT INTO appuser
              (nom, prenom, email, password, telephone, adresse, dateInscription, statut, emailVerifie)
              VALUES (?, ?, ?, ?, ?, ?, ?, 'actif', 0)");
            $s->execute([$d['nom'], $d['prenom'], $d['email'],
                password_hash($d['password'], PASSWORD_DEFAULT),
                $d['telephone'], $d['adresse'], date('Y-m-d')]);
            $idUser = (int)$db->lastInsertId();

            $ins = $db->prepare("INSERT INTO userrole (idUser, idRole) VALUES (?, ?)");
            foreach ($roles as $nomRole) {
                $ins->execute([$idUser, self::roleId($nomRole)]);
            }
            $db->commit();
            return $idUser;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    // Retourne l'id du rôle (le crée s'il n'existe pas)
    private static function roleId($nom) {
        $db = self::db();
        $s = $db->prepare("SELECT idRole FROM role WHERE nomRole = ?");
        $s->execute([$nom]);
        $id = $s->fetchColumn();
        if (!$id) {
            $db->prepare("INSERT INTO role (nomRole) VALUES (?)")->execute([$nom]);
            $id = $db->lastInsertId();
        }
        return (int)$id;
    }

    public static function roles($idUser) {
        $s = self::db()->prepare("SELECT r.nomRole FROM role r
            JOIN userrole ur ON ur.idRole = r.idRole WHERE ur.idUser = ?");
        $s->execute([$idUser]);
        return $s->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function modifier($id, $d) {
        $sql = "UPDATE appuser SET nom=?, prenom=?, telephone=?, adresse=?";
        $p = [$d['nom'], $d['prenom'], $d['telephone'], $d['adresse']];
        if (!empty($d['password'])) {
            $sql .= ", password=?";
            $p[] = password_hash($d['password'], PASSWORD_DEFAULT);
        }
        $sql .= " WHERE idUser=?";
        $p[] = $id;
        return self::db()->prepare($sql)->execute($p);
    }
}
