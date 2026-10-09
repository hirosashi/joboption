<?php
use App\Core\App;
use App\Core\View;
use App\Services\Master;

$q = static fn(array $p): string => App::url('/admin/applications' . (($s = http_build_query(array_filter($p))) !== '' ? '?' . $s : ''));
?>
<h1>応募<?= $jobId ? '（求人No.' . (int)$jobId . '）' : '' ?></h1>
<p class="toolbar">
  <span class="filter">
    <a href="<?= View::e($q(['job_id' => $jobId])) ?>" class="<?= $status === '' ? 'on' : '' ?>">すべて</a>
    <?php foreach (Master::APP_STATUS as $k => $label): ?><a href="<?= View::e($q(['status' => $k, 'job_id' => $jobId])) ?>" class="<?= $status === $k ? 'on' : '' ?>"><?= View::e($label) ?></a><?php endforeach; ?>
  </span>
  <a class="btn btn-sub" href="<?= View::e(App::url('/admin/applications/csv' . (($s = http_build_query(array_filter(['status' => $status, 'job_id' => $jobId]))) !== '' ? '?' . $s : ''))) ?>">CSVダウンロード</a>
</p>
<?php if ($rows === []): ?><p>該当する応募はありません。</p><?php else: ?>
<table class="list">
  <tr><th>No</th><th>応募日時</th><th>状況</th><th>お名前</th><th>電話番号</th><th>メール</th><th>求人</th><th>企業</th></tr>
  <?php foreach ($rows as $a): ?>
  <tr><td><?= (int)$a['id'] ?></td><td><a href="<?= View::e(App::url('/admin/applications/view?id=' . $a['id'])) ?>"><?= View::e(View::dt((string)$a['created_at'])) ?></a></td><td><span class="st st-<?= View::e($a['status']) ?>"><?= View::e(Master::APP_STATUS[$a['status']] ?? '') ?></span></td><td><?= View::e($a['name']) ?></td><td><?= View::e($a['tel']) ?></td><td><?= View::e($a['email']) ?></td><td><?= View::e($a['job_name']) ?></td><td><?= View::e($a['company_name']) ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
