<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <div class="d-flex flex-column align-items-center">
        <div class="card border-0 col-xs-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card-body">
                <?= \Altum\Alerts::output_alerts() ?>

                <h1 class="h4 card-title"><?= language()->reset_password->header ?></h1>
                <p class="text-muted"><?= language()->reset_password->subheader ?></p>

                <form action="" method="post" class="mt-4" role="form">
                    <input type="hidden" name="email" value="<?= $data->values['email'] ?>" class="form-control" />

                    <div class="form-group">
                        <input type="password" name="new_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" placeholder="<?= language()->reset_password->new_password ?>" required="required" autofocus="autofocus" />
                        <?= \Altum\Alerts::output_field_error('new_password') ?>
                    </div>

                    <div class="form-group">
                        <input type="password" name="repeat_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('repeat_password') ? 'is-invalid' : null ?>" placeholder="<?= language()->reset_password->repeat_password ?>" required="required" />
                        <?= \Altum\Alerts::output_field_error('repeat_password') ?>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block my-1"><?= language()->reset_password->submit ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <a href="login" class="text-muted"><?= language()->reset_password->return ?></a>
        </div>
    </div>
</div>
