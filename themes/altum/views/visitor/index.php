<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url('visitors') ?>"><?= language()->visitors->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= language()->visitor->breadcrumb ?></li>
        </ol>
    </nav>

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row">
        <div class="col-12 col-lg-3">
            <div class="">
                <?php

                $icon = new \Jdenticon\Identicon([
                    'value' => $data->visitor->visitor_uuid,
                    'size' => 75
                ]);

                ?>

                <img src="<?= $icon->getImageDataUri() ?>" class="visitor-big-avatar shadow-sm rounded-circle" alt="" />
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->custom_parameters ?></span>
                </div>

                <div class="col-7 col-lg-12">
                    <?php $data->visitor->custom_parameters = json_decode($data->visitor->custom_parameters, true); ?>

                    <?php if($data->visitor->custom_parameters && count($data->visitor->custom_parameters)): ?>

                        <div class="row">
                            <?php foreach($data->visitor->custom_parameters as $key => $value): ?>
                                <div class="col-4 text-muted font-weight-bold"><?= $key ?></div>
                                <div class="col-8 text-left"><?= $value ?></div>
                            <?php endforeach ?>
                        </div>

                    <?php else: ?>
                        -
                    <?php endif ?>
                </div>

            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->total_sessions ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><?= nr($data->visitor->total_sessions) ?></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->date ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><span data-toggle="tooltip" title="<?= \Altum\Date::get($data->visitor->date, 1) ?>"><?= \Altum\Date::get($data->visitor->date, 2) ?></span></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->last_date ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><span data-toggle="tooltip" title="<?= \Altum\Date::get($data->visitor->last_date, 1) ?>"><?= \Altum\Date::get($data->visitor->last_date, 2) ?></span></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->country ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div>
                        <img src="<?= ASSETS_FULL_URL . 'images/countries/' . ($data->visitor->country_code ? mb_strtolower($data->visitor->country_code) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                        <span class="align-middle"><?= $data->visitor->country_code ? get_country_from_country_code($data->visitor->country_code) :  language()->visitor->visitor->country_unknown ?></span>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->city ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div>
                        <div><?= $data->visitor->city_name ? $data->visitor->city_name : language()->visitor->visitor->city_unknown ?></div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->device_type ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><i class="fa fa-fw fa-<?= $data->visitor->device_type ?> fa-sm mr-1"></i> <?= language()->visitor->device_type->{$data->visitor->device_type} ?></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->operating_system ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><?= $data->visitor->os_name . ' ' . $data->visitor->os_version ?></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->browser ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><?= $data->visitor->browser_name . ' ' . $data->visitor->browser_version ?></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->browser_language ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><?= get_language_from_locale($data->visitor->browser_language) ?></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->screen_resolution ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><?= $data->visitor->screen_resolution ?></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-5 col-lg-12">
                    <span class="text-muted"><?= language()->visitor->visitor->average_time_per_session ?></span>
                </div>
                <div class="col-7 col-lg-12">
                    <div><?= \Altum\Date::get_seconds_to_his($data->average_time_per_session) ?></div>
                </div>
            </div>

            <div class="my-3">
                <button
                        type="button"
                        class="btn btn-outline-secondary btn-sm btn-block"
                        data-toggle="modal"
                        data-target="#visitor_delete"
                        data-visitor-id="<?= $data->visitor->visitor_id ?>"
                >
                    <i class="fa fa-fw fa-times fa-sm"></i> <?= language()->global->delete ?>
                </button>
            </div>
        </div>

        <div class="col-12 col-lg-9">
            <div class="d-flex flex-column flex-md-row justify-content-between mt-3 mb-5">
                <h1 class="h3"><?= language()->analytics->sessions ?></h1>

                <div>
                    <button
                            id="daterangepicker"
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            data-min-date="<?= \Altum\Date::get($this->website->date, 4) ?>"
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

            <?php if(!$data->sessions_result->num_rows): ?>
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= language()->visitor->basic->no_data ?>" />
                    <h2 class="h4 text-muted"><?= language()->visitor->basic->no_data ?></h2>
                </div>
            <?php else: ?>

                <?php while($row = $data->sessions_result->fetch_object()): ?>

                <div class="card border-0 mb-3">
                    <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">

                        <div class="d-flex flex-column mb-2 mb-md-0">
                            <span><?= \Altum\Date::get($row->date, 2) ?></span>
                            <span class="text-muted">
                                <?= \Altum\Date::get($row->date, 3) ?> <i class="fa fa-fw fa-sm fa-arrow-right"></i> <?= \Altum\Date::get($row->last_date, 3) ?>
                            </span>
                        </div>

                        <a href="#" class="mb-2 mb-md-0" data-toggle="modal" data-target="#session_events_modal" data-session-id="<?= $row->session_id ?>">
                            <i class="fa fa-fw fa-eye fa-sm text-muted"></i> <?= sprintf(language()->visitor->basic->pageviews, '<strong>' . nr($row->pageviews) . '</strong>') ?>
                        </a>

                        <?php if($row->sessions_replays_session_id): ?>
                        <a class="mb-2 mb-md-0" href="<?= url('replay/' . $row->sessions_replays_session_id) ?>">
                            <i class="fa fa-fw fa-play-circle fa-sm text-muted"></i> <?= language()->visitor->basic->replays ?>
                        </a>
                        <?php endif ?>

                        <div class="d-flex flex-column text-muted mb-2 mb-md-0">
                            <?= sprintf(language()->visitor->basic->time_spent, (new \DateTime($row->last_date))->diff((new \DateTime($row->date)))->format('%H:%I:%S')) ?>
                        </div>

                    </div>
                </div>

                <?php endwhile ?>

            <?php endif ?>

        </div>
    </div>

</section>


<input type="hidden" name="start_date" value="<?= \Altum\Date::get($data->datetime['start_date'], 1) ?>" />
<input type="hidden" name="end_date" value="<?= \Altum\Date::get($data->datetime['end_date'], 1) ?>" />
<input type="hidden" name="visitor_id" value="<?= $data->visitor->visitor_id ?>" />

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>

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
            <?= json_encode(language()->global->date->all_time) ?>: [moment($('#daterangepicker').data('min-date')), moment()]
        },
        alwaysShowCalendars: true,
        linkedCalendars: false,
        singleCalendar: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {

        /* Redirect */
        redirect(`<?= url('visitor/' . $data->visitor->visitor_id) ?>?start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
