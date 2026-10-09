<?php
use App\Core\App;
use App\Core\Csrf;
use App\Core\View;
use App\Services\Master;
?>
<h1>応募の内容（No.<?= (int)$a['id'] ?>）</h1>
<table class="spec">
  <tr><th>応募日時</th><td><?= View::e(View::dt((string)$a['created_at'])) ?></td></tr>
  <tr><th>求人</th><td><a href="<?= View::e(App::url('/admin/jobs/edit?id=' . $a['job_id'])) ?>"><?= View::e($a['job_title']) ?></a></td></tr>
  <tr><th>企業</th><td><?= View::e($a['company_name']) ?></td></tr>
  <tr><th>お名前</th><td><?= View::e($a['name']) ?></td></tr>
  <tr><th>電話番号</th><td><?php if ($a['tel']): ?><a href="tel:<?= View::e(preg_replace('/[^0-9+]/', '', (string)$a['tel'])) ?>"><?= View::e($a['tel']) ?></a><?php endif; ?></td></tr>
  <tr><th>メール</th><td><?php if ($a['email']): ?><a href="mailto:<?= View::e($a['email']) ?>"><?= View::e($a['email']) ?></a><?php endif; ?></td></tr>
  <tr><th>ひとこと</th><td><?= nl2br(View::e($a['message'])) ?></td></tr>
</table>
<form method="post" action="<?= View::e(App::url('/admin/applications/save')) ?>" class="form narrow-form">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
  <label class="field"><span class="lbl">対応状況</span><?= View::select('status', Master::APP_STATUS, $a['status']) ?></label>
  <label class="field"><span class="lbl">対応メモ（社内用）</span><textarea name="memo" rows="4" placeholder="例：10/10 電話済み。10/15 面接予定。"><?= View::e($a['memo']) ?></textarea></label>
  <button class="btn btn-main">保存する</button>
</form>
<p><a href="<?= View::e(App::url('/admin/applications')) ?>">← 応募一覧へ</a></p>
