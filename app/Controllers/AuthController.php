<?php
namespace App\Controllers;
use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        $this->page = 'login'; $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = User::findByEmail($_POST['email'] ?? '');
            if (!$u || !password_verify($_POST['password'] ?? '', $u['PASSWORD'])) $error = 'Email ou mot de passe incorrect.';
            elseif ($u['STATUT'] === 'BLOQUE') $error = 'Votre compte est bloqué. Contactez le support.';
            else {
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => (int)$u['ID_USER'], 'nom' => $u['NOM'], 'prenom' => $u['PRENOM'], 'roles' => User::roles((int)$u['ID_USER'])];
                $this->redirect(hasRole('ADMIN') ? 'admin-dashboard' : 'home');
            }
        }
        $this->render('login', ['error' => $error]);
    }
    public function register(): void
    {
        $this->page = 'register'; $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) || strlen($_POST['password'] ?? '') < 6)
                $error = 'Email invalide ou mot de passe trop court (6 caractères minimum).';
            elseif (empty(trim($_POST['nom'] ?? '')) || empty(trim($_POST['prenom'] ?? ''))) $error = 'Nom et prénom obligatoires.';
            elseif (User::create($_POST)) $this->redirect('login');
            else $error = 'Inscription impossible (email déjà utilisé ou base non connectée).';
        }
        $this->render('register', ['error' => $error]);
    }
    public function logout(): void { session_destroy(); $this->redirect('home'); }
}
