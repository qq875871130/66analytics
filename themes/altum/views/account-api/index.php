<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <?= $this->views['app_account_sidebar'] ?>

    <div class="col">
        <header class="">
            <div class="container">
                <?= \Altum\Alerts::output_alerts() ?>

                <h1 class="h3"><?= language()->account_api->header ?></h1>
                <p class="text-muted"><?= sprintf(language()->account_api->subheader, '<a href="' . url('api-documentation') . '">', '</a>') ?></p>
            </div>
        </header>

        <section class="container">
            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                <div <?= $this->user->plan_settings->api_is_enabled ? null : 'data-toggle="tooltip" title="' . language()->global->info_message->plan_feature_no_access . '"' ?>>
                    <div class="form-group <?= $this->user->plan_settings->api_is_enabled ? null : 'container-disabled' ?>">
                        <label for="api_key"><?= language()->account_api->api_key ?></label>
                        <input type="text" id="api_key" name="api_key" value="<?= $this->user->api_key ?>" class="form-control" readonly="readonly" />
                    </div>
                </div>


                <button type="submit" name="submit" class="btn btn-block btn-outline-secondary"><?= language()->account_api->button ?></button>
            </form>
        </section>
    </div>
</div>
