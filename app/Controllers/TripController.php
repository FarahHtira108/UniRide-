<?php
namespace App\Controllers;
use App\Models\Trip;

class TripController extends Controller
{
    public function search(): void
    {
        $this->page = 'search'; $this->title = 'Recherche | UniRide';
        $f = ['depart' => $_GET['depart'] ?? '', 'arrivee' => $_GET['arrivee'] ?? '', 'prix_max' => $_GET['prix_max'] ?? ''];
        $this->render('search', ['trips' => Trip::all($f), 'f' => $f]);
    }
    public function show(): void
    {
        $this->page = 'search';
        $trip = Trip::find((int)($_GET['id'] ?? 0));
        if (!$trip) { $this->redirect('404'); }
        $this->render('trip', ['trip' => $trip]);
    }
    public function create(): void
    {
        $this->page = 'publish'; $this->title = 'Publier un trajet | UniRide';
        if (empty($_SESSION['user'])) { $this->redirect('login'); }
        $msg = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $msg = Trip::create($_POST + ['id_user' => $_SESSION['user']['id']]) ? 'Trajet publié.' : 'Erreur : trajet non enregistré.';
        }
        $this->render('publish', ['msg' => $msg]);
    }
}
