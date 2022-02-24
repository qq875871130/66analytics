<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <?= $this->views['app_account_sidebar'] ?>

    <div class="col">

        <header class="header">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between">
                    <div>
                        <h1 class="h3"><?= language()->account_plan->header ?></h1>
                    </div>

                    <?php if(settings()->payment->is_enabled): ?>
                        <div class="col-auto p-0">
                            <?php if($this->user->plan_id == 'free'): ?>
                                <a href="<?= url('plan/upgrade') ?>" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-arrow-up"></i> <?= language()->account->plan->upgrade_plan ?></a>
                            <?php else: ?>
                                <a href="<?= url('plan/renew') ?>" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-sync-alt"></i> <?= language()->account->plan->renew_plan ?></a>
                            <?php endif ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </header>

        <section class="container">

            <?= \Altum\Alerts::output_alerts() ?>

            <div class="row">
                <div class="col-12 col-xl-4">
                    <h2 class="h4"><?= $this->user->plan->name ?></h2>

                    <?php if($this->user->plan_id != 'free' && (new \DateTime($this->user->plan_expiration_date)) < (new \DateTime())->modify('+5 years')): ?>
                        <p class="text-muted">
                            <?=
                            $this->user->payment_subscription_id ?
                                sprintf(language()->account_plan->plan->renews, '<strong>' . \Altum\Date::get($this->user->plan_expiration_date, 2) . '</strong>', language()->pay->custom_plan->{$this->user->payment_processor}, nr($this->user->payment_total_amount), $this->user->payment_currency)
                                : sprintf(language()->account_plan->plan->expires, '<strong>' . \Altum\Date::get($this->user->plan_expiration_date, 2) . '</strong>')
                            ?>
                        </p>
                    <?php endif ?>
                </div>

                <div class="col">
                    <?= (new \Altum\Views\View('partials/plan_features'))->run(['plan_settings' => $this->user->plan_settings]) ?>
                </div>
            </div>

            <?php if($this->user->plan_id != 'free' && $this->user->payment_subscription_id): ?>
            <div class="mt-8 d-flex justify-content-between">
                <div>
                    <h2 class="h4"><?= language()->account_plan->cancel->header ?></h2>
                    <p class="text-muted"><?= language()->account_plan->cancel->subheader ?></p>
                </div>

                <div class="col-auto">
                    <a href="<?= url('account-plan/cancel_subscription' . \Altum\Middlewares\Csrf::get_url_query()) ?>" class="btn btn-secondary" onclick='return confirm(<?= json_encode(language()->account_plan->cancel->confirm_message) ?>)'><?= language()->account_plan->cancel->cancel ?></a>
                </div>
            </div>
            <?php endif ?>

        </section>

    </div>
</div>

