<div class="container py-4">
  <h1 class="h2 fw-bold">Résultats de recherche</h1>
  <form class="row g-2 mb-4" method="get">
    <input type="hidden" name="page" value="search">
    <div class="col-md"><input class="form-control" name="depart" value="<?= e($f['depart']) ?>" placeholder="Départ"></div>
    <div class="col-md"><input class="form-control" name="arrivee" value="<?= e($f['arrivee']) ?>" placeholder="Arrivée"></div>
    <div class="col-md"><input class="form-control" type="number" name="prix_max" value="<?= e($f['prix_max']) ?>" placeholder="Prix max (DT)"></div>
    <div class="col-md-auto"><button class="btn btn-uni">Rechercher</button></div>
  </form>
  <?php if (!$trips): ?><p class="text-muted">Aucun trajet trouvé. Modifiez vos critères ou publiez le vôtre.</p><?php endif; ?>
  <?php foreach ($trips as $trip) require __DIR__ . '/_trip_row.php'; ?>
</div>
