<?php
require_once __DIR__ . '/User.php';
class Admin
{
    private static function db()
    {
        return Config::getConnexion();
    }
    public static function estAdmin($idUser)
    {
        $roles = User::roles($idUser);

        foreach ($roles as $nomRole) {
            if (stripos($nomRole, 'admin') !== false) {
                return true;
            }
        }

        return false;
    }
    public static function stats()
    {
        $db = self::db();

        $total = (int) $db->query("SELECT COUNT(*) FROM appuser")->fetchColumn();
        $actifs = (int) $db->query("SELECT COUNT(*) FROM appuser WHERE LOWER(statut) = 'actif'")->fetchColumn();
        $mois = (int) $db->query("SELECT COUNT(*) FROM appuser WHERE dateInscription >= DATE_FORMAT(CURDATE(), '%Y-%m-01')")->fetchColumn();
        $verifies = (int) $db->query("SELECT COUNT(*) FROM appuser WHERE emailVerifie = 1")->fetchColumn();

        $sqlRoles = "SELECT r.nomRole, COUNT(ur.idUser) AS n
                     FROM role r
                     LEFT JOIN userrole ur ON ur.idRole = r.idRole
                     GROUP BY r.idRole, r.nomRole";
        $parRole = $db->query($sqlRoles)->fetchAll();

        $sqlDerniers = "SELECT idUser, nom, prenom, email, dateInscription, statut
                        FROM appuser
                        ORDER BY idUser DESC
                        LIMIT 5";
        $derniers = $db->query($sqlDerniers)->fetchAll();

        return [
            'total' => $total,
            'actifs' => $actifs,
            'inactifs' => $total - $actifs,
            'mois' => $mois,
            'verifies' => $verifies,
            'parRole' => $parRole,
            'derniers' => $derniers
        ];
    }

    public static function utilisateurs($recherche = '', $statut = '')
    {
        $sql = "SELECT u.idUser, u.nom, u.prenom, u.email, u.telephone, u.adresse, u.dateInscription,
                       u.statut, u.emailVerifie,
                       GROUP_CONCAT(r.idRole) AS roleIds,
                       GROUP_CONCAT(r.nomRole SEPARATOR ', ') AS roles
                FROM appuser u
                LEFT JOIN userrole ur ON ur.idUser = u.idUser
                LEFT JOIN role r ON r.idRole = ur.idRole
                WHERE (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? OR u.telephone LIKE ?)";

        $texteRecherche = '%' . $recherche . '%';
        $parametres = [
            $texteRecherche,
            $texteRecherche,
            $texteRecherche,
            $texteRecherche
        ];

        if ($statut !== '') {
            $sql .= " AND LOWER(u.statut) = ?";
            $parametres[] = $statut;
        }

        $sql .= " GROUP BY u.idUser ORDER BY u.idUser DESC";

        $db = self::db();
        $requete = $db->prepare($sql);
        $requete->execute($parametres);

        return $requete->fetchAll();
    }

    public static function emailPris($email, $exceptId = 0)
    {
        $sql = "SELECT COUNT(*) FROM appuser WHERE email = ? AND idUser <> ?";
        $db = self::db();
        $requete = $db->prepare($sql);
        $requete->execute([$email, $exceptId]);

        return $requete->fetchColumn() > 0;
    }

    private static function majRoles($idUser, $roles)
    {
        $db = self::db();
        $sqlSuppression = "DELETE FROM userrole WHERE idUser = ?";
        $suppression = $db->prepare($sqlSuppression);
        $suppression->execute([$idUser]);

        $sqlAjout = "INSERT INTO userrole (idUser, idRole) VALUES (?, ?)";
        $ajout = $db->prepare($sqlAjout);
        $roles = array_unique(array_map('intval', $roles));

        foreach ($roles as $idRole) {
            if ($idRole > 0) {
                $ajout->execute([$idUser, $idRole]);
            }
        }
    }

    public static function enregistrerUtilisateur($donnees, $roles)
    {
        $db = self::db();
        $db->beginTransaction();

        try {
            if (empty($donnees['idUser'])) {
                $sql = "INSERT INTO appuser
                        (nom, prenom, email, password, telephone, adresse, dateInscription, statut, emailVerifie)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $requete = $db->prepare($sql);
                $requete->execute([
                    $donnees['nom'],
                    $donnees['prenom'],
                    $donnees['email'],
                    password_hash($donnees['password'], PASSWORD_DEFAULT),
                    $donnees['telephone'],
                    $donnees['adresse'],
                    date('Y-m-d'),
                    $donnees['statut'],
                    $donnees['emailVerifie']
                ]);

                $idUser = (int) $db->lastInsertId();

            } else {
                $idUser = (int) $donnees['idUser'];

                $sql = "UPDATE appuser
                        SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?, statut = ?, emailVerifie = ?";

                $parametres = [
                    $donnees['nom'],
                    $donnees['prenom'],
                    $donnees['email'],
                    $donnees['telephone'],
                    $donnees['adresse'],
                    $donnees['statut'],
                    $donnees['emailVerifie']
                ];

                if ($donnees['password'] !== '') {
                    $sql .= ", password = ?";
                    $parametres[] = password_hash($donnees['password'], PASSWORD_DEFAULT);
                }

                $sql .= " WHERE idUser = ?";
                $parametres[] = $idUser;

                $requete = $db->prepare($sql);
                $requete->execute($parametres);
            }
            self::majRoles($idUser, $roles);
            $db->commit();

            return $idUser;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function changerStatut($id, $statut)
    {
        $sql = "UPDATE appuser SET statut = ? WHERE idUser = ?";
        $db = self::db();
        $requete = $db->prepare($sql);

        return $requete->execute([$statut, $id]);
    }

    public static function supprimerUtilisateur($id)
    {
        $db = self::db();
        $db->beginTransaction();

        try {
            $sqlRoles = "DELETE FROM userrole WHERE idUser = ?";
            $suppressionRoles = $db->prepare($sqlRoles);
            $suppressionRoles->execute([$id]);

            $sqlUtilisateur = "DELETE FROM appuser WHERE idUser = ?";
            $suppressionUtilisateur = $db->prepare($sqlUtilisateur);
            $suppressionUtilisateur->execute([$id]);

            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function roles()
    {
        $sql = "SELECT r.idRole, r.nomRole, COUNT(ur.idUser) AS nbUsers
                FROM role r
                LEFT JOIN userrole ur ON ur.idRole = r.idRole
                GROUP BY r.idRole, r.nomRole
                ORDER BY r.idRole";

        $db = self::db();
        return $db->query($sql)->fetchAll();
    }

    public static function roleExiste($nom, $exceptId = 0)
    {
        $sql = "SELECT COUNT(*) FROM role WHERE nomRole = ? AND idRole <> ?";
        $db = self::db();
        $requete = $db->prepare($sql);
        $requete->execute([$nom, $exceptId]);

        return $requete->fetchColumn() > 0;
    }

    public static function enregistrerRole($id, $nom)
    {
        $db = self::db();

        if ($id) {
            $sql = "UPDATE role SET nomRole = ? WHERE idRole = ?";
            $requete = $db->prepare($sql);
            return $requete->execute([$nom, $id]);
        }

        $sql = "INSERT INTO role (nomRole) VALUES (?)";
        $requete = $db->prepare($sql);

        return $requete->execute([$nom]);
    }

    public static function nbUtilisateursRole($id)
    {
        $sql = "SELECT COUNT(*) FROM userrole WHERE idRole = ?";
        $db = self::db();
        $requete = $db->prepare($sql);
        $requete->execute([$id]);

        return (int) $requete->fetchColumn();
    }


    public static function supprimerRole($id)
    {
        $sql = "DELETE FROM role WHERE idRole = ?";
        $db = self::db();
        $requete = $db->prepare($sql);

        return $requete->execute([$id]);
    }
}
