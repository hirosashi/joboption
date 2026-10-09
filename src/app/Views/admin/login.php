<?php use App\Core\App; use App\Core\Csrf; use App\Core\View; ?>
<form method="post" action="<?= View::e(App::url('/admin/login')) ?>" class="form login-box">
  <h1>管理画面ログイン</h1>
  <?= Csrf::field() ?>
  <label class="field"><span class="lbl">ID</span><input type="text" name="login_id" required autocomplete="username"></label>
  <label class="field"><span class="lbl">パスワード</span><input type="password" name="password" required autocomplete="current-password"></label>
  <button class="btn btn-main">ログイン</button>
</form>
