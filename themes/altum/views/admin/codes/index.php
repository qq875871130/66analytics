<?php defined('ALTUMCODE') || die() ?>

<?php if(count($data->codes) || count($data->filters->get)): ?>

    <div class="d-flex justify-content-between mb-4">
        <h1 class="h3"><i class="fa fa-fw fa-xs fa-tags text-primary-900 mr-2"></i> <?= language()->admin_codes->header ?></h1>

        <div class="col-auto d-flex">
            <div class="">
                <a href="<?= url('admin/code-create') ?>" class="btn btn-outline-primary"><i class="fa fa-fw fa-plus-circle"></i> <?= language()->admin_codes->create ?></a>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= language()->global->export ?>">
                        <i class="fa fa-fw fa-sm fa-download"></i>
                    </button>
                </div>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" title="<?= language()->global->filters->header ?>">
                        <i class="fa fa-fw fa-sm fa-filter"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= language()->global->filters->header ?></span>

                            <?php if(count($data->filters->get)): ?>
                                <a href="<?= url('admin/codes') ?>" class="text-muted"><?= language()->global->filters->reset ?></a>
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
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->name ?></option>
                                    <option value="code" <?= $data->filters->search_by == 'code' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->code ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_type" class="small"><?= language()->admin_codes->main->type ?></label>
                                <select name="type" id="filters_type" class="form-control form-control-sm">
                                    <option value=""><?= language()->global->filters->all ?></option>
                                    <option value="redeemable" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'redeemable' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->type_redeemable ?></option>
                                    <option value="discount" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == 'discount' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->type_discount ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= language()->global->filters->order_by ?></label>
                                <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= language()->global->filters->order_by_datetime ?></option>
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->name ?></option>
                                    <option value="code" <?= $data->filters->search_by == 'code' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->code ?></option>
                                    <option value="days" <?= $data->filters->search_by == 'days' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->days ?></option>
                                    <option value="redeemed" <?= $data->filters->search_by == 'redeemed' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->redeemed ?></option>
                                    <option value="discount" <?= $data->filters->search_by == 'discount' ? 'selected="selected"' : null ?>><?= language()->admin_codes->main->discount ?></option>
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
    </div>

    <?= \Altum\Alerts::output_alerts() ?>

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
                <th><?= language()->admin_codes->main->code ?></th>
                <th><?= language()->admin_codes->main->type ?></th>
                <th><?= language()->admin_codes->main->discount ?></th>
                <th><?= language()->admin_codes->main->quantity ?></th>
                <th><?= language()->admin_codes->main->redeemed ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data->codes as $row): ?>
                <tr>
                    <td data-bulk-table class="d-none">
                        <div class="custom-control custom-checkbox">
                            <input id="selected_id_<?= $row->code_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->code_id ?>" />
                            <label class="custom-control-label" for="selected_id_<?= $row->code_id ?>"></label>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <a href="<?= url('admin/code-update/' . $row->code_id) ?>"><?= $row->name ?></a>
                            <span><code><?= $row->code ?></code></span>
                        </div>
                    </td>
                    <td><?= language()->admin_codes->main->{'type_' . $row->type} ?></td>
                    <td><?= $row->discount . '%' ?></td>
                    <td><?= nr($row->quantity) ?></td>
                    <td><?= nr($row->redeemed) ?></td>
                    <td><?= include_view(THEME_PATH . 'views/admin/codes/admin_code_dropdown_button.php', ['id' => $row->code_id]) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3"><?= $data->pagination ?></div>

<?php else: ?>

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="d-flex flex-column flex-md-row align-items-md-center">
        <div class="mb-3 mb-md-0 mr-md-5">
            <i class="fa fa-fw fa-7x fa-tags text-primary-200"></i>
        </div>

        <div class="d-flex flex-column">
            <h1 class="h3"><?= language()->admin_codes->header_no_data ?></h1>
            <p class="text-muted"><?= language()->admin_codes->subheader_no_data ?></p>

            <div>
                <a href="<?= url('admin/code-create') ?>" class="btn btn-primary"><i class="fa fa-fw fa-sm fa-plus-circle"></i> <?= language()->admin_codes->create ?></a>
            </div>
        </div>
    </div>

<?php endif ?>

<?php require THEME_PATH . 'views/admin/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/partials/bulk_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/codes/code_delete_modal.php'), 'modals'); ?>

