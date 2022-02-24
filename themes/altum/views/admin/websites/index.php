<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-users text-primary-900 mr-2"></i> <?= language()->admin_websites->header ?></h1>
</div>

<div class="row justify-content-between mb-4">
    <div class="col-12 col-sm-6 col-md-4 mb-4">
        <div class="card d-flex flex-row h-100">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-eye mr-1"></i> <?= language()->admin_websites->display->total_sessions_events ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->total_sessions_events) ?></span></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 mb-4">
        <div class="card d-flex flex-row h-100">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-bell mr-1"></i> <?= language()->admin_websites->display->total_events_children ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->total_events_children) ?></span></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 mb-4">
        <div class="card d-flex flex-row h-100">
            <div class="card-body">
                <small class="text-muted"><i class="fa fa-fw fa-sm fa-video mr-1"></i> <?= language()->admin_websites->display->total_sessions_replays ?></small>

                <div class="mt-3"><span class="h4"><?= nr($data->total_sessions_replays) ?></span></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mb-3">
    <div class="dropdown">
        <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= language()->global->filters->header ?>">
                    <i class="fa fa-fw fa-sm fa-filter"></i>
                </button>

        <div class="dropdown-menu dropdown-menu-right filters-dropdown">
            <div class="dropdown-header d-flex justify-content-between">
                <span class="h6 m-0"><?= language()->global->filters->header ?></span>

                <?php if(count($data->filters->get)): ?>
                    <a href="<?= url('admin/websites') ?>" class="text-muted"><?= language()->global->filters->reset ?></a>
                <?php endif ?>
            </div>

            <div class="dropdown-divider"></div>

            <form action="" method="get" role="form">
                <div class="form-group px-4">
                    <label for="filters_search" class="small"><?= language()->global->filters->search ?></label>
                    <input type="search" name="search" id="filters_search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                </div>

                <div class="form-group px-4">
                    <label for="filters_search_by" class="small"><?= language()->global->filters->search_by ?></label>
                    <select name="search_by" id="filters_search_by" class="form-control form-control-sm">
                        <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= language()->admin_websites->filters->search_by_name ?></option>
                        <option value="host" <?= $data->filters->search_by == 'host' ? 'selected="selected"' : null ?>><?= language()->admin_websites->filters->search_by_host ?></option>
                    </select>
                </div>

                <div class="form-group px-4">
                    <label for="filters_is_enabled" class="small"><?= language()->global->filters->status ?></label>
                    <select name="is_enabled" id="filters_is_enabled" class="form-control form-control-sm">
                        <option value=""><?= language()->global->filters->all ?></option>
                        <option value="1" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '1' ? 'selected="selected"' : null ?>><?= language()->global->active ?></option>
                        <option value="0" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '0' ? 'selected="selected"' : null ?>><?= language()->global->disabled ?></option>
                    </select>
                </div>

                <div class="form-group px-4">
                    <label for="filters_tracking_type" class="small"><?= language()->admin_websites->filters->tracking_type ?></label>
                    <select name="tracking_type" id="filters_tracking_type" class="form-control form-control-sm">
                        <option value=""><?= language()->global->filters->all ?></option>
                        <option value="normal" <?= isset($data->filters->filters['tracking_type']) && $data->filters->filters['tracking_type'] == 'normal' ? 'selected="selected"' : null ?>><?= language()->admin_websites->filters->tracking_type_normal ?></option>
                        <option value="lightweight" <?= isset($data->filters->filters['tracking_type']) && $data->filters->filters['tracking_type'] == 'lightweight' ? 'selected="selected"' : null ?>><?= language()->admin_websites->filters->tracking_type_lightweight ?></option>
                    </select>
                </div>

                <div class="form-group px-4">
                    <label for="filters_order_by" class="small"><?= language()->global->filters->order_by ?></label>
                    <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                        <option value="date" <?= $data->filters->order_by == 'date' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_by_datetime ?></option>
                        <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= language()->admin_websites->filters->order_by_name ?></option>
                        <option value="host" <?= $data->filters->order_by == 'host' ? 'selected="selected"' : null ?>><?= language()->admin_websites->filters->order_by_host ?></option>
                    </select>
                </div>

                <div class="form-group px-4">
                    <label for="filters_order_type" class="small"><?= language()->global->filters->order_type ?></label>
                    <select name="order_type" id="filters_order_type" class="form-control form-control-sm">
                        <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_type_asc ?></option>
                        <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_type_desc ?></option>
                    </select>
                </div>

                <div class="form-group px-4">
                    <label for="filters_results_per_page" class="small"><?= language()->global->filters->results_per_page ?></label>
                    <select name="results_per_page" id="filters_results_per_page" class="form-control form-control-sm">
                        <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                            <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group px-4 mt-4">
                    <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= language()->global->submit ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="ml-3">
        <button id="bulk_enable" type="button" class="btn btn-outline-secondary" data-toggle="tooltip" title="<?= language()->global->bulk_actions ?>"><i class="fa fa-fw fa-sm fa-list"></i></button>

        <div id="bulk_group" class="btn-group d-none" role="group">
            <div class="btn-group" role="group">
                <button id="bulk_actions" type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                    <?= language()->global->bulk_actions ?> <span id="bulk_counter" class="d-none"></span>
                </button>
                <div class="dropdown-menu" aria-labelledby="bulk_actions">
                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulk_delete_modal"><?= language()->global->delete ?></a>
                </div>
            </div>

            <button id="bulk_disable" type="button" class="btn btn-outline-secondary" data-toggle="tooltip" title="<?= language()->global->close ?>"><i class="fa fa-fw fa-times"></i></button>
        </div>
    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<form id="table" action="<?= SITE_URL . 'admin/websites/bulk' ?>" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />
    <input type="hidden" name="type" value="" data-bulk-type />

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th data-bulk-table class="d-none">
                    <div class="custom-control custom-checkbox">
                        <input id="bulk_select_all" type="checkbox" class="custom-control-input" />
                        <label class="custom-control-label" for="bulk_select_all"></label>
                    </div>
                </th>
                <th><?= language()->admin_websites->table->user ?></th>
                <th><?= language()->admin_websites->table->website ?></th>
                <th><?= language()->admin_websites->table->usage ?></th>
                <th><?= language()->admin_websites->table->is_enabled ?></th>
                <th><?= language()->admin_websites->table->date ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data->websites as $row): ?>
                <?php //ALTUMCODE:DEMO if(DEMO) {$row->user_email = 'hidden@demo.com'; $row->user_name = $row->name = $row->host = 'hidden on demo';} ?>
                <tr>
                    <td data-bulk-table class="d-none">
                        <div class="custom-control custom-checkbox">
                            <input id="selected_website_id_<?= $row->website_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->website_id ?>" />
                            <label class="custom-control-label" for="selected_website_id_<?= $row->website_id ?>"></label>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <div>
                                <a href="<?= url('admin/user-view/' . $row->user_id) ?>"><?= $row->user_name ?></a>
                            </div>

                            <span class="text-muted"><?= $row->user_email ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span><?= $row->name ?></span>
                            <div class="d-flex align-items-center text-muted">
                                <img src="https://external-content.duckduckgo.com/ip3/<?= $row->host ?>.ico" class="img-fluid icon-favicon mr-1" />

                                <?= $row->host . $row->path ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <small>
                            <div class="text-muted">
                                <?= language()->websites->websites->sessions_events ?>
                                <strong><?= nr($row->current_month_sessions_events, 1, true) ?></strong>
                            </div>

                            <?php if($row->tracking_type == 'normal'): ?>
                                <div class="text-muted">
                                    <?= language()->websites->websites->events_children ?>
                                    <strong><?= nr($row->current_month_events_children, 1, true) ?></strong>
                                </div>

                                <div class="text-muted">
                                    <?= language()->websites->websites->sessions_replays ?>
                                    <strong><?= nr($row->current_month_sessions_replays, 1, true) ?></strong>
                                </div>
                            <?php endif ?>
                        </small>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <div>
                                <?php if($row->is_enabled == 1): ?>
                                    <span class="badge badge-pill badge-success"><i class="fa fa-fw fa-check"></i> <?= language()->global->active ?></span>
                                <?php elseif($row->is_enabled == 0): ?>
                                    <span class="badge badge-pill badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= language()->global->disabled ?></span>
                                <?php endif ?>
                            </div>

                            <div class="text-muted">
                                <?php if($row->tracking_type == 'normal'): ?>
                                    <?= language()->websites->websites->tracking_type_normal ?>
                                <?php endif ?>
                                <?php if($row->tracking_type == 'lightweight'): ?>
                                    <?= language()->websites->websites->tracking_type_lightweight ?>
                                <?php endif ?>
                            </div>
                        </div>
                    </td>
                    <td>
                    <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->date) ?>">
                        <?= \Altum\Date::get($row->date, 2) ?>
                    </span>
                    </td>
                    <td><?= include_view(THEME_PATH . 'views/admin/websites/admin_website_dropdown_button.php', ['id' => $row->website_id]) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</form>

<div class="mt-3"><?= $data->pagination ?></div>

<?php require THEME_PATH . 'views/admin/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/websites/website_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/partials/bulk_delete_modal.php'), 'modals'); ?>
