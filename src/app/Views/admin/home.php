<?php
use App\Core\App;
use App\Core\View;
use App\Services\Master;
?>
<h1>ホーム</h1>
<div class="stats">
  <a href="<?= View::e(App::url('/admin/applications?status=new')) ?>" class="<?= $newApps ? 'hot' : '' ?>"><b><?= (int)$newApps ?></b>未対応の応募</a>
  <a href="<?= View::e(App::url('/admin/requests')) ?>" class="<?= $newRequests ? 'hot' : '' ?>"><b><?= (int)$newRequests ?></b>未対応の掲載申込</a>
  <a href="<?= View::e(App::url('/admin/jobs?status=published')) ?>"><b><?= (int)$published ?></b>公開中の求人</a>
  <a href="<?= View::e(App::url('/admin/jobs?status=published')) ?>"><b><?= (int)$expired ?></b>期限切れ（非表示）</a>
</div>
<p><a class="btn btn-main" href="<?= View::e(App::url('/admin/jobs/edit')) ?>">＋ 求人を登録</a> <a class="btn btn-sub" href="<?= View::e(App::url('/admin/companies/edit')) ?>">＋ 企業を登録</a></p>
<h2>最近の応募</h2>
<?php if ($apps === []): ?><p>まだ応募はありません。</p><?php else: ?>
<table class="list">
  <tr><th>応募日時</th><th>状況</th><th>お名前</th><th>求人</th><th>企業</th></tr>
  <?php foreach ($apps as $a): ?>
  <tr><td><a href="<?= View::e(App::url('/admin/applications/view?id=' . $a['id'])) ?>"><?= View::e(View::dt((string)$a['created_at'])) ?></a></td><td><span class="st st-<?= View::e($a['status']) ?>"><?= View::e(Master::APP_STATUS[$a['status']] ?? '') ?></span></td><td><?= View::e($a['name']) ?></td><td><?= View::e($a['job_name']) ?></td><td><?= View::e($a['company_name']) ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
