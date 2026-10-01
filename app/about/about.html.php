<?php require __DIR__ . '/../before.html.php'; ?>

<h1><?= e($page['title']); ?></h1>

<hr>

<ul>
  <li>
    Segment 1: <a href="<?= url($page['segment1']) ?>"><?= e($page['segment1']) ?></a>
  </li>
  <li>
    Segment 2: <a href="<?= url($page['segment2']) ?>"><?= e($page['segment2']) ?></a>
  </li>
</ul>

<div>
  <?= $page['content'] ?>
</div>

<?php require __DIR__ . '/../after.html.php'; ?>
