<?php
use App\Core\App;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\View;

/** @var string $content */
$u = Auth::user();
$path = App::currentPath();
$menu = ['/admin' => 'ホーム', '/admin/jobs' => '求人', '/admin/companies' => '企業', '/admin/applications' => '応募', '/admin/requests' => '掲載申込', '/admin/masters' => '職種・タグ'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= View::e(($title ?? '') . '｜お仕事55号 管理') ?></title>
<link rel="stylesheet" href="<?= View::e(App::url('/assets/style.css')) ?>">
</head>
<body class="adm">
<header class="adm-hd">
  <a class="logo" href="<?= View::e(App::url('/admin')) ?>">お仕事55号 <small>管理</small></a>
  <?php if ($u !== null): ?>
  <nav>
    <?php foreach ($menu as $p => $label): ?>
      <a href="<?= View::e(App::url($p)) ?>" class="<?= ($p === '/admin' ? $path === $p : str_starts_with($path, $p)) ? 'on' : '' ?>"><?= View::e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="adm-user">
    <a href="<?= View::e(App::url('/')) ?>" target="_blank">サイトを見る</a>
    <a href="<?= View::e(App::url('/admin/password')) ?>"><?= View::e($u['name']) ?></a>
    <form method="post" action="<?= View::e(App::url('/admin/logout')) ?>"><?= Csrf::field() ?><button class="link">ログアウト</button></form>
  </div>
  <?php endif; ?>
</header>
<main class="adm-main">
  <?php foreach (Session::pullFlash() as $f): ?><p class="flash <?= View::e($f['kind']) ?>"><?= View::e($f['message']) ?></p><?php endforeach; ?>
  <?= $content ?>
</main>
</body>
</html>
