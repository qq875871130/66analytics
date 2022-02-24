<?php defined('ALTUMCODE') || die() ?>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row">
        <div class="col-12 col-lg-3 mb-5 mb-lg-0">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="<?= url('help') ?>" class="nav-link <?= $data->page == 'introduction' ? 'active' : null ?>">
                        <i class="fa fa-fw fa-sm fa-file mr-1"></i> <?= language()->help->introduction->menu ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('help/setup') ?>" class="nav-link <?= $data->page == 'setup' ? 'active' : null ?>">
                        <i class="fa fa-fw fa-sm fa-plus-circle mr-1"></i> <?= language()->help->setup->menu ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('help/install') ?>" class="nav-link <?= $data->page == 'install' ? 'active' : null ?>">
                        <i class="fa fa-fw fa-sm fa-code mr-1"></i> <?= language()->help->install->menu ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('help/lightweight-tracking') ?>" class="nav-link <?= $data->page == 'lightweight_tracking' ? 'active' : null ?>">
                        <i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i> <?= language()->help->lightweight_tracking->menu ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= url('help/advanced-tracking') ?>" class="nav-link <?= $data->page == 'advanced_tracking' ? 'active' : null ?>">
                        <i class="fa fa-fw fa-sm fa-eye mr-1"></i> <?= language()->help->advanced_tracking->menu ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="col col-lg-9">
            <?= $this->views['page'] ?>
        </div>
    </div>
</div>
