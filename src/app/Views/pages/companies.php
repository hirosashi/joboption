<?php
use App\Core\App;
use App\Core\Csrf;
use App\Core\View;

/** @var array<string, string> $v @var array<int, string> $errors */
?>
<div class="in narrow">
  <p class="crumb"><a href="<?= View::e(App::url('/')) ?>">トップ</a> ＞ 掲載をご希望の企業様へ</p>
  <h1 class="page-ttl">掲載をご希望の企業様へ</h1>
  <p>お仕事55号 は、経験豊かなシニア世代をはじめ、年齢を問わず「自分のペースで働きたい」方と企業をつなぐ求人サイトです。</p>
  <ul class="check">
    <li>求人の掲載は<strong>無料</strong>です</li>
    <li>原稿は運営者が作成をお手伝いします（お電話でのヒアリングでOK）</li>
    <li>応募があるとメールでお知らせします</li>
    <li>シニアの方が働きやすくなる業務の仕組みづくり（業務アプリ等）もご相談いただけます</li>
  </ul>
  <h2 class="sec-ttl" id="form">掲載のお申し込み・ご相談</h2>
  <?php if ($errors !== []): ?><div class="errors"><?php foreach ($errors as $e): ?><p><?= View::e($e) ?></p><?php endforeach; ?></div><?php endif; ?>
  <form method="post" action="<?= View::e(App::url('/for-companies/send')) ?>" class="form">
    <?= Csrf::field() ?>
    <p class="hp"><label>ウェブサイト<input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
    <label class="field"><span class="lbl">会社名・店舗名 <em>必須</em></span><input type="text" name="company_name" value="<?= View::e($v['company_name']) ?>" required maxlength="100" autocomplete="organization"></label>
    <label class="field"><span class="lbl">ご担当者名 <em>必須</em></span><input type="text" name="contact_name" value="<?= View::e($v['contact_name']) ?>" required maxlength="50" autocomplete="name"></label>
    <p class="note">電話番号かメールアドレスの<strong>どちらか一方</strong>を入力してください。</p>
    <label class="field"><span class="lbl">電話番号</span><input type="tel" name="tel" value="<?= View::e($v['tel']) ?>" maxlength="20" autocomplete="tel"></label>
    <label class="field"><span class="lbl">メールアドレス</span><input type="email" name="email" value="<?= View::e($v['email']) ?>" maxlength="200" autocomplete="email"></label>
    <label class="field"><span class="lbl">ご相談内容 <em class="any">任意</em></span><textarea name="message" rows="4" maxlength="2000" placeholder="例：週3日の清掃スタッフを2名募集したい"><?= View::e($v['message']) ?></textarea></label>
    <button class="btn btn-main btn-lg">送信する</button>
  </form>
</div>
