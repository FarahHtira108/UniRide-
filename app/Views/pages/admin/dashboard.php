<h1 class="h3 fw-bold mb-4">Bonjour Admin</h1>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="stat"><i class="bi bi-people"></i><b><?= $nbUsers ?></b><span>Utilisateurs inscrits</span></div></div>
  <div class="col-md-4"><div class="stat"><i class="bi bi-car-front"></i><b><?= $nbTrips ?></b><span>Trajets publiés</span></div></div>
  <div class="col-md-4"><div class="stat"><i class="bi bi-leaf"></i><b>-</b><span>CO₂ évité</span></div></div>
</div>
<h2 class="h5">Trajets récents</h2>
<?php require __DIR__ . '/_trips_table.php'; ?>
