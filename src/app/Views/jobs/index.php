<?php
use App\Core\App;
use App\Core\View;

/** @var array<int, array<string, mixed>> $jobs */
$qs = static function (int $p) use ($f): string {
    return App::url('/jobs?' . http_build_query(array_filter($f + ['page' => $p > 1 ? $p : ''])));
};
?>
<div class="in">
  <p class="crumb"><a href="<?= View::e(App::url('/')) ?>">トップ</a> ＞ お仕事一覧</p>
  <h1 class="page-ttl"><?= View::e($title) ?><span class="count"><?= (int)$total ?> 件</span></h1>
  <details class="search-box"<?= $total === 0 || array_filter($f) === [] ? ' open' : '' ?>>
    <summary>条件を変えてさがす</summary>
    <?= View::partial('jobs/_search', ['f' => $f, 'prefs' => $prefs, 'categories' => $categories, 'tags' => $tags]) ?>
  </details>
  <?php if ($jobs === []): ?>
    <p class="empty">条件に合うお仕事が見つかりませんでした。条件を減らしてお試しください。</p>
  <?php else: ?>
    <ul class="cards"><?php foreach ($jobs as $job): ?><?= View::partial('jobs/_card', ['job' => $job]) ?><?php endforeach; ?></ul>
  <?php endif; ?>
  <?php if ($pages > 1): ?>
  <nav class="pager">
    <?php if ($page > 1): ?><a href="<?= View::e($qs($page - 1)) ?>">＜ 前へ</a><?php endif; ?>
    <span><?= (int)$page ?> / <?= (int)$pages ?></span>
    <?php if ($page < $pages): ?><a href="<?= View::e($qs($page + 1)) ?>">次へ ＞</a><?php endif; ?>
  </nav>
  <?php endif; ?>
</div>
