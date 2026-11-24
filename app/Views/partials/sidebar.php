<?php $currentPage = $currentPage ?? 'dashboard'; ?>
<aside class="sidebar">
  <a href="index.php?page=admin-dashboard" class="brand d-block p-4 text-white">Uni<span>Ride</span></a>
  <nav class="nav flex-column px-2">
    <a class="nav-link <?= navActive('dashboard', $currentPage) ?>" href="index.php?page=admin-dashboard"><i class="bi bi-house"></i> Tableau de bord</a>
    <a class="nav-link <?= navActive('users', $currentPage) ?>" href="index.php?page=admin-users"><i class="bi bi-people"></i> Utilisateurs</a>
    <a class="nav-link <?= navActive('trips', $currentPage) ?>" href="index.php?page=admin-trips"><i class="bi bi-car-front"></i> Trajets</a>
    <a class="nav-link" href="index.php?page=home"><i class="bi bi-box-arrow-left"></i> Retour au site</a>
  </nav>
</aside>
