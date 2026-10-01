<?php require __DIR__ . '/../before.html.php'; ?>

<h1><?= e($page['title']); ?></h1>

<hr>

<p>Dynamic route segment:</p>

<ul>
  <li>foo: <?= $page['route:foo'] ?></li>
  <li>bar: <?= $page['route:bar'] ?></li>
</ul>

<?php require __DIR__ . '/../after.html.php'; ?>
