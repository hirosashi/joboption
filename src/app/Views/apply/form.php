<?php
use App\Core\App;
use App\Core\Csrf;
use App\Core\View;

/** @var array<string, mixed> $job @var array<string, string> $v @var array<int, string> $errors */
?>
<div class="in narrow">
  <ol class="flow"><li class="on">入力</li><li>確認</li><li>完了</li></ol>
  <h1 class="page-ttl">応募する</h1>
  <?= View::partial('apply/_job', ['job' => $job]) ?>
  <?php if ($errors !== []): ?><div class="errors"><?php foreach ($errors as $e): ?><p><?= View::e($e) ?></p><?php endforeach; ?></div><?php endif; ?>
  <form method="post" action="<?= View::e(App::url('/jobs/' . $job['id'] . '/apply/confirm')) ?>" class="form">
    <?= Csrf::field() ?>
    <p class="hp"><label>ウェブサイト<input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
    <label class="field"><span class="lbl">お名前 <em>必須</em></span>
      <input type="text" name="name" value="<?= View::e($v['name']) ?>" required maxlength="50" autocomplete="name" placeholder="例：山田 太郎"></label>
    <p class="note">電話番号かメールアドレスの<strong>どちらか一方</strong>を入力してください。</p>
    <label class="field"><span class="lbl">電話番号</span>
      <input type="tel" name="tel" value="<?= View::e($v['tel']) ?>" maxlength="20" autocomplete="tel" placeholder="例：090-1234-5678"></label>
    <label class="field"><span class="lbl">メールアドレス</span>
      <input type="email" name="email" value="<?= View::e($v['email']) ?>" maxlength="200" autocomplete="email" placeholder="例：taro@example.com"></label>
    <label class="field"><span class="lbl">ひとこと <em class="any">任意</em></span>
      <textarea name="message" rows="4" maxlength="1000" placeholder="例：週3日、午前中の勤務を希望します。連絡は夕方以降だと助かります。"><?= View::e($v['message']) ?></textarea></label>
    <p class="note">ご入力いただいた内容は、応募先の企業と運営者が採用連絡のためだけに使います（<a href="<?= View::e(App::url('/privacy')) ?>" target="_blank">プライバシーポリシー</a>）。</p>
    <button class="btn btn-main btn-lg">入力内容を確認する</button>
  </form>
</div>
