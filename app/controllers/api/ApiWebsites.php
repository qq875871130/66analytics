<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Response;
use Altum\Traits\Apiable;

class ApiWebsites extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request();

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':

                /* Detect if we only need an object, or the whole list */
                if(isset($this->params[0])) {
                    $this->get();
                } else {
                    $this->get_all();
                }

            break;

            case 'POST':

                /* Detect what method to use */
                if(isset($this->params[0])) {
                    $this->patch();
                } else {
                    $this->post();
                }

            break;

            case 'DELETE':
                $this->delete();
            break;
        }

        $this->return_404();
    }

    private function get_all() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], [], []));
        $filters->set_default_order_by('website_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `websites` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/websites?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `websites`
            WHERE
                `user_id` = {$this->api_user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->website_id,
                'pixel_key' => $row->pixel_key,
                'name' => $row->name,
                'scheme' => $row->scheme,
                'host' => $row->host,
                'path' => $row->path,
                'tracking_type' => $row->tracking_type,
                'excluded_ips' => $row->excluded_ips,
                'events_children_is_enabled' => (bool) $row->events_children_is_enabled,
                'sessions_replays_is_enabled' => (bool) $row->sessions_replays_is_enabled,
                'email_reports_is_enabled' => (bool) $row->email_reports_is_enabled,
                'email_reports_last_date' => $row->email_reports_last_date,
                'is_enabled' => (bool) $row->is_enabled,
                'date' => $row->date,
            ];

            $data[] = $row;
        }

        /* Prepare the data */
        $meta = [
            'page' => $_GET['page'] ?? 1,
            'total_pages' => $paginator->getNumPages(),
            'results_per_page' => $filters->get_results_per_page(),
            'total_results' => (int) $total_rows,
        ];

        /* Prepare the pagination links */
        $others = ['links' => [
            'first' => $paginator->getPageUrl(1),
            'last' => $paginator->getNumPages() ? $paginator->getPageUrl($paginator->getNumPages()) : null,
            'next' => $paginator->getNextUrl(),
            'prev' => $paginator->getPrevUrl(),
            'self' => $paginator->getPageUrl($_GET['page'] ?? 1)
        ]];

        Response::jsonapi_success($data, $meta, 200, $others);
    }

    private function get() {

        $website_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $website = db()->where('website_id', $website_id)->where('user_id', $this->api_user->user_id)->getOne('websites');

        /* We haven't found the resource */
        if(!$website) {
            Response::jsonapi_error([[
                'title' => language()->api->error_message->not_found,
                'status' => '404'
            ]], null, 404);
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $website->website_id,
            'pixel_key' => $website->pixel_key,
            'name' => $website->name,
            'scheme' => $website->scheme,
            'host' => $website->host,
            'path' => $website->path,
            'tracking_type' => $website->tracking_type,
            'excluded_ips' => $website->excluded_ips,
            'events_children_is_enabled' => (bool) $website->events_children_is_enabled,
            'sessions_replays_is_enabled' => (bool) $website->sessions_replays_is_enabled,
            'email_reports_is_enabled' => (bool) $website->email_reports_is_enabled,
            'email_reports_last_date' => $website->email_reports_last_date,
            'is_enabled' => (bool) $website->is_enabled,
            'date' => $website->date,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for the plan limit */
        $total_rows = db()->where('user_id', $this->api_user->user_id)->getValue('websites', 'count(`website_id`)');

        if($this->api_user->plan_settings->websites_limit != -1 && $total_rows >= $this->api_user->plan_settings->websites_limit) {
            $this->response_error(language()->website_create_modal->error_message->websites_limit, 401);
        }

        /* Check for any errors */
        $required_fields = ['name', 'host'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(language()->global->error_message->empty_fields, 401);
                break 1;
            }
        }

        $_POST['name'] = trim($_POST['name']);
        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['https://', 'http://']) ? $_POST['scheme'] : 'https://';
        $_POST['host'] = mb_strtolower(trim($_POST['host']));
        $_POST['tracking_type'] = isset($_POST['tracking_type']) && in_array($_POST['tracking_type'], ['lightweight', 'normal']) ? Database::clean_string($_POST['tracking_type']) : 'lightweight';
        $_POST['events_children_is_enabled'] = (int) isset($_POST['events_children_is_enabled']);
        $_POST['sessions_replays_is_enabled'] = (int) isset($_POST['sessions_replays_is_enabled']);
        $_POST['email_reports_is_enabled'] = (int) isset($_POST['email_reports_is_enabled']);
        $_POST['excluded_ips'] = implode(',', array_map(function($value) {
            return Database::clean_string(trim($value));
        }, explode(',', $_POST['excluded_ips'] ?? null)));
        $is_enabled = 1;

        /* Domain checking */
        $pslManager = new \Pdp\PublicSuffixListManager();
        $parser = new \Pdp\Parser($pslManager->getList());
        $url = $parser->parseUrl($_POST['host']);
        $punnnycode = new \TrueBV\Punycode();
        $host = Database::clean_string($punnnycode->encode($url->getHost()));
        $path = Database::clean_string($url->getPath()) ? preg_replace('/\/+$/', '', Database::clean_string($url->getPath())) : null;

        /* Generate an unique pixel key for the website */
        $pixel_key = string_generate(16);
        while(db()->where('pixel_key', $pixel_key)->getOne('websites', ['pixel_key'])) {
            $pixel_key = string_generate(16);
        }

        /* Database query */
        $website_id = db()->insert('websites', [
            'user_id' => $this->api_user->user_id,
            'pixel_key' => $pixel_key,
            'name' => $_POST['name'],
            'scheme' => $_POST['scheme'],
            'host' => $host,
            'path' => $path,
            'excluded_ips' => $_POST['excluded_ips'],
            'tracking_type' => $_POST['tracking_type'],
            'events_children_is_enabled' => $_POST['events_children_is_enabled'],
            'sessions_replays_is_enabled' => $_POST['sessions_replays_is_enabled'],
            'email_reports_is_enabled' => $_POST['email_reports_is_enabled'],
            'email_reports_last_date' => Date::$date,
            'is_enabled' => $is_enabled,
            'date' => Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('websites_' . $this->api_user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('website_id=' . $website_id);

        /* Prepare the data */
        $data = [
            'id' => $website_id
        ];

        Response::jsonapi_success($data, null, 201);

    }

    private function patch() {

        $website_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $website = db()->where('website_id', $website_id)->where('user_id', $this->api_user->user_id)->getOne('websites');

        /* We haven't found the resource */
        if(!$website) {
            $this->response_error(language()->api->error_message->not_found, 404);
        }

        $_POST['name'] = trim($_POST['name'] ?? $website->name);
        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['https://', 'http://']) ? $_POST['scheme'] : $website->scheme;
        $_POST['host'] = mb_strtolower(trim($_POST['host'] ?? $website->host));
        $_POST['events_children_is_enabled'] = (int) ($_POST['events_children_is_enabled'] ?? $website->events_children_is_enabled);
        $_POST['sessions_replays_is_enabled'] = (int) ($_POST['sessions_replays_is_enabled'] ?? $website->sessions_replays_is_enabled);
        $_POST['email_reports_is_enabled'] = (int) ($_POST['email_reports_is_enabled'] ?? $website->email_reports_is_enabled);
        $_POST['is_enabled'] = (int) ($_POST['is_enabled'] ?? $website->is_enabled);
        $_POST['excluded_ips'] = implode(',', array_map(function($value) {
            return Database::clean_string(trim($value));
        }, explode(',', $_POST['excluded_ips'] ?? $website->excluded_ips)));

        /* Domain checking */
        $pslManager = new \Pdp\PublicSuffixListManager();
        $parser = new \Pdp\Parser($pslManager->getList());
        $url = $parser->parseUrl($_POST['host']);
        $punnnycode = new \TrueBV\Punycode();
        $host = Database::clean_string($punnnycode->encode($url->getHost()));
        $path = Database::clean_string($url->getPath()) ? preg_replace('/\/+$/', '', Database::clean_string($url->getPath())) : null;

        /* Database query */
        db()->where('website_id', $website_id)->where('user_id', $this->api_user->user_id)->update('websites', [
            'name' => $_POST['name'],
            'scheme' => $_POST['scheme'],
            'host' => $host,
            'path' => $path,
            'excluded_ips' => $_POST['excluded_ips'],
            'events_children_is_enabled' => $_POST['events_children_is_enabled'],
            'sessions_replays_is_enabled' => $_POST['sessions_replays_is_enabled'],
            'email_reports_is_enabled' => $_POST['email_reports_is_enabled'],
            'is_enabled' => $_POST['is_enabled'],
            'date' => Date::$date,
        ]);

        /* Clear cache */
        \Altum\Cache::$adapter->deleteItem('websites_' . $this->api_user->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('website_id=' . $website_id);

        /* Prepare the data */
        $data = [
            'id' => $website->website_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $website_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $website = db()->where('website_id', $website_id)->where('user_id', $this->api_user->user_id)->getOne('websites');

        /* We haven't found the resource */
        if(!$website) {
            $this->response_error(language()->api->error_message->not_found, 404);
        }

        /* Delete from database */
        db()->where('website_id', $website_id)->where('user_id', $this->api_user->user_id)->delete('websites');

        /* Clear cache */
        \Altum\Cache::$adapter->deleteItem('websites_' . $this->api_user->user_id);
        \Altum\Cache::$store_adapter->deleteItemsByTag('session_replay_website_' . $website_id);
        \Altum\Cache::$adapter->deleteItemsByTag('website_id=' . $website_id);

        http_response_code(200);
        die();

    }

}
