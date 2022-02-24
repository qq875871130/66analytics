<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar app-navbar navbar-expand-lg navbar-light bg-white">
    <div class="container-fluid">

        <?php if(count($this->websites)): ?>
        <div class="dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                <img src="https://external-content.duckduckgo.com/ip3/<?= $this->website->host ?>.ico" class="img-fluid icon-favicon mr-1" /> <span class="text-gray-700"><?= $this->website->host . ($this->website->path ?? null) ?></span>
            </a>

            <div class="dropdown-menu">
                <?php foreach($this->websites as $row): ?>
                <a href="<?= url('dashboard?website_id=' . $row->website_id . '&redirect=' . \Altum\Routing\Router::$controller_key) ?>" class="dropdown-item" href="<?= url('account') ?>">
                    <div>
                        <img src="https://external-content.duckduckgo.com/ip3/<?= $row->host ?>.ico" class="img-fluid icon-favicon mr-1" />
                        <?= $row->host . ($row->path ?? null) ?>
                    </div>
                    <div>
                        <?php if($row->is_enabled): ?>
                        <small data-toggle="tooltip" title="<?= language()->global->active ?>"><i class="fa fa-fw fa-check text-success"></i></small>
                        <?php else: ?>
                        <small data-toggle="tooltip" title="<?= language()->global->disabled ?>"><i class="fa fa-fw fa-slash text-warning"></i></small>
                        <?php endif ?>

                        <small class="text-muted"><?= language()->websites->websites->{'tracking_type_' . $row->tracking_type} ?></small>
                    </div>
                </a>
                <?php endforeach ?>
            </div>
        </div>

            <?php if($this->team): ?>
                <div class="d-flex align-items-baseline">
                    <span class="text-muted"><?= sprintf(language()->global->team->is_enabled, '<strong>' . $this->team->name . '</strong>') ?></span>
                    <small class="text-muted">&nbsp;(<a href="#" id="team_logout"><?= language()->global->team->logout ?></a>)</small>
                </div>

                <?php ob_start() ?>
                <script>
                    $('#team_logout').on('click', event => {
                        delete_cookie('selected_team_id', <?= json_encode(COOKIE_PATH) ?>);
                        redirect('dashboard');

                        event.preventDefault();
                    });
                </script>
                <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
            <?php endif ?>
        <?php endif ?>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_navbar" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= language()->global->accessibility->toggle_navigation ?>">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_navbar">
            <ul class="navbar-nav align-items-lg-center">

                <?php foreach($data->pages as $data): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $data->url ?>" target="<?= $data->target ?>"><?= $data->title ?></a></li>
                <?php endforeach ?>

                <li class="ml-lg-3 dropdown">
                    <a class="nav-link dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <img src="<?= get_gravatar($this->user->email) ?>" class="app-navbar-avatar mr-3" loading="lazy" />

                            <div class="d-flex flex-column mr-3">
                                <span class="text-gray-700"><?= $this->user->name ?></span>
                                <small class="text-muted"><?= $this->user->email ?></small>
                            </div>

                            <i class="fa fa-fw fa-caret-down"></i>
                        </div>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <?php if(\Altum\Middlewares\Authentication::is_admin()): ?>
                            <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fa fa-fw fa-sm fa-user-shield mr-1"></i> <?= language()->global->menu->admin ?></a>
                        <?php endif ?>
                        <a class="dropdown-item" href="<?= url('teams') ?>"><i class="fa fa-fw fa-sm fa-user-shield mr-1"></i> <?= language()->teams->menu ?></a>
                        <a class="dropdown-item" href="<?= url('account') ?>"><i class="fa fa-fw fa-sm fa-wrench mr-1"></i> <?= language()->account->menu ?></a>
                        <a class="dropdown-item" href="<?= url('account-plan') ?>"><i class="fa fa-fw fa-sm fa-box-open mr-1"></i> <?= language()->account_plan->menu ?></a>
                        <?php if(settings()->payment->is_enabled): ?>
                            <a class="dropdown-item" href="<?= url('account-payments') ?>"><i class="fa fa-fw fa-sm fa-dollar-sign mr-1"></i> <?= language()->account_payments->menu ?></a>

                            <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                                <a class="dropdown-item" href="<?= url('referrals') ?>"><i class="fa fa-fw fa-sm fa-wallet mr-1"></i> <?= language()->referrals->menu ?></a>
                            <?php endif ?>
                        <?php endif ?>
                        <a class="dropdown-item" href="<?= url('account-api') ?>"><i class="fa fa-fw fa-sm fa-code mr-1"></i> <?= language()->account_api->menu ?></a>
                        <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa fa-fw fa-sm fa-sign-out-alt mr-1"></i> <?= language()->global->menu->logout ?></a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
