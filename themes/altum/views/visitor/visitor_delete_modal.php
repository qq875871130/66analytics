<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="visitor_delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-fw fa-sm fa-trash-alt text-gray-700"></i>
                    <?= language()->visitor_delete_modal->header ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="visitor_delete" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="visitor_id" value="" />

                    <div class="notification-container"></div>

                    <p class="text-muted"><?= language()->visitor_delete_modal->subheader ?></p>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-lg btn-block btn-danger"><?= language()->global->delete ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    /* On modal show load new data */
    $('#visitor_delete').on('show.bs.modal', event => {
        let visitor_id = $(event.relatedTarget).data('visitor-id');

        $(event.currentTarget).find('input[name="visitor_id"]').val(visitor_id);
    });

    $('form[name="visitor_delete"]').on('submit', event => {
        let visitor_id = $(event.currentTarget).find('input[name="visitor_id"]').val();

        $.ajax({
            type: 'POST',
            url: 'visitors-ajax/delete',
            data: $(event.currentTarget).serialize(),
            success: (data) => {
                let notification_container = event.currentTarget.querySelector('.notification-container');
                notification_container.innerHTML = '';

                if (data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    /* Clear input values */
                    $(event.currentTarget).find('input[name="visitor_id"]').val('');

                    display_notifications(data.message, 'success', notification_container);

                    setTimeout(() => {
                        /* Hide modal */
                        $('#visitor_delete').modal('hide');

                        /* Update html if needed or redirect */
                        if($(`[data-visitor-id="${visitor_id}"]`).length) {
                            $(`[data-visitor-id="${visitor_id}"]`).remove();
                        } else {
                            redirect('visitors');
                        }

                        /* Remove the notification */
                        notification_container.html('');

                    }, 1000);

                }
            },
            dataType: 'json'
        });

        event.preventDefault();
    })
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
