<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="sessions_replays_is_enabled"><i class="fa fa-fw fa-sm fa-video text-muted mr-1"></i> <?= language()->admin_settings->analytics->sessions_replays_is_enabled ?></label>
        <select id="sessions_replays_is_enabled" name="sessions_replays_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->analytics->sessions_replays_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
            <option value="0" <?= !settings()->analytics->sessions_replays_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
        </select>
        <small class="form-text text-muted"><?= language()->admin_settings->analytics->sessions_replays_is_enabled_help ?></small>
        <small class="form-text text-muted"><?= language()->admin_settings->analytics->sessions_replays_is_enabled_help2 ?></small>
        <small class="form-text text-muted"><?= sprintf(language()->admin_settings->analytics->sessions_replays_is_enabled_help3, ini_get('post_max_size')) ?></small>
    </div>

    <div class="form-group">
        <label for="sessions_replays_minimum_duration"><?= language()->admin_settings->analytics->sessions_replays_minimum_duration ?></label>
        <input id="sessions_replays_minimum_duration" type="number" min="1" name="sessions_replays_minimum_duration" class="form-control form-control-lg" value="<?= settings()->analytics->sessions_replays_minimum_duration ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->analytics->sessions_replays_minimum_duration_help ?></small>
        <small class="form-text text-muted"><?= language()->admin_settings->analytics->sessions_replays_minimum_duration_help2 ?></small>
    </div>

    <div class="form-group">
        <label for="websites_heatmaps_is_enabled"><i class="fa fa-fw fa-sm fa-fire text-muted mr-1"></i> <?= language()->admin_settings->analytics->websites_heatmaps_is_enabled ?></label>
        <select id="websites_heatmaps_is_enabled" name="websites_heatmaps_is_enabled" class="form-control form-control-lg">
            <option value="1" <?= settings()->analytics->websites_heatmaps_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->yes ?></option>
            <option value="0" <?= !settings()->analytics->websites_heatmaps_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->no ?></option>
        </select>
        <small class="form-text text-muted"><?= language()->admin_settings->analytics->websites_heatmaps_is_enabled_help ?></small>
    </div>

    <div class="form-group">
        <label for="pixel_cache"><?= language()->admin_settings->analytics->pixel_cache ?></label>
        <input id="pixel_cache" type="number" min="0" name="pixel_cache" class="form-control form-control-lg" value="<?= settings()->analytics->pixel_cache ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->analytics->pixel_cache_help ?></small>
    </div>

    <div class="form-group">
        <label for="pixel_exposed_identifier"><?= language()->admin_settings->analytics->pixel_exposed_identifier ?></label>
        <input id="pixel_exposed_identifier" type="text" name="pixel_exposed_identifier" class="form-control form-control-lg" value="<?= settings()->analytics->pixel_exposed_identifier ?>" />
        <small class="form-text text-muted"><?= language()->admin_settings->analytics->pixel_exposed_identifier_help ?></small>
    </div>

    <div class="form-group">
        <label for="email_reports_is_enabled"><i class="fa fa-fw fa-sm fa-fire text-muted mr-1"></i> <?= language()->admin_settings->analytics->email_reports_is_enabled ?></label>
        <select id="email_reports_is_enabled" name="email_reports_is_enabled" class="form-control form-control-lg">
            <option value="0" <?= !settings()->analytics->email_reports_is_enabled ? 'selected="selected"' : null ?>><?= language()->global->disabled ?></option>
            <option value="weekly" <?= settings()->analytics->email_reports_is_enabled == 'weekly' ? 'selected="selected"' : null ?>><?= language()->admin_settings->analytics->email_reports_is_enabled_weekly ?></option>
            <option value="monthly" <?= settings()->analytics->email_reports_is_enabled == 'monthly' ? 'selected="selected"' : null ?>><?= language()->admin_settings->analytics->email_reports_is_enabled_monthly ?></option>
        </select>
        <small class="form-text text-muted"><?= language()->admin_settings->analytics->email_reports_is_enabled_help ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->update ?></button>
