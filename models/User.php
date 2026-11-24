<?php
// Inclure le fichier 
require_once __DIR__ . '/../config.php';

class User
{
    // Recuperer la connexion a la base de donnees.
    private static function db()
    {
        return Config::getConnexion();
    }

    // Verifier si un email existe deja dans la table appuser.
    public static function emailExiste($email)
    {
        $sql = "SELECT COUNT(*) FROM appuser WHERE email = ?";
        $db = self::db();
        $requete = $db->prepare($sql);
        $requete->execute([$email]);

        return $requete->fetchColumn() > 0;
    }

    // Chercher un utilisateur a partir de son email.
    public static function trouverParEmail($email)
    {
        $sql = "SELECT * FROM appuser WHERE email = ?";
        $db = self::db();
        $requete = $db->prepare($sql);
        $requete->execute([$email]);

        return $requete->fetch();
    }

    // Chercher un utilisateur a partir de son identifiant.
    public static function trouverParId($id)
    {
        $sql = "SELECT * FROM appuser WHERE idUser = ?";
        $db = self::db();
        $requete = $db->prepare($sql);
        $requete->execute([$id]);

        return $requete->fetch();
    }

    // Ajouter un utilisateur et lui attribuer ses roles.
    public static function creer($donnees, $roles = ['Utilisateur'])
    {
        $db = self::db();

        // Demarrer une transaction pour enregistrer toutes les donnees ensemble.
        $db->beginTransaction();

        try {
            $sql = "INSERT INTO appuser
                    (nom, prenom, email, password, telephone, adresse, dateInscription, statut, emailVerifie)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'actif', 0)";

            $requete = $db->prepare($sql);
            $requete->execute([
                $donnees['nom'],
                $donnees['prenom'],
                $donnees['email'],
                password_hash($donnees['password'], PASSWORD_DEFAULT),
                $donnees['telephone'],
                $donnees['adresse'],
                date('Y-m-d')
            ]);

            // Recuperer l'identifiant du nouvel utilisateur.
            $idUser = (int) $db->lastInsertId();

            // Ajouter chaque role dans la table de liaison userrole.
            $sqlRole = "INSERT INTO userrole (idUser, idRole) VALUES (?, ?)";
            $ajoutRole = $db->prepare($sqlRole);

            foreach ($roles as $nomRole) {
                $idRole = self::roleId($nomRole);
                $ajoutRole->execute([$idUser, $idRole]);
            }

            $db->commit();
            return $idUser;

        } catch (Exception $e) {
            // Annuler les modifications si une erreur se produit.
            $db->rollBack();
            throw $e;
        }
    }

    // Recuperer l'identifiant d'un role. Creer le role s'il n'existe pas.
    private static function roleId($nomRole)
    {
        $db = self::db();
        $sql = "SELECT idRole FROM role WHERE nomRole = ?";
        $requete = $db->prepare($sql);
        $requete->execute([$nomRole]);
        $idRole = $requete->fetchColumn();

        if (!$idRole) {
            $sql = "INSERT INTO role (nomRole) VALUES (?)";
            $ajout = $db->prepare($sql);
            $ajout->execute([$nomRole]);
            $idRole = $db->lastInsertId();
        }

        return (int) $idRole;
    }

    // Recuperer tous les roles attribues a un utilisateur.
    public static function roles($idUser)
    {
        $sql = "SELECT r.nomRole
                FROM role r
                JOIN userrole ur ON ur.idRole = r.idRole
                WHERE ur.idUser = ?";

        $db = self::db();
        $requete = $db->prepare($sql);
        $requete->execute([$idUser]);

        return $requete->fetchAll(PDO::FETCH_COLUMN);
    }

    // Modifier les informations personnelles d'un utilisateur.
    public static function modifier($id, $donnees)
    {
        $sql = "UPDATE appuser SET nom = ?, prenom = ?, telephone = ?, adresse = ?";
        $parametres = [
            $donnees['nom'],
            $donnees['prenom'],
            $donnees['telephone'],
            $donnees['adresse']
        ];

        // Modifier le mot de passe seulement si un nouveau mot de passe est saisi.
        if (!empty($donnees['password'])) {
            $sql .= ", password = ?";
            $parametres[] = password_hash($donnees['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE idUser = ?";
        $parametres[] = $id;

        $db = self::db();
        $requete = $db->prepare($sql);

        return $requete->execute($parametres);
    }
}
