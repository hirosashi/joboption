<?php use App\Core\App; use App\Core\View; ?>
<h1>企業</h1>
<p class="toolbar"><a class="btn btn-main" href="<?= View::e(App::url('/admin/companies/edit')) ?>">＋ 企業を登録</a></p>
<table class="list">
  <tr><th>No</th><th>会社名</th><th>担当者</th><th>電話番号</th><th>応募通知メール</th><th>公開中の求人</th></tr>
  <?php foreach ($rows as $c): ?>
  <tr><td><?= (int)$c['id'] ?></td><td><a href="<?= View::e(App::url('/admin/companies/edit?id=' . $c['id'])) ?>"><?= View::e($c['name']) ?></a></td><td><?= View::e($c['contact_name']) ?></td><td><?= View::e($c['tel']) ?></td><td><?= View::e($c['email']) ?></td><td><?= (int)$c['job_count'] ?></td></tr>
  <?php endforeach; ?>
</table>
