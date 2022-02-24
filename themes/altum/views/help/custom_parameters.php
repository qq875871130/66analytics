<?php defined('ALTUMCODE') || die() ?>

<h1><?= language()->help->custom_parameters->header ?></h1>
<p><?= language()->help->custom_parameters->p1 ?></p>
<p><?= language()->help->custom_parameters->p2 ?></p>
<p><?= language()->help->custom_parameters->p3 ?></p>
<p><?= language()->help->custom_parameters->p4 ?></p>

<pre id="pixel_key_html" class="pre-custom rounded">&lt;script async src="<?= url('pixel/12345678910111213') ?>" data-custom-parameters='{"name": "John Doe", "email": "john@example.com"}'&gt&lt;/script&gt</pre>

<p><?= language()->help->custom_parameters->p5 ?></p>
