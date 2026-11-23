<?php
namespace App\Controllers;
use App\Models\Trip;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->page = 'home';
        $this->render('home', ['trips' => array_slice(Trip::all(), 0, 3)]);
    }
    public function about(): void { $this->page = 'about'; $this->title = 'À propos | UniRide'; $this->render('about'); }
    public function notFound(): void { $this->page = ''; $this->render('404'); }
}
