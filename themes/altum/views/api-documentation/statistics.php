<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= language()->index->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li><a href="<?= url('api-documentation') ?>"><?= language()->api_documentation->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= language()->api_documentation->statistics->breadcrumb ?></li>
        </ol>
    </nav>

    <h1 class="h4"><?= language()->api_documentation->statistics->header ?></h1>

    <div class="accordion">
        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#statistics_read" aria-expanded="true" aria-controls="statistics_read">
                        <?= language()->api_documentation->statistics->read_header ?>
                    </a>
                </h3>
            </div>

            <div id="statistics_read" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->endpoint ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-success mr-3">GET</span> <span class="text-muted"><?= SITE_URL ?>api/statistics/</span><span class="text-primary">{website_id}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->example ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request GET \<br />
                                --url '<?= SITE_URL ?>api/statistics/<span class="text-primary">{website_id}</span>?start_date=<span class="text-primary">2020-01-01</span>&end_date=<span class="text-primary">2021-01-01</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-custom-container mb-4">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= language()->api_documentation->parameters ?></th>
                                <th><?= language()->api_documentation->details ?></th>
                                <th><?= language()->api_documentation->description ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>start_date</td>
                                <td>
                                    <span class="badge badge-danger"><?= language()->api_documentation->required ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->statistics->start_date ?></td>
                            </tr>
                            <tr>
                                <td>end_date</td>
                                <td>
                                    <span class="badge badge-danger"><?= language()->api_documentation->required ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->statistics->end_date ?></td>
                            </tr>
                            <tr>
                                <td>type</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->statistics->type ?></td>
                            </tr>
                            <tr>
                                <td>country_code</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->statistics->country_code ?></td>
                            </tr>
                            <tr>
                                <td>utm_source</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->statistics->utm_source ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <label><?= language()->api_documentation->response ?></label>
                        <div class="card bg-gray-100 border-0">
                                        <pre class="card-body">
{
    "data": [
        {
          "path": "/dashboard",
          "pageviews": 500,
          "bounces": 10
        },
        {
          "path": "/websites",
          "pageviews": 250,
          "bounces": 0
        },
        {
          "path": "/",
          "pageviews": 200,
          "bounces": 36
        },
        {
          "path": "/register",
          "pageviews": 100,
          "bounces": 25
        },
        {
          "path": "login",
          "pageviews": 50,
          "bounces": 10
        },
    ]
}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
