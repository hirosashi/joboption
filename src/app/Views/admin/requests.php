<?php
use App\Core\App;
use App\Core\Csrf;
use App\Core\View;
use App\Services\Master;
?>
<h1>掲載申込</h1>
<?php if ($rows === []): ?><p>まだ申込はありません。</p><?php else: ?>
<table class="list">
  <tr><th>受付日時</th><th>会社名</th><th>担当者</th><th>電話番号</th><th>メール</th><th>ご相談内容</th><th>状況</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= View::e(View::dt((string)$r['created_at'])) ?></td><td><?= View::e($r['company_name']) ?></td><td><?= View::e($r['contact_name']) ?></td><td><?= View::e($r['tel']) ?></td><td><?= View::e($r['email']) ?></td><td><?= nl2br(View::e($r['message'])) ?></td>
    <td><form method="post" action="<?= View::e(App::url('/admin/requests/save')) ?>" class="inline"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><?= View::select('status', Master::REQUEST_STATUS, $r['status'], 'onchange="this.form.submit()"') ?></form></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
