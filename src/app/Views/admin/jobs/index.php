<?php
use App\Core\App;
use App\Core\View;
use App\Services\Jobs;
use App\Services\Master;
use App\Core\Clock;
?>
<h1>求人</h1>
<p class="toolbar">
  <a class="btn btn-main" href="<?= View::e(App::url('/admin/jobs/edit')) ?>">＋ 求人を登録</a>
  <span class="filter">
    <a href="<?= View::e(App::url('/admin/jobs')) ?>" class="<?= $status === '' ? 'on' : '' ?>">すべて</a>
    <?php foreach (Master::JOB_STATUS as $k => $label): ?><a href="<?= View::e(App::url('/admin/jobs?status=' . $k)) ?>" class="<?= $status === $k ? 'on' : '' ?>"><?= View::e($label) ?></a><?php endforeach; ?>
  </span>
</p>
<table class="list">
  <tr><th>No</th><th>状態</th><th>求人タイトル</th><th>企業</th><th>給与</th><th>勤務地</th><th>掲載期限</th><th>応募</th></tr>
  <?php foreach ($rows as $j): $expired = $j['valid_until'] !== null && (string)$j['valid_until'] < Clock::today(); ?>
  <tr>
    <td><?= (int)$j['id'] ?></td>
    <td><span class="st st-<?= View::e($j['status']) ?>"><?= View::e(Master::JOB_STATUS[$j['status']] ?? '') ?></span></td>
    <td><a href="<?= View::e(App::url('/admin/jobs/edit?id=' . $j['id'])) ?>"><?= View::e($j['title']) ?></a></td>
    <td><?= View::e($j['company_name']) ?></td>
    <td><?= View::e(Jobs::salary($j)) ?></td>
    <td><?= View::e(Jobs::place($j)) ?></td>
    <td class="<?= $expired ? 'warn' : '' ?>"><?= View::e(View::d($j['valid_until'])) ?><?= $expired ? '（期限切れ）' : '' ?></td>
    <td><a href="<?= View::e(App::url('/admin/applications?job_id=' . $j['id'])) ?>"><?= (int)$j['app_count'] ?></a></td>
  </tr>
  <?php endforeach; ?>
</table>
