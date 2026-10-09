<?php
use App\Core\App;
use App\Core\Csrf;
use App\Core\View;

/** @var array<string, mixed> $job @var array<string, string> $v */
?>
<div class="in narrow">
  <ol class="flow"><li>入力</li><li class="on">確認</li><li>完了</li></ol>
  <h1 class="page-ttl">入力内容の確認</h1>
  <?= View::partial('apply/_job', ['job' => $job]) ?>
  <table class="spec">
    <tr><th>お名前</th><td><?= View::e($v['name']) ?></td></tr>
    <tr><th>電話番号</th><td><?= View::e($v['tel'] ?: '（なし）') ?></td></tr>
    <tr><th>メールアドレス</th><td><?= View::e($v['email'] ?: '（なし）') ?></td></tr>
    <tr><th>ひとこと</th><td><?= nl2br(View::e($v['message'] ?: '（なし）')) ?></td></tr>
  </table>
  <form method="post" action="<?= View::e(App::url('/jobs/' . $job['id'] . '/apply/send')) ?>" class="form">
    <?= Csrf::field() ?>
    <button class="btn btn-main btn-lg">この内容で応募する</button>
  </form>
  <p class="center"><a class="btn btn-sub" href="<?= View::e(App::url('/jobs/' . $job['id'] . '/apply')) ?>">戻って直す</a></p>
</div>
