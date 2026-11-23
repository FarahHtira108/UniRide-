<div class="trip-row d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-3 bg-white rounded-4 shadow-sm">
  <div><strong><?= e($trip['CONDUCTEUR']) ?></strong><br><small class="text-muted"><?= $trip['NB_PLACES_DISPONIBLES'] ?> place(s) libre(s)</small></div>
  <div><?= e(date('d/m H:i', strtotime($trip['DATE_DEPART']))) ?><br><?= e($trip['LIEU_DEPART']) ?> → <?= e($trip['LIEU_ARRIVEE']) ?></div>
  <div class="fw-bold fs-5"><?= e($trip['PRIX']) ?> DT <small class="text-muted fw-normal">/ place</small></div>
  <a class="btn btn-uni" href="index.php?page=trip&id=<?= (int)$trip['ID_TRIP'] ?>">Réserver</a>
</div>
