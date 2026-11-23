<div class="container py-4" style="max-width:640px">
  <h1 class="h2 fw-bold">Publier un trajet</h1>
  <?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>
  <form method="post" class="bg-white p-4 rounded-4 shadow-sm row g-3">
    <div class="col-md-6"><label class="form-label">Départ</label><input class="form-control" name="depart" required></div>
    <div class="col-md-6"><label class="form-label">Arrivée</label><input class="form-control" name="arrivee" value="ESPRIT" required></div>
    <div class="col-md-6"><label class="form-label">Date et heure</label><input type="datetime-local" class="form-control" name="date" required></div>
    <div class="col-md-3"><label class="form-label">Prix (DT)</label><input type="number" min="0" step="0.5" class="form-control" name="prix" required></div>
    <div class="col-md-3"><label class="form-label">Places</label><input type="number" min="1" max="6" class="form-control" name="places" required></div>
    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"></textarea></div>
    <div class="col-12"><button class="btn btn-uni w-100">Publier le trajet</button></div>
  </form>
</div>
