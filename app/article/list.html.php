<?php require __DIR__ . '/../before.html.php'; ?>

<?php
$categories = generate_field_list('/article', 'category');
$tags = generate_field_list('/article', 'tags');
?>

<h1><?= e($page['title']) ?></h1>

<hr>

<form method="GET" action="<?= url($page['route:list']) ?>" class="row g-2 mb-3">
  <div class="col-sm-auto">
    <input class="form-control form-control-sm" name="q" value="<?= e($page['param:q']) ?>" placeholder="Search">
  </div>

  <?php if (!empty($categories)): ?>
    <div class="col-auto dropdown">
      <?php if (!empty($categories['selected'][0])): ?>
        <input type="hidden" name="category" value="<?= e($categories['selected'][0]) ?>">
      <?php endif ?>

      <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
        Category<?= $categories['selected'][0] ?? null ? ': ' . $categories['selected'][0] : '' ?>
      </button>

      <ul class="dropdown-menu">
        <?php foreach ($categories['list'] as $category): ?>
          <li>
            <a class="dropdown-item<?= $category['active'] ? ' active' : '' ?>" href="<?= url(merge_query_url($category['route'])) ?>">
              <?= e($category['title']) ?>
            </a>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif ?>

  <?php if (!empty($tags)): ?>
    <div class="col-auto dropdown">
      <?php foreach ($tags['selected'] as $value): ?>
        <input type="hidden" name="tags[]" value="<?= e($value) ?>">
      <?php endforeach; ?>

      <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
        Tags<?= $tags['selected'] ? ': ' . e(implode(', ', $tags['selected'])) : '' ?>
      </button>

      <ul class="dropdown-menu">
        <?php foreach ($tags['list'] as $tag): ?>
          <li>
            <a class="dropdown-item<?= $tag['active'] ? ' active' : '' ?>" href="<?= url(toggle_query_value($tag['route'], 'tags', $tag['title'])) ?>">
              <?= e($tag['title']) ?>
            </a>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif ?>

  <?php if ($page['param:q'] || $page['param:category'] || $page['param:tags']): ?>
    <div class="col-auto d-flex align-items-center">
      <a class="btn btn-light btn-sm lh-1 d-inline-flex align-items-center justify-content-center" href="<?= url($page['route:list']) ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
        </svg>
      </a>
    </div>
  <?php endif ?>
</form>

<?php if (!empty($pages['list'])): ?>
  <ul class="d-flex flex-column gap-2">
    <?php foreach ($pages['list'] as $p): ?>
      <li>
        <div class="d-flex flex-column">
          <a href="<?= url($p['route']) ?>">
            <?= e($p['title']) ?>
          </a>
          <span><?= format_date($p['date'], 'd M Y') ?></span>
        </div>
      </li>
    <?php endforeach ?>
  </ul>

  <?php if (!empty($pagination)): ?>
    <nav>
      <ul class="pagination pagination-sm">
        <?php if ($pagination['prev']): ?>
          <li class="page-item">
            <a class="page-link" href="<?= url($pagination['prev']) ?>">&laquo; Prev</a>
          </li>
        <?php else: ?>
          <li class="page-item disabled">
            <span class="page-link">&laquo; Prev</span>
          </li>
        <?php endif ?>

        <?php foreach ($pagination['pages'] as $p): ?>
          <?php if ($p['page'] === '...'): ?>
            <li class="page-item disabled">
              <span class="page-link">...</span>
            </li>
          <?php else: ?>
            <li class="page-item<?= $p['active'] ? ' active' : '' ?>">
              <a class="page-link" href="<?= url($p['route']) ?>">
                <?= $p['page'] ?>
              </a>
            </li>
          <?php endif ?>
        <?php endforeach ?>

        <?php if ($pagination['next']): ?>
          <li class="page-item">
            <a class="page-link" href="<?= url($pagination['next']) ?>">Next &raquo;</a>
          </li>
        <?php else: ?>
          <li class="page-item disabled">
            <span class="page-link">Next &raquo;</span>
          </li>
        <?php endif ?>
      </ul>
    </nav>
  <?php endif ?>
<?php else: ?>
  <p>No articles found.</p>
<?php endif ?>

<?php require __DIR__ . '/../after.html.php'; ?>
