<div class="container py-5" style="max-width:480px">
  <h1 class="h2 fw-bold">Inscription</h1>
  <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="bg-white p-4 rounded-4 shadow-sm row g-3">
    <div class="col-6"><label class="form-label">Nom</label><input name="nom" class="form-control" required></div>
    <div class="col-6"><label class="form-label">Prénom</label><input name="prenom" class="form-control" required></div>
    <div class="col-12"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="col-12"><label class="form-label">Téléphone</label><input name="telephone" class="form-control"></div>
    <div class="col-12"><label class="form-label">Mot de passe</label><input type="password" name="password" class="form-control" required></div>
    <div class="col-12 form-check ms-2"><input type="checkbox" class="form-check-input" name="conducteur" id="c"><label for="c" class="form-check-label">Je suis aussi conducteur</label></div>
    <div class="col-12"><button class="btn btn-uni w-100">Créer mon compte</button></div>
  </form>
</div>
