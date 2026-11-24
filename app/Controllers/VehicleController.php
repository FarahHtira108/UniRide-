<?php
namespace App\Controllers;
use App\Models\Vehicle;

/** Module de référence : copiez ce schéma (liste / formulaire / suppression) pour votre module. */
class VehicleController extends Controller
{
    protected string $page = 'vehicles'; protected string $title = 'Mes véhicules | UniRide';

    public function index(): void
    {
        $this->requireRole('CONDUCTEUR');
        $this->render('vehicles', ['vehicles' => Vehicle::forUser($_SESSION['user']['id'])]);
    }
    public function form(): void
    {
        $this->requireRole('CONDUCTEUR'); $uid = $_SESSION['user']['id'];
        $id = (int)($_GET['id'] ?? 0) ?: null; $v = $id ? Vehicle::find($id, $uid) : null; $error = null;
        if ($id && !$v) $this->redirect('vehicles');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (trim($_POST['marque'] ?? '') === '' || (int)($_POST['nb_places'] ?? 0) < 1) $error = 'Marque et nombre de places obligatoires.';
            elseif (Vehicle::save($_POST, $uid, $id)) $this->redirect('vehicles');
            else $error = 'Enregistrement impossible.';
            $v = $_POST;
        }
        $this->render('vehicle_form', ['v' => $v, 'id' => $id, 'error' => $error]);
    }
    public function delete(): void
    {
        $this->requireRole('CONDUCTEUR');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') Vehicle::delete((int)($_POST['id'] ?? 0), $_SESSION['user']['id']);
        $this->redirect('vehicles');
    }
}
