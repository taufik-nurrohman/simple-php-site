<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page['title']) ?> — <?= e($site['title']) ?></title>
  <link rel="stylesheet" href="<?= url('/app/asset/bootstrap/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= url('/app/asset/style.css') ?>">
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= $site['url'] ?>">
      <?= e($site['title']) ?>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="<?= url('/article') ?>">Article</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= url('/about') ?>">About</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-3">
