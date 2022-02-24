<?php defined('ALTUMCODE') || die() ?>

<header class="header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li><a href="<?= url('teams') ?>"><?= language()->teams->breadcrumb ?></a> <i class="fa fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page"><?= language()->team->breadcrumb ?></li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-md-row justify-content-between">
            <div>
                <h1 class="h3"><i class="fa fa-fw fa-xs fa-users text-gray-700"></i> <?= sprintf(language()->team->team->header, $data->team->name) ?></h1>
                <p class="text-muted"><?= sprintf(language()->team->team->subheader, \Altum\Date::get($data->team->date, 2)) ?></p>
                <p>
                    <?php foreach($data->team->websites_ids as $website_id): ?>
                        <span class="badge badge-primary mr-1"><?= $this->websites[$website_id]->host . $this->websites[$website_id]->path ?></span>
                    <?php endforeach ?>
                </p>
            </div>
        </div>

    </div>
</header>

<?php require THEME_PATH . 'views/partials/ads_header.php' ?>

<section class="container">

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="mt-8">
        <div class="d-flex flex-column flex-md-row justify-content-between mb-5">
            <div>
                <h2 class="h4"><?= language()->team->teams_associations->header ?></h2>
                <p class="text-muted"><?= language()->team->teams_associations->subheader ?></p>
            </div>

            <div class="col-auto p-0">
                <button type="button" data-toggle="modal" data-target="#team_association_create" class="btn btn-primary rounded-pill"><i class="fa fa-fw fa-plus-circle"></i> <?= language()->team->teams_associations->create ?></button>
            </div>
        </div>

        <?php if($data->teams_associations_result->num_rows): ?>
        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= language()->team->teams_associations->user ?></th>
                    <th><?= language()->team->teams_associations->date ?></th>
                    <th><?= language()->team->teams_associations->is_accepted ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                <?php while($team_association = $data->teams_associations_result->fetch_object()): ?>
                    <tr data-team-association-id="<?= $team_association->team_association_id ?>">
                        <td>
                            <div class="d-flex">

                                <?php if($team_association->is_accepted): ?>
                                    <img src="<?= get_gravatar($team_association->email) ?>" class="team-user-avatar rounded-circle shadow-sm mr-3" alt="" />

                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold"><?= $team_association->name ?></span>
                                        <span class="text-muted"><?= $team_association->email ?></span>
                                    </div>
                                <?php else: ?>
                                    <img src="<?= get_gravatar($team_association->user_email) ?>" class="team-user-avatar rounded-circle shadow-sm mr-3" alt="" />

                                    <div class="d-flex flex-column align-self-center">
                                        <span class="text-muted"><?= $team_association->user_email ?></span>
                                    </div>
                                <?php endif ?>
                            </div>
                        </td>

                        <td class="text-muted">
                            <?= \Altum\Date::get($team_association->date, 2) ?>
                        </td>

                        <td>
                            <?php if($team_association->is_accepted): ?>
                            <span class="badge badge-pill badge-success">
                                <i class="fa fa-fw fa-check"></i> <?= language()->team->teams_associations->accepted_date ?>
                            </span>
                            <small class="text-muted"><?= \Altum\Date::get($team_association->date, 2) ?></small>
                            <?php else: ?>
                            <span class="badge badge-pill badge-warning">
                                <?= language()->team->teams_associations->is_accepted_invited ?>
                            </span>
                            <?php endif ?>
                        </td>

                        <td>
                            <a
                                    href="#"
                                    class="text-muted text-decoration-none"
                                    data-toggle="modal"
                                    data-target="#team_association_delete"
                                    data-team-association-id="<?= $team_association->team_association_id ?>"
                            >
                                <i class="fa fa-fw fa-times fa-sm"></i> <?= language()->global->delete ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile ?>

                </tbody>
            </table>
        </div>

        <?php else: ?>
        <div class="d-flex flex-column align-items-center justify-content-center">
            <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-6 col-lg-4 mb-3" alt="<?= language()->team->teams_associations->no_data ?>" />
            <h2 class="h4 text-muted"><?= language()->team->teams_associations->no_data ?></h2>
            <p><?= language()->team->teams_associations->no_data_help ?></a></p>
        </div>
        <?php endif ?>
    </div>
</section>
