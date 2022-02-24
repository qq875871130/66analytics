<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="plugin_uninstall_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="modal-title">
                        <i class="fa fa-fw fa-sm fa-trash-alt text-primary-900 mr-2"></i>
                        <?= language()->admin_plugin_uninstall_modal->header ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <p class="text-muted"><?= language()->admin_plugin_uninstall_modal->subheader ?></p>

                <div class="mt-4">
                    <a href="" id="plugin_uninstall_modal_url" class="btn btn-lg btn-block btn-danger"><?= language()->admin_plugins->uninstall ?></a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';

    /* On modal show load new data */
    $('#plugin_uninstall_modal').on('show.bs.modal', event => {
        let plugin_id = $(event.relatedTarget).data('plugin-id');

        $(event.currentTarget).find('#plugin_uninstall_modal_url').attr('href', `${url}admin/plugins/uninstall/${plugin_id}&global_token=${global_token}`);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>