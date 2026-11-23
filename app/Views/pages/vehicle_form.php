<?php $g = fn($k) => e($v[$k] ?? $v[strtoupper($k)] ?? ''); ?>
<div class="container py-4" style="max-width:640px">
  <h1 class="h2 fw-bold"><?= $id ? 'Modifier le véhicule' : 'Ajouter un véhicule' ?></h1>
  <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="bg-white p-4 rounded-4 shadow-sm row g-3">
    <div class="col-md-6"><label class="form-label">Marque</label><input class="form-control" name="marque" value="<?= e($v['MARQUE'] ?? $v['marque'] ?? '') ?>" required></div>
    <div class="col-md-6"><label class="form-label">Modèle</label><input class="form-control" name="modele" value="<?= e($v['MODELE'] ?? $v['modele'] ?? '') ?>"></div>
    <div class="col-md-6"><label class="form-label">Immatriculation</label><input class="form-control" name="immatriculation" value="<?= e($v['IMMATRICULATION'] ?? $v['immatriculation'] ?? '') ?>"></div>
    <div class="col-md-6"><label class="form-label">Couleur</label><input class="form-control" name="couleur" value="<?= e($v['COULEUR'] ?? $v['couleur'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Année</label><input type="number" class="form-control" name="annee" value="<?= e($v['ANNEE'] ?? $v['annee'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Places</label><input type="number" min="1" max="8" class="form-control" name="nb_places" value="<?= e($v['NB_PLACES'] ?? $v['nb_places'] ?? '') ?>" required></div>
    <div class="col-md-4"><label class="form-label">Type</label><input class="form-control" name="type" placeholder="Citadine, berline..." value="<?= e($v['TYPE_VEHICLE'] ?? $v['type'] ?? '') ?>"></div>
    <div class="col-12 d-flex gap-2"><button class="btn btn-uni">Enregistrer</button><a class="btn btn-link" href="index.php?page=vehicles">Annuler</a></div>
  </form>
</div>
