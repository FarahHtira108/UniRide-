<?php
namespace App\Controllers;
use App\Models\User;

class ProfileController extends Controller
{
    protected string $page = 'profile';
    public function show(): void
    {
        $this->requireLogin(); $msg = null; $id = $_SESSION['user']['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $d = array_map('trim', $_POST + ['adresse' => '', 'telephone' => '']);
            if ($d['nom'] !== '' && $d['prenom'] !== '' && User::updateProfile($id, $d)) {
                $_SESSION['user']['nom'] = $d['nom']; $_SESSION['user']['prenom'] = $d['prenom']; $msg = 'Profil mis à jour.';
            } else $msg = 'Mise à jour impossible.';
        }
        $this->render('profile', ['user' => User::find($id), 'msg' => $msg]);
    }
}
