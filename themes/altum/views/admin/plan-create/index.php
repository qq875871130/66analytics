<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/plans') ?>"><?= language()->admin_plans->breadcrumb ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= language()->admin_plan_create->breadcrumb ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 mr-1"><i class="fa fa-fw fa-xs fa-box-open text-primary-900 mr-2"></i> <?= language()->admin_plan_create->header ?></h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card">
    <div class="card-body">

        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label for="name"><?= language()->admin_plans->main->name ?></label>
                <input type="text" id="name" name="name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="description"><?= language()->admin_plans->main->description ?></label>
                <input type="text" id="description" name="description" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('description') ? 'is-invalid' : null ?>" value="" />
                <?= \Altum\Alerts::output_field_error('description') ?>
            </div>

            <div class="form-group">
                <label for="order"><?= language()->admin_plans->main->order ?></label>
                <input type="number" min="0" id="order" name="order" class="form-control form-control-lg" value="" />
            </div>

            <div class="form-group">
                <label for="trial_days"><?= language()->admin_plans->main->trial_days ?></label>
                <input id="trial_days" type="number" min="0" name="trial_days" class="form-control form-control-lg" value="0" />
                <div><small class="form-text text-muted"><?= language()->admin_plans->main->trial_days_help ?></small></div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-xl-4">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="monthly_price"><?= language()->admin_plans->main->monthly_price ?> <small class="form-text text-muted"><?= settings()->payment->currency ?></small></label>
                            <input type="text" id="monthly_price" name="monthly_price" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('monthly_price') ? 'is-invalid' : null ?>" required="required" />
                            <?= \Altum\Alerts::output_field_error('monthly_price') ?>
                            <small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->price_help, language()->admin_plans->main->monthly_price) ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-4">
                    <div class="form-group">
                        <label for="annual_price"><?= language()->admin_plans->main->annual_price ?> <small class="form-text text-muted"><?= settings()->payment->currency ?></small></label>
                        <input type="text" id="annual_price" name="annual_price" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('annual_price') ? 'is-invalid' : null ?>" required="required" />
                        <?= \Altum\Alerts::output_field_error('annual_price') ?>
                        <small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->price_help, language()->admin_plans->main->annual_price) ?></small>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-4">
                    <div class="form-group">
                        <label for="lifetime_price"><?= language()->admin_plans->main->lifetime_price ?> <small class="form-text text-muted"><?= settings()->payment->currency ?></small></label>
                        <input type="text" id="lifetime_price" name="lifetime_price" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('lifetime_price') ? 'is-invalid' : null ?>" required="required" />
                        <?= \Altum\Alerts::output_field_error('lifetime_price') ?>
                        <small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->price_help, language()->admin_plans->main->lifetime_price) ?></small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="taxes_ids"><?= language()->admin_plans->main->taxes_ids ?></label>
                <select id="taxes_ids" name="taxes_ids[]" class="form-control form-control-lg" multiple="multiple">
                    <?php if($data->taxes): ?>
                        <?php foreach($data->taxes as $tax): ?>
                            <option value="<?= $tax->tax_id ?>">
                                <?= $tax->name . ' - ' . $tax->description ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
                <small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->taxes_ids_help, '<a href="' . url('admin/taxes') .'">', '</a>') ?></small>
            </div>

            <div class="form-group">
                <label for="codes_ids"><?= language()->admin_plans->main->codes_ids ?></label>
                <select id="codes_ids" name="codes_ids[]" class="form-control form-control-lg" multiple="multiple">
                    <?php if($data->codes): ?>
                        <?php foreach($data->codes as $code): ?>
                            <option value="<?= $code->code_id ?>">
                                <?= $code->name . ' - ' . $code->code ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>
                <small class="form-text text-muted"><?= sprintf(language()->admin_plans->main->codes_ids_help, '<a href="' . url('admin/codes') .'">', '</a>') ?></small>
            </div>

            <div class="form-group">
                <label for="color"><?= language()->admin_plans->main->color ?></label>
                <input type="text" id="color" name="color" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('color') ? 'is-invalid' : null ?>" value="" />
                <?= \Altum\Alerts::output_field_error('color') ?>
                <small class="form-text text-muted"><?= language()->admin_plans->main->color_help ?></small>
            </div>

            <div class="form-group">
                <label for="status"><?= language()->admin_plans->main->status ?></label>
                <select id="status" name="status" class="form-control form-control-lg">
                    <option value="1"><?= language()->global->active ?></option>
                    <option value="0"><?= language()->global->disabled ?></option>
                    <option value="2"><?= language()->global->hidden ?></option>
                </select>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= language()->admin_plans->plan->header ?></h2>

            <div>
                <div class="form-group">
                    <label for="websites_limit"><?= language()->admin_plans->plan->websites_limit ?></label>
                    <input type="number" id="websites_limit" name="websites_limit" min="-1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->websites_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="sessions_events_limit"><?= language()->admin_plans->plan->sessions_events_limit ?> <small class="form-text text-muted"><?= language()->admin_plans->plan->per_month ?></small></label>
                    <input type="number" id="sessions_events_limit" name="sessions_events_limit" min="-1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->sessions_events_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="events_children_limit"><?= language()->admin_plans->plan->events_children_limit ?> <small class="form-text text-muted"><?= language()->admin_plans->plan->per_month ?></small></label>
                    <input type="number" id="events_children_limit" name="events_children_limit" min="-1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->events_children_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="events_children_retention"><?= language()->admin_plans->plan->events_children_retention ?> <small class="form-text text-muted"><?= language()->global->date->days ?></small></label>
                    <input type="number" id="events_children_retention" name="events_children_retention" min="-1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->events_children_retention_help ?></small>
                </div>

                <div class="form-group">
                    <label for="sessions_replays_limit"><?= language()->admin_plans->plan->sessions_replays_limit ?> <small class="form-text text-muted"><?= language()->admin_plans->plan->per_month ?></small></label>
                    <input type="number" id="sessions_replays_limit" name="sessions_replays_limit" min="-1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->sessions_replays_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="sessions_replays_retention"><?= language()->admin_plans->plan->sessions_replays_retention ?> <small class="form-text text-muted"><?= language()->global->date->days ?></small></label>
                    <input type="number" id="sessions_replays_retention" name="sessions_replays_retention" min="-1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->sessions_replays_retention_help ?></small>
                </div>

                <div class="form-group">
                    <label for="sessions_replays_time_limit"><?= language()->admin_plans->plan->sessions_replays_time_limit ?> <small class="form-text text-muted"><?= language()->global->date->minutes ?></small></label>
                    <input type="number" id="sessions_replays_time_limit" name="sessions_replays_time_limit" min="1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->sessions_replays_time_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="websites_heatmaps_limit"><?= language()->admin_plans->plan->websites_heatmaps_limit ?></label>
                    <input type="number" id="websites_heatmaps_limit" name="websites_heatmaps_limit" min="-1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->websites_heatmaps_limit_help ?></small>
                </div>

                <div class="form-group">
                    <label for="websites_goals_limit"><?= language()->admin_plans->plan->websites_goals_limit ?></label>
                    <input type="number" id="websites_goals_limit" name="websites_goals_limit" min="-1" class="form-control form-control-lg" value="0" required="required" />
                    <small class="form-text text-muted"><?= language()->admin_plans->plan->websites_goals_limit_help ?></small>
                </div>

                <div class="mb-3">
                    <div class="custom-control custom-switch">
                        <input id="email_reports_is_enabled" name="email_reports_is_enabled" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="email_reports_is_enabled"><?= language()->admin_plans->plan->email_reports_is_enabled ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->email_reports_is_enabled_help ?></small></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="custom-control custom-switch">
                        <input id="teams_is_enabled" name="teams_is_enabled" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="teams_is_enabled"><?= language()->admin_plans->plan->teams_is_enabled ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->teams_is_enabled_help ?></small></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="custom-control custom-switch">
                        <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="no_ads"><?= language()->admin_plans->plan->no_ads ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->no_ads_help ?></small></div>
                    </div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="api_is_enabled" name="api_is_enabled" type="checkbox" class="custom-control-input">
                    <label class="custom-control-label" for="api_is_enabled"><?= language()->admin_plans->plan->api_is_enabled ?></label>
                    <div><small class="form-text text-muted"><?= language()->admin_plans->plan->api_is_enabled_help ?></small></div>
                </div>

                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <div class="custom-control custom-switch my-3">
                        <input id="affiliate_is_enabled" name="affiliate_is_enabled" type="checkbox" class="custom-control-input">
                        <label class="custom-control-label" for="affiliate_is_enabled"><?= language()->admin_plans->plan->affiliate_is_enabled ?></label>
                        <div><small class="form-text text-muted"><?= language()->admin_plans->plan->affiliate_is_enabled_help ?></small></div>
                    </div>
                <?php endif ?>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->create ?></button>

        </form>

    </div>
</div>
