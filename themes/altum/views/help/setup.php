<?php defined('ALTUMCODE') || die() ?>

<h1><?= sprintf(language()->help->setup->header, settings()->main->title) ?></h1>

<p><?= language()->help->setup->p1 ?></p>

<ol class="">
    <li class="mb-2">
        <?= language()->help->setup->step1 ?>
    </li>
    <li class="mb-2">
        <?= sprintf(language()->help->setup->step2, url('websites')) ?>
    </li>
    <li class="mb-2">
        <?= language()->help->setup->step3 ?>
    </li>
    <li class="mb-2">
        <?= language()->help->setup->step4 ?>
    </li>
    <li class="mb-2">
        <?= language()->help->setup->step5 ?>
    </li>
    <li class="mb-2">
        <?= language()->help->setup->step6 ?>
    </li>
</ol>
