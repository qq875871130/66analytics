<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Middlewares\Csrf;
use Altum\Models\Model;

class AdminWebsites extends Controller {

    public function index() {

        /* Some statistics for the widgets */
        $total_sessions_events = database()->query("SELECT MAX(`event_id`) AS `total` FROM `sessions_events`")->fetch_object()->total;
        $total_events_children = database()->query("SELECT MAX(`id`) AS `total` FROM `events_children`")->fetch_object()->total;
        $total_sessions_replays = database()->query("SELECT MAX(`replay_id`) AS `total` FROM `sessions_replays`")->fetch_object()->total;

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'tracking_type', 'user_id'], ['name', 'host'], ['email', 'date', 'name']));
        $filters->set_default_order_by('website_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `websites` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/websites?' . $filters->get_get() . '&page=%d')));

        /* Get the users */
        $websites = [];
        $websites_result = database()->query("
            SELECT
                `websites`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`
            FROM
                `websites`
            LEFT JOIN
                `users` ON `websites`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('websites')}
                {$filters->get_sql_order_by('websites')}
            
            {$paginator->get_sql_limit()}
        ");
        while($row = $websites_result->fetch_object()) {
            $websites[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'total_sessions_events' => $total_sessions_events,
            'total_events_children' => $total_events_children,
            'total_sessions_replays' => $total_sessions_replays,
            'websites' => $websites,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\Views\View('admin/websites/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/websites');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/websites');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/websites');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check()) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $website_id) {

                        if(!$website = db()->where('website_id', $website_id)->getOne('websites', ['website_id', 'user_id'])) {
                            continue;
                        }

                        /* Delete the website */
                        db()->where('website_id', $website_id)->delete('websites');

                        /* Clear cache */
                        \Altum\Cache::$adapter->deleteItem('websites_' . $website->user_id);
                        \Altum\Cache::$store_adapter->deleteItemsByTag('session_replay_website_' . $website_id);
                        \Altum\Cache::$adapter->deleteItemsByTag('website_id=' . $website_id);

                    }
                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(language()->admin_bulk_delete_modal->success_message);

        }

        redirect('admin/websites');
    }

    public function delete() {

        $website_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!Csrf::check('global_token')) {
            Alerts::add_error(language()->global->error_message->invalid_csrf_token);
        }

        if(!$website = db()->where('website_id', $website_id)->getOne('websites', ['website_id', 'user_id', 'name'])) {
            redirect('admin/websites');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the website */
            db()->where('website_id', $website->website_id)->delete('websites');

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItem('websites_' . $website->user_id);
            \Altum\Cache::$store_adapter->deleteItemsByTag('session_replay_website_' . $website->website_id);
            \Altum\Cache::$adapter->deleteItemsByTag('website_id=' . $website->website_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(language()->global->success_message->delete1, '<strong>' . $website->name . '</strong>'));

        }

        redirect('admin/websites');
    }

}
