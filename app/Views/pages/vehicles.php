<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2 fw-bold m-0">Mes véhicules</h1>
    <a class="btn btn-uni" href="index.php?page=vehicle-form">Ajouter un véhicule</a>
  </div>
  <?php if (!$vehicles): ?><p class="text-muted">Aucun véhicule. Ajoutez le vôtre pour publier des trajets.</p><?php endif; ?>
  <?php if ($vehicles): ?>
  <table class="table bg-white rounded-3 shadow-sm">
    <thead><tr><th>Véhicule</th><th>Immatriculation</th><th>Couleur</th><th>Année</th><th>Places</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($vehicles as $v): ?>
      <tr><td><?= e($v['MARQUE'] . ' ' . $v['MODELE']) ?></td><td><?= e($v['IMMATRICULATION']) ?></td><td><?= e($v['COULEUR']) ?></td><td><?= e($v['ANNEE']) ?></td><td><?= (int)$v['NB_PLACES'] ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-uni" href="index.php?page=vehicle-form&id=<?= (int)$v['ID_VEHICLE'] ?>">Modifier</a>
          <form method="post" action="index.php?page=vehicle-delete" class="d-inline" onsubmit="return confirm('Supprimer ce véhicule ?')">
            <input type="hidden" name="id" value="<?= (int)$v['ID_VEHICLE'] ?>"><button class="btn btn-sm btn-outline-danger">Supprimer</button></form></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
