<?php
use App\Core\App;
use App\Core\View;

/** @var array<string, mixed> $job */
?>
<div class="in narrow">
  <ol class="flow"><li>入力</li><li>確認</li><li class="on">完了</li></ol>
  <h1 class="page-ttl">ご応募ありがとうございました</h1>
  <?= View::partial('apply/_job', ['job' => $job]) ?>
  <p>応募を受け付けました。担当者から数日以内に、ご入力の電話番号またはメールアドレスへご連絡します。</p>
  <p class="note">数日たっても連絡がない場合は、お手数ですが<a href="<?= View::e(App::url('/about')) ?>">運営者</a>までお問い合わせください。</p>
  <p class="center"><a class="btn btn-sub" href="<?= View::e(App::url('/jobs')) ?>">ほかのお仕事も見る</a></p>
</div>
