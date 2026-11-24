<table class="table bg-white rounded-3 shadow-sm">
  <thead><tr><th>Trajet</th><th>Conducteur</th><th>Date</th><th>Prix</th><th>Places</th></tr></thead>
  <tbody>
  <?php foreach ($trips as $t): ?>
    <tr><td><?= e($t['LIEU_DEPART']) ?> → <?= e($t['LIEU_ARRIVEE']) ?></td><td><?= e($t['CONDUCTEUR']) ?></td>
        <td><?= e(date('d/m/Y H:i', strtotime($t['DATE_DEPART']))) ?></td><td><?= e($t['PRIX']) ?> DT</td><td><?= (int)$t['NB_PLACES_DISPONIBLES'] ?></td></tr>
  <?php endforeach; ?>
  </tbody>
</table>
