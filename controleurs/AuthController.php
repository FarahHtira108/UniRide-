<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/User.php';

function rep($ok, $message = '', $extra = []) {
    echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}
function champ($k) { return trim($_POST[$k] ?? ''); }

$action = $_GET['action'] ?? '';
if ($action !== 'session' && $_SERVER['REQUEST_METHOD'] !== 'POST') rep(false, 'Méthode invalide.');

try {
    switch ($action) {

        case 'session': // l'utilisateur est-il connecté ?
            if (empty($_SESSION['idUser'])) rep(false);
            $u = User::trouverParId($_SESSION['idUser']);
            if (!$u) rep(false);
            unset($u['password']);
            rep(true, '', ['user' => $u, 'roles' => User::roles($u['idUser'])]);

        case 'inscription':
            $d = ['nom' => champ('nom'), 'prenom' => champ('prenom'), 'email' => champ('email'),
                  'telephone' => champ('telephone'), 'adresse' => champ('adresse'),
                  'password' => $_POST['password'] ?? ''];
            if ($d['nom'] === '' || $d['prenom'] === '')           rep(false, 'Nom et prénom obligatoires.');
            if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL))    rep(false, 'Email invalide.');
            if (!preg_match('/^[0-9+ ]{8,15}$/', $d['telephone']))  rep(false, 'Téléphone invalide.');
            if (strlen($d['password']) < 8)                         rep(false, 'Mot de passe : 8 caractères minimum.');
            if ($d['password'] !== ($_POST['confirm'] ?? ''))       rep(false, 'Les mots de passe ne correspondent pas.');
            if (User::emailExiste($d['email']))                     rep(false, 'Cet email est déjà utilisé.');
            // Rôles autorisés à l'inscription : l'administrateur ne peut JAMAIS être choisi ici
            $autorises = ['Utilisateur', 'Conducteur'];
            $roles = array_values(array_intersect($autorises, (array)($_POST['roles'] ?? [])));
            if (!$roles) rep(false, 'Choisissez au moins un rôle : Utilisateur ou Conducteur.');
            User::creer($d, $roles);
            rep(true, 'Compte créé ! Vous pouvez vous connecter.');

        case 'connexion':
            $u = User::trouverParEmail(champ('email'));
            if ($u && strlen($u['password']) < 60)
                rep(false, 'La colonne password de la table appuser est trop courte. Exécutez : ALTER TABLE appuser MODIFY password VARCHAR(255); puis recréez le compte.');
            if (!$u || !password_verify($_POST['password'] ?? '', $u['password']))
                rep(false, 'Email ou mot de passe incorrect.');
            if (strtolower(trim($u['statut'])) !== 'actif') rep(false, 'Votre compte est désactivé.');
            session_regenerate_id(true);
            $_SESSION['idUser'] = $u['idUser'];
            rep(true, 'Connexion réussie.');

        case 'profil':
            if (empty($_SESSION['idUser'])) rep(false, 'Veuillez vous connecter.');
            $d = ['nom' => champ('nom'), 'prenom' => champ('prenom'),
                  'telephone' => champ('telephone'), 'adresse' => champ('adresse'),
                  'password' => $_POST['password'] ?? ''];
            if ($d['nom'] === '' || $d['prenom'] === '')            rep(false, 'Nom et prénom obligatoires.');
            if (!preg_match('/^[0-9+ ]{8,15}$/', $d['telephone']))   rep(false, 'Téléphone invalide.');
            if ($d['password'] !== '' && strlen($d['password']) < 8) rep(false, 'Mot de passe : 8 caractères minimum.');
            User::modifier($_SESSION['idUser'], $d);
            rep(true, 'Profil mis à jour.');

        case 'deconnexion':
            session_destroy();
            rep(true, 'Déconnecté.');

        default:
            rep(false, 'Action inconnue.');
    }
} catch (Exception $e) {
    rep(false, 'Erreur serveur : ' . $e->getMessage());
}
