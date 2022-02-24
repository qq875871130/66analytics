<?php defined('ALTUMCODE') || die() ?>

<h1><?= language()->help->advanced_tracking->header ?></h1>

<p><?= language()->help->advanced_tracking->p1 ?></p>

<div class="mb-4">
    <ul>
        <li class="mb-2"><a href="<?= url('help/goals') ?>"><?= language()->help->goals->menu ?></a></li>
        <li class="mb-2"><a href="<?= url('help/custom-parameters') ?>"><?= language()->help->custom_parameters->menu ?></a></li>
        <li class="mb-2"><a href="<?= url('help/opt-out') ?>"><?= language()->help->opt_out->menu ?></a></li>
        <li class="mb-2"><a href="<?= url('help/dnt') ?>"><?= language()->help->dnt->menu ?></a></li>
    </ul>
</div>

<div class="mb-4">
    <p class="font-weight-bold"><?= language()->help->pros ?></p>

    <ul>
        <li class="mb-2"><?= language()->help->advanced_tracking->pro_1 ?></li>
        <li class="mb-2"><?= language()->help->advanced_tracking->pro_2 ?></li>
        <li class="mb-2"><?= language()->help->advanced_tracking->pro_3 ?></li>
        <li class="mb-2"><?= language()->help->advanced_tracking->pro_4 ?></li>
    </ul>
</div>

<div class="mb-4">
    <p class="font-weight-bold"><?= language()->help->cons ?></p>

    <ul>
        <li class="mb-2"><?= language()->help->advanced_tracking->con_1 ?></li>
        <li class="mb-2"><?= language()->help->advanced_tracking->con_2 ?></li>
    </ul>
</div>
