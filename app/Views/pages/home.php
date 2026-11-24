<section class="hero py-5">
  <div class="container py-5">
    <h1 class="display-4 fw-bold">Un trajet partagé,<br>un coût <span class="text-uni">réduit !</span></h1>
    <p class="lead">UniRide, la plateforme de covoiturage entre étudiants de Tunis.</p>
    <form class="row g-2 bg-white p-3 rounded-4 shadow-sm" action="index.php" method="get">
      <input type="hidden" name="page" value="search">
      <div class="col-md"><input class="form-control" name="depart" placeholder="Départ"></div>
      <div class="col-md"><input class="form-control" name="arrivee" placeholder="Destination"></div>
      <div class="col-md-auto"><button class="btn btn-uni w-100"><i class="bi bi-search"></i> Rechercher</button></div>
    </form>
  </div>
</section>
<section class="container my-5">
  <div class="row text-center g-3">
    <?php foreach ([['piggy-bank','Économique','Réduisez vos frais'],['leaf','Écologique','Limitez la pollution'],['people','Social','Entraidez-vous'],['lightning','Numérique','Simplifiez vos trajets']] as [$i,$t,$d]): ?>
      <div class="col-6 col-md-3"><i class="bi bi-<?= $i ?> fs-1 text-uni"></i><h5 class="mt-2"><?= $t ?></h5><small><?= $d ?></small></div>
    <?php endforeach; ?>
  </div>
</section>
<section class="container">
  <h2 class="fw-bold mb-3">Prochains trajets</h2>
  <?php foreach ($trips as $trip) require __DIR__ . '/_trip_row.php'; ?>
</section>
