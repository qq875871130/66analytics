<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-lg-row justify-content-between mb-4">
    <div>
        <div class="d-flex justify-content-between">
            <h1 class="h3"><i class="fa fa-fw fa-xs fa-chart-line text-primary-900 mr-2"></i> <?= sprintf(language()->admin_statistics->header) ?></h1>
        </div>
        <p class="text-muted"><?= language()->admin_statistics->subheader ?></p>
    </div>

    <div class="col-auto p-0">
        <button
                id="daterangepicker"
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-max-date="<?= \Altum\Date::get('', 4) ?>"
        >
            <i class="fa fa-fw fa-calendar mr-1"></i>
            <span>
                <?php if($data->datetime['start_date'] == $data->datetime['end_date']): ?>
                    <?= \Altum\Date::get($data->datetime['start_date'], 2, \Altum\Date::$default_timezone) ?>
                <?php else: ?>
                    <?= \Altum\Date::get($data->datetime['start_date'], 2, \Altum\Date::$default_timezone) . ' - ' . \Altum\Date::get($data->datetime['end_date'], 2, \Altum\Date::$default_timezone) ?>
                <?php endif ?>
            </span>
            <i class="fa fa-fw fa-caret-down ml-1"></i>
        </button>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="row">
    <div class="mb-5 mb-xl-0 col-12 col-xl-3">
        <div class="nav flex-column nav-pills">
            <a class="nav-link <?= $data->type == 'growth' ? 'active' : null ?>" href="<?= url('admin/statistics/growth?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-seedling mr-1"></i> <?= language()->admin_statistics->growth->menu ?></a>
            <?php if(in_array(settings()->license->type, ['Extended License','extended'])): ?>
                <a class="nav-link <?= $data->type == 'payments' ? 'active' : null ?>" href="<?= url('admin/statistics/payments?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= language()->admin_statistics->payments->menu ?></a>
                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <a class="nav-link <?= $data->type == 'affiliates_commissions' ? 'active' : null ?>" href="<?= url('admin/statistics/affiliates_commissions?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= language()->admin_statistics->affiliates_commissions->menu ?></a>
                    <a class="nav-link <?= $data->type == 'affiliates_withdrawals' ? 'active' : null ?>" href="<?= url('admin/statistics/affiliates_withdrawals?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= language()->admin_statistics->affiliates_withdrawals->menu ?></a>
                <?php endif ?>
            <?php endif ?>
            <a class="nav-link <?= $data->type == 'websites' ? 'active' : null ?>" href="<?= url('admin/statistics/websites?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-server mr-1"></i> <?= language()->admin_statistics->websites->menu ?></a>
            <a class="nav-link <?= $data->type == 'lightweight_events' ? 'active' : null ?>" href="<?= url('admin/statistics/lightweight_events?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-seedling mr-1"></i> <?= language()->admin_statistics->lightweight_events->menu ?></a>
            <a class="nav-link <?= $data->type == 'sessions_events' ? 'active' : null ?>" href="<?= url('admin/statistics/sessions_events?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-seedling mr-1"></i> <?= language()->admin_statistics->sessions_events->menu ?></a>
            <a class="nav-link <?= $data->type == 'events_children' ? 'active' : null ?>" href="<?= url('admin/statistics/events_children?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-bell mr-1"></i> <?= language()->admin_statistics->events_children->menu ?></a>
            <a class="nav-link <?= $data->type == 'sessions_replays' ? 'active' : null ?>" href="<?= url('admin/statistics/sessions_replays?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-video mr-1"></i> <?= language()->admin_statistics->sessions_replays->menu ?></a>
            <a class="nav-link <?= $data->type == 'websites_heatmaps' ? 'active' : null ?>" href="<?= url('admin/statistics/websites_heatmaps?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-fire mr-1"></i> <?= language()->admin_statistics->websites_heatmaps->menu ?></a>
            <a class="nav-link <?= $data->type == 'websites_goals' ? 'active' : null ?>" href="<?= url('admin/statistics/websites_goals?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-bullseye mr-1"></i> <?= language()->admin_statistics->websites_goals->menu ?></a>
            <a class="nav-link <?= $data->type == 'goals_conversions' ? 'active' : null ?>" href="<?= url('admin/statistics/goals_conversions?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-bullseye mr-1"></i> <?= language()->admin_statistics->goals_conversions->menu ?></a>
            <a class="nav-link <?= $data->type == 'teams' ? 'active' : null ?>" href="<?= url('admin/statistics/teams?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-user-shield mr-1"></i> <?= language()->admin_statistics->teams->menu ?></a>
            <a class="nav-link <?= $data->type == 'email_reports' ? 'active' : null ?>" href="<?= url('admin/statistics/email_reports?start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>"><i class="fa fa-fw fa-sm fa-envelope mr-1"></i> <?= language()->admin_statistics->email_reports->menu ?></a>
        </div>
    </div>

    <div class="col-12 col-xl-9">

        <?php

        /* Load the proper type view */
        $partial = require THEME_PATH . 'views/admin/statistics/partials/' . $data->type . '.php';

        echo $partial->html;

        ?>

    </div>
</div>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/Chart.bundle.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/admin_chartjs_defaults.js' ?>"></script>

    <script>
        'use strict';

        moment.tz.setDefault(<?= json_encode($this->user->timezone) ?>);

        /* Daterangepicker */
        $('#daterangepicker').daterangepicker({
            startDate: <?= json_encode($data->datetime['start_date']) ?>,
            endDate: <?= json_encode($data->datetime['end_date']) ?>,
            minDate: $('#daterangepicker').data('min-date'),
            maxDate: $('#daterangepicker').data('max-date'),
            ranges: {
                <?= json_encode(language()->global->date->today) ?>: [moment(), moment()],
                <?= json_encode(language()->global->date->yesterday) ?>: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                <?= json_encode(language()->global->date->last_7_days) ?>: [moment().subtract(6, 'days'), moment()],
                <?= json_encode(language()->global->date->last_30_days) ?>: [moment().subtract(29, 'days'), moment()],
                <?= json_encode(language()->global->date->this_month) ?>: [moment().startOf('month'), moment().endOf('month')],
                <?= json_encode(language()->global->date->last_month) ?>: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                <?= json_encode(language()->global->date->all_time) ?>: [moment('2015-01-01'), moment()]
            },
            alwaysShowCalendars: true,
            linkedCalendars: false,
            singleCalendar: true,
            locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
        }, (start, end, label) => {

            /* Redirect */
            redirect(`<?= url('admin/statistics/' . $data->type) ?>?start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

        });

        let css = window.getComputedStyle(document.body)
    </script>

<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php ob_start() ?>
<?= $partial->javascript ?>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
