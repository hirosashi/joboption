<?php
use App\Core\App;
use App\Core\View;

/** @var array<int, array<string, mixed>> $jobs @var array<int, string> $tags */
?>
<section class="hero">
  <div class="in">
    <h1>経験をいかして、<br class="sp">もう一度はたらく。</h1>
    <p>週2日から・短時間から。年齢を問わず、あなたのペースで働けるお仕事を集めました。<br>応募は<strong>会員登録なし</strong>、お名前と連絡先だけでかんたんです。</p>
    <?= View::partial('jobs/_search', ['prefs' => $prefs, 'categories' => $categories, 'tags' => $tags]) ?>
  </div>
</section>

<section class="sec">
  <div class="in">
    <h2 class="sec-ttl">こだわりからさがす</h2>
    <ul class="chips">
      <?php foreach ($tags as $id => $name): ?>
      <li><a href="<?= View::e(App::url('/jobs?tag=' . $id)) ?>"><?= View::e($name) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="sec">
  <div class="in">
    <h2 class="sec-ttl">新着のお仕事<span class="count">掲載中 <?= (int)$total ?> 件</span></h2>
    <?php if ($jobs === []): ?>
      <p>現在掲載中のお仕事はありません。</p>
    <?php else: ?>
    <ul class="cards"><?php foreach ($jobs as $job): ?><?= View::partial('jobs/_card', ['job' => $job]) ?><?php endforeach; ?></ul>
    <p class="center"><a class="btn btn-sub" href="<?= View::e(App::url('/jobs')) ?>">すべてのお仕事を見る</a></p>
    <?php endif; ?>
  </div>
</section>

<section class="sec sec-steps">
  <div class="in">
    <h2 class="sec-ttl">応募はかんたん3ステップ</h2>
    <ol class="steps">
      <li><b>1</b>お仕事をえらぶ<span>エリア・職種・こだわりでさがせます</span></li>
      <li><b>2</b>お名前と連絡先を入力<span>会員登録は不要です（1分ほど）</span></li>
      <li><b>3</b>担当者から連絡<span>面接の日程などをご案内します</span></li>
    </ol>
  </div>
</section>

<section class="sec sec-co">
  <div class="in">
    <h2 class="sec-ttl">人材をお探しの企業様へ</h2>
    <p>経験豊かなシニア世代の採用を考えている企業様、求人を無料で掲載できます。</p>
    <p><a class="btn btn-sub" href="<?= View::e(App::url('/for-companies')) ?>">掲載について見る</a></p>
  </div>
</section>
