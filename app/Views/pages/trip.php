<div class="container py-4" style="max-width:720px">
  <h1 class="h2 fw-bold">Détails du trajet</h1>
  <div class="bg-white p-4 rounded-4 shadow-sm">
    <p><i class="bi bi-person"></i> <?= e($trip['CONDUCTEUR']) ?></p>
    <p><i class="bi bi-geo-alt"></i> <?= e($trip['LIEU_DEPART']) ?> → <?= e($trip['LIEU_ARRIVEE']) ?></p>
    <p><i class="bi bi-calendar"></i> <?= e(date('d/m/Y à H:i', strtotime($trip['DATE_DEPART']))) ?></p>
    <p><i class="bi bi-people"></i> Places disponibles : <?= (int)$trip['NB_PLACES_DISPONIBLES'] ?> - <strong><?= e($trip['PRIX']) ?> DT</strong> / place</p>
    <p class="text-muted"><?= e($trip['DESCRIPTION']) ?></p>
    <label class="form-label">Nombre de places</label>
    <select class="form-select mb-3"><?php for ($i = 1; $i <= $trip['NB_PLACES_DISPONIBLES']; $i++) echo "<option>$i</option>"; ?></select>
    <button class="btn btn-uni w-100">Envoyer la demande</button>
  </div>
</div>
