<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <?= $this->views['app_account_sidebar'] ?>

    <div class="col">
        <header class="">
            <div class="container">
                <?= \Altum\Alerts::output_alerts() ?>

                <h1 class="h3"><?= language()->account_delete->header ?></h1>
                <p class="text-muted"><?= language()->account_delete->subheader ?></p>
            </div>
        </header>

        <section class="container">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="current_password"><?= language()->account_delete->current_password ?></label>
                    <input type="password" id="current_password" name="current_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('current_password') ? 'is-invalid' : null ?>" />
                    <?= \Altum\Alerts::output_field_error('current_password') ?>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-secondary"><?= language()->global->delete ?></button>
            </form>

        </section>

    </div>
</div>

