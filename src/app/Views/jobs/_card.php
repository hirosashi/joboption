<?php
use App\Core\App;
use App\Core\View;
use App\Services\Jobs;
use App\Services\Master;

/** @var array<string, mixed> $job */
?>
<li class="card">
  <a href="<?= View::e(App::url('/jobs/' . $job['id'])) ?>">
    <p class="card-cat"><?= View::e($job['category_name'] ?? '') ?>｜<?= View::e(Master::EMPLOYMENT_TYPES[$job['employment_type']] ?? '') ?></p>
    <h3 class="card-ttl"><?= View::e($job['title']) ?></h3>
    <p class="card-co"><?= View::e($job['company_name']) ?></p>
    <dl class="card-dl">
      <dt>給与</dt><dd class="pay"><?= View::e(Jobs::salary($job)) ?></dd>
      <dt>勤務地</dt><dd><?= View::e(Jobs::place($job)) ?></dd>
      <dt>勤務日</dt><dd><?= View::e($job['work_days']) ?></dd>
      <dt>時間</dt><dd><?= View::e($job['work_hours']) ?></dd>
    </dl>
    <?php if ($job['tags'] !== []): ?>
    <ul class="tags"><?php foreach ($job['tags'] as $t): ?><li><?= View::e($t) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
    <span class="card-more">くわしく見る</span>
  </a>
</li>
