<?php
require_once __DIR__ . '/User.php';

class Admin {
    private static function db() { return Config::getConnexion(); }

    public static function estAdmin($idUser) {
        foreach (User::roles($idUser) as $r) {
            if (stripos($r, 'admin') !== false) return true;
        }
        return false;
    }

    // ---------- Statistiques ----------
    public static function stats() {
        $db = self::db();
        $total   = (int)$db->query("SELECT COUNT(*) FROM appuser")->fetchColumn();
        $actifs  = (int)$db->query("SELECT COUNT(*) FROM appuser WHERE LOWER(statut)='actif'")->fetchColumn();
        $mois    = (int)$db->query("SELECT COUNT(*) FROM appuser WHERE dateInscription >= DATE_FORMAT(CURDATE(),'%Y-%m-01')")->fetchColumn();
        $verifies = (int)$db->query("SELECT COUNT(*) FROM appuser WHERE emailVerifie = 1")->fetchColumn();
        $parRole = $db->query("SELECT r.nomRole, COUNT(ur.idUser) AS n FROM role r
                               LEFT JOIN userrole ur ON ur.idRole = r.idRole GROUP BY r.idRole, r.nomRole")->fetchAll();
        $derniers = $db->query("SELECT idUser, nom, prenom, email, dateInscription, statut
                                FROM appuser ORDER BY idUser DESC LIMIT 5")->fetchAll();
        return ['total' => $total, 'actifs' => $actifs, 'inactifs' => $total - $actifs,
                'mois' => $mois, 'verifies' => $verifies, 'parRole' => $parRole, 'derniers' => $derniers];
    }

    // ---------- Utilisateurs ----------
    public static function utilisateurs($q = '', $statut = '') {
        $sql = "SELECT u.idUser, u.nom, u.prenom, u.email, u.telephone, u.adresse, u.dateInscription,
                       u.statut, u.emailVerifie,
                       GROUP_CONCAT(r.idRole) AS roleIds, GROUP_CONCAT(r.nomRole SEPARATOR ', ') AS roles
                FROM appuser u
                LEFT JOIN userrole ur ON ur.idUser = u.idUser
                LEFT JOIN role r ON r.idRole = ur.idRole
                WHERE (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? OR u.telephone LIKE ?)";
        $like = '%' . $q . '%';
        $p = [$like, $like, $like, $like];
        if ($statut !== '') { $sql .= " AND LOWER(u.statut) = ?"; $p[] = $statut; }
        $sql .= " GROUP BY u.idUser ORDER BY u.idUser DESC";
        $s = self::db()->prepare($sql);
        $s->execute($p);
        return $s->fetchAll();
    }

    public static function emailPris($email, $exceptId = 0) {
        $s = self::db()->prepare("SELECT COUNT(*) FROM appuser WHERE email = ? AND idUser <> ?");
        $s->execute([$email, $exceptId]);
        return $s->fetchColumn() > 0;
    }

    private static function majRoles($idUser, $roles) {
        $db = self::db();
        $db->prepare("DELETE FROM userrole WHERE idUser = ?")->execute([$idUser]);
        $ins = $db->prepare("INSERT INTO userrole (idUser, idRole) VALUES (?, ?)");
        foreach (array_unique(array_map('intval', $roles)) as $idRole) {
            if ($idRole > 0) $ins->execute([$idUser, $idRole]);
        }
    }

    public static function enregistrerUtilisateur($d, $roles) {
        $db = self::db();
        $db->beginTransaction();
        try {
            if (empty($d['idUser'])) {
                $db->prepare("INSERT INTO appuser (nom, prenom, email, password, telephone, adresse, dateInscription, statut, emailVerifie)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")
                   ->execute([$d['nom'], $d['prenom'], $d['email'], password_hash($d['password'], PASSWORD_DEFAULT),
                              $d['telephone'], $d['adresse'], date('Y-m-d'), $d['statut'], $d['emailVerifie']]);
                $id = (int)$db->lastInsertId();
            } else {
                $id = (int)$d['idUser'];
                $sql = "UPDATE appuser SET nom=?, prenom=?, email=?, telephone=?, adresse=?, statut=?, emailVerifie=?";
                $p = [$d['nom'], $d['prenom'], $d['email'], $d['telephone'], $d['adresse'], $d['statut'], $d['emailVerifie']];
                if ($d['password'] !== '') { $sql .= ", password=?"; $p[] = password_hash($d['password'], PASSWORD_DEFAULT); }
                $sql .= " WHERE idUser=?"; $p[] = $id;
                $db->prepare($sql)->execute($p);
            }
            self::majRoles($id, $roles);
            $db->commit();
            return $id;
        } catch (Exception $e) { $db->rollBack(); throw $e; }
    }

    public static function changerStatut($id, $statut) {
        return self::db()->prepare("UPDATE appuser SET statut=? WHERE idUser=?")->execute([$statut, $id]);
    }

    public static function supprimerUtilisateur($id) {
        $db = self::db();
        $db->beginTransaction();
        try {
            $db->prepare("DELETE FROM userrole WHERE idUser=?")->execute([$id]);
            $db->prepare("DELETE FROM appuser WHERE idUser=?")->execute([$id]);
            $db->commit();
        } catch (Exception $e) { $db->rollBack(); throw $e; }
    }

    // ---------- Rôles ----------
    public static function roles() {
        return self::db()->query("SELECT r.idRole, r.nomRole, COUNT(ur.idUser) AS nbUsers
                                  FROM role r LEFT JOIN userrole ur ON ur.idRole = r.idRole
                                  GROUP BY r.idRole, r.nomRole ORDER BY r.idRole")->fetchAll();
    }
    public static function roleExiste($nom, $exceptId = 0) {
        $s = self::db()->prepare("SELECT COUNT(*) FROM role WHERE nomRole = ? AND idRole <> ?");
        $s->execute([$nom, $exceptId]);
        return $s->fetchColumn() > 0;
    }
    public static function enregistrerRole($id, $nom) {
        if ($id) return self::db()->prepare("UPDATE role SET nomRole=? WHERE idRole=?")->execute([$nom, $id]);
        return self::db()->prepare("INSERT INTO role (nomRole) VALUES (?)")->execute([$nom]);
    }
    public static function nbUtilisateursRole($id) {
        $s = self::db()->prepare("SELECT COUNT(*) FROM userrole WHERE idRole=?");
        $s->execute([$id]);
        return (int)$s->fetchColumn();
    }
    public static function supprimerRole($id) {
        return self::db()->prepare("DELETE FROM role WHERE idRole=?")->execute([$id]);
    }
}
