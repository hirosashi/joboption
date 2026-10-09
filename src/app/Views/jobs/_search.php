<?php
use App\Core\App;
use App\Core\View;

/** @var array<int, string> $prefs @var array<int, string> $categories @var array<int, string> $tags */
$f = $f ?? ['pref' => '', 'cat' => 0, 'tag' => 0, 'q' => ''];
?>
<form class="search" method="get" action="<?= View::e(App::url('/jobs')) ?>">
  <label><span>エリア</span>
    <select name="pref"><option value="">すべてのエリア</option>
      <?php foreach ($prefs as $p): ?><option value="<?= View::e($p) ?>"<?= $f['pref'] === $p ? ' selected' : '' ?>><?= View::e($p) ?></option><?php endforeach; ?>
    </select>
  </label>
  <label><span>職種</span>
    <select name="cat"><option value="">すべての職種</option>
      <?php foreach ($categories as $id => $name): ?><option value="<?= (int)$id ?>"<?= (int)$f['cat'] === $id ? ' selected' : '' ?>><?= View::e($name) ?></option><?php endforeach; ?>
    </select>
  </label>
  <label><span>こだわり</span>
    <select name="tag"><option value="">指定なし</option>
      <?php foreach ($tags as $id => $name): ?><option value="<?= (int)$id ?>"<?= (int)$f['tag'] === $id ? ' selected' : '' ?>><?= View::e($name) ?></option><?php endforeach; ?>
    </select>
  </label>
  <label class="q"><span>キーワード</span><input type="search" name="q" value="<?= View::e($f['q']) ?>" placeholder="例：清掃　駅名　会社名"></label>
  <button class="btn btn-main">この条件でさがす</button>
</form>
