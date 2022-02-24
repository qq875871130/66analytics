<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <div class="d-flex flex-column align-items-center">
        <div class="card border-0 col-xs-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card-body">
                <?= \Altum\Alerts::output_alerts() ?>

                <h1 class="h4 card-title d-flex justify-content-between"><?= language()->lost_password->header ?></h1>
                <p class="text-muted"><?= language()->lost_password->subheader ?></p>

                <form action="" method="post" class="mt-4" role="form">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->values['email'] ?>" placeholder="<?= language()->lost_password->email ?>" required="required" autofocus="autofocus" />
                        <?= \Altum\Alerts::output_field_error('email') ?>
                    </div>

                    <?php if(settings()->captcha->lost_password_is_enabled): ?>
                        <div class="form-group">
                            <?php $data->captcha->display() ?>
                        </div>
                    <?php endif ?>

                    <div class="form-group mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block my-1"><?= language()->lost_password->submit ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <a href="login" class="text-muted"><?= language()->lost_password->return ?></a>
        </div>
    </div>
</div>

