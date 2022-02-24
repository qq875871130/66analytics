<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-video fa-xs text-muted"></i> <?= language()->admin_statistics->sessions_replays->header ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['sessions_replays']) ?></span> <?= language()->admin_statistics->sessions_replays->chart ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="sessions_replays"></canvas>
        </div>
    </div>
</div>

<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    'use strict';

    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    /* Display chart */
    let sessions_replays_chart = document.getElementById('sessions_replays').getContext('2d');
    color_gradient = sessions_replays_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    new Chart(sessions_replays_chart, {
        type: 'line',
        data: {
            labels: <?= $data->sessions_replays_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(language()->admin_statistics->sessions_replays->chart) ?>,
                    data: <?= $data->sessions_replays_chart['sessions_replays'] ?? '[]' ?>,
                    backgroundColor: color_gradient,
                    borderColor: color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
