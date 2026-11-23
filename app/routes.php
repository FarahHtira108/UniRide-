<?php
return [
    'home' => ['HomeController', 'index'], 'about' => ['HomeController', 'about'], '404' => ['HomeController', 'notFound'],
    'search' => ['TripController', 'search'], 'trip' => ['TripController', 'show'], 'publish' => ['TripController', 'create'],
    // Utilisateurs (commun à tout le groupe)
    'login' => ['AuthController', 'login'], 'register' => ['AuthController', 'register'], 'logout' => ['AuthController', 'logout'],
    'profile' => ['ProfileController', 'show'],
    // Véhicules (module modèle CRUD)
    'vehicles' => ['VehicleController', 'index'], 'vehicle-form' => ['VehicleController', 'form'], 'vehicle-delete' => ['VehicleController', 'delete'],
    // Back-office
    'admin-dashboard' => ['AdminController', 'dashboard'], 'admin-users' => ['AdminController', 'users'],
    'admin-user-action' => ['AdminController', 'userAction'], 'admin-trips' => ['AdminController', 'trips'],
];
