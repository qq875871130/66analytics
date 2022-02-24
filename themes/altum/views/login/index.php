<?php defined('ALTUMCODE') || die() ?>

<div class="container">

    <div class="d-flex flex-column align-items-center">
        <div class="card border-0 col-xs-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card-body">
                <?= \Altum\Alerts::output_alerts() ?>

                <form action="" method="post" class="mt-4" role="form">
                    <div class="form-group">
                        <input type="text" name="email" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" placeholder="<?= language()->login->form->email_placeholder ?>" value="<?= $data->values['email'] ?>" aria-label="<?= language()->login->form->email ?>" required="required" autofocus="autofocus" />
                        <?= \Altum\Alerts::output_field_error('email') ?>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" placeholder="<?= language()->login->form->password_placeholder ?>" aria-label="<?= language()->login->form->password ?>" value="<?= $data->user ? $data->values['password'] : null ?>" required="required" />
                        <?= \Altum\Alerts::output_field_error('password') ?>
                    </div>

                    <?php if($data->user && $data->user->twofa_secret && $data->user->status == 1): ?>
                        <div class="form-group">
                            <input id="twofa_token" type="text" name="twofa_token" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('twofa_token') ? 'is-invalid' : null ?>" placeholder="<?= language()->login->form->twofa_token_placeholder ?>" required="required" autocomplete="off" />
                            <?= \Altum\Alerts::output_field_error('twofa_token') ?>
                        </div>
                    <?php endif ?>

                    <?php if(settings()->captcha->login_is_enabled): ?>
                    <div class="form-group">
                        <?php $data->captcha->display() ?>
                    </div>
                    <?php endif ?>

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="rememberme" class="custom-control-input" id="rememberme">
                            <label class="custom-control-label" for="rememberme"><small class="text-muted"><?= language()->login->form->remember_me ?></small></label>
                        </div>

                        <small><a href="lost-password" class="text-muted"><?= language()->login->display->lost_password ?></a> / <a href="resend-activation" class="text-muted" role="button"><?= language()->login->display->resend_activation ?></a></small>
                    </div>


                    <div class="form-group mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block my-1"><?= language()->login->form->login ?></button>
                    </div>

                    <div class="mt-4">
                        <?php if(settings()->facebook->is_enabled): ?>
                            <div class="mt-2">
                                <a href="<?= url('login/facebook-initiate') ?>" class="btn btn-light btn-block">
                                    <img src="<?= ASSETS_FULL_URL . 'images/facebook.svg' ?>" class="mr-1" />
                                    <?= language()->login->display->facebook ?>
                                </a>
                            </div>
                        <?php endif ?>
                        <?php if(settings()->google->is_enabled): ?>
                            <div class="mt-2">
                                <a href="<?= url('login/google-initiate') ?>" class="btn btn-light btn-block">
                                    <img src="<?= ASSETS_FULL_URL . 'images/google.svg' ?>" class="mr-1" />
                                    <?= language()->login->display->google ?>
                                </a>
                            </div>
                        <?php endif ?>
                        <?php if(settings()->twitter->is_enabled): ?>
                            <div class="mt-2">
                                <a href="<?= url('login/twitter-initiate') ?>" class="btn btn-light btn-block">
                                    <img src="<?= ASSETS_FULL_URL . 'images/twitter.svg' ?>" class="mr-1" />
                                    <?= language()->login->display->twitter ?>
                                </a>
                            </div>
                        <?php endif ?>
                    </div>

                </form>
            </div>
        </div>

        <?php if(settings()->users->register_is_enabled): ?>
            <div class="mt-4">
                <?= sprintf(language()->login->display->register, '<a href="' . url('register') . '" class="font-weight-bold">' . language()->login->display->register_help . '</a>') ?></a>
            </div>
        <?php endif ?>
    </div>
</div>
