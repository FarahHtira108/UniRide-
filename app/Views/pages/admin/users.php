<h1 class="h3 fw-bold mb-3">Utilisateurs</h1>
<table class="table bg-white rounded-3 shadow-sm align-middle">
  <thead><tr><th>Nom</th><th>Email</th><th>Rôles</th><th>Statut</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($users as $u): $self = (int)$u['ID_USER'] === $_SESSION['user']['id']; ?>
    <tr><td><?= e($u['PRENOM'] . ' ' . $u['NOM']) ?></td><td><?= e($u['EMAIL']) ?></td><td><?= e($u['ROLES']) ?></td>
      <td><span class="badge <?= $u['STATUT'] === 'ACTIF' ? 'text-bg-success' : 'text-bg-danger' ?>"><?= e($u['STATUT']) ?></span></td>
      <td class="text-end">
      <?php if (!$self): ?>
        <form method="post" action="index.php?page=admin-user-action" class="d-inline-flex gap-1">
          <input type="hidden" name="id" value="<?= (int)$u['ID_USER'] ?>">
          <button name="action" value="<?= $u['STATUT'] === 'ACTIF' ? 'block' : 'unblock' ?>" class="btn btn-sm btn-outline-secondary"><?= $u['STATUT'] === 'ACTIF' ? 'Bloquer' : 'Débloquer' ?></button>
          <button name="action" value="driver" class="btn btn-sm btn-outline-uni">Conducteur</button>
          <button name="action" value="admin" class="btn btn-sm btn-outline-uni">Admin</button>
          <button name="action" value="delete" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</button>
        </form>
      <?php endif; ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$users): ?><tr><td colspan="5" class="text-muted">Aucun utilisateur (ou base non connectée).</td></tr><?php endif; ?>
  </tbody>
</table>
