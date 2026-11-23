<?php
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<div class="admin-main">
  <?php require_once __DIR__ . '/partials/header.php'; ?>
  <section class="p-4"><?= $content ?></section>
</div>
<?php require_once __DIR__ . '/partials/scripts.php'; ?>
