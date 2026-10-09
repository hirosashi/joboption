<?php
use App\Core\App;
use App\Core\Csrf;
use App\Core\View;
use App\Services\Master;

$f = static fn(string $k, string $label, string $type = 'text'): string =>
    '<label class="field"><span class="lbl">' . View::e($label) . '</span><input type="' . $type . '" name="' . $k . '" value="' . View::e($c[$k] ?? '') . '" maxlength="255"></label>';
$id = (int)$c['id'];
?>
<h1><?= View::e($title) ?></h1>
<form method="post" action="<?= View::e(App::url('/admin/companies/save')) ?>" class="form grid-form">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= $id ?>">
  <fieldset><legend>企業情報</legend>
    <label class="field wide"><span class="lbl">会社名・店舗名 <em>必須</em></span><input type="text" name="name" value="<?= View::e($c['name']) ?>" required maxlength="200"></label>
    <?= $f('contact_name', '担当者名') ?>
    <?= $f('tel', '電話番号', 'tel') ?>
    <?= $f('email', '応募通知メール（空欄なら運営にのみ通知）', 'email') ?>
    <?= $f('zip', '郵便番号') ?>
    <?= $f('address', '住所') ?>
    <?= $f('url', 'ホームページ', 'url') ?>
    <label class="field wide"><span class="lbl">社内メモ（公開されません）</span><textarea name="note" rows="3"><?= View::e($c['note']) ?></textarea></label>
  </fieldset>
  <p class="actions"><button class="btn btn-main">保存する</button></p>
</form>
<?php if ($id): ?>
<h2>この企業の求人</h2>
<p><a class="btn btn-sub" href="<?= View::e(App::url('/admin/jobs/edit?company_id=' . $id)) ?>">＋ この企業の求人を登録</a></p>
<ul><?php foreach ($jobs as $j): ?><li><a href="<?= View::e(App::url('/admin/jobs/edit?id=' . $j['id'])) ?>"><?= View::e($j['title']) ?></a>（<?= View::e(Master::JOB_STATUS[$j['status']] ?? '') ?>）</li><?php endforeach; ?></ul>
<?php endif; ?>
