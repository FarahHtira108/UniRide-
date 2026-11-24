<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/User.php';
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
if ($action !== 'session' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    rep(false, 'Méthode invalide.');
}

try {
    switch ($action) {
        case 'session':
            if (empty($_SESSION['idUser'])) {
                rep(false);
            }

            $utilisateur = User::trouverParId($_SESSION['idUser']);

            if (!$utilisateur) {
                rep(false);
            }
            unset($utilisateur['password']);

            $roles = User::roles($utilisateur['idUser']);
            rep(true, '', ['user' => $utilisateur, 'roles' => $roles]);

        case 'inscription':
            $donnees = [
                'nom' => champ('nom'),
                'prenom' => champ('prenom'),
                'email' => champ('email'),
                'telephone' => champ('telephone'),
                'adresse' => champ('adresse'),
                'password' => $_POST['password'] ?? ''
            ];

            if ($donnees['nom'] === '' || $donnees['prenom'] === '') {
                rep(false, 'Nom et prénom obligatoires.');
            }

            if (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
                rep(false, 'Email invalide.');
            }

            if (!preg_match('/^[0-9+ ]{8,15}$/', $donnees['telephone'])) {
                rep(false, 'Téléphone invalide.');
            }

            if (strlen($donnees['password']) < 8) {
                rep(false, 'Mot de passe : 8 caractères minimum.');
            }

            if ($donnees['password'] !== ($_POST['confirm'] ?? '')) {
                rep(false, 'Les mots de passe ne correspondent pas.');
            }

            if (User::emailExiste($donnees['email'])) {
                rep(false, 'Cet email est déjà utilisé.');
            }

            $rolesAutorises = ['Utilisateur', 'Conducteur'];
            $rolesChoisis = array_values(
                array_intersect($rolesAutorises, (array) ($_POST['roles'] ?? []))
            );

            if (!$rolesChoisis) {
                rep(false, 'Choisissez au moins un rôle : Utilisateur ou Conducteur.');
            }

            User::creer($donnees, $rolesChoisis);
            rep(true, 'Compte créé ! Vous pouvez vous connecter.');

        case 'connexion':
            $utilisateur = User::trouverParEmail(champ('email'));

            if ($utilisateur && strlen($utilisateur['password']) < 60) {
                rep(false, 'La colonne password de la table appuser est trop courte. Exécutez : ALTER TABLE appuser MODIFY password VARCHAR(255); puis recréez le compte.');
            }

            if (!$utilisateur || !password_verify($_POST['password'] ?? '', $utilisateur['password'])) {
                rep(false, 'Email ou mot de passe incorrect.');
            }

            if (strtolower(trim($utilisateur['statut'])) !== 'actif') {
                rep(false, 'Votre compte est désactivé.');
            }
            session_regenerate_id(true);
            $_SESSION['idUser'] = $utilisateur['idUser'];

            rep(true, 'Connexion réussie.');

        case 'profil':
            if (empty($_SESSION['idUser'])) {
                rep(false, 'Veuillez vous connecter.');
            }

            $donnees = [
                'nom' => champ('nom'),
                'prenom' => champ('prenom'),
                'telephone' => champ('telephone'),
                'adresse' => champ('adresse'),
                'password' => $_POST['password'] ?? ''
            ];

            if ($donnees['nom'] === '' || $donnees['prenom'] === '') {
                rep(false, 'Nom et prénom obligatoires.');
            }

            if (!preg_match('/^[0-9+ ]{8,15}$/', $donnees['telephone'])) {
                rep(false, 'Téléphone invalide.');
            }

            if ($donnees['password'] !== '' && strlen($donnees['password']) < 8) {
                rep(false, 'Mot de passe : 8 caractères minimum.');
            }

            User::modifier($_SESSION['idUser'], $donnees);
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
