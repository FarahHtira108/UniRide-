<div class="container py-5" style="max-width:480px">
  <h1 class="h2 fw-bold">Connexion</h1>
  <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="bg-white p-4 rounded-4 shadow-sm">
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Mot de passe</label><input type="password" name="password" class="form-control" required></div>
    <button class="btn btn-uni w-100">Se connecter</button>
  </form>
</div>
