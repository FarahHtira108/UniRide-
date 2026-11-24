<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Admin.php';

function rep($ok, $message = '', $extra = [])
{
    $reponse = array_merge(
        ['ok' => $ok, 'message' => $message],
        $extra
    );

    echo json_encode($reponse, JSON_UNESCAPED_UNICODE);
    exit;
}

function champ($nomChamp)
{
    return trim($_POST[$nomChamp] ?? '');
}

$action = $_GET['action'] ?? '';


if (empty($_SESSION['idUser'])) {
    rep(false, 'Veuillez vous connecter.', ['code' => 'connexion']);
}


if (!Admin::estAdmin($_SESSION['idUser'])) {
    rep(false, 'Accès réservé aux administrateurs.', ['code' => 'interdit']);
}

$idMoi = (int) $_SESSION['idUser'];
$actionsModification = [
    'utilisateur_enregistrer',
    'utilisateur_statut',
    'utilisateur_supprimer',
    'role_enregistrer',
    'role_supprimer'
];

if (in_array($action, $actionsModification) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    rep(false, 'Méthode invalide.');
}

try {
    switch ($action) {
        case 'moi':
            $utilisateur = User::trouverParId($idMoi);
            $nomComplet = $utilisateur['prenom'] . ' ' . $utilisateur['nom'];
            rep(true, '', ['nom' => $nomComplet, 'idUser' => $idMoi]);

        case 'stats':
            $statistiques = Admin::stats();
            rep(true, '', ['stats' => $statistiques]);

        case 'utilisateurs':
            $statut = strtolower(trim($_GET['statut'] ?? ''));
            $recherche = trim($_GET['q'] ?? '');
            $utilisateurs = Admin::utilisateurs($recherche, $statut);

            rep(true, '', [
                'utilisateurs' => $utilisateurs,
                'moi' => $idMoi
            ]);

        case 'roles':
            $roles = Admin::roles();
            rep(true, '', ['roles' => $roles]);

        case 'utilisateur_enregistrer':
            $donnees = [
                'idUser' => (int) champ('idUser'),
                'nom' => champ('nom'),
                'prenom' => champ('prenom'),
                'email' => champ('email'),
                'telephone' => champ('telephone'),
                'adresse' => champ('adresse'),
                'statut' => champ('statut') === 'inactif' ? 'inactif' : 'actif',
                'emailVerifie' => isset($_POST['emailVerifie']) ? 1 : 0,
                'password' => $_POST['password'] ?? ''
            ];

            if (isset($_POST['roles']) && is_array($_POST['roles'])) {
                $rolesChoisis = $_POST['roles'];
            } else {
                $rolesChoisis = [];
            }

            if ($donnees['nom'] === '' || $donnees['prenom'] === '') {
                rep(false, 'Nom et prénom obligatoires.');
            }

            if (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
                rep(false, 'Email invalide.');
            }

            if (!preg_match('/^[0-9+ ]{8,15}$/', $donnees['telephone'])) {
                rep(false, 'Téléphone invalide.');
            }

            if (!$donnees['idUser'] && strlen($donnees['password']) < 8) {
                rep(false, 'Mot de passe : 8 caractères minimum.');
            }

            if ($donnees['idUser'] && $donnees['password'] !== '' && strlen($donnees['password']) < 8) {
                rep(false, 'Mot de passe : 8 caractères minimum.');
            }

            if (Admin::emailPris($donnees['email'], $donnees['idUser'])) {
                rep(false, 'Cet email est déjà utilisé.');
            }

            if ($donnees['idUser'] === $idMoi && $donnees['statut'] === 'inactif') {
                rep(false, 'Vous ne pouvez pas désactiver votre propre compte.');
            }

            if ($donnees['idUser'] === $idMoi) {
                $garderRoleAdmin = false;
                $tousLesRoles = Admin::roles();

                foreach ($tousLesRoles as $role) {
                    if (in_array($role['idRole'], $rolesChoisis) && stripos($role['nomRole'], 'admin') !== false) {
                        $garderRoleAdmin = true;
                    }
                }

                if (!$garderRoleAdmin) {
                    rep(false, 'Vous ne pouvez pas retirer votre propre rôle administrateur.');
                }
            }

            Admin::enregistrerUtilisateur($donnees, $rolesChoisis);

            if ($donnees['idUser']) {
                rep(true, 'Utilisateur modifié.');
            } else {
                rep(true, 'Utilisateur créé.');
            }

        case 'utilisateur_statut':
            $idUser = (int) champ('idUser');
            $statut = champ('statut') === 'inactif' ? 'inactif' : 'actif';

            if ($idUser === $idMoi && $statut === 'inactif') {
                rep(false, 'Vous ne pouvez pas désactiver votre propre compte.');
            }

            Admin::changerStatut($idUser, $statut);
            rep(true, 'Statut mis à jour.');

        case 'utilisateur_supprimer':
            $idUser = (int) champ('idUser');

            if ($idUser === $idMoi) {
                rep(false, 'Vous ne pouvez pas supprimer votre propre compte.');
            }

            Admin::supprimerUtilisateur($idUser);
            rep(true, 'Utilisateur supprimé.');

        case 'role_enregistrer':
            $idRole = (int) champ('idRole');
            $nomRole = champ('nomRole');

            if ($nomRole === '' || strlen($nomRole) > 50) {
                rep(false, 'Nom du rôle invalide (1 à 50 caractères).');
            }

            if (Admin::roleExiste($nomRole, $idRole)) {
                rep(false, 'Ce rôle existe déjà.');
            }

            Admin::enregistrerRole($idRole, $nomRole);

            if ($idRole) {
                rep(true, 'Rôle modifié.');
            } else {
                rep(true, 'Rôle créé.');
            }

        case 'role_supprimer':
            $idRole = (int) champ('idRole');

            if (Admin::nbUtilisateursRole($idRole) > 0) {
                rep(false, 'Impossible : des utilisateurs ont encore ce rôle.');
            }

            Admin::supprimerRole($idRole);
            rep(true, 'Rôle supprimé.');

        default:
            rep(false, 'Action inconnue.');
    }
} catch (Exception $e) {
    rep(false, 'Erreur serveur : ' . $e->getMessage());
}
