<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/users') ?>"><?= language()->admin_users->breadcrumb ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= language()->admin_user_update->breadcrumb ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex align-items-center">
        <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= language()->admin_user_update->header ?></h1>

        <?= include_view(THEME_PATH . 'views/admin/users/admin_user_dropdown_button.php', ['id' => $data->user->user_id]) ?>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<?php //ALTUMCODE:DEMO if(DEMO) {$data->user->email = 'hidden@demo.com'; $data->user->name = $data->user->ip = 'hidden on demo';} ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label for="name"><?= language()->admin_users->main->name ?></label>
                <input id="name" type="text" name="name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->user->name ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="email"><?= language()->admin_users->main->email ?></label>
                <input id="email" type="email" name="email" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->user->email ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('email') ?>
            </div>

            <div class="form-group">
                <label for="status"><?= language()->admin_users->main->status ?></label>
                <select id="status" name="status" class="form-control form-control-lg">
                    <option value="2" <?= $data->user->status == 2 ? 'selected="selected"' : null ?>><?= language()->admin_users->main->status_disabled ?></option>
                    <option value="1" <?= $data->user->status == 1 ? 'selected="selected"' : null ?>><?= language()->admin_users->main->status_active ?></option>
                    <option value="0" <?= $data->user->status == 0 ? 'selected="selected"' : null ?>><?= language()->admin_users->main->status_unconfirmed ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="type"><?= language()->admin_users->main->type ?></label>
                <select id="type" name="type" class="form-control form-control-lg">
                    <option value="1" <?= $data->user->type == 1 ? 'selected="selected"' : null ?>><?= language()->admin_users->main->type_admin ?></option>
                    <option value="0" <?= $data->user->type == 0 ? 'selected="selected"' : null ?>><?= language()->admin_users->main->type_user ?></option>
                </select>
                <small class="form-text text-muted"><?= language()->admin_users->main->type_help ?></small>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= language()->admin_user_update->plan->header ?></h2>

            <div class="form-group">
                <label for="plan_id"><?= language()->admin_users->main->plan_id ?></label>
                <select id="plan_id" name="plan_id" class="form-control form-control-lg">
                    <option value="free" <?= $data->user->plan->plan_id == 'free' ? 'selected="selected"' : null ?>><?= settings()->plan_free->name ?></option>
                    <option value="custom" <?= $data->user->plan->plan_id == 'custom' ? 'selected="selected"' : null ?>><?= settings()->plan_custom->name ?></option>

                    <?php foreach($data->plans as $plan): ?>
                        <option value="<?= $plan->plan_id ?>" <?= $data->user->plan->plan_id == $plan->plan_id ? 'selected="selected"' : null ?>><?= $plan->name ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="plan_trial_done"><?= language()->admin_users->main->plan_trial_done ?></label>
                <select id="plan_trial_done" name="plan_trial_done" class="form-control form-control-lg">
                    <option value="1" <?= $data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
                    <option value="0" <?= !$data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
                </select>
            </div>

            <div id="plan_expiration_date_container" class="form-group">
                <label for="plan_expiration_date"><?= language()->admin_users->main->plan_expiration_date ?></label>
                <input id="plan_expiration_date" type="text" name="plan_expiration_date" class="form-control form-control-lg" autocomplete="off" value="<?= $data->user->plan_expiration_date ?>">
                <div class="invalid-feedback">
                    <?= language()->admin_user_update->plan->plan_expiration_date_invalid ?>
                </div>
            </div>

            <div id="plan_settings" style="display: none">
                <div class="form-group">
                    <label for="websites_limit"><?= language()->admin_plans->plan->websites_limit ?></label>
                    <input type="number" id="websites_limit" name="websites_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->websites_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->websites_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="sessions_events_limit"><?= language()->admin_plans->plan->sessions_events_limit ?> <small class="form-text text-muted"><?= language()->admin_plans->plan->per_month ?></small></label>
                    <input type="number" id="sessions_events_limit" name="sessions_events_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->sessions_events_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->sessions_events_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="events_children_limit"><?= language()->admin_plans->plan->events_children_limit ?> <small class="form-text text-muted"><?= language()->admin_plans->plan->per_month ?></small></label>
                    <input type="number" id="events_children_limit" name="events_children_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->events_children_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->events_children_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="events_children_retention"><?= language()->admin_plans->plan->events_children_retention ?> <small class="form-text text-muted"><?= language()->global->date->days ?></small></label>
                    <input type="number" id="events_children_retention" name="events_children_retention" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->events_children_retention ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->events_children_retention_help ?></small>
                </div>

                <div class="form-group">
                    <label for="sessions_replays_limit"><?= language()->admin_plans->plan->sessions_replays_limit ?> <small class="form-text text-muted"><?= language()->admin_plans->plan->per_month ?></small></label>
                    <input type="number" id="sessions_replays_limit" name="sessions_replays_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->sessions_replays_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->sessions_replays_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="sessions_replays_retention"><?= language()->admin_plans->plan->sessions_replays_retention ?> <small class="form-text text-muted"><?= language()->global->date->days ?></small></label>
                    <input type="number" id="sessions_replays_retention" name="sessions_replays_retention" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->sessions_replays_retention ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->sessions_replays_retention_help ?></small>
                </div>

                <div class="form-group">
                    <label for="sessions_replays_time_limit"><?= language()->admin_plans->plan->sessions_replays_time_limit ?> <small class="form-text text-muted"><?= language()->global->date->minutes ?></small></label>
                    <input type="number" id="sessions_replays_time_limit" name="sessions_replays_time_limit" min="1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->sessions_replays_time_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->sessions_replays_time_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="websites_heatmaps_limit"><?= language()->admin_plans->plan->websites_heatmaps_limit ?></label>
                    <input type="number" id="websites_heatmaps_limit" name="websites_heatmaps_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->websites_heatmaps_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->websites_heatmaps_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="websites_goals_limit"><?= language()->admin_plans->plan->websites_goals_limit ?></label>
                    <input type="number" id="websites_goals_limit" name="websites_goals_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->websites_goals_limit ?>" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->websites_goals_limit_help ?></small>
                </div>

                <div class="mb-3">
                    <div class="custom-control custom-switch">
                        <input id="email_reports_is_enabled" name="email_reports_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->email_reports_is_enabled ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="email_reports_is_enabled"><?= language()->admin_plans->plan->email_reports_is_enabled ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->email_reports_is_enabled_help ?></small></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="custom-control custom-switch">
                        <input id="teams_is_enabled" name="teams_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->teams_is_enabled ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="teams_is_enabled"><?= language()->admin_plans->plan->teams_is_enabled ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->teams_is_enabled_help ?></small></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="custom-control custom-switch">
                        <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->no_ads ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="no_ads"><?= language()->admin_plans->plan->no_ads ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->no_ads_help ?></small></div>
                    </div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="api_is_enabled" name="api_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->api_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="api_is_enabled"><?= language()->admin_plans->plan->api_is_enabled ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->api_is_enabled_help ?></small></div>
                </div>

                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <div class="custom-control custom-switch my-3">
                        <input id="affiliate_is_enabled" name="affiliate_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->affiliate_is_enabled ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="affiliate_is_enabled"><?= language()->admin_plans->plan->affiliate_is_enabled ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->affiliate_is_enabled_help ?></small></div>
                    </div>
                <?php endif ?>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= language()->admin_user_update->change_password->header ?></h2>
            <p class="text-muted"><?= language()->admin_user_update->change_password->subheader ?></p>

            <div class="form-group">
                <label for="new_password"><?= language()->admin_user_update->change_password->new_password ?></label>
                <input id="new_password" type="password" name="new_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                <?= \Altum\Alerts::output_field_error('new_password') ?>
            </div>

            <div class="form-group">
                <label for="repeat_password"><?= language()->admin_user_update->change_password->repeat_password ?></label>
                <input id="repeat_password" type="password" name="repeat_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                <?= \Altum\Alerts::output_field_error('new_password') ?>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
        </form>
    </div>
</div>

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

    let check_plan_id = () => {
        let selected_plan_id = document.querySelector('[name="plan_id"]').value;

        if(selected_plan_id == 'free') {
            document.querySelector('#plan_expiration_date_container').style.display = 'none';
        } else {
            document.querySelector('#plan_expiration_date_container').style.display = 'block';
        }

        if(selected_plan_id == 'custom') {
            document.querySelector('#plan_settings').style.display = 'block';
        } else {
            document.querySelector('#plan_settings').style.display = 'none';
        }
    };

    check_plan_id();

    /* Dont show expiration date when the chosen plan is the free one */
    document.querySelector('[name="plan_id"]').addEventListener('change', check_plan_id);

    /* Check for expiration date to show a warning if expired */
    let check_plan_expiration_date = () => {
        let plan_expiration_date = document.querySelector('[name="plan_expiration_date"]');

        let plan_expiration_date_object = new Date(plan_expiration_date.value);
        let today_date_object = new Date();

        if(plan_expiration_date_object < today_date_object) {
            plan_expiration_date.classList.add('is-invalid');
        } else {
            plan_expiration_date.classList.remove('is-invalid');
        }
    };

    check_plan_expiration_date();
    document.querySelector('[name="plan_expiration_date"]').addEventListener('change', check_plan_expiration_date);

    /* Daterangepicker */
    $('[name="plan_expiration_date"]').daterangepicker({
        startDate: <?= json_encode($data->user->plan_expiration_date) ?>,
        minDate: new Date(),
        alwaysShowCalendars: true,
        singleCalendar: true,
        singleDatePicker: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {
        check_plan_expiration_date()
    });

</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_login_modal.php'), 'modals'); ?>
