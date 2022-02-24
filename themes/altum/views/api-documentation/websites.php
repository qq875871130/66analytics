<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li><a href="<?= url() ?>"><?= language()->index->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li><a href="<?= url('api-documentation') ?>"><?= language()->api_documentation->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
            <li class="active" aria-current="page"><?= language()->api_documentation->websites->breadcrumb ?></li>
        </ol>
    </nav>

    <h1 class="h4"><?= language()->api_documentation->websites->header ?></h1>

    <div class="accordion">
        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#websites_read_all" aria-expanded="true" aria-controls="websites_read_all">
                        <?= language()->api_documentation->websites->read_all_header ?>
                    </a>
                </h3>
            </div>

            <div id="websites_read_all" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->endpoint ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-success mr-3">GET</span> <span class="text-muted"><?= SITE_URL ?>api/websites/</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->example ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request GET \<br />
                                --url '<?= SITE_URL ?>api/websites/' \<br />
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
                                <td>page</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->int ?></span>
                                </td>
                                <td><?= language()->api_documentation->filters->page ?></td>
                            </tr>
                            <tr>
                                <td>results_per_page</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->int ?></span>
                                </td>
                                <td><?= sprintf(language()->api_documentation->filters->results_per_page, '<code>' . implode('</code> , <code>', [10, 25, 50, 100, 250, 500]) . '</code>', 25) ?></td>
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
            "id": 1,
            "pixel_key": "1234567890123456",
            "name": "Localhost",
            "scheme": "https://",
            "host": "example.com",
            "path": "/",
            "tracking_type": "normal",
            "excluded_ips": "",
            "events_children_is_enabled": false,
            "sessions_replays_is_enabled": false,
            "email_reports_is_enabled": false,
            "email_reports_last_date": "2020-06-23 19:01:22",
            "is_enabled": true,
            "date": "2019-11-01 12:00:30"
        },
    ],
    "meta": {
        "page": 1,
        "results_per_page": 25,
        "total": 1,
        "total_pages": 1
    },
    "links": {
        "first": "<?= SITE_URL ?>api/websites?&page=1",
        "last": "<?= SITE_URL ?>api/websites?&page=1",
        "next": null,
        "prev": null,
        "self": "<?= SITE_URL ?>api/websites?&page=1"
    }
}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#websites_read" aria-expanded="true" aria-controls="websites_read">
                        <?= language()->api_documentation->websites->read_header ?>
                    </a>
                </h3>
            </div>

            <div id="websites_read" class="collapse">
                <div class="card-body">


                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->endpoint ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-success mr-3">GET</span> <span class="text-muted"><?= SITE_URL ?>api/websites/</span><span class="text-primary">{website_id}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->example ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request GET \<br />
                                --url '<?= SITE_URL ?>api/websites/<span class="text-primary">{website_id}</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= language()->api_documentation->response ?></label>
                        <div class="card bg-gray-100 border-0">
                                        <pre class="card-body">
{
    "data": {
        "id": 1,
        "pixel_key": "1234567890123456",
        "name": "Localhost",
        "scheme": "https://",
        "host": "example.com",
        "path": "/",
        "tracking_type": "normal",
        "excluded_ips": "",
        "events_children_is_enabled": false,
        "sessions_replays_is_enabled": false,
        "email_reports_is_enabled": false,
        "email_reports_last_date": "2020-06-23 19:01:22",
        "is_enabled": true,
        "date": "2019-11-01 12:00:30"
    }
}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#websites_create" aria-expanded="true" aria-controls="websites_create">
                        <?= language()->api_documentation->websites->create_header ?>
                    </a>
                </h3>
            </div>

            <div id="websites_create" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->endpoint ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-info mr-3">POST</span> <span class="text-muted"><?= SITE_URL ?>api/websites</span>
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
                                <td>name</td>
                                <td>
                                    <span class="badge badge-danger"><?= language()->api_documentation->required ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>scheme</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->scheme ?></td>
                            </tr>
                            <tr>
                                <td>host</td>
                                <td>
                                    <span class="badge badge-danger"><?= language()->api_documentation->required ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->host ?></td>
                            </tr>
                            <tr>
                                <td>tracking_type</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->tracking_type ?></td>
                            </tr>
                            <tr>
                                <td>excluded_ips</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->excluded_ips ?></td>
                            </tr>
                            <tr>
                                <td>events_children_is_enabled</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->boolean ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->events_children_is_enabled ?></td>
                            </tr>
                            <tr>
                                <td>sessions_replays_is_enabled</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->boolean ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->sessions_replays_is_enabled ?></td>
                            </tr>
                            <tr>
                                <td>email_reports_is_enabled</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->boolean ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->email_reports_is_enabled ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->example ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request POST \<br />
                                --url '<?= SITE_URL ?>api/websites' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                                --header 'Content-Type: multipart/form-data' \<br />
                                --form 'host=<span class="text-primary">website.com</span>' \<br />
                                --form 'name=<span class="text-primary">Example</span>' \<br />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= language()->api_documentation->response ?></label>
                        <div class="card bg-gray-100 border-0">
                                        <pre class="card-body">
{
    "data": {
        "id": 1
    }
}</pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#websites_update" aria-expanded="true" aria-controls="websites_update">
                        <?= language()->api_documentation->websites->update_header ?>
                    </a>
                </h3>
            </div>

            <div id="websites_update" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->endpoint ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-info mr-3">POST</span> <span class="text-muted"><?= SITE_URL ?>api/websites/</span><span class="text-primary">{website_id}</span>
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
                                <td>name</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>scheme</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->scheme ?></td>
                            </tr>
                            <tr>
                                <td>host</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->host ?></td>
                            </tr>
                            <tr>
                                <td>excluded_ips</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->string ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->excluded_ips ?></td>
                            </tr>
                            <tr>
                                <td>events_children_is_enabled</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->boolean ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->events_children_is_enabled ?></td>
                            </tr>
                            <tr>
                                <td>sessions_replays_is_enabled</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->boolean ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->sessions_replays_is_enabled ?></td>
                            </tr>
                            <tr>
                                <td>email_reports_is_enabled</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->boolean ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->email_reports_is_enabled ?></td>
                            </tr>
                            <tr>
                                <td>is_enabled</td>
                                <td>
                                    <span class="badge badge-info"><?= language()->api_documentation->optional ?></span>
                                    <span class="badge badge-secondary"><?= language()->api_documentation->boolean ?></span>
                                </td>
                                <td><?= language()->api_documentation->websites->is_enabled ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->example ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request POST \<br />
                                --url '<?= SITE_URL ?>api/websites/<span class="text-primary">{website_id}</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                                --header 'Content-Type: multipart/form-data' \<br />
                                --form 'name=<span class="text-primary">Example</span>' \<br />
                                --form 'is_enabled=<span class="text-primary">0</span>' \<br />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= language()->api_documentation->response ?></label>
                        <div class="card bg-gray-100 border-0">
                                        <pre class="card-body">
{
    "data": {
        "id": 1
    }
}</pre>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-gray-50 p-3 position-relative">
                <h3 class="h6 m-0">
                    <a href="#" class="stretched-link" data-toggle="collapse" data-target="#websites_delete" aria-expanded="true" aria-controls="websites_delete">
                        <?= language()->api_documentation->websites->delete_header ?>
                    </a>
                </h3>
            </div>

            <div id="websites_delete" class="collapse">
                <div class="card-body">

                    <div class="form-group mb-4">
                        <label><?= language()->api_documentation->endpoint ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                <span class="badge badge-danger mr-3">DELETE</span> <span class="text-muted"><?= SITE_URL ?>api/websites/</span><span class="text-primary">{website_id}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= language()->api_documentation->example ?></label>
                        <div class="card bg-gray-100 border-0">
                            <div class="card-body">
                                curl --request DELETE \<br />
                                --url '<?= SITE_URL ?>api/websites/<span class="text-primary">{website_id}</span>' \<br />
                                --header 'Authorization: Bearer <span class="text-primary">{api_key}</span>' \<br />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
