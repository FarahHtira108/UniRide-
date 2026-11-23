<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand brand" href="index.php?page=home"><i class="bi bi-car-front-fill"></i> Uni<span>Ride</span></a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div id="nav" class="collapse navbar-collapse">
      <div class="navbar-nav mx-auto">
        <a class="nav-link <?= navActive('home', $currentPage) ?>" href="index.php?page=home">Accueil</a>
        <a class="nav-link <?= navActive('search', $currentPage) ?>" href="index.php?page=search">Recherche</a>
        <a class="nav-link <?= navActive('publish', $currentPage) ?>" href="index.php?page=publish">Publier un trajet</a>
        <?php if (hasRole('CONDUCTEUR')): ?><a class="nav-link <?= navActive('vehicles', $currentPage) ?>" href="index.php?page=vehicles">Mes véhicules</a><?php endif; ?>
        <a class="nav-link <?= navActive('about', $currentPage) ?>" href="index.php?page=about">À propos</a>
      </div>
      <?php if (!empty($_SESSION['user'])): ?>
        <?php if (hasRole('ADMIN')): ?><a class="btn btn-outline-uni me-2" href="index.php?page=admin-dashboard">Admin</a><?php endif; ?>
        <a class="me-3 text-decoration-none <?= navActive('profile', $currentPage) ?>" href="index.php?page=profile"><i class="bi bi-person-circle"></i> <?= e($_SESSION['user']['prenom']) ?></a>
        <a class="btn btn-outline-uni" href="index.php?page=logout">Déconnexion</a>
      <?php else: ?>
        <a class="btn btn-link text-decoration-none" href="index.php?page=login">Connexion</a>
        <a class="btn btn-uni" href="index.php?page=register">S'inscrire</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
