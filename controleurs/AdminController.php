<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/Admin.php';

function rep($ok, $message = '', $extra = []) {
    echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}
function champ($k) { return trim($_POST[$k] ?? ''); }

$action = $_GET['action'] ?? '';

// Accès réservé aux administrateurs
if (empty($_SESSION['idUser']))
    rep(false, 'Veuillez vous connecter.', ['code' => 'connexion']);
if (!Admin::estAdmin($_SESSION['idUser']))
    rep(false, 'Accès réservé aux administrateurs.', ['code' => 'interdit']);

$moi = (int)$_SESSION['idUser'];
$mutations = ['utilisateur_enregistrer', 'utilisateur_statut', 'utilisateur_supprimer', 'role_enregistrer', 'role_supprimer'];
if (in_array($action, $mutations) && $_SERVER['REQUEST_METHOD'] !== 'POST') rep(false, 'Méthode invalide.');

try {
    switch ($action) {

        case 'moi':
            $u = User::trouverParId($moi);
            rep(true, '', ['nom' => $u['prenom'] . ' ' . $u['nom'], 'idUser' => $moi]);

        case 'stats':
            rep(true, '', ['stats' => Admin::stats()]);

        case 'utilisateurs':
            $st = strtolower(trim($_GET['statut'] ?? ''));
            rep(true, '', ['utilisateurs' => Admin::utilisateurs(trim($_GET['q'] ?? ''), $st), 'moi' => $moi]);

        case 'roles':
            rep(true, '', ['roles' => Admin::roles()]);

        case 'utilisateur_enregistrer':
            $d = ['idUser' => (int)champ('idUser'), 'nom' => champ('nom'), 'prenom' => champ('prenom'),
                  'email' => champ('email'), 'telephone' => champ('telephone'), 'adresse' => champ('adresse'),
                  'statut' => champ('statut') === 'inactif' ? 'inactif' : 'actif',
                  'emailVerifie' => isset($_POST['emailVerifie']) ? 1 : 0,
                  'password' => $_POST['password'] ?? ''];
            $roles = isset($_POST['roles']) && is_array($_POST['roles']) ? $_POST['roles'] : [];

            if ($d['nom'] === '' || $d['prenom'] === '')           rep(false, 'Nom et prénom obligatoires.');
            if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL))    rep(false, 'Email invalide.');
            if (!preg_match('/^[0-9+ ]{8,15}$/', $d['telephone']))  rep(false, 'Téléphone invalide.');
            if (!$d['idUser'] && strlen($d['password']) < 8)        rep(false, 'Mot de passe : 8 caractères minimum.');
            if ($d['idUser'] && $d['password'] !== '' && strlen($d['password']) < 8) rep(false, 'Mot de passe : 8 caractères minimum.');
            if (Admin::emailPris($d['email'], $d['idUser']))        rep(false, 'Cet email est déjà utilisé.');
            if ($d['idUser'] === $moi && $d['statut'] === 'inactif') rep(false, 'Vous ne pouvez pas désactiver votre propre compte.');

            if ($d['idUser'] === $moi) {   // évite de se retirer soi-même le rôle administrateur
                $garde = false;
                foreach (Admin::roles() as $r) {
                    if (in_array($r['idRole'], $roles) && stripos($r['nomRole'], 'admin') !== false) $garde = true;
                }
                if (!$garde) rep(false, 'Vous ne pouvez pas retirer votre propre rôle administrateur.');
            }
            Admin::enregistrerUtilisateur($d, $roles);
            rep(true, $d['idUser'] ? 'Utilisateur modifié.' : 'Utilisateur créé.');

        case 'utilisateur_statut':
            $id = (int)champ('idUser');
            $st = champ('statut') === 'inactif' ? 'inactif' : 'actif';
            if ($id === $moi && $st === 'inactif') rep(false, 'Vous ne pouvez pas désactiver votre propre compte.');
            Admin::changerStatut($id, $st);
            rep(true, 'Statut mis à jour.');

        case 'utilisateur_supprimer':
            $id = (int)champ('idUser');
            if ($id === $moi) rep(false, 'Vous ne pouvez pas supprimer votre propre compte.');
            Admin::supprimerUtilisateur($id);
            rep(true, 'Utilisateur supprimé.');

        case 'role_enregistrer':
            $id = (int)champ('idRole'); $nom = champ('nomRole');
            if ($nom === '' || strlen($nom) > 50) rep(false, 'Nom du rôle invalide (1 à 50 caractères).');
            if (Admin::roleExiste($nom, $id))     rep(false, 'Ce rôle existe déjà.');
            Admin::enregistrerRole($id, $nom);
            rep(true, $id ? 'Rôle modifié.' : 'Rôle créé.');

        case 'role_supprimer':
            $id = (int)champ('idRole');
            if (Admin::nbUtilisateursRole($id) > 0) rep(false, 'Impossible : des utilisateurs ont encore ce rôle.');
            Admin::supprimerRole($id);
            rep(true, 'Rôle supprimé.');

        default:
            rep(false, 'Action inconnue.');
    }
} catch (Exception $e) {
    rep(false, 'Erreur serveur : ' . $e->getMessage());
}
