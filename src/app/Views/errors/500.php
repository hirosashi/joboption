<?php use App\Core\App; use App\Core\View; ?>
<div class="in narrow"><h1 class="page-ttl">エラーが発生しました</h1><p>処理を完了できませんでした。時間をおいて再度お試しください。</p>
<?php if (!empty($debug) && isset($error) && $error instanceof \Throwable): ?><pre class="debug"><?= View::e($error->getMessage() . "\n" . $error->getFile() . ':' . $error->getLine()) ?></pre><?php endif; ?>
<p><a class="btn btn-sub" href="<?= View::e(App::url('/')) ?>">トップへ</a></p></div>
