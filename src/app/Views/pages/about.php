<?php
use App\Core\App;
use App\Core\View;

$s = App::config()['site'] ?? [];
?>
<div class="in narrow">
  <h1 class="page-ttl">運営者情報</h1>
  <table class="spec">
    <tr><th>サイト名</th><td><?= View::e($s['name'] ?? '') ?></td></tr>
    <tr><th>運営者</th><td><?= View::e($s['operator'] ?? '') ?></td></tr>
    <tr><th>所在地</th><td><?= View::e($s['operator_address'] ?? '') ?></td></tr>
    <tr><th>電話番号</th><td><?= View::e($s['operator_tel'] ?? '') ?></td></tr>
    <tr><th>メール</th><td><?= View::e($s['operator_email'] ?? '') ?></td></tr>
    <tr><th>届出</th><td>特定募集情報等提供事業 届出受理番号：（届出後に記載）</td></tr>
  </table>
</div>
