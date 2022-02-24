<?php defined('ALTUMCODE') || die() ?>

<div class="container">

    <div class="d-flex flex-column align-items-center">
        <div class="card border-0 col-xs-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card-body">
                <?= \Altum\Alerts::output_alerts() ?>

                <h4 class="card-title"><?= language()->register->header ?></h4>

                <form action="" method="post" class="mt-4" role="form">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->values['name'] ?>" maxlength="32" placeholder="<?= language()->register->form->name_placeholder ?>" aria-label="<?= language()->register->form->name ?>" required="required" autofocus="autofocus" />
                        <?= \Altum\Alerts::output_field_error('name') ?>
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->values['email'] ?>" maxlength="128" placeholder="<?= language()->register->form->email_placeholder ?>" aria-label="<?= language()->register->form->email ?>" required="required" <?= $data->unique_registration_identifier ? 'readonly="readonly"' : null ?> />
                        <?= \Altum\Alerts::output_field_error('email') ?>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" value="<?= $data->values['password'] ?>" placeholder="<?= language()->register->form->password_placeholder ?>" aria-label="<?= language()->register->form->password ?>" required="required" />
                        <?= \Altum\Alerts::output_field_error('password') ?>
                    </div>

                    <?php if(settings()->captcha->register_is_enabled): ?>
                        <div class="form-group">
                            <?php $data->captcha->display() ?>
                        </div>
                    <?php endif ?>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="accept" class="custom-control-input" id="accept" required="required">
                        <label class="custom-control-label" for="accept">
                            <small class="text-muted">
                                <?= sprintf(
                                    language()->register->form->accept,
                                    '<a href="' . settings()->main->terms_and_conditions_url . '" target="_blank">' . language()->global->terms_and_conditions . '</a>',
                                    '<a href="' . settings()->main->privacy_policy_url . '" target="_blank">' . language()->global->privacy_policy . '</a>'
                                ) ?>
                            </small>
                        </label>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block"><?= language()->register->form->register ?></button>
                    </div>

                    <div class="mt-4">
                        <?php if(settings()->facebook->is_enabled): ?>
                            <div class="mt-2">
                                <a href="<?= url('login/facebook-initiate') ?>" class="btn btn-light btn-block"><?= sprintf(language()->login->display->facebook, "<i class=\"fab fa-fw fa-facebook\"></i>") ?></a>
                            </div>
                        <?php endif ?>
                        <?php if(settings()->google->is_enabled): ?>
                            <div class="mt-2">
                                <a href="<?= url('login/google-initiate') ?>" class="btn btn-light btn-block"><?= sprintf(language()->login->display->google, "<i class=\"fab fa-fw fa-google\"></i>") ?></a>
                            </div>
                        <?php endif ?>
                        <?php if(settings()->twitter->is_enabled): ?>
                            <div class="mt-2">
                                <a href="<?= url('login/twitter-initiate') ?>" class="btn btn-light btn-block"><?= sprintf(language()->login->display->twitter, "<i class=\"fab fa-fw fa-twitter\"></i>") ?></a>
                            </div>
                        <?php endif ?>
                    </div>

                </form>
            </div>
        </div>

        <div class="mt-4">
            <a href="login" class="text-muted"><?= language()->register->login ?></a>
        </div>
    </div>
</div>


