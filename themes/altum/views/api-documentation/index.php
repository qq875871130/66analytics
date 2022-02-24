<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= language()->index->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= language()->api_documentation->breadcrumb ?></li>
        </ol>
    </nav>

    <div class="row mb-5">
        <div class="col-12 col-lg-7 mb-4 mb-lg-0">
            <h1 class="h4"><?= language()->api_documentation->header ?></h1>
            <p class="text-muted"><?= language()->api_documentation->subheader ?></p>
        </div>

        <div class="col-12 col-lg-4 offset-lg-1">
            <div class="mb-3">
                <a href="<?= url('account-api') ?>" target="_blank" class="btn btn-block btn-outline-primary"><?= language()->api_documentation->api_key ?></a>
            </div>

            <div class="form-group">
                <label for="base_url"><?= language()->api_documentation->base_url ?></label>
                <input type="text" id="base_url" value="<?= SITE_URL . 'api' ?>" class="form-control" readonly="readonly" />
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="mb-4">
            <h2 class="h5"><?= language()->api_documentation->authentication->header ?></h2>
            <p class="text-muted"><?= language()->api_documentation->authentication->subheader ?></p>
        </div>

        <div class="form-group">
            <label><?= language()->api_documentation->example ?></label>
            <div class="card bg-gray-50 border-0">
                <div class="card-body">
                    curl --request GET \<br />
                    --url '<?= SITE_URL . 'api/' ?><span class="text-primary">{endpoint}</span>' \<br />
                    --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/user') ?>" class="stretched-link">
                        <i class="fa fa-fw fa-user text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body">
                    <?= language()->api_documentation->user->header ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/websites') ?>" class="stretched-link">
                        <i class="fa fa-fw fa-server text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body">
                    <?= language()->api_documentation->websites->header ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/statistics') ?>" class="stretched-link">
                        <i class="fa fa-fw fa-chart-bar text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body">
                    <?= language()->api_documentation->statistics->header ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/payments') ?>" class="stretched-link">
                        <i class="fa fa-fw fa-dollar-sign text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body">
                    <?= language()->api_documentation->payments->header ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4 mb-4 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/users_logs') ?>" class="stretched-link">
                        <i class="fa fa-fw fa-scroll text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body">
                    <?= language()->api_documentation->users_logs->header ?>
                </div>
            </div>
        </div>
    </div>
</div>
