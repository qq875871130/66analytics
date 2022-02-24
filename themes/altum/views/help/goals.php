<?php defined('ALTUMCODE') || die() ?>

<h1><?= language()->help->goals->header ?></h1>
<p><?= language()->help->goals->p1 ?></p>

<h2><?= language()->help->goals->pageview->header ?></h2>
<p><?= language()->help->goals->pageview->p1 ?></p>
<p><?= language()->help->goals->pageview->p2 ?></p>

<h2><?= language()->help->goals->custom->header ?></h2>
<p><?= language()->help->goals->custom->p1 ?></p>
<p><?= language()->help->goals->custom->p2 ?></p>
<p><?= language()->help->goals->custom->p3 ?></p>
<ul>
    <li class="mb-2"><?= language()->help->goals->custom->li1 ?></li>
    <li class="mb-2"><?= language()->help->goals->custom->li2 ?></li>
    <li class="mb-2"><?= language()->help->goals->custom->li3 ?></li>
</ul>
<p><?= language()->help->goals->custom->p4 ?></p>

<pre id="pixel_key_html" class="pre-custom rounded"><?= settings()->analytics->pixel_exposed_identifier ?>.goal('my-goal');</pre>

<p><?= language()->help->goals->custom->p5 ?></p>
