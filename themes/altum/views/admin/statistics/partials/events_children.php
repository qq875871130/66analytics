<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-bell fa-xs text-muted"></i> <?= language()->admin_statistics->events_children->header ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['click']) ?></span> <?= language()->admin_statistics->events_children->chart_click ?>
            </div>
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['form']) ?></span> <?= language()->admin_statistics->events_children->chart_form ?>
            </div>
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['scroll']) ?></span> <?= language()->admin_statistics->events_children->chart_scroll ?>
            </div>
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['resize']) ?></span> <?= language()->admin_statistics->events_children->chart_resize ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="events_children"></canvas>
        </div>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    let click_color = css.getPropertyValue('--teal');
    let form_color = css.getPropertyValue('--indigo');
    let scroll_color = css.getPropertyValue('--cyan');
    let resize_color = css.getPropertyValue('--blue');

    /* Display chart */
    new Chart(document.getElementById('events_children').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $data->events_children_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(language()->admin_statistics->events_children->chart_click) ?>,
                    data: <?= $data->events_children_chart['click'] ?? '[]' ?>,
                    backgroundColor: click_color,
                    borderColor: click_color,
                    fill: false
                },
                {
                    label: <?= json_encode(language()->admin_statistics->events_children->chart_form) ?>,
                    data: <?= $data->events_children_chart['form'] ?? '[]' ?>,
                    backgroundColor: form_color,
                    borderColor: form_color,
                    fill: false
                },
                {
                    label: <?= json_encode(language()->admin_statistics->events_children->chart_scroll) ?>,
                    data: <?= $data->events_children_chart['scroll'] ?? '[]' ?>,
                    backgroundColor: scroll_color,
                    borderColor: scroll_color,
                    fill: false
                },
                {
                    label: <?= json_encode(language()->admin_statistics->events_children->chart_resize) ?>,
                    data: <?= $data->events_children_chart['resize'] ?? '[]' ?>,
                    backgroundColor: resize_color,
                    borderColor: resize_color,
                    fill: false
                }
            ]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
