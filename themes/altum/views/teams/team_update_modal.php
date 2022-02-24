<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="team_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->team_update_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="team_update" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="request_type" value="update" />
                    <input type="hidden" name="team_id" value="" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <label><i class="fa fa-sm fa-fw fa-signature text-muted mr-1"></i> <?= language()->team_update_modal->input->name ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" required="required" />
                    </div>

                    <div class="form-group">
                        <label><?= language()->team_update_modal->input->websites_ids ?></label>
                        <select multiple="multiple" name="websites_ids[]" class="form-control form-control-lg">
                            <?php foreach($this->websites as $key => $value) echo '<option value="' . $key . '">' . $value->name . ' - ' . $value->host . $value->path . '</option>' ?>
                        </select>
                        <small class="form-text text-muted"><?= language()->team_update_modal->input->websites_ids_help ?></small>
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary"><?= language()->global->submit ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#team_update').on('show.bs.modal', event => {
        let team_id = $(event.relatedTarget).data('team-id');
        let name = $(event.relatedTarget).data('name');
        let websites_ids = $(event.relatedTarget).data('websites-ids');

        $(event.currentTarget).find('input[name="team_id"]').val(team_id);
        $(event.currentTarget).find('input[name="name"]').val(name);
        $(event.currentTarget).find(`select[name="websites_ids\[\]"] option`).removeAttr('selected');
        for(let website_id of websites_ids) {
            $(event.currentTarget).find(`select[name="websites_ids\[\]"] option[value="${website_id}"]`).attr('selected', 'selected');
        }
    });

    $('form[name="team_update"]').on('submit', event => {

        $.ajax({
            type: 'POST',
            url: 'teams-ajax',
            data: $(event.currentTarget).serialize(),
            success: (data) => {
                let notification_container = event.currentTarget.querySelector('.notification-container');
                notification_container.innerHTML = '';

                if (data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Hide modal */
                    $('#team_update').modal('hide');

                    /* Clear input values */
                    $('form[name="team_update"] input').val('');

                    /* Refresh */
                    redirect(`teams`);

                }
            },
            dataType: 'json'
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
