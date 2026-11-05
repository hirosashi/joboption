<?php
use App\Core\App;
use App\Core\Session;
use App\Core\View;

/** @var string $content */
$site = App::config()['site'] ?? [];
$siteName = (string)($site['name'] ?? 'お仕事55号');
$pageTitle = ($title ?? '') === '' ? $siteName . '｜' . ($site['tagline'] ?? '') : $title . '｜' . $siteName;
$path = App::currentPath();
$metaDesc = (string)($description ?? '') !== '' ? (string)$description : $siteName . 'は、シニア世代が経験をいかして活躍できるお仕事を集めた求人サイトです。会員登録なしでかんたんに応募できます。';
$canonical = App::absUrl($path);
$ogImageUrl = !empty($ogImage) ? (string)$ogImage : App::absUrl('/assets/ogp.png');
$flashes = Session::pullFlash();
$gaId = (string)(App::config()['ga_measurement_id'] ?? '');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= View::e($pageTitle) ?></title>
<meta name="description" content="<?= View::e($metaDesc) ?>">
<link rel="canonical" href="<?= View::e($canonical) ?>">
<meta property="og:site_name" content="<?= View::e($siteName) ?>">
<meta property="og:type" content="<?= View::e($ogType ?? 'website') ?>">
<meta property="og:title" content="<?= View::e($pageTitle) ?>">
<meta property="og:description" content="<?= View::e($metaDesc) ?>">
<meta property="og:url" content="<?= View::e($canonical) ?>">
<meta property="og:image" content="<?= View::e($ogImageUrl) ?>">
<meta property="og:locale" content="ja_JP">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= View::e($pageTitle) ?>">
<meta name="twitter:description" content="<?= View::e($metaDesc) ?>">
<meta name="twitter:image" content="<?= View::e($ogImageUrl) ?>">
<meta name="theme-color" content="#e8590c">
<?php if (!empty(App::config()['noindex']) || !empty($noindex)): ?><meta name="robots" content="noindex,nofollow"><?php endif; ?>
<link rel="icon" href="<?= View::e(App::url('/favicon.ico')) ?>" sizes="48x48">
<link rel="icon" href="<?= View::e(App::url('/assets/icon-192.png')) ?>" type="image/png" sizes="192x192">
<link rel="apple-touch-icon" href="<?= View::e(App::url('/assets/apple-touch-icon.png')) ?>">
<link rel="stylesheet" href="<?= View::e(App::url('/assets/style.css')) ?>">
<?php if (!empty($jsonLd)): ?><script type="application/ld+json"><?= $jsonLd ?></script><?php endif; ?>
<?php if (preg_match('/^G-[A-Z0-9]+$/', $gaId)): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= View::e($gaId) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= View::e($gaId) ?>');</script>
<?php endif; ?>
</head>
<body>
<header class="hd">
  <div class="in">
    <a class="logo" href="<?= View::e(App::url('/')) ?>"><img src="<?= View::e(App::url('/assets/logo.png')) ?>" width="266" height="60" alt="<?= View::e($siteName . '｜' . ($site['tagline'] ?? '')) ?>"></a>
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
    <div class="ft-row">
    <a class="ft-logo" href="<?= View::e(App::url('/')) ?>"><img src="<?= View::e(App::url('/assets/logo-footer.png')) ?>" width="110" height="95" alt="<?= View::e($siteName) ?>"></a>
    <nav class="ft-nav">
      <a href="<?= View::e(App::url('/jobs')) ?>">お仕事をさがす</a>
      <a href="<?= View::e(App::url('/for-companies')) ?>">掲載をご希望の企業様へ</a>
      <a href="<?= View::e(App::url('/about')) ?>">運営者情報</a>
      <a href="<?= View::e(App::url('/terms')) ?>">利用規約</a>
      <a href="<?= View::e(App::url('/privacy')) ?>">プライバシーポリシー</a>
    </nav>
    </div>
    <p class="copy">&copy; <?= View::e($siteName) ?></p>
  </div>
</footer>
</body>
</html>
