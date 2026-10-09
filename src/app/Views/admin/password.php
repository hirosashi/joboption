<?php use App\Core\App; use App\Core\Csrf; use App\Core\View; ?>
<h1>パスワード変更</h1>
<form method="post" action="<?= View::e(App::url('/admin/password')) ?>" class="form narrow-form">
  <?= Csrf::field() ?>
  <label class="field"><span class="lbl">現在のパスワード</span><input type="password" name="current" required autocomplete="current-password"></label>
  <label class="field"><span class="lbl">新しいパスワード（8文字以上）</span><input type="password" name="new" required minlength="8" autocomplete="new-password"></label>
  <label class="field"><span class="lbl">新しいパスワード（確認）</span><input type="password" name="new2" required minlength="8" autocomplete="new-password"></label>
  <button class="btn btn-main">変更する</button>
</form>
