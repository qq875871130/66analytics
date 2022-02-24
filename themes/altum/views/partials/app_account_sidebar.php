<?php defined('ALTUMCODE') || die() ?>

<div class="col-12 col-xl-3 mb-3 mb-xl-0">
    <div class="container">
        <ul class="nav nav-pills flex-column flex-md-row flex-xl-column">
            <li class="nav-item">
                <a href="<?= url('account') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account' ? 'active' : null ?>">
                    <i class="fa fa-fw fa-sm fa-wrench mr-1"></i> <?= language()->account->menu ?>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= url('account-plan') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-plan' ? 'active' : null ?>">
                    <i class="fa fa-fw fa-sm fa-box-open mr-1"></i> <?= language()->account_plan->menu ?>
                </a>
            </li>

            <?php if(settings()->payment->is_enabled): ?>
            <li class="nav-item">
                <a href="<?= url('account-payments') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-payments' ? 'active' : null ?>">
                    <i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= language()->account_payments->menu ?>
                </a>
            </li>

                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= \Altum\Routing\Router::$controller_key == 'referrals' ? 'active' : null ?>" href="<?= url('referrals') ?>">
                            <i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= language()->referrals->menu ?>
                        </a>
                    </li>
                <?php endif ?>
            <?php endif ?>

            <li class="nav-item">
                <a href="<?= url('account-logs') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-logs' ? 'active' : null ?>">
                    <i class="fa fa-fw fa-sm fa-scroll mr-1"></i> <?= language()->account_logs->menu ?>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= url('account-api') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-api' ? 'active' : null ?>">
                    <i class="fa fa-fw fa-sm fa-code mr-1"></i> <?= language()->account_api->menu ?>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= url('account-delete') ?>" class="nav-link <?= \Altum\Routing\Router::$controller_key == 'account-delete' ? 'active' : null ?>">
                    <i class="fa fa-fw fa-sm fa-times mr-1"></i> <?= language()->account_delete->menu ?>
                </a>
            </li>
        </ul>
    </div>
</div>
