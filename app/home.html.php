<?php require __DIR__ . '/before.html.php'; ?>

<?php
$articles = generate_page_list(
  route: '/article',
  count: 5,
  // page: $page['param:page'],
  // perPage: 10,
);
// $pagination = generate_pagination(
//     currentPage: $articles['current_page'],
//     lastPage: $articles['last_page'],
//     baseUrl: '',
//     extraParams: [],
// );
?>

<h1><?= e($page['title']) ?></h1>

<hr>

<?php if (!empty($articles)): ?>
  <h2><?= e($articles['title']) ?></h2>
  <ul class="d-flex flex-column gap-2">
    <?php foreach ($articles['list'] as $article): ?>
      <li>
        <div class="d-flex flex-column">
          <a href="<?= url($article['route']) ?>">
            <?= e($article['title']) ?>
          </a>
          <span><?= format_date($article['date'], 'd M Y') ?></span>
        </div>
      </li>
    <?php endforeach ?>
    <li>
      <a href="<?= url($articles['route']) ?>">View all</a>
    </li>
  </ul>
<?php endif ?>

<?php require __DIR__ . '/after.html.php'; ?>
