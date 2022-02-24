<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="goal_create_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><?= language()->goal_create_modal->header ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= language()->global->close ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-muted"><?= language()->goal_create_modal->subheader ?></p>

                <form name="goal_create" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" required="required" />

                    <div class="notification-container"></div>

                    <div class="form-group">
                        <select name="type" class="form-control form-control-lg">
                            <option value="pageview"><?= language()->goal_create_modal->input->type_pageview ?></option>
                            <option value="custom"><?= language()->goal_create_modal->input->type_custom ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fa fa-fw fa-sm fa-signature text-gray-700 mr-1"></i> <?= language()->goal_create_modal->input->name ?></label>
                        <input type="text" class="form-control form-control-lg" name="name" required="required" />
                    </div>

                    <div class="d-none" id="goal_create_type_pageview">

                        <div class="form-group">
                            <label><i class="fa fa-fw fa-sm fa-link text-gray-700 mr-1"></i> <?= language()->goal_create_modal->input->path ?></label>
                            <div class="input-group">
                                <div id="path_prepend" class="input-group-prepend">
                                    <span class="input-group-text"><?= $this->website->host . $this->website->path . '/' ?></span>
                                </div>

                                <input type="text" name="path" class="form-control form-control-lg" placeholder="<?= language()->goal_create_modal->input->path_placeholder ?>" />
                            </div>
                        </div>

                    </div>

                    <div class="d-none" id="goal_create_type_custom">

                        <div class="form-group">
                            <label><i class="fa fa-fw fa-sm fa-fingerprint text-gray-700 mr-1"></i> <?= language()->goal_create_modal->input->key ?></label>
                            <input type="text" class="form-control form-control-lg" name="key" value="<?= get_slug(string_generate(16)) ?>" placeholder="<?= language()->goal_create_modal->input->key_placeholder ?>" />
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-fw fa-sm fa-code text-gray-700 mr-1"></i> <?= language()->goal_create_modal->input->code ?></label>
                            <input type="text" class="form-control form-control-lg" name="code" value="" readonly="readonly" />
                            <small class="form-text text-muted"><?= language()->goal_create_modal->input->code_help ?></small>
                        </div>

                    </div>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary"><?= language()->global->create ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>

    /* Tab switcher */
    let goal_create_input_type_handler = () => {
        let type = document.querySelector('#goal_create_modal select[name="type"]').value;

        switch(type) {
            case 'pageview':

                document.querySelector('#goal_create_type_pageview').classList.remove('d-none');
                document.querySelector('#goal_create_type_custom').classList.add('d-none');

            break;

            case 'custom':

                document.querySelector('#goal_create_type_pageview').classList.add('d-none');
                document.querySelector('#goal_create_type_custom').classList.remove('d-none');

            break;
        }

    }

    document.querySelector('#goal_create_modal select[name="type"]').addEventListener('change', goal_create_input_type_handler);

    goal_create_input_type_handler();


    let goal_create_update_code = () => {

        let key = $('#goal_create_modal input[name="key"]').val();

        let code = `<?= settings()->analytics->pixel_exposed_identifier ?>.goal('${key}')`;

        $('#goal_create_modal input[name="code"]').val(code);

    };

    $('#goal_create_modal input[name="key"]').on('change paste keyup', goal_create_update_code);

    goal_create_update_code();


    $('form[name="goal_create"]').on('submit', event => {

        $.ajax({
            type: 'POST',
            url: 'goals-ajax/create',
            data: $(event.currentTarget).serialize(),
            success: (data) => {
                let notification_container = event.currentTarget.querySelector('.notification-container');
                notification_container.innerHTML = '';

                if(data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                }

                else if(data.status == 'success') {

                    display_notifications(data.message, 'success', notification_container);

                    setTimeout(() => {

                        /* Hide modal */
                        $('#goal_create').modal('hide');

                        /* Clear input values */
                        $('form[name="goal_create"] input').val('');

                        /* Refresh */
                        redirect('dashboard/goals');

                        /* Remove the notification */
                        notification_container.html('');

                    }, 1000);

                }
            },
            dataType: 'json'
        });

        event.preventDefault();

    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
