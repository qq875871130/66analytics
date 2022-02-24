<?php defined('ALTUMCODE') || die() ?>

<ul class="list-style-none m-0">
    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->websites_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->websites_limit ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->websites_limit, '<strong>' . ($data->plan_settings->websites_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->websites_limit)) . '</strong>') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->sessions_events_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->sessions_events_limit ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->sessions_events_limit, '<strong>' . ($data->plan_settings->sessions_events_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->sessions_events_limit)) . '</strong>') . ' <small class="text-muted">' . language()->global->plan_settings->per_month . '</small>' ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->events_children_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->events_children_limit ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->events_children_limit, '<strong>' . ($data->plan_settings->events_children_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->events_children_limit)) . '</strong>') . ' <small class="text-muted">' . language()->global->plan_settings->per_month . '</small>' ?>
        </div>
    </li>

    <?php if($data->plan_settings->events_children_limit != 0): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 fa-check-circle text-success"></i>
            <div>
                <?= sprintf(language()->global->plan_settings->events_children_retention, '<strong>' . ($data->plan_settings->events_children_retention == -1 ? language()->global->unlimited : nr($data->plan_settings->events_children_retention)) . '</strong>') ?>
            </div>
        </li>
    <?php endif ?>

    <?php if(settings()->analytics->sessions_replays_is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->sessions_replays_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->sessions_replays_limit ? null : 'text-muted' ?>">
                <?= sprintf(language()->global->plan_settings->sessions_replays_limit, '<strong>' . ($data->plan_settings->sessions_replays_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->sessions_replays_limit)) . '</strong>') . ' <small class="text-muted">' . language()->global->plan_settings->per_month . '</small>' ?>
            </div>
        </li>
    <?php endif ?>

    <?php if($data->plan_settings->sessions_replays_limit != 0): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 fa-check-circle text-success"></i>
            <div>
                <?= sprintf(language()->global->plan_settings->sessions_replays_retention, '<strong>' . ($data->plan_settings->sessions_replays_retention == -1 ? language()->global->unlimited : nr($data->plan_settings->sessions_replays_retention)) . '</strong>') ?>
            </div>
        </li>
    <?php endif ?>

    <?php if(settings()->analytics->websites_heatmaps_is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->websites_heatmaps_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->websites_heatmaps_limit ? null : 'text-muted' ?>">
                <?= sprintf(language()->global->plan_settings->websites_heatmaps_limit, '<strong>' . ($data->plan_settings->websites_heatmaps_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->websites_heatmaps_limit)) . '</strong>') ?>
            </div>
        </li>
    <?php endif ?>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->websites_goals_limit ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->websites_goals_limit ? null : 'text-muted' ?>">
            <?= sprintf(language()->global->plan_settings->websites_goals_limit, '<strong>' . ($data->plan_settings->websites_goals_limit == -1 ? language()->global->unlimited : nr($data->plan_settings->websites_goals_limit)) . '</strong>') ?>
        </div>
    </li>

    <?php if(settings()->analytics->email_reports_is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->email_reports_is_enabled ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->email_reports_is_enabled ? null : 'text-muted' ?>">
                <?= $data->plan_settings->email_reports_is_enabled ? language()->global->plan_settings->{'email_reports_is_enabled_' . settings()->analytics->email_reports_is_enabled} : language()->global->plan_settings->email_reports_is_enabled ?>
            </div>
        </li>
    <?php endif ?>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->teams_is_enabled ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->teams_is_enabled ? null : 'text-muted' ?>">
            <?= language()->global->plan_settings->teams_is_enabled ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->no_ads ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->no_ads ? null : 'text-muted' ?>">
            <?= language()->global->plan_settings->no_ads ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->api_is_enabled ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->api_is_enabled ? null : 'text-muted' ?>">
            <?= language()->global->plan_settings->api_is_enabled ?>
        </div>
    </li>

    <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->affiliate_is_enabled ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->affiliate_is_enabled ? null : 'text-muted' ?>">
                <?= language()->global->plan_settings->affiliate_is_enabled ?>
            </div>
        </li>
    <?php endif ?>
</ul>
