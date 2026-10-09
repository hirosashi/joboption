<?php
use App\Core\App;
use App\Core\Session;
use App\Core\View;

/** @var string $content */
$site = App::config()['site'] ?? [];
$siteName = (string)($site['name'] ?? 'joboption');
$pageTitle = ($title ?? '') === '' ? $siteName . '｜' . ($site['tagline'] ?? '') : $title . '｜' . $siteName;
$path = App::currentPath();
$flashes = Session::pullFlash();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= View::e($pageTitle) ?></title>
<?php if (!empty($description)): ?><meta name="description" content="<?= View::e($description) ?>"><?php endif; ?>
<?php if (!empty(App::config()['noindex']) || !empty($noindex)): ?><meta name="robots" content="noindex,nofollow"><?php endif; ?>
<link rel="stylesheet" href="<?= View::e(App::url('/assets/style.css')) ?>">
<?php if (!empty($jsonLd)): ?><script type="application/ld+json"><?= $jsonLd ?></script><?php endif; ?>
</head>
<body>
<header class="hd">
  <div class="in">
    <a class="logo" href="<?= View::e(App::url('/')) ?>"><?= View::e($siteName) ?><small><?= View::e($site['tagline'] ?? '') ?></small></a>
    <nav class="hd-nav">
      <a href="<?= View::e(App::url('/jobs')) ?>" class="<?= str_starts_with($path, '/jobs') ? 'on' : '' ?>">お仕事をさがす</a>
      <a href="<?= View::e(App::url('/for-companies')) ?>" class="<?= str_starts_with($path, '/for-companies') ? 'on' : '' ?>">企業の方へ</a>
    </nav>
  </div>
</header>
<main class="main">
  <?php foreach ($flashes as $f): ?><div class="in"><p class="flash <?= View::e($f['kind']) ?>"><?= View::e($f['message']) ?></p></div><?php endforeach; ?>
  <?= $content ?>
</main>
<footer class="ft">
  <div class="in">
    <nav class="ft-nav">
      <a href="<?= View::e(App::url('/jobs')) ?>">お仕事をさがす</a>
      <a href="<?= View::e(App::url('/for-companies')) ?>">掲載をご希望の企業様へ</a>
      <a href="<?= View::e(App::url('/about')) ?>">運営者情報</a>
      <a href="<?= View::e(App::url('/terms')) ?>">利用規約</a>
      <a href="<?= View::e(App::url('/privacy')) ?>">プライバシーポリシー</a>
    </nav>
    <p class="copy">&copy; <?= View::e($siteName) ?></p>
  </div>
</footer>
</body>
</html>
