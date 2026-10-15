<?php
use App\Core\App;
use App\Core\View;
use App\Services\Jobs;
use App\Services\Master;

/** @var array<string, mixed> $job */
$rows = [
    '職種' => $job['job_name'],
    '雇用形態' => Master::EMPLOYMENT_TYPES[$job['employment_type']] ?? '',
    '給与' => Jobs::salary($job) . ($job['salary_note'] ? "\n" . $job['salary_note'] : ''),
    '勤務地' => $job['address'] ?: Jobs::place($job),
    'アクセス' => $job['station'],
    '勤務日数' => $job['work_days'],
    '勤務時間' => $job['work_hours'],
    '休日' => $job['holidays'],
    '応募資格' => $job['requirements'],
    '待遇・福利厚生' => $job['benefits'],
    '試用期間' => $job['trial_period'],
    '加入保険' => $job['insurance'],
    '受動喫煙対策' => $job['smoking'],
    '就業場所・業務の変更の範囲' => $job['change_scope'],
    '契約期間' => $job['contract_period'],
    '募集企業' => $job['company_name'],
    '掲載期限' => $job['valid_until'] ? View::d((string)$job['valid_until']) . ' まで' : '',
];
$applyUrl = App::url('/jobs/' . $job['id'] . '/apply');
?>
<div class="in detail">
  <p class="crumb"><a href="<?= View::e(App::url('/')) ?>">トップ</a> ＞ <a href="<?= View::e(App::url('/jobs')) ?>">お仕事一覧</a> ＞ <?= View::e($job['job_name']) ?></p>
  <p class="card-cat"><?= View::e($job['category_name'] ?? '') ?>｜<?= View::e(Master::EMPLOYMENT_TYPES[$job['employment_type']] ?? '') ?></p>
  <h1 class="page-ttl"><?= View::e($job['title']) ?></h1>
  <p class="detail-co"><?= View::e($job['company_name']) ?></p>
  <?php if ($job['tags'] !== []): ?><ul class="tags"><?php foreach ($job['tags'] as $t): ?><li><?= View::e($t) ?></li><?php endforeach; ?></ul><?php endif; ?>

  <dl class="point">
    <div><dt>給与</dt><dd class="pay"><?= View::e(Jobs::salary($job)) ?></dd></div>
    <div><dt>勤務地</dt><dd><?= View::e(Jobs::place($job)) ?></dd></div>
    <div><dt>勤務日</dt><dd><?= View::e($job['work_days']) ?></dd></div>
    <div><dt>時間</dt><dd><?= View::e($job['work_hours']) ?></dd></div>
  </dl>

  <?php if ($job['catch_copy']): ?><p class="catch"><?= View::e($job['catch_copy']) ?></p><?php endif; ?>

  <h2 class="sec-ttl">仕事内容</h2>
  <p class="text"><?= nl2br(View::e($job['description'])) ?></p>

  <h2 class="sec-ttl">募集要項</h2>
  <table class="spec">
    <?php foreach ($rows as $k => $val): if (trim((string)$val) === '') { continue; } ?>
    <tr><th><?= View::e($k) ?></th><td><?= nl2br(View::e($val)) ?></td></tr>
    <?php endforeach; ?>
  </table>

  <div class="apply-box">
    <p>このお仕事に興味をお持ちの方は、お気軽にご応募ください。<br>会員登録は不要です。</p>
    <a class="btn btn-main btn-lg" href="<?= View::e($applyUrl) ?>">このお仕事に応募する</a>
    <?php if ($job['apply_tel']): ?>
      <p class="tel">お電話での応募：<a href="tel:<?= View::e(preg_replace('/[^0-9+]/', '', (string)$job['apply_tel'])) ?>"><?= View::e($job['apply_tel']) ?></a><br><small>「お仕事55号 を見た」とお伝えください</small></p>
    <?php endif; ?>
  </div>
</div>
<div class="fixed-apply">
  <a class="btn btn-main" href="<?= View::e($applyUrl) ?>">応募する（登録不要）</a>
  <?php if ($job['apply_tel']): ?><a class="btn btn-tel" href="tel:<?= View::e(preg_replace('/[^0-9+]/', '', (string)$job['apply_tel'])) ?>">電話する</a><?php endif; ?>
</div>
