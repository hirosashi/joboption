<?php
use App\Core\App;
use App\Core\Csrf;
use App\Core\View;
use App\Services\Master;

/** @var array<string, mixed> $job */
$t = static fn(string $k, string $label, string $ph = '', int $max = 255): string =>
    '<label class="field"><span class="lbl">' . View::e($label) . '</span><input type="text" name="' . $k . '" value="' . View::e($job[$k] ?? '') . '" maxlength="' . $max . '" placeholder="' . View::e($ph) . '"></label>';
$ta = static fn(string $k, string $label, string $ph = ''): string =>
    '<label class="field"><span class="lbl">' . View::e($label) . '</span><textarea name="' . $k . '" rows="5" placeholder="' . View::e($ph) . '">' . View::e($job[$k] ?? '') . '</textarea></label>';
$id = (int)$job['id'];
?>
<h1><?= View::e($title) ?><?= $id ? '（No.' . $id . '）' : '' ?></h1>
<?php if ($companies === []): ?>
  <p class="flash warn">先に<a href="<?= View::e(App::url('/admin/companies/edit')) ?>">企業を登録</a>してください。</p>
<?php endif; ?>
<?php if ($id && $job['status'] === 'published'): ?><p><a href="<?= View::e(App::url('/jobs/' . $id)) ?>" target="_blank">公開ページを見る ↗</a></p><?php endif; ?>
<form method="post" action="<?= View::e(App::url('/admin/jobs/save')) ?>" class="form grid-form">
  <?= Csrf::field() ?>
  <input type="hidden" name="id" value="<?= $id ?>">
  <fieldset><legend>基本</legend>
    <label class="field"><span class="lbl">状態</span><?= View::select('status', Master::JOB_STATUS, $job['status']) ?></label>
    <label class="field"><span class="lbl">掲載企業 <em>必須</em></span><?= View::select('company_id', $companies, $job['company_id'], 'required', '選んでください') ?></label>
    <label class="field wide"><span class="lbl">求人タイトル <em>必須</em></span><input type="text" name="title" value="<?= View::e($job['title']) ?>" required maxlength="200" placeholder="例：週3日・9時〜16時｜マンション管理員"></label>
    <label class="field"><span class="lbl">職種名 <em>必須</em></span><input type="text" name="job_name" value="<?= View::e($job['job_name']) ?>" required maxlength="100" placeholder="例：マンション管理員"></label>
    <label class="field"><span class="lbl">職種（分類）</span><?= View::select('category_id', $categories, $job['category_id'], '', '選んでください') ?></label>
    <label class="field"><span class="lbl">雇用形態</span><?= View::select('employment_type', Master::EMPLOYMENT_TYPES, $job['employment_type']) ?></label>
    <label class="field"><span class="lbl">掲載期限</span><input type="date" name="valid_until" value="<?= View::e($job['valid_until']) ?>"></label>
    <?= str_replace('class="field"', 'class="field wide"', $t('catch_copy', 'キャッチコピー（一覧・説明文に使います）', '例：60代の方も多数活躍中。マイペースに働けます。')) ?>
  </fieldset>
  <fieldset><legend>給与</legend>
    <label class="field"><span class="lbl">給与の種類</span><?= View::select('salary_type', Master::SALARY_TYPES, $job['salary_type']) ?></label>
    <label class="field"><span class="lbl">下限（円）</span><input type="text" inputmode="numeric" name="salary_min" value="<?= View::e($job['salary_min']) ?>"></label>
    <label class="field"><span class="lbl">上限（円・なければ空欄）</span><input type="text" inputmode="numeric" name="salary_max" value="<?= View::e($job['salary_max']) ?>"></label>
    <?= $t('salary_note', '給与の補足', '例：交通費別途支給（月2万円まで）') ?>
  </fieldset>
  <fieldset><legend>勤務地・勤務時間</legend>
    <label class="field"><span class="lbl">都道府県 <em>必須</em></span><?= View::select('prefecture', array_combine(Master::PREFECTURES, Master::PREFECTURES), $job['prefecture'], 'required', '選んでください') ?></label>
    <?= $t('city', '市区町村', '例：世田谷区') ?>
    <?= $t('address', '勤務地住所', '例：東京都世田谷区〇〇1-2-3') ?>
    <?= $t('station', 'アクセス', '例：小田急線「経堂駅」徒歩5分') ?>
    <?= $t('work_days', '勤務日数', '例：週3日（月・水・金）') ?>
    <?= $t('work_hours', '勤務時間', '例：9:00〜16:00（休憩60分）') ?>
    <?= $t('holidays', '休日', '例：土日祝') ?>
    <?= $t('apply_tel', '電話応募の番号（空欄なら非表示）', '', 30) ?>
  </fieldset>
  <fieldset><legend>仕事内容・条件</legend>
    <?= $ta('description', '仕事内容') ?>
    <?= $ta('requirements', '応募資格', '例：未経験OK。年齢不問。') ?>
    <?= $ta('benefits', '待遇・福利厚生') ?>
  </fieldset>
  <fieldset><legend>明示が必要な労働条件（職業安定法）</legend>
    <?= $t('trial_period', '試用期間', '例：あり（3か月・条件変更なし）') ?>
    <?= $t('insurance', '加入保険', '例：雇用保険・労災保険') ?>
    <?= $t('smoking', '受動喫煙対策', '例：屋内禁煙') ?>
    <?= $t('change_scope', '就業場所・業務の変更の範囲', '例：変更なし') ?>
    <?= $t('contract_period', '契約期間', '例：1年ごとの更新（更新上限なし）') ?>
  </fieldset>
  <fieldset><legend>特徴タグ</legend>
    <div class="checks">
      <?php foreach ($tags as $tid => $name): ?>
      <label><input type="checkbox" name="tags[]" value="<?= (int)$tid ?>"<?= isset($job['tags'][$tid]) ? ' checked' : '' ?>> <?= View::e($name) ?></label>
      <?php endforeach; ?>
    </div>
  </fieldset>
  <p class="actions"><button class="btn btn-main">保存する</button></p>
</form>
<?php if ($id): ?>
<div class="sub-actions">
  <form method="post" action="<?= View::e(App::url('/admin/jobs/copy')) ?>"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= $id ?>"><button class="btn btn-sub">この求人をコピーして新規作成</button></form>
  <form method="post" action="<?= View::e(App::url('/admin/jobs/delete')) ?>" onsubmit="return confirm('削除しますか？（応募がある場合は掲載終了になります）')"><?= Csrf::field() ?><input type="hidden" name="id" value="<?= $id ?>"><button class="btn btn-del">削除</button></form>
</div>
<?php endif; ?>
