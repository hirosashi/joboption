<?php
use App\Core\App;
use App\Core\Csrf;
use App\Core\View;
?>
<h1>職種・タグ</h1>
<div class="cols">
<?php foreach ($lists as $table => $rows): ?>
  <form method="post" action="<?= View::e(App::url('/admin/masters/save')) ?>" class="form">
    <h2><?= View::e($labels[$table]) ?></h2>
    <?= Csrf::field() ?>
    <input type="hidden" name="table" value="<?= View::e($table) ?>">
    <table class="list">
      <tr><th>並び順</th><th>名前</th><th>使用数</th><th>削除</th></tr>
      <?php foreach ($rows as $r): $rid = (int)$r['id']; ?>
      <tr><td><input type="number" name="rows[<?= $rid ?>][sort_no]" value="<?= (int)$r['sort_no'] ?>" class="num"></td><td><input type="text" name="rows[<?= $rid ?>][name]" value="<?= View::e($r['name']) ?>" maxlength="100"></td><td><?= (int)$r['used'] ?></td><td><?php if ((int)$r['used'] === 0): ?><input type="checkbox" name="rows[<?= $rid ?>][delete]" value="1"><?php endif; ?></td></tr>
      <?php endforeach; ?>
      <tr><td><input type="number" name="rows[new][sort_no]" value="<?= (count($rows) + 1) * 10 ?>" class="num"></td><td><input type="text" name="rows[new][name]" placeholder="追加する場合に入力" maxlength="100"></td><td></td><td></td></tr>
    </table>
    <button class="btn btn-main">保存する</button>
  </form>
<?php endforeach; ?>
</div>
