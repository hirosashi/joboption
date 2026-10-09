<?php
use App\Core\View;
use App\Services\Jobs;

/** @var array<string, mixed> $job */
?>
<div class="apply-job">
  <p class="apply-job-ttl"><?= View::e($job['title']) ?></p>
  <p><?= View::e($job['company_name']) ?>｜<?= View::e(Jobs::salary($job)) ?>｜<?= View::e(Jobs::place($job)) ?></p>
</div>
