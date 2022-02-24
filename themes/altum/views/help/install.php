<?php defined('ALTUMCODE') || die() ?>

<h1><?= sprintf(language()->help->install->header, settings()->main->title) ?></h1>

<p><?= language()->help->install->p1 ?></p>

<p><?= language()->help->install->p2 ?></p>

<ol class="">
    <li class="mb-2">
        <?= language()->help->install->step1 ?>
    </li>
    <li class="mb-2">
        <?= sprintf(language()->help->install->step2, url('websites')) ?>
    </li>
    <li class="mb-2">
        <?= language()->help->install->step3 ?>
    </li>
    <li class="mb-2">
        <?= language()->help->install->step4 ?>
    </li>
    <li class="mb-2">
        <?= language()->help->install->step5 ?>
    </li>
    <li class="mb-2">
        <?= language()->help->install->step6 ?>
    </li>
</ol>
