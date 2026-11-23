# UniRide - Covoiturage étudiant (Tunis) - VitaNova Team
1. Importer database/uniride.sql dans phpMyAdmin (schéma V2 du diagramme de classes).
2. Dossier dans C:\xampp\htdocs\uniride  ->  http://localhost/uniride/index.php?page=home
3. Admin de test : admin@uniride.tn / password (à changer).

## Ajouter votre module (même schéma que Vehicle)
Model (app/Models) + Controller (app/Controllers) + vues (app/Views/pages) + routes (app/routes.php) + lien navbar/sidebar.
Protéger avec $this->requireRole('CONDUCTEUR') ou ('ADMIN'). Toujours des requêtes préparées (PDO).
Modules : Nour = véhicules | Farah = trajets | Nassim = réservations + paiements | Aziz = évaluations + favoris | Yosri = messagerie + support | Mohamed = offres + fidélité.
