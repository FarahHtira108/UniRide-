<div class="container py-4" style="max-width:640px">
  <h1 class="h2 fw-bold">Mon profil</h1>
  <?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>
  <form method="post" class="bg-white p-4 rounded-4 shadow-sm row g-3">
    <div class="col-md-6"><label class="form-label">Nom</label><input class="form-control" name="nom" value="<?= e($user['NOM']) ?>" required></div>
    <div class="col-md-6"><label class="form-label">Prénom</label><input class="form-control" name="prenom" value="<?= e($user['PRENOM']) ?>" required></div>
    <div class="col-12"><label class="form-label">Email</label><input class="form-control" value="<?= e($user['EMAIL']) ?>" disabled></div>
    <div class="col-md-6"><label class="form-label">Téléphone</label><input class="form-control" name="telephone" value="<?= e($user['TELEPHONE']) ?>"></div>
    <div class="col-md-6"><label class="form-label">Adresse</label><input class="form-control" name="adresse" value="<?= e($user['ADRESSE']) ?>"></div>
    <div class="col-12"><small class="text-muted">Rôles : <?= e(implode(', ', $_SESSION['user']['roles'])) ?></small></div>
    <div class="col-12"><button class="btn btn-uni w-100">Enregistrer</button></div>
  </form>
</div>
