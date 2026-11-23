<?php
namespace App\Controllers;
use App\Models\{Trip, User};

class AdminController extends Controller
{
    protected string $title = 'Back-office | UniRide';
    public function __construct() { $this->requireRole('ADMIN'); }

    public function dashboard(): void
    {
        $this->page = 'dashboard';
        $this->render('admin/dashboard', ['nbUsers' => User::count(), 'nbTrips' => count(Trip::all()), 'trips' => array_slice(Trip::all(), 0, 5)], 'admin');
    }
    public function users(): void { $this->page = 'users'; $this->render('admin/users', ['users' => User::all()], 'admin'); }
    public function trips(): void { $this->page = 'trips'; $this->render('admin/trips', ['trips' => Trip::all()], 'admin'); }

    public function userAction(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id && $id !== $_SESSION['user']['id']) {
            switch ($_POST['action'] ?? '') {
                case 'block':   User::setStatut($id, 'BLOQUE'); break;
                case 'unblock': User::setStatut($id, 'ACTIF'); break;
                case 'delete':  User::delete($id); break;
                case 'driver':  User::toggleRole($id, 'CONDUCTEUR'); break;
                case 'admin':   User::toggleRole($id, 'ADMIN'); break;
            }
        }
        $this->redirect('admin-users');
    }
}
